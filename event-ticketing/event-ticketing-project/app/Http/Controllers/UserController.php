<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventCategories;
use App\Models\Accounts;
use App\Models\Events;
use App\Models\IssuedTicket;
use App\Models\Transaction;

/**
 * UserController
 *
 * Handles all public/user‐facing pages: dashboard, browse events,
 * organizer profiles, and creator listing.
 *
 * Used by: user.dashboard, user.explore, user.organizer.profile, user.explore.creators routes.
 */
class UserController extends Controller
{
    /**
     * User dashboard — hero carousel, featured events, upcoming tickets, pending payments.
     */
    public function dashboard()
    {
        $user = auth()->user();

        $categories = EventCategories::all();
        $organizers = Accounts::where('role', 'organizer')->latest()->take(8)->get();

        // Hero carousel: 5 most recent published events
        $heroEvents = Events::where('status', 'published')
            ->with(['category', 'organizer', 'ticketTypes'])
            ->latest()
            ->take(5)
            ->get();

        // Featured section: 4 recently published events
        $featuredEvents = Events::where('status', 'published')
            ->with(['category', 'organizer', 'ticketTypes'])
            ->latest()
            ->take(4)
            ->get();

        // Lightweight event list for the calendar/map widget
        $allEventsLite = Events::where('status', 'published')
            ->with(['organizer:id,name'])
            ->select('id_event', 'title', 'date', 'banner', 'organizer_id', 'city')
            ->orderBy('date', 'asc')
            ->get()
            ->map(fn ($ev) => [
                'id'             => $ev->id_event,
                'title'          => $ev->title,
                'date_formatted' => $ev->date->translatedFormat("d M 'y"),
                'city'           => $ev->city,
                'banner'         => $ev->banner ? asset('storage/' . $ev->banner) : null,
                'organizer'      => $ev->organizer?->name ?? 'Platform',
                'url'            => route('events.show', $ev->id_event),
            ]);

        // User's upcoming active tickets (future events only)
        $upcomingTickets = IssuedTicket::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['eventTicketType.event.organizer', 'eventTicketType.ticketType'])
            ->get()
            ->filter(fn ($ticket) => optional($ticket->eventTicketType->event)->date >= now())
            ->sortBy(fn ($ticket) => $ticket->eventTicketType->event->date)
            ->take(4)
            ->values();

        // Pending (unpaid) transactions that haven't expired yet
        $pendingTransactions = Transaction::where('accounts_id', $user->id)
            ->where('status', 'pending')
            ->where('deadline_payment', '>', now())
            ->orderBy('deadline_payment', 'asc')
            ->get();

