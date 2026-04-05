<x-app-layout>
    <div class="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-container">
                <div>
                    <h2 class="lowercase" style="font-size: 1.875rem; font-weight: 800; color: var(--text-dark); margin: 0;">
                        platform financials.
                    </h2>
                    <p class="lowercase" style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                        monitor overall platform revenue and payouts
                    </p>
                </div>
            </div>
        </header>

        <!-- Quick Stats -->
        <section class="stats-grid">
            <div class="stat-card" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: white;">
                <span class="text-xs font-bold uppercase tracking-widest" style="color: #bfdbfe;">Total Gross Revenue</span>
                <h4 style="color: white; font-size: 2rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #dbeafe; margin: 0;">all events combined.</p>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); color: white;">
                <span class="text-xs font-bold uppercase tracking-widest" style="color: #6ee7b7;">Platform Fee (5%)</span>
                <h4 style="color: white; font-size: 2rem;">Rp {{ number_format($platformFee, 0, ',', '.') }}</h4>
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #a7f3d0; margin: 0;">revenue for platform.</p>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold uppercase tracking-widest text-muted" style="color: var(--text-muted);">Total Tickets Sold</span>
                <h4>{{ number_format($totalTicketsSold) }} <span style="font-size: 1rem; color: var(--text-muted);">tickets</span></h4>
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #16a34a; margin: 0;">across the platform.</p>
            </div>
        </section>

        <!-- Payout breakdown -->
        <section class="event-section pt-8">
            <div class="flex items-center gap-4 mb-6">
                <h3 class="lowercase" style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0;">
                    payout breakdown
                </h3>
            </div>

            <div style="background: white; border-radius: var(--radius-xl); border: 1px solid var(--gray-border); overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead style="background: var(--gray-light);">
                            <tr>
                                <th style="padding: 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; tracking: 0.05em; border-bottom: 2px solid var(--gray-border);">Event</th>
                                <th style="padding: 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; tracking: 0.05em; border-bottom: 2px solid var(--gray-border);">Organizer</th>
                                <th style="padding: 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; tracking: 0.05em; border-bottom: 2px solid var(--gray-border); text-align: center;">Sold</th>
                                <th style="padding: 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; tracking: 0.05em; border-bottom: 2px solid var(--gray-border); text-align: right;">Gross Rev</th>
                                <th style="padding: 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; tracking: 0.05em; border-bottom: 2px solid var(--gray-border); text-align: right;">Our Fee (5%)</th>
                                <th style="padding: 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; tracking: 0.05em; border-bottom: 2px solid var(--gray-border); text-align: right;">Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($financialData as $data)
                            <tr style="border-bottom: 1px solid var(--gray-border);">
                                <td style="padding: 1rem; font-weight: 800; color: var(--text-dark);">
                                    {{ $data['event']->title }}<br>
                                    <span style="font-size: 11px; font-weight: 600; color: var(--text-muted);">{{ $data['event']->date->format('M d, Y') }}</span>
                                </td>
                                <td style="padding: 1rem; font-weight: 600; color: var(--text-muted);">
                                    {{ $data['organizer'] }}
                                </td>
                                <td style="padding: 1rem; text-align: center; font-weight: 700; color: #16a34a;">
                                    {{ $data['tickets_sold'] }}
                                </td>
                                <td style="padding: 1rem; text-align: right; font-weight: 600; color: var(--text-dark);">
                                    Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}
                                </td>
                                <td style="padding: 1rem; text-align: right; font-weight: 700; color: #059669;">
                                    Rp {{ number_format($data['platform_fee'], 0, ',', '.') }}
                                </td>
                                <td style="padding: 1rem; text-align: right; font-weight: 800; color: #1e40af;">
                                    Rp {{ number_format($data['organizer_payout'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-muted); font-weight: 700;">No completed sales data yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
