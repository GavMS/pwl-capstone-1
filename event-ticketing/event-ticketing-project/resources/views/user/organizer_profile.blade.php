<x-app-layout>
    <div class="min-h-screen bg-[#F9F9F9] font-sans antialiased pb-24">
        
        <!-- Organizer Header Profile -->
        <div class="bg-white border-b border-gray-200 pt-16 pb-12 px-4 shadow-sm relative overflow-hidden">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 right-0 -m-32 w-64 h-64 bg-[#38b2ac]/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -m-32 w-64 h-64 bg-[#38b2ac]/5 rounded-full blur-3xl"></div>

            <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-6 md:gap-10 relative z-10 w-full justify-center md:justify-start">
                
                <!-- Avatar -->
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white shadow-xl overflow-hidden bg-gray-100 flex-shrink-0 relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($organizer->name) }}&color=555555&background=E5E5E3&size=256" class="w-full h-full object-cover" alt="{{ $organizer->name }}">
                    <!-- Verified logic could go here if we ever have it -->
                </div>

                <!-- Info Box -->
                <div class="text-center md:text-left flex flex-col items-center md:items-start gap-4 flex-grow">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-[10px] font-extrabold uppercase tracking-widest border border-teal-100 mb-2">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                            Official Organizer
                        </span>
                        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">{{ $organizer->name }}</h1>
                    </div>

                    <!-- Single Statistic: Expected by User -->
                    <div class="flex gap-8 mt-2 items-center">
                        <div class="flex flex-col items-center md:items-start">
                            <span class="text-2xl font-black text-[#38b2ac] leading-none">{{ $events->total() }}</span>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Events Hosted</span>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="hidden md:block">
                    <a href="mailto:{{ $organizer->email }}" class="px-8 py-3 bg-[#38b2ac] hover:bg-teal-600 text-white rounded-full font-bold shadow-md shadow-teal-500/30 transition flex gap-2 items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Contact
                    </a>
                </div>
            </div>
        </div>

        <!-- Event List Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-bold text-gray-900 lowercase tracking-tight mb-8">events by {{ $organizer->name }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($events as $event)
                    <a href="{{ route('events.show', $event->id_event) }}" class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-300 border border-gray-100 flex flex-col h-full relative">
                        <div class="relative h-48 overflow-hidden bg-gray-200 flex-shrink-0">
                            @if($event->banner)
                                <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-tr from-gray-200 to-gray-300"></div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-[10px] font-extrabold uppercase tracking-widest text-[#555555] shadow-sm">
                                    {{ $event->category->name }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="font-extrabold text-lg text-[#333333] mb-2 line-clamp-2 leading-tight group-hover:text-black">{{ $event->title }}</h3>
                            
                            <div class="mt-auto space-y-2 mb-4">
                                <p class="text-gray-500 text-xs font-semibold flex items-center justify-start gap-1.5 lowercase">
                                    <svg class="w-4 h-4 text-[#AAAAAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $event->location }}</span>
                                </p>
                                <p class="text-gray-500 text-xs font-semibold flex items-center justify-start gap-1.5 lowercase">
                                    <svg class="w-4 h-4 text-[#AAAAAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $event->date->format('d M \'y') }} &bull; {{ $event->date->format('H:i') }}
                                </p>
                                {{-- City & Format badges --}}
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 uppercase tracking-wide">
                                        {{ $event->city }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $event->format === 'online' ? 'bg-green-50 text-green-700' : 'bg-orange-50 text-orange-700' }}">
                                        {{ $event->format === 'online' ? '🌐 Online' : '📍 Onsite' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-dashed border-gray-200 mt-auto flex justify-between items-end">
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Starting from</span>
                                <span class="text-base font-extrabold text-[#e02424]">
                                    @if($event->ticketTypes->min('price') == 0)
                                        Free
                                    @else
                                        Rp{{ number_format($event->ticketTypes->min('price'), 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 shadow-sm mt-4">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Events Yet</h3>
                        <p class="text-gray-500 font-medium">This creator has no published events at this time.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $events->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
