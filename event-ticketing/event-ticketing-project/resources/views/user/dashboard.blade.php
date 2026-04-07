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

            <!-- ── Live Search Bar ── -->
            <section class="relative z-40">
                <div class="relative bg-white rounded-full shadow-md border border-gray-100 flex items-center px-6 py-4 transition-all focus-within:ring-2 focus-within:ring-[#38b2ac]">
                    <svg class="w-6 h-6 text-gray-400 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="liveSearchInput" autocomplete="off" placeholder="Search for events..." class="w-full bg-transparent border-none text-lg lg:text-xl font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0 lowercase">
                    <button type="button" id="clearSearchBtn" class="hidden text-gray-400 hover:text-gray-600 ml-4 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <!-- Loading Spinner (dibuat hidden default, muncul sbntr agar terkesan live walau lokal) -->
                    <svg id="searchSpinner" class="hidden animate-spin ml-4 w-5 h-5 text-[#38b2ac]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <!-- Hasil Recommendation Dropdown -->
                <div id="searchResultsDropdown" class="hidden absolute top-full left-0 right-0 mt-3 bg-white border border-gray-100 rounded-3xl shadow-xl overflow-hidden max-h-[400px] overflow-y-auto w-full z-50">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Recommended Events</span>
                    </div>
                    <ul id="searchResultsList" class="divide-y divide-gray-100">
                        <!-- Hasil Javascript di-inject ke sini -->
                    </ul>
                </div>
            </section>

            <!-- ── Hero Banner (static first event) ── -->
            <section class="relative">
                @if($heroEvents->isEmpty())
                <div class="w-full h-[400px] bg-gray-200 rounded-[2rem] flex items-center justify-center">
                    <p class="text-gray-500 font-medium lowercase">no events available yet.</p>
                </div>
                @else
                @php $heroEvent = $heroEvents->first(); @endphp
                <a href="{{ route('events.show', $heroEvent->id_event) }}" class="block w-full h-[400px] rounded-[2rem] overflow-hidden relative shadow-sm group">
                    @if($heroEvent->banner)
                        <img src="{{ asset('storage/' . $heroEvent->banner) }}" alt="{{ $heroEvent->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-600 to-gray-900 group-hover:scale-105 transition duration-700"></div>
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
                </a>
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
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">event categories</h3>
                </div>
                
                @if($categories->isEmpty())
                <p class="text-gray-500 text-sm lowercase">no categories yet.</p>
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
                            <span class="text-[10px] font-bold text-[#555555] text-center uppercase tracking-widest">all events</span>
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
                    <a href="{{ route('user.explore') }}" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">see all &rarr;</a>
                </div>

                @if($featuredEvents->isEmpty())
                <p class="text-gray-500 text-sm lowercase">no events available yet.</p>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($featuredEvents as $event)
                    <a href="{{ route('events.show', $event->id_event) }}" class="bg-white rounded-[2rem] p-3 shadow-sm hover:shadow-md transition flex flex-col group cursor-pointer block">
                        <!-- Cover Image -->
                        <div class="w-full h-40 bg-gray-200 rounded-[1.5rem] overflow-hidden relative mb-4 flex-shrink-0">
                            @if($event->banner)
                            <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center group-hover:scale-105 transition duration-500">
                                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            @endif
                            <!-- Floating Price Tag -->
                            <div class="absolute bottom-3 right-3 bg-[#555555] text-white px-3 py-1 rounded-xl text-xs font-bold shadow backdrop-blur-sm">
                                @php
                                    $lowestPrice = $event->ticketTypes->min('pivot.price');
                                @endphp
                                @if($lowestPrice !== null)
                                    {{ $lowestPrice == 0 ? 'Free' : 'Rp' . number_format($lowestPrice, 0, ',', '.') }}
                                @else
                                    TBA
                                @endif
                            </div>
                        </div>
                        <!-- Info -->
                        <div class="px-2 pb-2 flex-grow flex flex-col">
                            <h4 class="text-[15px] font-bold text-[#444444] group-hover:text-[#38b2ac] transition leading-snug mb-1 line-clamp-2">{{ $event->title }}</h4>
                            <p class="text-[11px] font-bold text-[#777777] mb-4">{{ $event->date->translatedFormat('d M Y') }}</p>
                            <hr class="border-gray-100 mb-3">
                            <div class="flex items-center gap-2 mt-auto">
                                <div class="w-5 h-5 bg-[#E5E5E3] rounded-full flex-shrink-0 flex items-center justify-center text-[9px] font-bold text-gray-500">
                                    {{ substr($event->organizer?->name ?? '?', 0, 1) }}
                                </div>
                                <span class="text-[10px] font-semibold text-[#555555] truncate uppercase">{{ $event->organizer?->name ?? 'Unknown Organizer' }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </section>

            <!-- ── Promo Banner (static) ── -->
            <section>
                <div class="relative rounded-[2rem] shadow-sm overflow-hidden bg-[#444444] p-10 md:p-14">
                    <div class="relative z-10">
                        <span class="inline-block px-3 py-1 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg text-[10px] font-extrabold uppercase tracking-widest mb-3 shadow-sm">Flowtix</span>
                        <h2 class="text-xl md:text-3xl font-extrabold text-white mb-2 leading-tight">Discover the best events,<br class="hidden md:block"> all in one place.</h2>
                        <p class="text-[#CCCCCC] font-medium text-xs md:text-sm max-w-lg">From concerts to workshops — Flowtix makes it easy to find &amp; book tickets for your favorite events.</p>
                    </div>
                    <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-12 right-1/3 w-48 h-48 bg-orange-400/10 rounded-full blur-2xl pointer-events-none"></div>
                </div>
            </section>

            <!-- ── My Upcoming Tickets ── -->

            <section>
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">my upcoming tickets</h3>
                    <a href="#" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">all tickets &rarr;</a>
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
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">favorite creators</h3>
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
                        <span class="text-[10px] font-bold text-[#555555] text-center w-full truncate px-1">View All</span>
                    </a>
                </div>
            </section>
        </main>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data semua event dikonversi dari collection ke Javascript Array
            const allEvents = {!! json_encode($allEventsLite) !!};
            
            const searchInput = document.getElementById('liveSearchInput');
            const searchDropdown = document.getElementById('searchResultsDropdown');
            const searchList = document.getElementById('searchResultsList');
            const clearBtn = document.getElementById('clearSearchBtn');
            const spinner = document.getElementById('searchSpinner');

            // Debounce function manual (mencegah search jalan setiap keystroke trmpan buffering)
            let timeoutId;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeoutId); // Selalu bersihkan timeout lama dulu
                const query = this.value.trim().toLowerCase();
                
                // Show/hide clear button & hide dropdown if empty
                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                    searchDropdown.classList.add('hidden');
                    spinner.classList.add('hidden');
                    return;
                }

                // Show spinner briefly
                spinner.classList.remove('hidden');
                
                timeoutId = setTimeout(() => {
                    performSearch(query);
                    spinner.classList.add('hidden');
                }, 300); // 300ms delay agar optimal
            });

            clearBtn.addEventListener('click', function() {
                clearTimeout(timeoutId); // Batalkan pencarian jika tombol clear diklik
                searchInput.value = '';
                searchInput.focus();
                clearBtn.classList.add('hidden');
                searchDropdown.classList.add('hidden');
                spinner.classList.add('hidden');
            });

            // Sembunyikan dropdown kalau klik di luar area search
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.classList.add('hidden');
                }
            });

            // Munculkan lagi kalau klik input tapi teksnya ada isinya
            searchInput.addEventListener('click', function() {
                if (this.value.trim().length > 0 && searchList.children.length > 0) {
                    searchDropdown.classList.remove('hidden');
                }
            });

            function performSearch(query) {
                const results = allEvents.filter(ev => {
                    return ev.title.toLowerCase().includes(query);
                });

                renderResults(results, query);
            }

            function renderResults(results, query) {
                searchList.innerHTML = '';
                
                if (results.length === 0) {
                    searchList.innerHTML = `
                        <li class="px-6 py-8 text-center">
                            <span class="text-sm font-medium text-gray-500 lowercase">No events found for "${query}".</span>
                        </li>`;
                } else {
                    // Tampilkan maksimal 5 hasil agar tidak telalu penuh
                    const limit = results.slice(0, 5);
                    let htmlList = '';
                    
                    limit.forEach(ev => {
                        const imgTag = ev.banner 
                            ? `<img src="${ev.banner}" class="w-16 h-16 rounded-xl object-cover border border-gray-100 flex-shrink-0" alt="${ev.title}">`
                            : `<div class="w-16 h-16 bg-gray-200 rounded-xl flex-shrink-0"></div>`;
                            
                        htmlList += `
                            <li>
                                <a href="${ev.url}" class="px-6 py-4 hover:bg-gray-50 flex items-center gap-5 transition group">
                                    ${imgTag}
                                    <div class="flex flex-col overflow-hidden">
                                        <h4 class="font-extrabold text-gray-800 text-sm mb-1 group-hover:text-[#38b2ac] transition truncate">${ev.title}</h4>
                                        <p class="text-[11px] font-bold text-gray-500 lowercase flex items-center gap-1">
                                            <span>${ev.date_formatted}</span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                            <span>${ev.city}</span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                            <span>${ev.organizer}</span>
                                        </p>
                                    </div>
                                </a>
                            </li>
                        `;
                    });

                    // Add "Lihat semua hasil pencarian x" jika ada sisa sisa hasil selain 5 tsb
                    if(results.length > 5) {
                        htmlList += `
                            <li>
                                <a href="/explore?search=${encodeURIComponent(query)}" class="px-6 py-4 bg-gray-50/50 hover:bg-gray-100 flex items-center justify-center transition border-t border-gray-100">
                                    <span class="text-xs font-bold text-[#38b2ac] uppercase tracking-widest">View ${results.length} more results</span>
                                </a>
                            </li>
                        `;
                    }

                    searchList.innerHTML = htmlList;
                }
                
                searchDropdown.classList.remove('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>
