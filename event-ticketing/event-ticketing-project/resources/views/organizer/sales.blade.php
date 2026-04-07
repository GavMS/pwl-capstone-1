<x-app-layout>
    <div class="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-container">
                <div>
                    <h2 class="lowercase" style="font-size: 1.875rem; font-weight: 800; color: var(--text-dark); margin: 0;">
                        sales &amp; payouts.
                    </h2>
                    <p class="lowercase" style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                        track your ticket sales and revenue
                    </p>
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
</x-app-layout>
