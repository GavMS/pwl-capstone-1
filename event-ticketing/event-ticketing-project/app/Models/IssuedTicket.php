<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IssuedTicket Model
 *
 * Represents a single issued e-ticket with a unique QR code.
 * Created after successful payment. Tracks attendee info and scan status.
 *
 * Used by: UserTicketController, TicketScanController, CheckoutController, PaymentCallbackController.
 */
class IssuedTicket extends Model
{
    protected $fillable = [
        'user_id',
        'event_ticket_type_id',
        'transaction_id',
        'unique_code',
        'qr_image',
        'status',
        'scanned_at',
        'attendee_name',
        'attendee_email',
        'attendee_phone',
        'attendee_id_card',
        'attendee_dob',
        'attendee_gender',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** The event ticket type (category + pricing) */
    public function eventTicketType()
    {
        return $this->belongsTo(EventTicketType::class, 'event_ticket_type_id');
    }

    /** The buyer / ticket owner */
    public function user()
    {
        return $this->belongsTo(Accounts::class, 'user_id');
    }

    /** The transaction that generated this ticket */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
