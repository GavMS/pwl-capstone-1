<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * WaitingList Model
 *
 * Queue entry for users waiting to purchase tickets.
 * Users are promoted to a ShoppingSession when stock becomes available.
 * Stores the user's desired tickets (wishlist) and queue position.
 *
 * Used by: QueueController (enter, waitingRoom, status, skipCategory).
 */
class WaitingList extends Model
{
    protected $fillable = ['user_id', 'event_id', 'position', 'status', 'wishlist', 'expires_at'];

    protected $casts = [
        'wishlist'   => 'array',
        'expires_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────

    public function user()
    {
        return $this->belongsTo(Accounts::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'id_event');
    }

    // ─── Helpers ─────────────────────────────────────────

    /** Check if a "granted" status has expired */
    public function isGrantedExpired(): bool
    {
        return $this->status === 'granted' && $this->expires_at && now()->isAfter($this->expires_at);
    }
}
