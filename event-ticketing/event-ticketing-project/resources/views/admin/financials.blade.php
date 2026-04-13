<x-app-layout>
    <div class="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 class="lowercase" style="font-size: 1.875rem; font-weight: 800; color: var(--text-dark); margin: 0;">
                        platform financials.
                    </h2>
                    <p class="lowercase" style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                        monitor overall platform revenue and payouts
                    </p>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('admin.financials.export.excel') }}" style="display: inline-flex; items-center; padding: 0.6rem 1.25rem; background: #10b981; color: white; border-radius: 0.75rem; font-weight: 700; font-size: 0.8rem; text-transform: lowercase; text-decoration: none; box-shadow: var(--shadow-sm); transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        export excel
                    </a>
                    <a href="{{ route('admin.financials.export.pdf') }}" style="display: inline-flex; items-center; padding: 0.6rem 1.25rem; background: #ef4444; color: white; border-radius: 0.75rem; font-weight: 700; font-size: 0.8rem; text-transform: lowercase; text-decoration: none; box-shadow: var(--shadow-sm); transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        export pdf
                    </a>
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
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
                <h3 class="lowercase" style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0;">
                    payout breakdown
                </h3>
                <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em;">
                    click a row to see details
                </span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($financialData as $index => $data)

                {{-- ── Row card ── --}}
                <div style="background: white; border-radius: 1.25rem; border: 1px solid var(--gray-border); overflow: hidden;">

                    {{-- Clickable summary row --}}
                    <button
                        type="button"
                        onclick="toggleEventDetail({{ $index }})"
                        style="width: 100%; background: none; border: none; cursor: pointer; padding: 0; text-align: left;"
                        id="row-btn-{{ $index }}"
                    >
                        <div style="display: grid; grid-template-columns: 2fr 1.2fr 0.6fr 1fr 1fr 1fr 2rem; align-items: center; padding: 1.1rem 1.25rem; gap: 0.75rem; transition: background 0.15s;" class="fin-row" id="fin-row-{{ $index }}">

                            {{-- Event --}}
                            <div style="text-align: left;">
                                <p style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark); margin: 0 0 0.15rem 0;">{{ $data['event']->title }}</p>
                                <p style="font-size: 11px; font-weight: 600; color: var(--text-muted); margin: 0;">{{ $data['event']->date->format('M d, Y') }}</p>
                            </div>

                            {{-- Organizer --}}
                            <div style="text-align: left;">
                                <p style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin: 0;">{{ $data['organizer'] }}</p>
                            </div>

                            {{-- Sold --}}
                            <div style="text-align: center;">
                                <span style="font-weight: 800; font-size: 1rem; color: #16a34a;">{{ $data['tickets_sold'] }}</span>
                            </div>

                            {{-- Gross Rev --}}
                            <div style="text-align: right;">
                                <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-dark);">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</span>
                            </div>

                            {{-- Fee --}}
                            <div style="text-align: right;">
                                <span style="font-weight: 700; font-size: 0.85rem; color: #059669;">Rp {{ number_format($data['platform_fee'], 0, ',', '.') }}</span>
                            </div>

                            {{-- Payout --}}
                            <div style="text-align: right;">
                                <span style="font-weight: 800; font-size: 0.85rem; color: #1e40af;">Rp {{ number_format($data['organizer_payout'], 0, ',', '.') }}</span>
                            </div>

                            {{-- Chevron --}}
                            <div style="display: flex; align-items: center; justify-content: center;">
                                <svg id="chevron-{{ $index }}" style="width: 1.1rem; height: 1.1rem; color: var(--text-muted); transition: transform 0.25s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Column headers — only on first row --}}
                        @if($index === 0)
                        @endif
                    </button>

                    {{-- ── Expandable detail panel ── --}}
                    <div id="detail-{{ $index }}" style="display: none; border-top: 1px solid var(--gray-border);">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 1.75rem 1.5rem;">

                            {{-- Left: Doughnut chart --}}
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--text-muted); margin: 0 0 1rem 0; text-align: center;">Revenue by ticket type</p>
                                <div style="position: relative; width: 100%; max-width: 220px; margin: 0 auto;">
                                    <canvas id="doughnut-{{ $index }}" height="220"></canvas>
                                    <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; transform: translateY(-25px);">
                                        <p style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin: 0;">Total</p>
                                        <p style="font-size: 1rem; font-weight: 800; color: var(--text-dark); margin: 0.15rem 0 0 0;">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Right: Ticket breakdown table --}}
                            <div>
                                <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--text-muted); margin: 0 0 1rem 0;">Ticket type breakdown</p>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid var(--gray-light);">
                                            <th style="padding: 0.5rem 0.75rem 0.5rem 0; text-align: left; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted);">Type</th>
                                            <th style="padding: 0.5rem 0.75rem; text-align: right; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted);">Price</th>
                                            <th style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted);">Sold / Cap</th>
                                            <th style="padding: 0.5rem 0 0.5rem 0.75rem; text-align: right; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted);">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['ticket_details'] as $t)
                                        <tr style="border-bottom: 1px solid var(--gray-light);">
                                            <td style="padding: 0.75rem 0.75rem 0.75rem 0; font-weight: 700; color: var(--text-dark);">{{ $t['name'] }}</td>
                                            <td style="padding: 0.75rem; text-align: right; color: var(--text-muted); font-weight: 600;">
                                                {{ $t['price'] == 0 ? 'Free' : 'Rp ' . number_format($t['price'], 0, ',', '.') }}
                                            </td>
                                            <td style="padding: 0.75rem; text-align: center;">
                                                <span style="font-weight: 800; color: #16a34a;">{{ $t['sold'] }}</span>
                                                <span style="color: var(--text-muted); font-size: 11px;"> / {{ $t['initial_capacity'] }}</span>
                                            </td>
                                            <td style="padding: 0.75rem 0 0.75rem 0.75rem; text-align: right; font-weight: 700; color: var(--text-dark);">
                                                Rp {{ number_format($t['revenue'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        {{-- totals row --}}
                                        <tr style="background: var(--gray-light); border-radius: 0.5rem;">
                                            <td colspan="2" style="padding: 0.7rem 0.75rem 0.7rem 0; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-dark);">Total</td>
                                            <td style="padding: 0.7rem 0.75rem; text-align: center; font-weight: 800; color: #16a34a;">{{ $data['tickets_sold'] }}</td>
                                            <td style="padding: 0.7rem 0 0.7rem 0.75rem; text-align: right; font-weight: 800; color: #1e40af;">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- Fee split summary --}}
                                <div style="margin-top: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div style="background: #f0fdf4; border-radius: 0.75rem; padding: 0.75rem 1rem;">
                                        <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #166534; margin: 0 0 0.2rem 0;">Platform fee (5%)</p>
                                        <p style="font-size: 1rem; font-weight: 800; color: #15803d; margin: 0;">Rp {{ number_format($data['platform_fee'], 0, ',', '.') }}</p>
                                    </div>
                                    <div style="background: #eff6ff; border-radius: 0.75rem; padding: 0.75rem 1rem;">
                                        <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #1e40af; margin: 0 0 0.2rem 0;">Organizer payout</p>
                                        <p style="font-size: 1rem; font-weight: 800; color: #1d4ed8; margin: 0;">Rp {{ number_format($data['organizer_payout'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                @empty
                <div style="background: white; padding: 3rem; border-radius: 1.25rem; text-align: center; border: 1px solid var(--gray-border);">
                    <p style="font-weight: 700; color: var(--text-muted); margin: 0;">No completed sales data yet.</p>
                </div>
                @endforelse
            </div>

            {{-- Column header label bar (above the rows, sticky) --}}
            @if(count($financialData) > 0)
            <div style="display: grid; grid-template-columns: 2fr 1.2fr 0.6fr 1fr 1fr 1fr 2rem; gap: 0.75rem; padding: 0.5rem 1.25rem; margin-bottom: 0.25rem; position: sticky; top: 0; z-index: 1;" id="col-headers">
            </div>
            @endif
        </section>
    </div>

    {{-- Pass all chart data to JS --}}
    @php
        $allChartData = collect($financialData)->map(function ($d) {
            return [
                'labels'   => collect($d['ticket_details'])->pluck('name')->values(),
                'revenues' => collect($d['ticket_details'])->pluck('revenue')->values(),
            ];
        })->values();
    @endphp

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const allChartData = @json($allChartData);
        const openStates   = {};  // track which panels are open
        const chartInstances = {};

        // Palette
        const palette = [
            '#1e40af','#0284c7','#0891b2','#0d9488','#059669',
            '#65a30d','#ca8a04','#ea580c','#dc2626','#9333ea'
        ];

        function toggleEventDetail(index) {
            const panel   = document.getElementById('detail-' + index);
            const chevron = document.getElementById('chevron-' + index);
            const row     = document.getElementById('fin-row-' + index);

            const isOpen = openStates[index];
            openStates[index] = !isOpen;

            if (!isOpen) {
                // Open
                panel.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
                row.style.background = '#f8fafc';

                // Draw doughnut chart only once
                if (!chartInstances[index]) {
                    const data = allChartData[index];
                    const ctx  = document.getElementById('doughnut-' + index).getContext('2d');
                    chartInstances[index] = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.revenues,
                                backgroundColor: palette.slice(0, data.labels.length),
                                borderWidth: 3,
                                borderColor: '#ffffff',
                                hoverBorderWidth: 4,
                            }]
                        },
                        options: {
                            cutout: '68%',
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#555555',
                                        font: { size: 11, weight: '700' },
                                        usePointStyle: true,
                                        pointStyleWidth: 8,
                                        boxHeight: 6,
                                        padding: 14,
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#ffffff',
                                    titleColor: '#444444',
                                    bodyColor: '#777777',
                                    borderColor: '#e5e7eb',
                                    borderWidth: 1,
                                    padding: 12,
                                    cornerRadius: 14,
                                    callbacks: {
                                        label: function(ctx) {
                                            return '  Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } else {
                // Close
                panel.style.display = 'none';
                chevron.style.transform = 'rotate(0deg)';
                row.style.background = '';
            }
        }

        // Hover tint
        document.querySelectorAll('.fin-row').forEach(function(row) {
            row.addEventListener('mouseenter', function() {
                if (row.style.background === '') row.style.background = '#fafafa';
            });
            row.addEventListener('mouseleave', function() {
                const idx = row.id.replace('fin-row-', '');
                if (!openStates[idx]) row.style.background = '';
            });
        });
    </script>
    @endpush
</x-app-layout>
