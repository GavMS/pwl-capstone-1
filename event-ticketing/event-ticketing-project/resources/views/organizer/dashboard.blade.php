<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                        organizer hub.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        manage your events easily, {{ $user->name }}
                    </p>
                </div>
                <div>
                    <a href="#" class="inline-block px-6 py-3 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition shadow-sm text-sm">
                        + create event
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 flex flex-col gap-14">
            
            <!-- Quick Stats -->
            <section>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Stat 1 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col gap-2 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#E5E5E3] rounded-full opacity-50 group-hover:scale-150 transition duration-700"></div>
                        <div class="flex items-center justify-between relative z-10">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Total Events</span>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2 relative z-10">{{ number_format($stats['total_events']) }}</h4>
                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mt-1 relative z-10">managed events.</p>
                    </div>

                    <!-- Stat 2 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col gap-2 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#E5E5E3] rounded-full opacity-50 group-hover:scale-150 transition duration-700"></div>
                        <div class="flex items-center justify-between relative z-10">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Active Events</span>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2 relative z-10">{{ number_format($stats['active_events']) }}</h4>
                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mt-1 relative z-10">published live.</p>
                    </div>

                    <!-- Stat 3 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col gap-2 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#E5E5E3] rounded-full opacity-50 group-hover:scale-150 transition duration-700"></div>
                        <div class="flex items-center justify-between relative z-10">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Drafts</span>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2 relative z-10">{{ number_format($stats['draft_events']) }}</h4>
                        <p class="text-[10px] font-bold text-yellow-600 uppercase tracking-widest mt-1 relative z-10">waiting to publish.</p>
                    </div>
                </div>
            </section>

            <!-- My Events List -->
            <section>
                <div class="flex justify-between items-end mb-6">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">my active events</h3>
                    <div class="flex gap-4">
                        <a href="#" class="text-sm font-bold text-black border-b-2 border-black lowercase transition pb-1">active</a>
                        <a href="#" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition pb-1">drafts</a>
                        <a href="#" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition pb-1">past</a>
                    </div>
                </div>
                
                <div class="flex flex-col gap-5">
                    @forelse ($events as $event)
                        <div class="bg-white p-4 sm:p-6 rounded-[2rem] shadow-sm flex flex-col sm:flex-row items-center gap-6 hover:shadow-md transition">
                            <div class="w-full sm:w-32 h-32 bg-gray-200 rounded-2xl overflow-hidden flex-shrink-0">
                                @if($event->banner)
                                    <img src="{{ asset('storage/' . $event->banner) }}" alt="Event" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-[#F4F4F4] text-[#BBBBBB]">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow w-full">
                                <div class="flex items-center gap-3 mb-2">
                                    @php
                                        $statusColor = match($event->status) {
                                            'published' => 'bg-green-100 text-green-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            'completed' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-gray-100 text-gray-600'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 {{ $statusColor }} text-[10px] font-bold rounded-lg uppercase tracking-widest">{{ $event->status }}</span>
                                    <span class="text-xs font-bold text-[#777777]">{{ $event->date->format('M d, Y') }}</span>
                                </div>
                                <h4 class="text-xl font-bold text-[#444444] leading-tight mb-2">{{ $event->title }}</h4>
                                <p class="text-xs text-[#777777] lowercase line-clamp-1">{{ $event->location }}</p>
                            </div>
                            <div class="flex sm:flex-col gap-2 w-full sm:w-auto mt-4 sm:mt-0">
                                <a href="{{ route('organizer.events.edit', $event->id_event) }}" class="flex-1 sm:flex-none px-6 py-3 bg-[#555555] text-white rounded-xl font-bold text-sm hover:bg-black transition lowercase flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    edit
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-12 rounded-[2rem] shadow-sm text-center">
                            <p class="text-[#777777] font-bold lowercase">you haven't created any events yet.</p>
                        </div>
                    @endforelse
                </div>

                </div>
            </section>
        </main>

    </div>
</x-app-layout>
