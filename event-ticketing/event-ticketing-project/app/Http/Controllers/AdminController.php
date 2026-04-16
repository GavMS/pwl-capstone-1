<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Accounts;
use App\Models\Events;
use App\Models\EventTicketType;
use App\Exports\AdminFinancialsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * AdminController
 *
 * Dashboard & financial reporting for the Admin role.
 * Used by: admin.dashboard, admin.financials, and export routes.
 */
class AdminController extends Controller
{
    /**
     * Admin dashboard — platform-wide statistics and monthly revenue chart.
     */
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

        // Revenue chart data: last 6 months
        [$chartMonths, $chartRevenue] = $this->buildMonthlyRevenueChart();

        return view('admin.dashboard', compact(
            'user', 'stats', 'new_users', 'recent_events', 'chartMonths', 'chartRevenue'
        ));
    }

    /**
     * Financial report page — per-event breakdown with platform fee calculation.
     */
    public function financials(Request $request)
    {
        $data = $this->getFinancialData();
        return view('admin.financials', $data);
    }

    /**
     * Export financial report as Excel file.
     */
    public function exportFinancialsExcel()
    {
        $data = $this->getFinancialData();
        return Excel::download(
            new AdminFinancialsExport($data['financialData']),
            'admin_financials.xlsx'
        );
    }

    /**
     * Export financial report as PDF file.
     */
    public function exportFinancialsPDF()
    {
        $data = $this->getFinancialData();
        $pdf  = Pdf::loadView('pdf.admin_financials', $data);
        return $pdf->download('admin_financials.pdf');
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Build monthly revenue chart data for the last 6 months.
     *
     * @return array [months[], revenue[]]
     */
    private function buildMonthlyRevenueChart(): array
    {
        $months  = [];
        $revenue = [];

        for ($i = 5; $i >= 0; $i--) {
            $month    = now()->subMonths($i);
            $months[] = $month->format('M Y');

            $revenue[] = (float) DB::table('transaction')
                ->where('status', 'success')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_price');
        }

        return [$months, $revenue];
    }

    /**
     * Aggregate financial data across all events (used by view, Excel, and PDF exports).
     * Calculates per-event revenue, platform fee (5%), and organizer payout (95%).
     */
    private function getFinancialData(): array
    {
        $events = Events::with('organizer')->get();

        $totalRevenue     = 0;
        $totalTicketsSold = 0;
        $financialData    = [];

        foreach ($events as $event) {
            $ticketTypes = EventTicketType::where('event_id', $event->id_event)
                ->with('ticketType', 'issuedTickets')
                ->get();

            $eventRevenue     = 0;
            $eventTicketsSold = 0;

            foreach ($ticketTypes as $ett) {
                $sold          = $ett->issuedTickets->count();
                $eventTicketsSold += $sold;
                $eventRevenue     += $sold * $ett->price;
            }

            $totalRevenue     += $eventRevenue;
            $totalTicketsSold += $eventTicketsSold;

            // Only include events that have at least one sale
            if ($eventTicketsSold > 0) {
                $ticketDetails = $ticketTypes->map(fn ($ett) => [
                    'name'             => $ett->ticketType->name ?? 'Unknown',
                    'price'            => $ett->price,
                    'sold'             => $ett->issuedTickets->count(),
                    'revenue'          => $ett->issuedTickets->count() * $ett->price,
                    'stock'            => $ett->stock,
                    'initial_capacity' => $ett->stock + $ett->issuedTickets->count(),
                ])->toArray();

                $financialData[] = [
                    'event'            => $event,
                    'organizer'        => $event->organizer->name ?? '-',
                    'total_revenue'    => $eventRevenue,
                    'tickets_sold'     => $eventTicketsSold,
                    'platform_fee'     => $eventRevenue * 0.05,
                    'organizer_payout' => $eventRevenue * 0.95,
                    'ticket_details'   => $ticketDetails,
                ];
            }
        }

        $platformFee = $totalRevenue * 0.05;

        return compact('totalRevenue', 'totalTicketsSold', 'platformFee', 'financialData');
    }
}
