<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingSession extends Model
{
    protected $fillable = ['user_id', 'event_id', 'wishlist', 'expires_at'];

    protected $casts = [
        'wishlist' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Accounts::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'id_event');
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }
}
