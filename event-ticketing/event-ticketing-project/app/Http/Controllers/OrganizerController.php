<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\EventTicketType;
use Illuminate\Support\Facades\DB;

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

        // Chart: monthly revenue for organizer's events (last 6 months)
        $chartMonths = [];
        $chartRevenue = [];
        $chartTickets = [];
        // Get organizer's event IDs
        $organizerEventIds = Events::where('organizer_id', $user->id)->pluck('id_event')->toArray();
        $ettIds = EventTicketType::whereIn('event_id', $organizerEventIds)->pluck('id')->toArray();

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartMonths[] = $month->format('M Y');

            // Count issued tickets sold in that month for organizer's events
            $ticketsSold = DB::table('issued_tickets')
                ->whereIn('event_ticket_type_id', $ettIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $chartTickets[] = $ticketsSold;

            // Sum revenue: join issued_tickets with event_ticket_types to get price
            $revenue = DB::table('issued_tickets')
                ->join('event_ticket_types', 'issued_tickets.event_ticket_type_id', '=', 'event_ticket_types.id')
                ->whereIn('issued_tickets.event_ticket_type_id', $ettIds)
                ->whereYear('issued_tickets.created_at', $month->year)
                ->whereMonth('issued_tickets.created_at', $month->month)
                ->sum('event_ticket_types.price');
            $chartRevenue[] = (float) $revenue;
        }

        return view('organizer.dashboard', compact('user', 'stats', 'events', 'status', 'chartMonths', 'chartRevenue', 'chartTickets'));
    }

    // Halaman Sales & Payouts untuk Organizer
    private function getSalesData()
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

        return compact('user', 'salesData', 'totalRevenue', 'totalTicketsSold');
    }

    public function sales(Request $request)
    {
        $data = $this->getSalesData();
        return view('organizer.sales', $data);
    }

    public function exportSalesExcel()
    {
        $data = $this->getSalesData();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OrganizerSalesExport($data['salesData']), 'organizer_sales.xlsx');
    }

    public function exportSalesPDF()
    {
        $data = $this->getSalesData();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.organizer_sales', $data);
        return $pdf->download('organizer_sales.pdf');
    }
}
