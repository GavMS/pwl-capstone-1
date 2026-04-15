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

    /**
     * Mathematically calculate the true reserved stock for this ticket category
     * by summing actual requested quantities from active sessions and pending transactions.
     *
     * @param int|null $excludeUserId Skip sessions/transactions from this user (prevent self-blocking)
     * @return int
     */
    public function getReservedStock($excludeUserId = null)
    {
        $totalReserved = 0;

        // 1. Calculate from active ShoppingSessions
        $sessionsQuery = \App\Models\ShoppingSession::where('event_id', $this->event_id)
            ->where(function ($q) {
                $q->whereJsonContains('wishlist', ['id' => (int)$this->id])
                  ->orWhereJsonContains('wishlist', ['id' => (string)$this->id]);
            });
            
        if ($excludeUserId) {
            $sessionsQuery->where('user_id', '!=', $excludeUserId);
        }

        $sessions = $sessionsQuery->get(['wishlist']);
        
        foreach ($sessions as $session) {
            $wishlist = $session->wishlist ?? [];
            foreach ($wishlist as $item) {
                if ($item['id'] == $this->id) {
                    $totalReserved += (int)($item['qty'] ?? 1);
                }
            }
        }

        // 2. Calculate from pending Transactions
        $transactionsQuery = \App\Models\Transaction::where('status', 'pending')
            ->where('deadline_payment', '>', now())
            ->where(function ($q) {
                $q->whereJsonContains('ticket_payload', ['event_ticket_type_id' => (int)$this->id])
                  ->orWhereJsonContains('ticket_payload', ['event_ticket_type_id' => (string)$this->id]);
            });

        if ($excludeUserId) {
            $transactionsQuery->where('accounts_id', '!=', $excludeUserId);
        }

        $transactions = $transactionsQuery->get(['ticket_payload']);
        
        foreach ($transactions as $tx) {
            $payload = $tx->ticket_payload ?? [];
            foreach ($payload as $item) {
                if ($item['event_ticket_type_id'] == $this->id) {
                    $totalReserved += (int)($item['quantity'] ?? 1);
                }
            }
        }

        return $totalReserved;
    }
}
