<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Events;
use App\Models\EventTicketType;
use App\Models\ShoppingSession;
use App\Models\WaitingList;

/**
 * QueueController
 *
 * Manages the ticket‐purchasing queue system:
 *   - enter()        → Attempt to get a checkout slot or join waiting list
 *   - waitingRoom()  → Show waiting room UI while user waits
 *   - status()       → AJAX poll: check queue position / promotion
 *   - release()      → Voluntarily release a checkout slot
 *   - skipCategory() → Remove a sold-out ticket type from wishlist
 *
 * Used by: queue.* routes.
 */
class QueueController extends Controller
{
    /**
     * Enter the queue for an event.
     * If stock is available, grants an immediate ShoppingSession.
     * Otherwise, places the user in the WaitingList.
     */
    public function enter(Request $request, $event_id)
    {
        $user  = Auth::user();
        $event = Events::findOrFail($event_id);

        // Normalise cart data from various input formats
        $cartData = $this->normaliseCartInput($request);

        if (empty($cartData)) {
            return redirect()->route('events.show', $event_id)
                ->with('error', 'Please select a ticket first.');
        }

        $this->cleanupExpired($event_id);

        // Check for existing session — re-validate against current stock
        $existingSession = ShoppingSession::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if ($existingSession && !$existingSession->isExpired()) {
            if ($this->isSessionStillValid($existingSession, $user->id)) {
                return redirect()->route('events.show', $event_id)->with('queue_granted', true);
            }
            // Session is stale — revoke and fall through
            $existingSession->delete();
        } elseif ($existingSession) {
            $existingSession->delete();
        }

        $existingWait = WaitingList::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        // Attempt to reserve stock atomically
        $granted = DB::transaction(function () use ($user, $event_id, $existingWait, $cartData) {
            foreach ($cartData as $item) {
                $ett = EventTicketType::where('id', $item['id'])->lockForUpdate()->first();
                if (!$ett) continue;

                $activeHolders = $ett->getReservedStock($user->id);
                $requestedQty  = (int)($item['qty'] ?? 1);

                Log::debug("[Queue] Ticket #{$item['id']}: {$activeHolders} reserved / {$ett->stock} stock. Requesting: {$requestedQty}");

                if (($activeHolders + $requestedQty) > $ett->stock) {
                    return false; // Not enough stock — go to waiting list
                }
            }

            // Stock available — create session
            $wishlist = array_values(array_map(fn ($item) => [
                'id'    => (int)$item['id'],
                'name'  => (string)($item['name'] ?? 'Ticket'),
                'qty'   => (int)($item['qty'] ?? 1),
                'price' => (int)($item['price'] ?? 0),
            ], $cartData));

            ShoppingSession::updateOrCreate(
                ['user_id' => $user->id, 'event_id' => $event_id],
                ['wishlist' => $wishlist, 'expires_at' => now()->addMinutes(15)]
            );

            if ($existingWait) {
                $existingWait->delete();
                $this->reorderQueue($event_id);
            }

            return true;
        });

        if ($granted) {
            return redirect()->route('events.show', $event_id)->with('queue_granted', true);
        }

        // Stock unavailable — add to waiting list
        if (!$existingWait) {
            $lastPosition = WaitingList::where('event_id', $event_id)->max('position') ?? 0;
            WaitingList::create([
                'user_id'  => $user->id,
                'event_id' => $event_id,
                'position' => $lastPosition + 1,
                'status'   => 'waiting',
                'wishlist' => $cartData,
            ]);
        } else {
            $existingWait->update(['wishlist' => $cartData]);
        }

        return redirect()->route('queue.waiting-room', ['event_id' => $event_id]);
    }

