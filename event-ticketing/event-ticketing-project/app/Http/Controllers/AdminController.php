<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accounts;
use App\Models\Events;

class AdminController extends Controller
{
    // Halaman dashboard admin
    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'total_users'   => Accounts::count(),
            'total_events'  => Events::count(),
            'active_events' => Events::where('status', 'published')->count(),
        ];

        $new_users     = Accounts::latest()->take(5)->get();
        $recent_events = Events::with('category', 'organizer')->latest()->take(5)->get();

        return view('admin.dashboard', compact('user', 'stats', 'new_users', 'recent_events'));
    }

    public function financials(Request $request)
    {
        $events = \App\Models\Events::with('organizer')->get();
        
        $totalRevenue = 0;
        $totalTicketsSold = 0;
        $platformFee = 0; // Ex: admin takes 5%
        $financialData = [];

        foreach ($events as $event) {
            $ticketTypes = \App\Models\EventTicketType::where('event_id', $event->id_event)
                                        ->with('ticketType', 'issuedTickets')
                                        ->get();
            
            $eventTotalRevenue = 0;
            $eventTicketsSold = 0;

            foreach ($ticketTypes as $ett) {
                $sold = $ett->issuedTickets->count();
                $revenue = $sold * $ett->price;
                
                $eventTicketsSold += $sold;
                $eventTotalRevenue += $revenue;
            }

            $totalRevenue += $eventTotalRevenue;
            $totalTicketsSold += $eventTicketsSold;

            if ($eventTicketsSold > 0) {
                $financialData[] = [
                    'event' => $event,
                    'organizer' => $event->organizer->name,
                    'total_revenue' => $eventTotalRevenue,
                    'tickets_sold' => $eventTicketsSold,
                    'platform_fee' => $eventTotalRevenue * 0.05,
                    'organizer_payout' => $eventTotalRevenue * 0.95
                ];
            }
        }

        $platformFee = $totalRevenue * 0.05;

        return view('admin.financials', compact('totalRevenue', 'totalTicketsSold', 'platformFee', 'financialData'));
    }
}
