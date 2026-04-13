<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="px-10 py-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-4xl font-extrabold text-[#555555] tracking-tight lowercase">
                        admin overview.
                    </h2>
                    <p class="text-[#777777] font-medium text-base lowercase mt-1">
                        system status & analytics
                    </p>
                </div>
                <div class="flex gap-4">
                    <a href="#" class="inline-block px-6 py-3 bg-[#F4F4F4] text-[#555555] rounded-2xl font-bold lowercase hover:bg-[#EBEBEB] transition shadow-sm">
                        settings
                    </a>
                    <a href="#" class="inline-block px-6 py-3 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition shadow-sm">
                        generate report
                    </a>
                </div>
            </div>
        </header>

        <main class="w-full px-10 mt-12 flex flex-col gap-16">
            
            <!-- Quick Stats -->
            <section>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
                    <!-- Stat 1 -->
                    <div class="bg-white p-10 rounded-[3rem] shadow-sm flex flex-col gap-4 border border-gray-50 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-[0.2em]">Total Users</span>
                            <div class="w-12 h-12 rounded-2xl bg-[#F4F4F4] flex items-center justify-center text-[#555555]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                        </div>
                        <h4 class="text-5xl font-extrabold text-[#444444] tracking-tighter mt-2">{{ number_format($stats['total_users']) }}</h4>
                        <p class="text-xs font-bold text-green-600 uppercase tracking-widest mt-1">active accounts</p>
                    </div>

                    <!-- Stat 2 -->
                    <div class="bg-white p-10 rounded-[3rem] shadow-sm flex flex-col gap-4 border border-gray-50 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-[0.2em]">Active Events</span>
                            <div class="w-12 h-12 rounded-2xl bg-[#F4F4F4] flex items-center justify-center text-[#555555]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        <h4 class="text-5xl font-extrabold text-[#444444] tracking-tighter mt-2">{{ number_format($stats['active_events']) }}</h4>
                        <p class="text-xs font-bold text-green-600 uppercase tracking-widest mt-1">published events</p>
                    </div>

                    <!-- Stat 3 -->
                    <div class="bg-white p-10 rounded-[3rem] shadow-sm flex flex-col gap-4 border border-gray-50 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-[0.2em]">Platform Revenue</span>
                            <div class="w-12 h-12 rounded-2xl bg-[#F4F4F4] flex items-center justify-center text-[#555555]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <h4 class="text-5xl font-extrabold text-[#444444] tracking-tighter mt-2">{{ number_format($stats['total_events']) }}</h4>
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mt-1">all created events</p>
                    </div>
                </div>
            </section>

            <!-- Transaction Chart -->
            <section>
                <div class="flex justify-between items-end mb-6">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">revenue overview.</h3>
                    <span class="text-xs font-bold text-[#999999] uppercase tracking-widest">last 6 months</span>
                </div>
                <div class="bg-white rounded-[2.5rem] shadow-sm p-8 border border-gray-50">
                    <canvas id="adminRevenueChart" height="100"></canvas>
                </div>
            </section>

            <!-- Data Overview -->
            <div class="grid grid-cols-1 2xl:grid-cols-2 gap-12">
                <!-- New Users -->
                <section>
                    <div class="flex justify-between items-end mb-6">
                        <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">newly joined.</h3>
                        <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">view all accounts</a>
                    </div>
                    
                    <div class="bg-white rounded-[2.5rem] shadow-sm overflow-hidden flex flex-col gap-2 p-4 border border-gray-50">
                        @forelse($new_users as $newUser)
                            <div class="flex items-center justify-between p-4 hover:bg-[#F4F4F4]/50 rounded-3xl transition group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-[#E5E5E3] flex items-center justify-center text-[#555555] font-bold text-sm uppercase shadow-sm">
                                        {{ substr($newUser->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-[#444444] leading-tight lowercase tracking-tight text-base">{{ $newUser->name }}</h5>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] font-bold text-[#999999] uppercase tracking-widest">{{ $newUser->role }}</span>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <span class="text-[10px] font-bold text-[#999999] lowercase">{{ $newUser->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.edit', $newUser->id) }}" class="w-10 h-10 bg-white border border-gray-100 text-[#777777] rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-sm hover:bg-black hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            </div>
                        @empty
                            <p class="p-8 text-center text-[#999999] lowercase italic">no new users yet.</p>
                        @endforelse
                    </div>
                </section>

                <!-- Recent Events -->
                <section>
                    <div class="flex justify-between items-end mb-6">
                        <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">latest events.</h3>
                        <a href="{{ route('admin.events.index') }}" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">manage events</a>
                    </div>
                    
                    <div class="bg-white rounded-[2.5rem] shadow-sm p-6 border border-gray-50">
                        <div class="flex flex-col gap-8 relative">
                            @forelse($recent_events as $recentEvent)
                                <div class="flex gap-5 relative group">
                                    <!-- Timeline line -->
                                    @if(!$loop->last)
                                        <div class="absolute left-6 top-12 bottom-[-2rem] w-px bg-gray-100 group-hover:bg-gray-200 transition"></div>
                                    @endif
                                    
                                    <div class="w-12 h-12 bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm flex-shrink-0 z-10">
                                        @if($recentEvent->banner)
                                            <img src="{{ asset('storage/' . $recentEvent->banner) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-[#F4F4F4] flex items-center justify-center text-[#999999]">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="pt-1 flex-1">
                                        <h5 class="text-base font-bold text-[#444444] lowercase tracking-tight leading-none">{{ $recentEvent->title }}</h5>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 bg-[#F4F4F4] text-[#777777] rounded-md uppercase tracking-widest">{{ $recentEvent->status }}</span>
                                            <span class="text-[10px] font-bold text-[#999999] lowercase">by {{ $recentEvent->organizer->name ?? 'platform' }}</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-[#999999] uppercase tracking-widest mt-2">{{ $recentEvent->created_at->format('d M, H:i') }}</p>
                                    </div>
                                    <a href="{{ route('admin.events.edit', $recentEvent->id_event) }}" class="inline-flex items-center text-[10px] font-bold text-[#555555] hover:text-black mt-1 lowercase opacity-0 group-hover:opacity-100 transition self-start pt-1">
                                        details ↗
                                    </a>
                                </div>
                            @empty
                                <p class="py-4 text-center text-[#999999] lowercase italic">no recent events.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
            
        </main>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const adminChartMonths  = @json($chartMonths);
        const adminChartRevenue = @json($chartRevenue);

        const ctx = document.getElementById('adminRevenueChart').getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(85, 85, 85, 0.18)');
        gradient.addColorStop(1, 'rgba(85, 85, 85, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: adminChartMonths,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: adminChartRevenue,
                    borderColor: '#444444',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#444444',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#444444',
                        bodyColor: '#777777',
                        borderColor: '#e5e5e3',
                        borderWidth: 1,
                        padding: 14,
                        cornerRadius: 16,
                        callbacks: {
                            label: function(context) {
                                return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
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
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