    /**
     * Show the waiting room page. Uses server-stored wishlist to avoid sessionStorage dependency.
     */
    public function waitingRoom($event_id)
    {
        $user  = Auth::user();
        $event = Events::findOrFail($event_id);

        $myWait = WaitingList::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if (!$myWait) {
            // Check if already promoted to checkout
            $session = ShoppingSession::where('user_id', $user->id)
                ->where('event_id', $event_id)
                ->first();

            if ($session && !$session->isExpired()) {
                return redirect()->route('events.show', $event_id)->with('queue_granted', true);
            }
            return redirect()->route('events.show', $event_id)->with('error', 'Please try purchasing tickets again.');
        }

        // Normalise wishlist for consistent JS consumption
        $serverWishlist = $this->normaliseWishlist($myWait->wishlist ?? []);
        $serverTicketIds = array_values(array_column($serverWishlist, 'id'));

        return view('user.waiting-room', compact('event', 'myWait', 'serverTicketIds', 'serverWishlist'));
    }

    /**
     * AJAX endpoint: check queue position and stock availability.
     */
    public function status(Request $request, $event_id)
    {
        $user = Auth::user();

        $this->cleanupExpired($event_id);
        $this->promoteQueue($event_id);

        // Check if tickets are completely sold out
        $totalStock  = EventTicketType::where('event_id', $event_id)->sum('stock');
        $activeCount = ShoppingSession::where('event_id', $event_id)->count();

        if ($totalStock <= 0 && $activeCount <= 0) {
            WaitingList::where('user_id', $user->id)->where('event_id', $event_id)->delete();

            return response()->json([
                'status'   => 'sold_out',
                'redirect' => route('events.show', $event_id) . '?error=sold_out',
                'message'  => 'Sorry, tickets for this event have just sold out.',
            ]);
        }

        // Check if user was promoted to checkout
        $session = ShoppingSession::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if ($session && !$session->isExpired()) {
            return response()->json([
                'status'   => 'granted',
                'redirect' => route('events.show', $event_id) . '?queue_granted=1',
            ]);
        }

        $myWait = WaitingList::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if (!$myWait) {
            return response()->json(['status' => 'not_found']);
        }

        // Build stock availability info for the user's wishlist
        $wishlistStock = $this->buildWishlistStockInfo($request, $myWait, $user->id);

        return response()->json([
            'status'         => $myWait->status,
            'position'       => $myWait->position ?? 1,
            'wishlist_stock'  => $wishlistStock,
            'server_time'    => now()->toTimeString(),
        ]);
    }

    /**
     * Release a checkout session voluntarily.
     */
    public function release(Request $request, $event_id)
    {
        $user = Auth::user();

        $deleted = ShoppingSession::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->delete();

        if ($deleted) {
            Log::info("User {$user->id} released session for event {$event_id}");
            $this->promoteQueue($event_id);
        }

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json(['status' => 'released']);
        }

