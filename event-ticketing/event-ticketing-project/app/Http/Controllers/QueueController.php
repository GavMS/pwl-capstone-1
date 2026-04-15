<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Events;
use App\Models\ShoppingSession;
use App\Models\WaitingList;

class QueueController extends Controller
{
    public function enter(Request $request, $event_id)
    {
        $user = Auth::user();
        $event = Events::findOrFail($event_id);
        
        // --- INPUT NORMALIZATION ---
        $rawCart = $request->input('wishlist') ?: $request->input('cart_data');
        $cartData = [];
        if (is_string($rawCart)) {
            $cartData = json_decode($rawCart, true) ?: [];
        } elseif (is_array($rawCart)) {
            $cartData = $rawCart;
        }
        
        // Force Indexed Array
        $cartData = array_values($cartData);

        if (empty($cartData)) {
            return redirect()->route('events.show', $event_id)->with('error', 'Please select a ticket first.');
        }

        $this->cleanupExpired($event_id);

        $existingSession = ShoppingSession::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        // If user has an existing session, we must RE-VALIDATE it against current stock.
        // We cannot blindly trust the old session — another user may have taken
        // the last slot since this session was created.
        if ($existingSession && !$existingSession->isExpired()) {
            $sessionIsStillValid = true;
            $sessionWishlist = $existingSession->wishlist ?? [];

            foreach ($sessionWishlist as $item) {
                $ticketTypeId = $item['id'];
                $ett = \App\Models\EventTicketType::find($ticketTypeId);
                if (!$ett) continue;

                // Count OTHER users holding this ticket (not counting self)
                $otherHolders = $ett->getReservedStock($user->id);
                $requestedQty = (int)($item['qty'] ?? 1);

                // If others already hold all slots, this user's session is stale/invalid
                if (($otherHolders + $requestedQty) > $ett->stock) {
                    $sessionIsStillValid = false;
                    break;
                }
            }

            if ($sessionIsStillValid) {
                // Session is genuinely valid — let them through
                return redirect()->route('events.show', $event_id)->with('queue_granted', true);
            } else {
                // Session is stale — revoke it and fall through to queue logic
                $existingSession->delete();
                $existingSession = null;
            }
        } elseif ($existingSession && $existingSession->isExpired()) {
            // Expired session — clean it up
            $existingSession->delete();
            $existingSession = null;
        }

        $existingWait = WaitingList::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        $granted = DB::transaction(function () use ($user, $event_id, $existingWait, $cartData) {
            $isBlocked = false;

            foreach ($cartData as $item) {
                $ticketTypeId = $item['id'];
                // lockForUpdate() is critical to prevent race conditions
                $ett = \App\Models\EventTicketType::where('id', $ticketTypeId)->lockForUpdate()->first();
                if (!$ett)
                    continue;

                // Get reserved stock accounting for actual quantities, EXCLUDING current user.
                $activeHolders = $ett->getReservedStock($user->id);
                $requestedQty = (int)($item['qty'] ?? 1);

                \Illuminate\Support\Facades\Log::debug("[Queue] Ticket #{$ticketTypeId}: {$activeHolders} reserved / {$ett->stock} stock. Requesting: {$requestedQty}");

                if (($activeHolders + $requestedQty) > $ett->stock) {
                    $isBlocked = true;
                    break;
                }
            }

            if (!$isBlocked) {
                $fullWishlist = [];
                foreach ($cartData as $item) {
                    $fullWishlist[] = [
                        'id'    => (int)$item['id'],
                        'name'  => (string)($item['name'] ?? 'Ticket'),
                        'qty'   => (int)($item['qty'] ?? 1),
                        'price' => (int)($item['price'] ?? 0),
                    ];
                }

                ShoppingSession::updateOrCreate(
                    ['user_id' => $user->id, 'event_id' => $event_id],
                    [
                        'wishlist'    => array_values($fullWishlist),
                        'expires_at'  => now()->addMinutes(15)
                    ]
                );

                if ($existingWait) {
                    $existingWait->delete();
                    $this->reorderQueue($event_id);
                }

                // DB::transaction() handles commit automatically — do NOT call DB::commit() here!
                return true;
            }

            return false;
        });

        if ($granted) {
            return redirect()->route('events.show', $event_id)->with('queue_granted', true);
        }

        if (!$existingWait) {
            $lastPosition = WaitingList::where('event_id', $event_id)->max('position') ?? 0;
            WaitingList::create([
                'user_id' => $user->id,
                'event_id' => $event_id,
                'position' => $lastPosition + 1,
                'status' => 'waiting',
                'wishlist' => $cartData
            ]);
        } else {
            // Update wishlist if they enter again with different selection while waiting
            $existingWait->update(['wishlist' => $cartData]);
        }

        return redirect()->route('queue.waiting-room', ['event_id' => $event_id]);
    }

