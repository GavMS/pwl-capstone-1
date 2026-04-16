<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\EventTicketType;
use App\Models\ShoppingSession;

/**
 * PaymentCallbackController
 *
 * Handles Midtrans webhook notifications (server-to-server).
 * When payment is confirmed, issues tickets via Transaction::issueTickets().
 *
 * Route: POST /midtrans/callback (excluded from CSRF verification)
 */
class PaymentCallbackController extends Controller
{
    /**
     * Process incoming Midtrans payment notification.
     */
    public function handle(Request $request)
    {
        $notification = json_decode($request->getContent());

        if (!$notification) {
            return response(['message' => 'Invalid payload'], 400);
        }

        // Verify signature to prevent spoofed callbacks
        $expectedSignature = hash(
            'sha512',
            $notification->order_id
                . $notification->status_code
                . $notification->gross_amount
                . config('midtrans.server_key')
        );

        if ($notification->signature_key !== $expectedSignature) {
            return response(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where('order_id', $notification->order_id)->first();

        if (!$transaction) {
            return response(['message' => 'Transaction not found'], 404);
        }

        // Skip if already successfully processed (idempotent)
        if ($transaction->status === 'success') {
            return response(['message' => 'Already processed']);
        }

        // Map Midtrans status to internal status
        $newStatus = $this->mapTransactionStatus(
            $notification->transaction_status,
            $notification->fraud_status ?? ''
        );

        $transaction->status         = $newStatus;
        $transaction->payment_method = $notification->payment_type;
        $transaction->save();

        // Issue tickets on successful payment (only if none issued yet)
        if ($newStatus === 'success' && $transaction->issuedTickets()->count() === 0) {
            DB::beginTransaction();
            try {
                $transaction->issueTickets();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Ticket generation failed for {$transaction->order_id}: " . $e->getMessage());
                return response(['message' => 'Internal error'], 500);
            }
        }

        // Clean up queue session on terminal states (success or expired)
        if (in_array($newStatus, ['success', 'expired'])) {
            $this->releaseQueueSession($transaction);
        }

        return response(['message' => 'OK']);
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Map Midtrans transaction_status to our internal status string.
     */
    private function mapTransactionStatus(string $midtransStatus, string $fraudStatus): string
    {
        if ($midtransStatus === 'capture' && $fraudStatus === 'accept') {
            return 'success';
        }

        if ($midtransStatus === 'settlement') {
            return 'success';
        }

        if (in_array($midtransStatus, ['cancel', 'deny', 'expire'])) {
            return 'expired';
        }

        return 'pending'; // Default: still waiting
    }

    /**
     * Release the ShoppingSession tied to this transaction's event.
     */
    private function releaseQueueSession(Transaction $transaction): void
    {
        $payload = $transaction->ticket_payload;
        if (empty($payload)) return;

        $ett = EventTicketType::find($payload[0]['event_ticket_type_id'] ?? null);

        if ($ett && $ett->event) {
            ShoppingSession::where('user_id', $transaction->accounts_id)
                ->where('event_id', $ett->event->id_event)
                ->delete();
        }
    }
}
