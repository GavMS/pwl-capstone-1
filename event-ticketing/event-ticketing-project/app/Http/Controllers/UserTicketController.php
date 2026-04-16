<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IssuedTicket;

/**
 * UserTicketController
 *
 * Handles the "My Tickets" section for authenticated users.
 *
 * Used by: user.my-tickets, user.ticket.detail routes.
 */
class UserTicketController extends Controller
{
    /**
     * List all tickets owned by the current user (paginated).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Flash success message when redirected from payment gateway
        if ($request->query('success') == 'true') {
            session()->flash('success', 'Your transaction was successful! Your tickets have been issued.');
        }

        $tickets = IssuedTicket::where('user_id', $user->id)
            ->with(['eventTicketType.event', 'eventTicketType.ticketType'])
            ->latest()
            ->paginate(9);

        return view('user.my-tickets', compact('tickets'));
    }

    /**
     * Show a single ticket detail page (for print / QR viewing).
     * Security: only the ticket owner can view their ticket.
     */
    public function show($uniqueCode)
    {
        $ticket = IssuedTicket::where('unique_code', $uniqueCode)
            ->where('user_id', Auth::id())
            ->with(['eventTicketType.event', 'eventTicketType.ticketType'])
            ->firstOrFail();

        return view('user.ticket-detail', compact('ticket'));
    }
}