    public function waitingRoom($event_id)
    {
        $user = Auth::user();
        $event = Events::findOrFail($event_id);

        $myWait = WaitingList::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if (!$myWait) {
            $session = ShoppingSession::where('user_id', $user->id)->where('event_id', $event_id)->first();
            if ($session && !$session->isExpired()) {
                return redirect()->route('events.show', $event_id)->with('queue_granted', true);
            }
            return redirect()->route('events.show', $event_id)->with('error', 'Please try purchasing tickets again.');
        }

        // Extract ticket IDs from the WaitingList wishlist (server-authoritative)
        // This ensures the JS polling always has the correct ticket IDs
        // regardless of whether sessionStorage is available.
        $rawWishlist = $myWait->wishlist ?? [];

        // Normalize: always produce a sequential (indexed) array, never associative.
        // The stored wishlist may be {"7": {...}} (assoc) or [{...}, {...}] (sequential).
        // array_values() ensures we always get [{id:7,...}, {id:8,...}] in JS.
        $serverWishlist = array_values(
            array_map(function ($item) {
                return [
                    'id'    => (int)($item['id'] ?? 0),
                    'name'  => (string)($item['name'] ?? 'Ticket'),
                    'price' => (int)($item['price'] ?? 0),
                    'qty'   => (int)($item['qty'] ?? 1),
                ];
            }, $rawWishlist)
        );

        $serverTicketIds = array_values(array_column($serverWishlist, 'id'));

        return view('user.waiting-room', compact('event', 'myWait', 'serverTicketIds', 'serverWishlist'));
    }

    public function status(Request $request, $event_id)
    {
        $user = Auth::user();

        $this->cleanupExpired($event_id);
        $this->promoteQueue($event_id);

        // Check if tickets are completely sold out
        $totalStock = \App\Models\EventTicketType::where('event_id', $event_id)->sum('stock');
        $activeCount = ShoppingSession::where('event_id', $event_id)->count();

        if ($totalStock <= 0 && $activeCount <= 0) {
            WaitingList::where('user_id', $user->id)
                ->where('event_id', $event_id)
                ->delete();

            return response()->json([
                'status' => 'sold_out',
                'redirect' => route('events.show', $event_id) . '?error=sold_out',
                'message' => 'Sorry, tickets for this event have just sold out.'
            ]);
        }

        $session = ShoppingSession::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if ($session && !$session->isExpired()) {
            return response()->json([
                'status' => 'granted',
                'redirect' => route('events.show', $event_id) . '?queue_granted=1',
            ]);
        }

        $myWait = WaitingList::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if (!$myWait) {
            return response()->json(['status' => 'not_found']);
        }

        // --- NEW: Smart Stock Info for Wishlist ---
        $wishlistStock = [];
        $requestedIds = $request->query('ticket_ids', []);

        if (!empty($requestedIds)) {
            $ticketTypes = \App\Models\EventTicketType::whereIn('id', $requestedIds)
                ->with('ticketType')
                ->get();

            foreach ($ticketTypes as $tt) {
                // Extract user's requested qty for this specific ticket type
                $requestedQty = 1;
                if ($myWait && $myWait->wishlist) {
                    foreach ($myWait->wishlist as $wItem) {
                        if ($wItem['id'] == $tt->id) {
                            $requestedQty = (int)($wItem['qty'] ?? 1);
                        }
                    }
                }

                $activeHolders = $tt->getReservedStock($user->id);

                $wishlistStock[] = [
                    'id' => (int)$tt->id,
                    'is_available' => ($activeHolders + $requestedQty) <= $tt->stock,
                    'is_sold_out' => $tt->stock < $requestedQty
                ];
            }
        }

        return response()->json([
            'status' => $myWait->status,
            'position' => $myWait->position ?? 1,
            'wishlist_stock' => $wishlistStock,
            'server_time' => now()->toTimeString(),
        ]);
    }

