<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ShoppingSession Model
 *
 * Temporary checkout session created when a user is "granted" from the queue.
 * Holds the user's wishlist and expires after 15 minutes.
 * Acts as a soft-lock on ticket stock to prevent overselling.
 *
 * Used by: QueueController, CheckoutController, CheckQueueSession middleware.
 */
class ShoppingSession extends Model
{
    protected $fillable = ['user_id', 'event_id', 'wishlist', 'expires_at'];

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

    /** Check if this session has expired */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }
}
