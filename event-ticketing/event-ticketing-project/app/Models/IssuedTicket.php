<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function eventTicketType()
    {
        return $this->belongsTo(EventTicketType::class, 'event_ticket_type_id');
    }

    public function user()
    {
        return $this->belongsTo(Accounts::class, 'user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
