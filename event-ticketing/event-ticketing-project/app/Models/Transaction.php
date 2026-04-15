<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';

    protected $guarded = ['id'];

    protected $casts = [
        'ticket_payload' => 'array',
        'customer_details' => 'array',
        'deadline_payment' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Accounts::class, 'accounts_id');
    }

    public function issuedTickets()
    {
        return $this->hasMany(IssuedTicket::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