        // User's active waiting lists
        $waitingLists = \App\Models\WaitingList::where('user_id', $user->id)
            ->where('status', 'waiting')
            ->with(['event:id_event,title,banner,date,location'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('user.dashboard', compact(
            'user', 'categories', 'organizers', 'heroEvents',
            'featuredEvents', 'allEventsLite', 'upcomingTickets', 'pendingTransactions', 'waitingLists'
        ));
    }

    /**
     * Browse / Explore Events — with multi-filter support (search, category, location, date, price, format).
     * Supports AJAX requests for live-search (returns JSON with rendered HTML).
     */
    public function explore(Request $request)
    {
        $query = Events::where('status', 'published')->with(['category', 'organizer', 'ticketTypes']);

        // Keyword search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Location (city) filter
        if ($request->filled('location')) {
            $query->where('city', $request->location);
        }

        // Date period filter
        if ($request->filled('tanggal')) {
            $this->applyDateFilter($query, $request->tanggal, $request->custom_date);
        }

        // Time-of-day filter
        if ($request->filled('waktu')) {
            $this->applyTimeFilter($query, $request->waktu);
        }

        // Price range filter (based on minimum ticket price)
        if ($request->filled('harga')) {
            $this->applyPriceFilter($query, $request->harga);
        }

        // Event format filter (onsite/online)
        if ($request->filled('format')) {
            $query->where('format', $request->input('format'));
        }

        $events     = $query->orderBy('date', 'asc')->paginate(12)->withQueryString();
        $categories = EventCategories::all();

        $popularLocations = [
            'Jakarta', 'Bandung', 'Bali', 'Surabaya', 'Yogyakarta',
            'Tangerang', 'Bekasi', 'Semarang', 'Medan', 'Solo',
        ];

        // Return partial HTML for AJAX live-search
        if ($request->ajax()) {
            return response()->json([
                'html'  => view('user.partials.explore_grid', compact('events'))->render(),
                'total' => $events->total(),
            ]);
        }

        return view('user.explore', compact('events', 'categories', 'popularLocations'));
    }

    /**
     * Public organizer profile page — shows bio and published events.
     */
    public function organizerProfile($id)
    {
        $organizer = Accounts::where('role', 'organizer')->findOrFail($id);

        $events = Events::where('organizer_id', $id)
            ->where('status', 'published')
            ->with(['category', 'ticketTypes'])
            ->latest()
            ->paginate(9);

        return view('user.organizer_profile', compact('organizer', 'events'));
    }

    /**
     * Browse all organizers / creators — sorted by published event count.
     */
    public function exploreCreators(Request $request)
    {
        $query = Accounts::where('role', 'organizer')
            ->withCount(['events' => fn ($q) => $q->where('status', 'published')]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $organizers = $query->orderBy('events_count', 'desc')->paginate(12)->withQueryString();

        return view('user.explore_creators', compact('organizers'));
    }

    // ─── Private Filter Helpers ──────────────────────────

    /**
     * Apply date period filter to the query.
     */
    private function applyDateFilter($query, string $period, ?string $customDate): void
    {
        match ($period) {
            'today'        => $query->whereDate('date', today()),
            'tomorrow'     => $query->whereDate('date', today()->addDay()),
            'this_week'    => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]),
            'this_weekend' => $query->whereBetween('date', [now()->nextOrSameWeekday(6)->startOfDay(), now()->nextOrSameWeekday(0)->endOfDay()]),
            'next_week'    => $query->whereBetween('date', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()]),
            'next_weekend' => $query->whereBetween('date', [now()->addWeek()->nextOrSameWeekday(6)->startOfDay(), now()->addWeek()->nextOrSameWeekday(0)->endOfDay()]),
            'this_month'   => $query->whereMonth('date', now()->month)->whereYear('date', now()->year),
            'custom'       => $customDate ? $query->whereDate('date', $customDate) : null,
            default        => null,
        };
    }

    /**
     * Apply time-of-day filter to the query.
     */
    private function applyTimeFilter($query, string $waktu): void
    {
        match ($waktu) {
            'pagi'  => $query->whereRaw("TIME(date) >= '05:00:00' AND TIME(date) < '10:00:00'"),
            'siang' => $query->whereRaw("TIME(date) >= '10:00:00' AND TIME(date) < '16:00:00'"),
            'malam' => $query->whereRaw("TIME(date) >= '16:00:00' OR TIME(date) < '05:00:00'"),
            default => null,
        };
    }

    /**
     * Apply price range filter based on minimum ticket price per event.
     */
    private function applyPriceFilter($query, string $harga): void
    {
        $sub = "(SELECT MIN(price) FROM event_ticket_types WHERE event_id = event.id_event)";

        match ($harga) {
            'gratis'   => $query->whereRaw("$sub = 0"),
            'under100' => $query->whereRaw("$sub > 0 AND $sub < 100000"),
            '100to250' => $query->whereRaw("$sub >= 100000 AND $sub <= 250000"),
            '251to500' => $query->whereRaw("$sub >= 251000 AND $sub <= 500000"),
            'over500'  => $query->whereRaw("$sub > 500000"),
            default    => null,
        };
    }
}
