<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * EventTicketType Model (Pivot)
 *
 * Represents the many‐to‐many relationship between Events and TicketTypes,
 * storing per-event pricing and stock for each ticket category.
 * Also calculates reserved stock from active sessions and pending transactions.
 *
 * Used by: EventController, CheckoutController, QueueController, TicketScanController.
 */
class EventTicketType extends Model
{
    protected $fillable = ['event_id', 'ticket_type_id', 'price', 'stock'];

    // ─── Relationships ───────────────────────────────────

    /** The ticket category (e.g. VIP, Regular) */
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id', 'id_ticket_type');
    }

    /** The event this ticket belongs to */
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'id_event');
    }

    /** All issued tickets of this type */
    public function issuedTickets()
    {
        return $this->hasMany(IssuedTicket::class, 'event_ticket_type_id');
    }

    // ─── Business Logic ──────────────────────────────────

    /**
     * Calculate how many tickets are "reserved" (not yet paid) by other users.
     * Considers both active ShoppingSessions and pending Transactions.
     *
     * Used by the queue system to prevent overselling.
     *
     * @param int|null $excludeUserId  Skip this user's reservations (prevents self-blocking)
     * @return int  Total reserved quantity
     */
    public function getReservedStock($excludeUserId = null): int
    {
        $totalReserved = 0;

        // 1. Count from active ShoppingSessions (users browsing checkout)
        $sessionsQuery = ShoppingSession::where('event_id', $this->event_id)
            ->where(function ($q) {
                $q->whereJsonContains('wishlist', ['id' => (int)$this->id])
                  ->orWhereJsonContains('wishlist', ['id' => (string)$this->id]);
            });

        if ($excludeUserId) {
            $sessionsQuery->where('user_id', '!=', $excludeUserId);
        }

        foreach ($sessionsQuery->get(['wishlist']) as $session) {
            foreach ($session->wishlist ?? [] as $item) {
                if ($item['id'] == $this->id) {
                    $totalReserved += (int)($item['qty'] ?? 1);
                }
            }
        }

        // 2. Count from pending Transactions (users who haven't paid yet)
        $transactionsQuery = Transaction::where('status', 'pending')
            ->where('deadline_payment', '>', now())
            ->where(function ($q) {
                $q->whereJsonContains('ticket_payload', ['event_ticket_type_id' => (int)$this->id])
                  ->orWhereJsonContains('ticket_payload', ['event_ticket_type_id' => (string)$this->id]);
            });

        if ($excludeUserId) {
            $transactionsQuery->where('accounts_id', '!=', $excludeUserId);
        }

        foreach ($transactionsQuery->get(['ticket_payload']) as $tx) {
            foreach ($tx->ticket_payload ?? [] as $item) {
                if ($item['event_ticket_type_id'] == $this->id) {
                    $totalReserved += (int)($item['quantity'] ?? 1);
                }
            }
        }

        return $totalReserved;
    }
}