        return redirect()->route('events.show', $event_id)
            ->with('success', 'Your queue session has been released.');
    }

    /**
     * Remove a specific ticket type from wishlist (when sold out).
     * If no tickets remain, cancel the waiting entry entirely.
     */
    public function skipCategory(Request $request, $event_id)
    {
        $user         = Auth::user();
        $ticketTypeId = $request->input('ticket_type_id');

        $waiting = WaitingList::where('user_id', $user->id)
            ->where('event_id', $event_id)
            ->first();

        if ($waiting && $waiting->wishlist) {
            $newWishlist = array_values(
                array_filter($waiting->wishlist, fn ($item) => $item['id'] != $ticketTypeId)
            );

            if (empty($newWishlist)) {
                $waiting->delete();
                return response()->json(['status' => 'cancelled']);
            }

            $waiting->update(['wishlist' => $newWishlist]);
            $this->promoteQueue($event_id);
        }

        return response()->json(['status' => 'updated']);
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Normalise cart input from query string or request body.
     */
    private function normaliseCartInput(Request $request): array
    {
        $rawCart = $request->input('wishlist') ?: $request->input('cart_data');
        $cartData = is_string($rawCart) ? (json_decode($rawCart, true) ?: []) : (is_array($rawCart) ? $rawCart : []);
        return array_values($cartData);
    }

    /**
     * Check if an existing ShoppingSession's wishlist is still fulfillable.
     */
    private function isSessionStillValid(ShoppingSession $session, int $userId): bool
    {
        foreach ($session->wishlist ?? [] as $item) {
            $ett = EventTicketType::find($item['id']);
            if (!$ett) continue;

            $otherHolders = $ett->getReservedStock($userId);
            $requestedQty = (int)($item['qty'] ?? 1);

            if (($otherHolders + $requestedQty) > $ett->stock) {
                return false;
            }
        }
        return true;
    }

    /**
     * Normalise wishlist array to a consistent sequential format.
     */
    private function normaliseWishlist(array $rawWishlist): array
    {
        return array_values(array_map(fn ($item) => [
            'id'    => (int)($item['id'] ?? 0),
            'name'  => (string)($item['name'] ?? 'Ticket'),
            'price' => (int)($item['price'] ?? 0),
            'qty'   => (int)($item['qty'] ?? 1),
        ], $rawWishlist));
    }

    /**
     * Build stock availability data for the user's wishlisted ticket types.
     */
    private function buildWishlistStockInfo(Request $request, WaitingList $myWait, int $userId): array
    {
        $requestedIds = $request->query('ticket_ids', []);
        if (empty($requestedIds)) return [];

        $ticketTypes = EventTicketType::whereIn('id', $requestedIds)->with('ticketType')->get();
        $result = [];

        foreach ($ticketTypes as $tt) {
            $requestedQty = 1;
            foreach ($myWait->wishlist ?? [] as $wItem) {
                if ($wItem['id'] == $tt->id) {
                    $requestedQty = (int)($wItem['qty'] ?? 1);
                }
            }

            $activeHolders = $tt->getReservedStock($userId);

            $result[] = [
                'id'           => (int)$tt->id,
                'is_available' => ($activeHolders + $requestedQty) <= $tt->stock,
                'is_sold_out'  => $tt->stock < $requestedQty,
            ];
        }

        return $result;
    }

    /**
     * Delete expired sessions and waiting-list grants, then reorder queue.
     */
    private function cleanupExpired($event_id): void
    {
        ShoppingSession::where('event_id', $event_id)
            ->where('expires_at', '<', now())
            ->delete();

        WaitingList::where('event_id', $event_id)
            ->where('status', 'granted')
            ->where('expires_at', '<', now())
            ->delete();

        $this->reorderQueue($event_id);
    }

    /**
     * Promote waiting-list users to checkout when stock becomes available.
     */
    private function promoteQueue($event_id): void
    {
        $waiting = WaitingList::where('event_id', $event_id)
            ->where('status', 'waiting')
            ->orderBy('position')
            ->get();

        foreach ($waiting as $entry) {
            $canPromote = true;

            foreach ($entry->wishlist ?? [] as $item) {
                $ett = EventTicketType::find($item['id']);
                if (!$ett) continue;

                $activeHolders = $ett->getReservedStock($entry->user_id);
                $requestedQty  = (int)($item['qty'] ?? 1);

                if (($activeHolders + $requestedQty) > $ett->stock) {
                    $canPromote = false;
                    break;
                }
            }

            if ($canPromote) {
                Log::info("Promoting User {$entry->user_id} to checkout for event {$event_id}");

                ShoppingSession::updateOrCreate(
                    ['user_id' => $entry->user_id, 'event_id' => $event_id],
                    ['wishlist' => $entry->wishlist, 'expires_at' => now()->addMinutes(15)]
                );
                $entry->delete();
            }
        }

        $this->reorderQueue($event_id);
    }

    /**
     * Reorder waiting list positions to be sequential (1, 2, 3...).
     */
    private function reorderQueue($event_id): void
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
