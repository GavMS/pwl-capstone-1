<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Events;
use App\Models\EventTicketType;
use App\Exports\OrganizerSalesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * OrganizerController
 *
 * Dashboard & sales reporting for the Organizer role.
 * Used by: organizer.dashboard, organizer.sales, and export routes.
 */
class OrganizerController extends Controller
{
    /**
     * Organizer dashboard — event stats, filtered event list, and monthly charts.
     */
    public function dashboard(Request $request)
    {
        $user   = auth()->user();
        $status = $request->query('status', 'active');

        $stats = [
            'total_events'  => Events::where('organizer_id', $user->id)->count(),
            'active_events' => Events::where('organizer_id', $user->id)->where('status', 'published')->count(),
            'draft_events'  => Events::where('organizer_id', $user->id)->where('status', 'draft')->count(),
        ];

        // Filter events by tab selection
        $query = Events::where('organizer_id', $user->id);

        if ($status === 'draft') {
            $query->where('status', 'draft');
        } elseif ($status === 'past') {
            $query->where('date', '<', now());
        } else {
            $query->where('status', 'published')->where('date', '>=', now());
        }

        $events = $query->latest()->take(5)->get();

        // Monthly chart data: revenue + tickets sold for last 6 months
        [$chartMonths, $chartRevenue, $chartTickets] = $this->buildMonthlyCharts($user->id);

        return view('organizer.dashboard', compact(
            'user', 'stats', 'events', 'status', 'chartMonths', 'chartRevenue', 'chartTickets'
        ));
    }

    /**
     * Sales & Payouts report page.
     */
    public function sales(Request $request)
    {
        $data = $this->getSalesData();
        return view('organizer.sales', $data);
    }

    /**
     * Export sales data as Excel download.
     */
    public function exportSalesExcel()
    {
        $data = $this->getSalesData();
        return Excel::download(
            new OrganizerSalesExport($data['salesData']),
            'organizer_sales.xlsx'
        );
    }

    /**
     * Export sales data as PDF download.
     */
    public function exportSalesPDF()
    {
        $data = $this->getSalesData();
        $pdf  = Pdf::loadView('pdf.organizer_sales', $data);
        return $pdf->download('organizer_sales.pdf');
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Build monthly revenue and ticket-sold charts for the organizer.
     *
     * @return array [months[], revenue[], tickets[]]
     */
    private function buildMonthlyCharts(int $organizerId): array
    {
        $organizerEventIds = Events::where('organizer_id', $organizerId)->pluck('id_event')->toArray();
        $ettIds            = EventTicketType::whereIn('event_id', $organizerEventIds)->pluck('id')->toArray();

        $months  = [];
        $revenue = [];
        $tickets = [];

        for ($i = 5; $i >= 0; $i--) {
            $month    = now()->subMonths($i);
            $months[] = $month->format('M Y');

            $tickets[] = DB::table('issued_tickets')
                ->whereIn('event_ticket_type_id', $ettIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $revenue[] = (float) DB::table('issued_tickets')
                ->join('event_ticket_types', 'issued_tickets.event_ticket_type_id', '=', 'event_ticket_types.id')
                ->whereIn('issued_tickets.event_ticket_type_id', $ettIds)
                ->whereYear('issued_tickets.created_at', $month->year)
                ->whereMonth('issued_tickets.created_at', $month->month)
                ->sum('event_ticket_types.price');
        }

        return [$months, $revenue, $tickets];
    }

    /**
     * Aggregate per-event sales data for the current organizer.
     * Used by the view, Excel export, and PDF export.
     */
    private function getSalesData(): array
    {
        $user   = auth()->user();
        $events = Events::where('organizer_id', $user->id)->get();

        $salesData        = [];
        $totalRevenue     = 0;
        $totalTicketsSold = 0;

        foreach ($events as $event) {
            $ticketTypes = EventTicketType::where('event_id', $event->id_event)
                ->with('ticketType', 'issuedTickets')
                ->get();

            $eventRevenue     = 0;
            $eventTicketsSold = 0;
            $ticketDetails    = [];

            foreach ($ticketTypes as $ett) {
                $sold    = $ett->issuedTickets->count();
                $revenue = $sold * $ett->price;

                $eventTicketsSold += $sold;
                $eventRevenue     += $revenue;

                $ticketDetails[] = [
                    'name'             => $ett->ticketType->name,
                    'price'            => $ett->price,
                    'sold'             => $sold,
                    'revenue'          => $revenue,
                    'stock'            => $ett->stock,
                    'initial_capacity' => $ett->stock + $sold,
                ];
            }

            $totalRevenue     += $eventRevenue;
            $totalTicketsSold += $eventTicketsSold;

            $salesData[] = [
                'event'         => $event,
                'total_revenue' => $eventRevenue,
                'tickets_sold'  => $eventTicketsSold,
                'ticket_details' => $ticketDetails,
            ];
        }

        return compact('user', 'salesData', 'totalRevenue', 'totalTicketsSold');
    }
}
