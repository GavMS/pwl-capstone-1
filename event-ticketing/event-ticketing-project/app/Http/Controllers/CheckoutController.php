<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Events;
use App\Models\EventTicketType;
use App\Models\ShoppingSession;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Models\VoucherUsage;

/**
 * CheckoutController
 *
 * Handles the full ticket purchase flow:
 *   1. confirm()  → Order review page (stock pre-check)
 *   2. process()  → Create Transaction + Midtrans Snap token
 *   3. payment()  → Show payment page
 *   4. recreate() → Retry failed payment with existing deadline
 *   5. mockSuccess() → Dev-only instant payment simulation
 *
 * Used by: routes/web.php (checkout.* routes)
 */
class CheckoutController extends Controller
{
    /**
     * Step 1: Validate selected tickets and show the order-details page.
     * Requires active ShoppingSession (queue guard).
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'event_id'           => 'required|integer',
            'tickets'            => 'required|array|min:1',
            'tickets.*.id'       => 'required|integer',
            'tickets.*.quantity' => 'required|integer|min:1|max:10',
        ]);

        $eventId      = $request->input('event_id');
        $ticketsInput = $request->input('tickets');

        // Queue guard: only users with an active ShoppingSession may proceed
        $session = ShoppingSession::where('user_id', Auth::id())
            ->where('event_id', $eventId)
            ->first();

        if (!$session || $session->isExpired()) {
            return redirect()->route('events.show', $eventId)
                ->with('error', 'Access denied. Please join the queue first.');
        }

        $event           = Events::findOrFail($eventId);
        $selectedTickets  = [];
        $totalPrice       = 0;

        foreach ($ticketsInput as $ticketData) {
            $ett = EventTicketType::with('ticketType')->findOrFail($ticketData['id']);

            if ($ett->stock < $ticketData['quantity']) {
                return back()->withErrors(['checkout' => "Ticket stock for '{$ett->ticketType->name}' is insufficient."]);
            }

            $totalPrice       += $ett->price * $ticketData['quantity'];
            $selectedTickets[] = [
                'id'       => $ett->id,
                'name'     => $ett->ticketType->name,
                'price'    => $ett->price,
                'quantity' => $ticketData['quantity'],
            ];
        }

        return view('user.order-details', compact('event', 'selectedTickets', 'totalPrice'));
    }

    /**
     * Step 2: Create a Transaction record, request Midtrans Snap token, redirect to payment.
     */
    public function process(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'event_id'          => 'required|integer',
            'tickets'           => 'required|json',
            'attendees'         => 'required|array',
            'attendees.*.name'  => 'required|string|max:255',
            'attendees.*.email' => 'required|email',
            'attendees.*.phone' => 'required|string|max:20',
            'attendees.*.id_card' => 'required|string|max:50',
            'agreement'         => 'accepted',
            'voucher_code'      => 'nullable|string',
        ]);

        $eventId = $request->input('event_id');

        // Double-check session validity (prevents race condition if session expired mid-form)
        $queueSession = ShoppingSession::where('user_id', $user->id)
            ->where('event_id', $eventId)
            ->first();

        if (!$queueSession || $queueSession->isExpired()) {
            return redirect()->route('events.show', $eventId)
                ->with('error', 'Your queue session has expired. Please restart the process.');
        }

        // Parse ticket payload & validate stock
        $tickets      = json_decode($request->input('tickets'), true);
        $totalPrice   = 0;
        $ticketPayload = [];

        foreach ($tickets as $ticketData) {
            $ett = EventTicketType::with('ticketType')->findOrFail($ticketData['id']);
            $qty = (int) $ticketData['quantity'];

            if ($ett->stock < $qty) {
                return back()->withErrors(['checkout' => "Sorry, the ticket stock for '{$ett->ticketType->name}' has run out."]);
            }

            $totalPrice     += $ett->price * $qty;
            $ticketPayload[] = [
                'event_ticket_type_id' => $ett->id,
                'event_title'          => $ett->event->title ?? 'Event',
                'ticket_name'          => $ett->ticketType->name,
                'price'                => $ett->price,
                'quantity'             => $qty,
            ];
        }

        $orderId  = 'TRX-' . time() . '-' . Str::random(5);
        $deadline = now()->addMinutes(15);

        // Apply voucher discount (if provided and valid)
        $appliedVoucherId = null;
        $discountAmount   = 0;

        if ($request->filled('voucher_code')) {
            $voucher = Voucher::where('code', strtoupper($request->voucher_code))->first();

            if ($voucher) {
                [$isValid, $message] = $voucher->isValidForOrder($eventId, $totalPrice);

                $hasUsed = VoucherUsage::where('voucher_id', $voucher->id)
                    ->where('user_id', $user->id)
                    ->whereNotNull('transaction_id')
                    ->exists();

                if ($isValid && !$hasUsed) {
                    $discountAmount   = $voucher->calculateDiscount($totalPrice);
                    $totalPrice       = max(0, $totalPrice - $discountAmount);
                    $appliedVoucherId = $voucher->id;
                }
            }
        }

        // Create transaction record
        $transaction = Transaction::create([
            'accounts_id'      => $user->id,
            'order_id'         => $orderId,
            'total_price'      => $totalPrice,
            'status'           => 'pending',
            'ticket_payload'   => $ticketPayload,
            'customer_details' => $request->input('attendees'),
            'deadline_payment' => $deadline,
            'voucher_id'       => $appliedVoucherId,
            'discount_amount'  => $discountAmount,
        ]);

        // Release queue session immediately — ticket lock is now in the Transaction
        $queueSession->delete();

        // Request Midtrans Snap token
        $this->initMidtrans();

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->input('attendees')[0]['name'],
                'email'      => $request->input('attendees')[0]['email'],
                'phone'      => $request->input('attendees')[0]['phone'],
            ],
            'expiry' => [
                'unit'     => 'second',
                'duration' => 900,
            ],
            'callbacks' => [
                'finish'   => route('user.my-tickets'),
                'unfinish' => route('checkout.payment', $orderId),
                'error'    => route('user.my-tickets'),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return redirect()->route('checkout.payment', $transaction->order_id);
        } catch (\Exception $e) {
            return redirect()->route('user.my-tickets')
                ->with('error', 'Midtrans Error: ' . $e->getMessage() . '. You can retry payment here.');
        }
    }

    /**
     * Show the payment page for a pending transaction.
     */
    public function payment($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)
            ->where('accounts_id', Auth::id())
            ->firstOrFail();

        if ($transaction->status === 'success') {
            return redirect()->route('user.my-tickets')->with('success', 'Transaksi ini sudah lunas.');
        }

        return view('user.payment', compact('transaction'));
    }

    /**
     * Recreate a failed/expired transaction with a new Snap token.
     * Preserves the original payment deadline and carries over voucher.
     */
    public function recreate(Request $request, $order_id)
    {
        $oldTransaction = Transaction::where('order_id', $order_id)
            ->where('accounts_id', Auth::id())
            ->firstOrFail();

        if ($oldTransaction->status === 'success') {
            return redirect()->route('user.my-tickets')->with('success', 'Transaksi ini sudah lunas.');
        }

        // Calculate remaining time from original deadline
        $deadline = $oldTransaction->deadline_payment
            ? Carbon::parse($oldTransaction->deadline_payment)
            : Carbon::parse($oldTransaction->created_at)->addMinutes(15);

        $timeLeftSeconds = (int) now()->diffInSeconds($deadline, false);

        if ($timeLeftSeconds <= 0) {
            $oldTransaction->update(['status' => 'expired']);
            return redirect()->route('user.my-tickets')->with('error', 'Payment time has expired.');
        }

        // Mark old transaction as failed and create a new one
        $oldTransaction->update(['status' => 'failed']);

        $newOrderId    = 'TRX-' . time() . '-' . Str::random(5);
        $newTransaction = Transaction::create([
            'accounts_id'      => $oldTransaction->accounts_id,
            'order_id'         => $newOrderId,
            'total_price'      => $oldTransaction->total_price,
            'status'           => 'pending',
            'ticket_payload'   => $oldTransaction->ticket_payload,
            'customer_details' => $oldTransaction->customer_details,
            'deadline_payment' => $deadline,
            'voucher_id'       => $oldTransaction->voucher_id,       // carry over voucher
            'discount_amount'  => $oldTransaction->discount_amount,  // carry over discount
        ]);

        $this->initMidtrans();

        $params = [
            'transaction_details' => [
                'order_id'     => $newOrderId,
                'gross_amount' => $newTransaction->total_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
            ],
            'callbacks' => [
                'finish'   => route('user.my-tickets'),
                'unfinish' => route('checkout.payment', $newOrderId),
                'error'    => route('user.my-tickets'),
            ],
            'expiry' => [
                'unit'     => 'second',
                'duration' => max(1, $timeLeftSeconds),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $newTransaction->update(['snap_token' => $snapToken]);
            return redirect()->route('checkout.payment', $newTransaction->order_id);
        } catch (\Exception $e) {
            return back()->withErrors(['checkout' => 'Failed requesting new Snap Token: ' . $e->getMessage()]);
        }
    }

    /**
     * Developer-only: simulate a successful payment without Midtrans.
     * Calls the centralised Transaction::issueTickets() method.
     */
    public function mockSuccess($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)
            ->where('accounts_id', Auth::id())
            ->firstOrFail();

        if ($transaction->status === 'success') {
            return redirect()->route('user.my-tickets');
        }

        $transaction->status         = 'success';
        $transaction->payment_method = 'developer_mock';
        $transaction->save();

        // Issue tickets only if none have been issued yet (idempotent guard)
        if ($transaction->issuedTickets()->count() === 0) {
            DB::beginTransaction();
            try {
                $transaction->issueTickets();

                // Clean up any lingering queue session
                $this->releaseQueueSession($transaction);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['checkout' => 'Mock payment failed: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('user.my-tickets')->with('success', 'Mock Payment Success!');
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Initialise Midtrans SDK configuration.
     */
    private function initMidtrans(): void
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds        = config('midtrans.is_3ds');

        \Midtrans\Config::$overrideNotifUrl = env('NGROK_URL')
            ? env('NGROK_URL') . '/midtrans/callback'
            : url('/midtrans/callback');
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
            ShoppingSession::where('user_id', Auth::id())
                ->where('event_id', $ett->event->id_event)
                ->delete();
        }
    }
}
