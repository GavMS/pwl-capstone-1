<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                        your dashboard.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        welcome back, {{ $user->name }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('user.explore') }}" class="inline-block px-6 py-3 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition shadow-sm text-sm">
                        browse events
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 flex flex-col gap-12 pb-12">

            <!-- ── Hero Banner (static first event) ── -->
            <section class="relative">
                @if($heroEvents->isEmpty())
                <div class="w-full h-[400px] bg-gray-200 rounded-[2rem] flex items-center justify-center">
                    <p class="text-gray-500 font-medium lowercase">belum ada event tersedia.</p>
                </div>
                @else
                @php $heroEvent = $heroEvents->first(); @endphp
                <div class="w-full h-[400px] rounded-[2rem] overflow-hidden relative shadow-sm">
                    @if($heroEvent->banner)
                        <img src="{{ asset('storage/' . $heroEvent->banner) }}" alt="{{ $heroEvent->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-600 to-gray-900"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>
                    <div class="absolute bottom-10 left-10 right-10 text-white md:w-2/3">
                        @if($heroEvent->category)
                        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-bold uppercase tracking-widest mb-3 border border-white/10">{{ $heroEvent->category->name }}</span>
                        @endif
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-3 leading-tight tracking-tight line-clamp-2">{{ $heroEvent->title }}</h2>
                        <p class="text-white/90 font-medium text-sm md:text-base flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $heroEvent->date->translatedFormat('l, d F Y') }}&nbsp;&bull;&nbsp;{{ $heroEvent->location }}
                        </p>
                    </div>
                </div>
                @endif
            </section>

            <!-- ── Kategori Event ── -->
            <section>
                <style>
                    /* Hide scrollbar for category carousel */
                    .hide-scrollbar::-webkit-scrollbar { display: none; }
                    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                </style>
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">kategori event</h3>
                </div>
                
                @if($categories->isEmpty())
                <p class="text-gray-500 text-sm lowercase">belum ada kategori.</p>
                @else
                <div class="relative group/carousel">
                    <div class="flex gap-4 overflow-x-auto snap-x hide-scrollbar pb-4" id="categoryContainer" style="scroll-behavior: smooth;">
                        @foreach($categories as $category)
                        <a href="{{ route('user.explore', ['category' => $category->id_category]) }}" class="snap-start flex-shrink-0 w-[140px] bg-white rounded-[1.5rem] p-4 flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md transition group">
                            <div class="w-14 h-14 bg-[#F4F4F4] group-hover:bg-[#E5E5E3] transition rounded-full flex items-center justify-center text-xl font-extrabold text-[#777777]">
                                {{ substr($category->name, 0, 1) }}
                            </div>
                            <span class="text-[10px] font-bold text-[#555555] text-center uppercase tracking-widest line-clamp-1 w-full" title="{{ $category->name }}">{{ $category->name }}</span>
                        </a>
                        @endforeach
                        
                        <!-- Tombol Lihat Semua Kategori -->
                        <a href="{{ route('user.explore') }}" class="snap-start flex-shrink-0 w-[140px] bg-white rounded-[1.5rem] p-4 flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md transition group border-2 border-dashed border-gray-200">
                            <div class="w-14 h-14 bg-[#F4F4F4] group-hover:bg-[#E5E5E3] transition rounded-full flex items-center justify-center text-[#777777]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </div>
                            <span class="text-[10px] font-bold text-[#555555] text-center uppercase tracking-widest">semua event</span>
                        </a>
                    </div>
                    
                    <!-- Navigation Button (Left) -->
                    <button onclick="document.getElementById('categoryContainer').scrollBy({left: -200, behavior: 'smooth'})" class="absolute left-0 top-[calc(50%-8px)] -translate-y-1/2 -translate-x-4 w-10 h-10 bg-white shadow-md rounded-full items-center justify-center text-gray-600 hover:text-black hidden group-hover/carousel:flex z-10 border border-gray-100 hover:scale-105 transition">
                        <svg class="w-5 h-5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    
                    <!-- Navigation Button (Right) -->
                    <button onclick="document.getElementById('categoryContainer').scrollBy({left: 200, behavior: 'smooth'})" class="absolute right-0 top-[calc(50%-8px)] -translate-y-1/2 translate-x-4 w-10 h-10 bg-white shadow-md rounded-full items-center justify-center text-gray-600 hover:text-black hidden group-hover/carousel:flex z-10 border border-gray-100 hover:scale-105 transition">
                        <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                @endif
            </section>

            <!-- ── Featured Events ── -->
            <section>
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">featured events</h3>
                    <a href="{{ route('user.explore') }}" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">lihat semua &rarr;</a>
                </div>

                @if($featuredEvents->isEmpty())
                <p class="text-gray-500 text-sm lowercase">belum ada event yang tersedia.</p>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($featuredEvents as $event)
                    <div class="bg-white rounded-[2rem] p-3 shadow-sm hover:shadow-md transition flex flex-col group cursor-pointer">
                        <!-- Cover Image -->
                        <div class="w-full h-40 bg-gray-200 rounded-[1.5rem] overflow-hidden relative mb-4 flex-shrink-0">
                            @if($event->banner)
                            <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            @endif
                            <!-- Floating Price Tag -->
                            <div class="absolute bottom-3 right-3 bg-[#555555] text-white px-3 py-1 rounded-xl text-xs font-bold shadow backdrop-blur-sm">
                                @php
                                    $lowestPrice = $event->ticketTypes->min('pivot.price');
                                @endphp
                                @if($lowestPrice !== null)
                                    {{ $lowestPrice == 0 ? 'Gratis' : 'Rp' . number_format($lowestPrice, 0, ',', '.') }}
                                @else
                                    TBA
                                @endif
                            </div>
                        </div>
                        <!-- Info -->
                        <div class="px-2 pb-2 flex-grow flex flex-col">
                            <h4 class="text-[15px] font-bold text-[#444444] leading-snug mb-1 line-clamp-2">{{ $event->title }}</h4>
                            <p class="text-[11px] font-bold text-[#777777] mb-4">{{ $event->date->translatedFormat('d M Y') }}</p>
                            <hr class="border-gray-100 mb-3">
                            <div class="flex items-center gap-2 mt-auto">
                                <div class="w-5 h-5 bg-[#E5E5E3] rounded-full flex-shrink-0 flex items-center justify-center text-[9px] font-bold text-gray-500">
                                    {{ substr($event->organizer?->name ?? '?', 0, 1) }}
                                </div>
                                <span class="text-[10px] font-semibold text-[#555555] truncate uppercase">{{ $event->organizer?->name ?? 'Unknown Organizer' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </section>

            <!-- ── Promo Banner (static) ── -->
            <section>
                <div class="relative rounded-[2rem] shadow-sm overflow-hidden bg-[#444444] p-10 md:p-14">
                    <div class="relative z-10">
                        <span class="inline-block px-3 py-1 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg text-[10px] font-extrabold uppercase tracking-widest mb-3 shadow-sm">Flowtix</span>
                        <h2 class="text-xl md:text-3xl font-extrabold text-white mb-2 leading-tight">Temukan event terbaik,<br class="hidden md:block"> di satu tempat.</h2>
                        <p class="text-[#CCCCCC] font-medium text-xs md:text-sm max-w-lg">Dari konser hingga workshop — Flowtix memudahkan kamu menemukan &amp; memesan tiket event favoritmu.</p>
                    </div>
                    <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-12 right-1/3 w-48 h-48 bg-orange-400/10 rounded-full blur-2xl pointer-events-none"></div>
                </div>
            </section>

            <!-- ── My Upcoming Tickets ── -->

            <section>
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">my upcoming tickets</h3>
                    <a href="#" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">semua tiket &rarr;</a>
                </div>
                
                {{-- TODO: Replace with real user tickets from DB when ticket purchase flow is implemented --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 opacity-90">
                    <div class="bg-transparent border-2 border-white p-4 rounded-[2rem] flex items-center gap-4 hover:bg-white hover:border-transparent transition cursor-pointer">
                        <div class="w-20 h-20 bg-gray-200 rounded-[1.2rem] overflow-hidden flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1540039155732-d674ce313cb6?q=80&w=400&auto=format&fit=crop" alt="Event" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow">
                            <span class="inline-block px-2 py-0.5 bg-[#F4F4F4] text-[#555555] text-[9px] font-bold rounded-md mb-1.5 uppercase tracking-widest">Oct 24, 2026</span>
                            <h4 class="text-base font-bold text-[#444444] leading-tight mb-1">Neon Lights Festival</h4>
                            <p class="text-[11px] font-medium text-[#777777]">GBK Stadium, Jakarta</p>
                        </div>
                    </div>
                    <div class="bg-transparent border-2 border-white p-4 rounded-[2rem] flex items-center gap-4 hover:bg-white hover:border-transparent transition cursor-pointer">
                        <div class="w-20 h-20 bg-gray-200 rounded-[1.2rem] overflow-hidden flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=400&auto=format&fit=crop" alt="Event" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow">
                            <span class="inline-block px-2 py-0.5 bg-[#F4F4F4] text-[#555555] text-[9px] font-bold rounded-md mb-1.5 uppercase tracking-widest">Nov 12, 2026</span>
                            <h4 class="text-base font-bold text-[#444444] leading-tight mb-1">Startup Summit '26</h4>
                            <p class="text-[11px] font-medium text-[#777777]">ICE BSD, Tangerang</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── Kreator Favorit ── -->
            <section class="pb-4">
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">kreator favorit</h3>
                </div>
                <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-4 md:gap-8">
                    @foreach($organizers->take(7) as $organizer)
                    <a href="{{ route('user.organizer.profile', $organizer->id) }}" class="flex flex-col items-center gap-2 group cursor-pointer hover:bg-transparent">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-white rounded-full p-1 shadow-sm group-hover:shadow-md transition">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($organizer->name) }}&color=555555&background=E5E5E3" class="w-full h-full rounded-full object-cover" alt="{{ $organizer->name }}">
                        </div>
                        <span class="text-[10px] font-bold text-[#555555] text-center w-full truncate px-1" title="{{ $organizer->name }}">{{ $organizer->name }}</span>
                    </a>
                    @endforeach

                    <!-- Lihat Semua Button inside grid -->
                    <a href="{{ route('user.explore.creators') }}" class="flex flex-col items-center gap-2 group cursor-pointer hover:bg-transparent">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-white rounded-full flex items-center justify-center p-1 shadow-sm group-hover:shadow-md transition text-[#555555]">
                            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                        <span class="text-[10px] font-bold text-[#555555] text-center w-full truncate px-1">Lihat Semua</span>
                    </a>
                </div>
            </section>
        </main>

    </div>
</x-app-layout>