    public function release(Request $request, $event_id)
    {
        $user = Auth::user();

        $deleted = ShoppingSession::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->delete();

        if ($deleted) {
            \Illuminate\Support\Facades\Log::info("User {$user->id} released session for event {$event_id}");
            $this->promoteQueue($event_id);
        }

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json(['status' => 'released']);
        }

        return redirect()->route('events.show', $event_id)->with('success', 'Your queue session has been released.');
    }

    public function skipCategory(Request $request, $event_id)
    {
        $user = Auth::user();
        $ticketTypeId = $request->input('ticket_type_id');

        $waiting = WaitingList::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if ($waiting && $waiting->wishlist) {
            $wishlist = $waiting->wishlist;
            // Remove the specific category
            $newWishlist = array_filter($wishlist, function ($item) use ($ticketTypeId) {
                return $item['id'] != $ticketTypeId;
            });

            // Re-index array
            $newWishlist = array_values($newWishlist);

            if (empty($newWishlist)) {
                $waiting->delete();
                return response()->json(['status' => 'cancelled']);
            }

            $waiting->update(['wishlist' => $newWishlist]);

            // Try to promote immediately after removing the blocking category
            $this->promoteQueue($event_id);
        }

        return response()->json(['status' => 'updated']);
    }

    private function cleanupExpired($event_id)
    {
        $expired = ShoppingSession::where('event_id', $event_id)
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $session) {
            $session->delete();
        }

        $expiredGrants = WaitingList::where('event_id', $event_id)
            ->where('status', 'granted')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredGrants as $grant) {
            $grant->delete();
        }

        $this->reorderQueue($event_id);
    }

    private function promoteQueue($event_id)
    {
        $event = Events::find($event_id);
        if (!$event)
            return;

        $waiting = WaitingList::where('event_id', $event_id)
            ->where('status', 'waiting')
            ->orderBy('position')
            ->get();

        foreach ($waiting as $entry) {
            $wishlist = $entry->wishlist ?? [];
            $canPromote = true;

            foreach ($wishlist as $item) {
                $ticketTypeId = $item['id'];
                $ett = \App\Models\EventTicketType::find($ticketTypeId);

                if (!$ett)
                    continue;

                $activeHolders = $ett->getReservedStock($entry->user_id);
                $requestedQty = (int)($item['qty'] ?? 1);

                if (($activeHolders + $requestedQty) > $ett->stock) {
                    $canPromote = false;
                    break;
                }
            }

            if ($canPromote) {
                \Illuminate\Support\Facades\Log::info("Promoting User {$entry->user_id} to checkout for event {$event_id}");
                ShoppingSession::updateOrCreate(
                    ['user_id' => $entry->user_id, 'event_id' => $event_id],
                    [
                        'wishlist' => $wishlist,
                        'expires_at' => now()->addMinutes(15)
                    ]
                );
                $entry->delete();
            }
        }

        $this->reorderQueue($event_id);
    }

    private function reorderQueue($event_id)
    {
        $waiting = WaitingList::where('event_id', $event_id)
            ->where('status', 'waiting')
            ->orderBy('position')
            ->get();

        foreach ($waiting as $index => $entry) {
            $entry->update(['position' => $index + 1]);
        }
    }
}
