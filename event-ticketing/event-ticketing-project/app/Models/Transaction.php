<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Transaction Model
 *
 * Represents a ticket purchase order. Stores payment info, ticket payload,
 * and customer (attendee) details. Used by CheckoutController & PaymentCallbackController.
 */
class Transaction extends Model
{
    protected $table = 'transaction';

    protected $guarded = ['id'];

    protected $casts = [
        'ticket_payload'   => 'array',
        'customer_details' => 'array',
        'deadline_payment' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────

    /** Buyer account */
    public function user()
    {
        return $this->belongsTo(Accounts::class, 'accounts_id');
    }

    /** Tickets generated after successful payment */
    public function issuedTickets()
    {
        return $this->hasMany(IssuedTicket::class);
    }

    /** Applied voucher (nullable) */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    // ─── Business Logic ──────────────────────────────────

    /**
     * Issue tickets for this transaction.
     *
     * Centralised logic shared by MockPayment (CheckoutController) and
     * the Midtrans webhook (PaymentCallbackController). Handles:
     *   1. Stock decrement with pessimistic locking
     *   2. IssuedTicket creation per attendee
     *   3. Email with PDF ticket attachment
     *   4. Voucher usage recording
     *
     * @return void
     * @throws \Exception on stock / DB error — caller should catch & rollback
     */
    public function issueTickets(): void
    {
        $payloadArray    = $this->ticket_payload;
        $customerDetails = $this->customer_details;
        $attendeeIndex   = 0;

        foreach ($payloadArray as $item) {
            $ett = EventTicketType::lockForUpdate()->findOrFail($item['event_ticket_type_id']);

            if ($ett->stock < $item['quantity']) {
                Log::error("Out of stock during ticket issuance for order {$this->order_id}");
                continue; // Skip — don't break the entire batch
            }

            $ett->decrement('stock', $item['quantity']);

            for ($i = 0; $i < $item['quantity']; $i++) {
                $attendee = $customerDetails[$attendeeIndex] ?? null;

                $ticket = IssuedTicket::create([
                    'user_id'              => $this->accounts_id,
                    'event_ticket_type_id' => $ett->id,
                    'transaction_id'       => $this->id,
                    'unique_code'          => 'TIX-' . strtoupper(Str::random(10)),
                    'status'               => 'active',
                    'attendee_name'        => $attendee['name']    ?? null,
                    'attendee_email'       => $attendee['email']   ?? null,
                    'attendee_phone'       => $attendee['phone']   ?? null,
                    'attendee_id_card'     => $attendee['id_card'] ?? null,
                    'attendee_dob'         => $attendee['dob']     ?? null,
                    'attendee_gender'      => $attendee['gender']  ?? null,
                ]);

                // Send e-ticket email (non-blocking — failure is logged, not thrown)
                if ($attendee && !empty($attendee['email'])) {
                    try {
                        Mail::to($attendee['email'])->send(new \App\Mail\TicketMailable($ticket));
                    } catch (\Exception $mailEx) {
                        Log::error("Failed to send ticket email: " . $mailEx->getMessage());
                    }
                }

                $attendeeIndex++;
            }
        }

        // Record voucher usage if a voucher was applied
        if ($this->voucher_id) {
            VoucherUsage::firstOrCreate(
                ['voucher_id' => $this->voucher_id, 'user_id' => $this->accounts_id],
                ['transaction_id' => $this->id, 'discount_amount' => $this->discount_amount]
            );
            Voucher::where('id', $this->voucher_id)->increment('used_count');
        }
    }
}
