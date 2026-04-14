<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\EventTicketType;
use App\Models\IssuedTicket;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        if (!$notification) {
            return response(['message' => 'Invalid payload'], 400);
        }

        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('midtrans.server_key'));

        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where('order_id', $notification->order_id)->first();

        if (!$transaction) {
            return response(['message' => 'Transaction not found'], 404);
        }

        $transactionStatus = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status ?? '';

        if (in_array($transaction->status, ['success'])) {
            return response(['message' => 'Already processed']);
        }

        $newStatus = $transaction->status;
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $newStatus = 'success';
            }
        } else if ($transactionStatus == 'settlement') {
            $newStatus = 'success';
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $newStatus = 'expired';
        } else if ($transactionStatus == 'pending') {
            $newStatus = 'pending';
        }

        $transaction->status = $newStatus;
        $transaction->payment_method = $type;
        $transaction->save();

        if ($transaction->status == 'success' && $transaction->issuedTickets()->count() == 0) {
            DB::beginTransaction();
            try {
                $payloadArray = $transaction->ticket_payload;
                $customerDetails = $transaction->customer_details;
                $attendeeIndex = 0;

                foreach ($payloadArray as $item) {
                    $ett = EventTicketType::lockForUpdate()->findOrFail($item['event_ticket_type_id']);

                    if ($ett->stock >= $item['quantity']) {
                        $ett->decrement('stock', $item['quantity']);

                        for ($i = 0; $i < $item['quantity']; $i++) {
                            $attendee = $customerDetails[$attendeeIndex] ?? null;

                            $ticket = IssuedTicket::create([
                                'user_id' => $transaction->accounts_id,
                                'event_ticket_type_id' => $ett->id,
                                'transaction_id' => $transaction->id,
                                'unique_code' => 'TIX-' . strtoupper(Str::random(10)),
                                'status' => 'active',
                                'attendee_name'        => $attendee['name'] ?? null,
                                'attendee_email'       => $attendee['email'] ?? null,
                                'attendee_phone'       => $attendee['phone'] ?? null,
                                'attendee_id_card'     => $attendee['id_card'] ?? null,
                                'attendee_dob'         => $attendee['dob'] ?? null,
                                'attendee_gender'      => $attendee['gender'] ?? null,
                            ]);

                            if ($attendee && !empty($attendee['email'])) {
                                try {
                                    \Illuminate\Support\Facades\Mail::to($attendee['email'])->send(new \App\Mail\TicketMailable($ticket));
                                } catch (\Exception $mailEx) {
                                    Log::error("Failed to send ticket email: " . $mailEx->getMessage());
                                }
                            }

                            $attendeeIndex++;
                        }
                    } else {
                        Log::error("Out of stock during callback for order {$orderId}");
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Ticket generation failed: " . $e->getMessage());
                return response(['message' => 'Internal error'], 500);
            }
        }

        return response(['message' => 'OK']);
    }
}
