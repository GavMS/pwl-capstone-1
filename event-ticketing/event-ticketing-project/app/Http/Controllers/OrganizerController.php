<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;

class OrganizerController extends Controller
{
    // Halaman dashboard organizer
    // Halaman dashboard organizer dengan filter status
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status', 'active');
        
        $stats = [
            'total_events'  => Events::where('organizer_id', auth()->id())->count(),
            'active_events' => Events::where('organizer_id', auth()->id())->where('status', 'published')->count(),
            'draft_events'  => Events::where('organizer_id', auth()->id())->where('status', 'draft')->count(),
        ];

        // Query dasar
        $query = Events::where('organizer_id', auth()->id());

        // Logika filter
        if ($status === 'draft') {
            $query->where('status', 'draft');
        } elseif ($status === 'past') {
            $query->where('date', '<', now());
        } else {
            // Default: Active Published
            $query->where('status', 'published')->where('date', '>=', now());
        }

        $events = $query->latest()->take(5)->get();

        return view('organizer.dashboard', compact('user', 'stats', 'events', 'status'));
    }

    // Halaman Sales & Payouts untuk Organizer
    public function sales(Request $request)
    {
        $user = auth()->user();

        // Ambil semua event milik organizer ini
        $events = Events::where('organizer_id', $user->id)
            ->with(['ticketTypes' => function ($query) {
                 // Tidak bisa load issuedTickets langsung dari pivot without explicit relationship query, 
                 // We will get them through the event
            }])->get();

        // Karena IssuedTicket terikat ke eventTicketType, kita fetch manual untuk mempermudah perhitungan
        $salesData = [];
        $totalRevenue = 0;
        $totalTicketsSold = 0;

        foreach ($events as $event) {
            $ticketTypes = \App\Models\EventTicketType::where('event_id', $event->id_event)
                                        ->with('ticketType', 'issuedTickets')
                                        ->get();
            
            $eventTotalRevenue = 0;
            $eventTicketsSold = 0;
            $ticketDetails = [];

            foreach ($ticketTypes as $ett) {
                $sold = $ett->issuedTickets->count();
                $revenue = $sold * $ett->price;
                
                $eventTicketsSold += $sold;
                $eventTotalRevenue += $revenue;

                $ticketDetails[] = [
                    'name' => $ett->ticketType->name,
                    'price' => $ett->price,
                    'sold' => $sold,
                    'revenue' => $revenue,
                    'stock' => $ett->stock, // Sisa stock
                    'initial_capacity' => $ett->stock + $sold
                ];
            }

            $totalRevenue += $eventTotalRevenue;
            $totalTicketsSold += $eventTicketsSold;

            $salesData[] = [
                'event' => $event,
                'total_revenue' => $eventTotalRevenue,
                'tickets_sold' => $eventTicketsSold,
                'ticket_details' => $ticketDetails
            ];
        }

        return view('organizer.sales', compact('user', 'salesData', 'totalRevenue', 'totalTicketsSold'));
    }
}
