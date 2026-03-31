<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'ticket_types';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'id_ticket_type';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'capacity',
    ];

    /**
     * The events that share this ticket type.
     */
    public function events()
    {
        return $this->belongsToMany(Events::class, 'event_ticket_types', 'ticket_type_id', 'event_id')
                    ->withPivot('price', 'stock')
                    ->withTimestamps();
    }
}
