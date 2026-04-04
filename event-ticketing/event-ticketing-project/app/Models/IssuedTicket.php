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
}
