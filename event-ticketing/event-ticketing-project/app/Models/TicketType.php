<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TicketType Model
 *
 * Master data for ticket categories (e.g. VIP, Regular, Early Bird).
 * Linked to events via the pivot table `event_ticket_types`.
 *
 * Used by: EventController (create/edit forms), TicketTypeController (CRUD), EventTicketType.
 */
class TicketType extends Model
{
    protected $table      = 'ticket_types';
    protected $primaryKey = 'id_ticket_type';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The events that share this ticket type (many-to-many via event_ticket_types).
     */
    public function events()
    {
        return $this->belongsToMany(Events::class, 'event_ticket_types', 'ticket_type_id', 'event_id')
            ->withPivot('price', 'stock')
            ->withTimestamps();
    }
}
