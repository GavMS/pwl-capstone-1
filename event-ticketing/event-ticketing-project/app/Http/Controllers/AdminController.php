<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accounts;
use App\Models\Events;
use Illuminate\Support\Facades\DB;

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

        // Chart: monthly transaction revenue for last 6 months
        $chartMonths = [];
        $chartRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartMonths[] = $month->format('M Y');
            $revenue = DB::table('transaction')
                ->where('status', 'success')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_price');
            $chartRevenue[] = (float) $revenue;
        }

        return view('admin.dashboard', compact('user', 'stats', 'new_users', 'recent_events', 'chartMonths', 'chartRevenue'));
    }

    private function getFinancialData()
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
                $ticketDetails = [];
                foreach ($ticketTypes as $ett) {
                    $sold = $ett->issuedTickets->count();
                    $ticketDetails[] = [
                        'name'             => $ett->ticketType->name ?? 'Unknown',
                        'price'            => $ett->price,
                        'sold'             => $sold,
                        'revenue'          => $sold * $ett->price,
                        'stock'            => $ett->stock,
                        'initial_capacity' => $ett->stock + $sold,
                    ];
                }

                $financialData[] = [
                    'event'            => $event,
                    'organizer'        => $event->organizer->name,
                    'total_revenue'    => $eventTotalRevenue,
                    'tickets_sold'     => $eventTicketsSold,
                    'platform_fee'     => $eventTotalRevenue * 0.05,
                    'organizer_payout' => $eventTotalRevenue * 0.95,
                    'ticket_details'   => $ticketDetails,
                ];
            }
        }

        $platformFee = $totalRevenue * 0.05;

        return compact('totalRevenue', 'totalTicketsSold', 'platformFee', 'financialData');
    }

    public function financials(Request $request)
    {
        $data = $this->getFinancialData();
        return view('admin.financials', $data);
    }

    public function exportFinancialsExcel()
    {
        $data = $this->getFinancialData();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AdminFinancialsExport($data['financialData']), 'admin_financials.xlsx');
    }

    public function exportFinancialsPDF()
    {
        $data = $this->getFinancialData();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin_financials', $data);
        return $pdf->download('admin_financials.pdf');
    }
}
