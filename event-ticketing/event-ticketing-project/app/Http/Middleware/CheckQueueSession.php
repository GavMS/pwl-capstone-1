<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingSession;

class CheckQueueSession
{
    public function handle(Request $request, Closure $next)
    {
        $eventId = $request->input('event_id') ?? $request->route('event_id');

        if (!$eventId) {
            return $next($request);
        }

        $user = Auth::user();

        $session = ShoppingSession::where('user_id', $user->id)
            ->where('event_id', $eventId)
            ->first();

        if (!$session || $session->isExpired()) {
            if ($session) $session->delete();
            // Redirect to events.show (NOT queue.enter) — queue.enter requires
            // a 'wishlist' parameter that is not available from this redirect.
            return redirect()->route('events.show', ['id' => $eventId])
                ->with('error', 'Your queue session has expired. Please reselect your tickets to join a new queue.');
        }

        return $next($request);
    }
}
