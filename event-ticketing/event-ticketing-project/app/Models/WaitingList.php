<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    protected $fillable = ['user_id', 'event_id', 'position', 'status', 'wishlist', 'expires_at'];

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

    public function isGrantedExpired(): bool
    {
        return $this->status === 'granted' && $this->expires_at && now()->isAfter($this->expires_at);
    }
}
