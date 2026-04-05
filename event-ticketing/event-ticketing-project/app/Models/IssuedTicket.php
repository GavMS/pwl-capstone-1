<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuedTicket extends Model
{
    protected $fillable = [
        'user_id',
        'event_ticket_type_id',
        'unique_code',
        'qr_image',
        'status',
    ];

    /**
     * Relasi ke pivot tabel event_ticket_types
     */
    public function eventTicketType()
    {
        return $this->belongsTo(EventTicketType::class, 'event_ticket_type_id');
    }

    /**
     * Helper shortcut ke Event lewat EventTicketType
     */
    public function event()
    {
        return $this->hasOneThrough(
            Events::class,
            EventTicketType::class,
            'id',               // FK di EventTicketType
            'id_event',         // FK di Events
            'event_ticket_type_id', // Kolom lokal di IssuedTicket
            'event_id'          // Kolom lokal di EventTicketType
        );
    }

    /**
     * Relasi ke User (Accounts)
     */
    public function user()
    {
        return $this->belongsTo(Accounts::class, 'user_id');
    }
}
