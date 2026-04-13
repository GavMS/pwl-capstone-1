<x-app-layout>
    <div class="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 class="lowercase" style="font-size: 1.875rem; font-weight: 800; color: var(--text-dark); margin: 0;">
                        sales &amp; payouts.
                    </h2>
                    <p class="lowercase" style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                        track your ticket sales and revenue
                    </p>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('organizer.sales.export.excel') }}" style="display: inline-flex; items-center; padding: 0.6rem 1.25rem; background: #10b981; color: white; border-radius: 0.75rem; font-weight: 700; font-size: 0.8rem; text-transform: lowercase; text-decoration: none; box-shadow: var(--shadow-sm); transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        export excel
                    </a>
                    <a href="{{ route('organizer.sales.export.pdf') }}" style="display: inline-flex; items-center; padding: 0.6rem 1.25rem; background: #ef4444; color: white; border-radius: 0.75rem; font-weight: 700; font-size: 0.8rem; text-transform: lowercase; text-decoration: none; box-shadow: var(--shadow-sm); transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        export pdf
                    </a>
                </div>
            </div>
        </header>

        <!-- Quick Stats -->
        <section class="stats-grid">
            <div class="stat-card" style="background: linear-gradient(135deg, #134e4a 0%, #115e59 100%); color: white;">
                <span class="text-xs font-bold uppercase tracking-widest" style="color: #99f6e4;">Total Revenue</span>
                <h4 style="color: white; font-size: 2rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #ccfbf1; margin: 0;">from all events.</p>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold uppercase tracking-widest text-muted" style="color: var(--text-muted);">Total Tickets Sold</span>
                <h4>{{ number_format($totalTicketsSold) }} <span style="font-size: 1rem; color: var(--text-muted);">tickets</span></h4>
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #16a34a; margin: 0;">across all your events.</p>
            </div>
        </section>

        <!-- Revenue Per Event Chart -->
        @if(count($salesData) > 0)
        <section class="event-section" style="padding-top: 0;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
                <h3 class="lowercase" style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0;">revenue by event.</h3>
                <span style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.15em;">all events</span>
            </div>
            <div style="background: white; border-radius: var(--radius-2xl); padding: 2rem; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-border);">
                <canvas id="salesEventChart" height="120"></canvas>
            </div>
        </section>
        @endif

        <!-- Sales per Event -->
        <section class="event-section pt-8">
            <div class="flex items-center gap-4 mb-6">
                <h3 class="lowercase" style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0 0 1.5rem 0;">
                    event breakdown
                </h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                @forelse ($salesData as $data)
                    {{-- Sales card: use raw styles, NOT .event-card class, to avoid alignment conflict --}}
                    <div style="background: white; border-radius: var(--radius-2xl); padding: 1.75rem 2rem; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-border);">

                        {{-- Event header: title left, revenue right --}}
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px dashed var(--gray-border);">
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="font-size: 1.2rem; font-weight: 800; color: var(--text-dark); margin: 0 0 0.3rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $data['event']->title }}
                                </h4>
                                <p class="lowercase" style="font-size: 12px; color: var(--text-muted); margin: 0;">
                                    {{ $data['event']->date->format('M d, Y') }} &bull; {{ $data['event']->location }}
                                </p>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin: 0 0 0.3rem 0;">
                                    Event Revenue
                                </p>
                                <span style="font-size: 1.4rem; font-weight: 800; color: #115e59;">
                                    Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Ticket breakdown table -->
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                                <thead>
                                    <tr>
                                        <th style="padding: 0.6rem 1rem 0.6rem 0; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); white-space: nowrap;">Ticket Type</th>
                                        <th style="padding: 0.6rem 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); white-space: nowrap;">Price</th>
                                        <th style="padding: 0.6rem 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); white-space: nowrap;">Sold / Cap</th>
                                        <th style="padding: 0.6rem 0 0.6rem 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); text-align: right; white-space: nowrap;">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['ticket_details'] as $t)
                                    <tr>
                                        <td style="padding: 0.9rem 1rem 0.9rem 0; border-bottom: 1px solid var(--gray-light); font-weight: 700; color: var(--text-dark);">
                                            {{ $t['name'] }}
                                        </td>
                                        <td style="padding: 0.9rem 1rem; border-bottom: 1px solid var(--gray-light); color: var(--text-muted); font-weight: 600;">
                                            {{ $t['price'] == 0 ? 'Free' : 'Rp ' . number_format($t['price'], 0, ',', '.') }}
                                        </td>
                                        <td style="padding: 0.9rem 1rem; border-bottom: 1px solid var(--gray-light);">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="font-weight: 700; color: #16a34a;">{{ $t['sold'] }}</span>
                                                <span style="color: var(--text-muted); font-size: 11px;">/ {{ $t['initial_capacity'] }}</span>
                                                <span style="font-size: 10px; padding: 2px 7px; background: var(--gray-light); border-radius: 4px; color: var(--text-muted); font-weight: 600;">{{ $t['stock'] }} left</span>
                                            </div>
                                        </td>
                                        <td style="padding: 0.9rem 0 0.9rem 1rem; border-bottom: 1px solid var(--gray-light); text-align: right; font-weight: 700; color: var(--text-dark);">
                                            Rp {{ number_format($t['revenue'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" style="padding: 1.5rem 0; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">
                                            No ticket types configured
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div style="background: white; padding: 4rem; border-radius: var(--radius-2xl); text-align: center; border: 1px solid var(--gray-border);">
                        <p class="lowercase" style="font-weight: 700; color: var(--text-muted); margin: 0;">no event sales data yet.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if(count($salesData) > 0)
        const salesEventLabels  = @json(collect($salesData)->pluck('event.title')->map(fn($t) => strlen($t) > 18 ? substr($t, 0, 18).'…' : $t)->values());
        const salesEventRevenue = @json(collect($salesData)->pluck('total_revenue')->values());
        const salesTicketsSold  = @json(collect($salesData)->pluck('tickets_sold')->values());

        const ctx3 = document.getElementById('salesEventChart').getContext('2d');

        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: salesEventLabels,
                datasets: [
                    {
                        label: 'Revenue (Rp)',
                        data: salesEventRevenue,
                        backgroundColor: '#115e59',
                        borderRadius: 10,
                        borderSkipped: false,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Tickets Sold',
                        data: salesTicketsSold,
                        backgroundColor: '#6ee7b7',
                        borderRadius: 10,
                        borderSkipped: false,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#777777',
                            font: { size: 11, weight: '700' },
                            usePointStyle: true,
                            pointStyleWidth: 8,
                            boxHeight: 6
                        }
                    },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#444444',
                        bodyColor: '#777777',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 14,
                        cornerRadius: 16,
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'y') {
                                    return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return ' ' + context.parsed.y + ' tickets';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#999999', font: { size: 11, weight: '700' } },
                        border: { display: false }
                    },
                    y: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: '#f4f4f4' },
                        ticks: {
                            color: '#999999',
                            font: { size: 11, weight: '700' },
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                return 'Rp ' + value;
                            }
                        },
                        border: { display: false }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: {
                            color: '#6ee7b7',
                            font: { size: 11, weight: '700' },
                            callback: function(value) { return value + ' tkts'; }
                        },
                        border: { display: false }
                    }
                }
            }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
