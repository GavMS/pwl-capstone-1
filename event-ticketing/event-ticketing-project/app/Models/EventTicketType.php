<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicketType extends Model
{
    protected $fillable = ['event_id', 'ticket_type_id', 'price', 'stock'];

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id', 'id_ticket_type');
    }

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'id_event');
    }

    public function issuedTickets()
    {
        return $this->hasMany(IssuedTicket::class, 'event_ticket_type_id');
    }
}
