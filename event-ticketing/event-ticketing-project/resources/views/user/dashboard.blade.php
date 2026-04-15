<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">

        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div
                class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                        your dashboard.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        welcome back, {{ $user->name }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('user.explore') }}"
                        class="inline-block px-6 py-3 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition shadow-sm text-sm">
                        browse events
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 flex flex-col gap-12 pb-12">

            <!-- ── Waiting for Payment Widget ── -->
            @if(isset($pendingTransactions) && !$pendingTransactions->isEmpty())
                <section class="animate-in fade-in slide-in-from-top-4 duration-700">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight flex items-center gap-2">
                            waiting for payment
                            <span class="flex h-2 w-2 relative">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                            </span>
                        </h3>
                    </div>

                    <div class="space-y-4">
                        @foreach($pendingTransactions as $trx)
                            @php
                                $payload = is_string($trx->ticket_payload) ? json_decode($trx->ticket_payload, true) : $trx->ticket_payload;
                                $firstTicket = $payload[0] ?? null;

                                $eventTitle = $firstTicket['event_title'] ?? 'Event';
                                $ticketName = $firstTicket['ticket_name'] ?? 'Ticket';

                                $displayTitle = "{$eventTitle} ({$ticketName})";
                            @endphp
                            <div
                                class="bg-white border-l-4 border-orange-500 rounded-[2rem] p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition hover:shadow-md">
                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-500 shrink-0">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-gray-800 text-[15px] leading-tight mb-1">
                                            {{ $displayTitle }}</h4>
                                        <div class="flex items-center gap-4 text-sm font-bold">
                                            <span
                                                class="text-orange-600 bg-orange-50 px-3 py-0.5 rounded-full flex items-center gap-1.5 min-w-[140px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                                <span class="pending-countdown"
                                                    data-deadline="{{ $trx->deadline_payment->toIso8601String() }}">Calculating...</span>
                                            </span>
                                            <span class="text-gray-400 text-xs mt-0.5">amount: Rp
                                                {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('checkout.payment', $trx->order_id) }}"
                                        class="px-8 py-3 bg-[#555555] text-white rounded-full font-bold text-xs hover:bg-black transition shadow-sm whitespace-nowrap lowercase">
                                        resume payment &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- ── Live Search Bar ── -->
            <section class="relative z-20">
                <div
                    class="relative bg-white rounded-full shadow-md border border-gray-100 flex items-center px-6 py-4 transition-all focus-within:ring-2 focus-within:ring-[#38b2ac]">
                    <svg class="w-6 h-6 text-gray-400 mr-4 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="liveSearchInput" autocomplete="off" placeholder="Search for events..."
                        class="w-full bg-transparent border-none text-lg lg:text-xl font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0 lowercase">
                    <button type="button" id="clearSearchBtn" class="hidden text-gray-400 hover:text-gray-600 ml-4 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <!-- Loading Spinner (dibuat hidden default, muncul sbntr agar terkesan live walau lokal) -->
                    <svg id="searchSpinner" class="hidden animate-spin ml-4 w-5 h-5 text-[#38b2ac]"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>

                <!-- Hasil Recommendation Dropdown -->
                <div id="searchResultsDropdown"
                    class="hidden absolute top-full left-0 right-0 mt-3 bg-white border border-gray-100 rounded-3xl shadow-xl overflow-hidden max-h-[400px] overflow-y-auto w-full z-30">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Recommended
                            Events</span>
                    </div>
                    <ul id="searchResultsList" class="divide-y divide-gray-100">
                        <!-- Hasil Javascript di-inject ke sini -->
                    </ul>
                </div>
            </section>

            <!-- ── Hero Banner Carousel ── -->
            <section class="relative">
                @if($heroEvents->isEmpty())
                    <div class="w-full h-[400px] bg-gray-200 rounded-[2rem] flex items-center justify-center">
                        <p class="text-gray-500 font-medium lowercase">no events available yet.</p>
                    </div>
                @else
                    <div x-data="{
                        current: 0,
                        total: {{ $heroEvents->count() }},
                        autoSlide: null,
                        startAuto() {
                            this.autoSlide = setInterval(() => { this.next() }, 5000);
                        },
                        stopAuto() {
                            clearInterval(this.autoSlide);
                        },
                        next() {
                            this.current = (this.current + 1) % this.total;
                        },
                        prev() {
                            this.current = (this.current - 1 + this.total) % this.total;
                        }
                    }" x-init="startAuto()" @mouseenter="stopAuto()" @mouseleave="startAuto()"
                        class="relative w-full h-[400px] rounded-[2rem] overflow-hidden shadow-sm group">

                        {{-- Slides --}}
                        @foreach($heroEvents as $index => $heroEvent)
                            <a href="{{ route('events.show', $heroEvent->id_event) }}"
                                x-show="current === {{ $index }}"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 scale-105"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="absolute inset-0 block">
                                @if($heroEvent->banner)
                                    <img src="{{ $heroEvent->banner_url }}" alt="{{ $heroEvent->title }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-600 to-gray-900"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>
                                <div class="absolute bottom-10 left-10 right-10 text-white md:w-2/3">
                                    @if($heroEvent->category)
                                        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-bold uppercase tracking-widest mb-3 border border-white/10">{{ $heroEvent->category->name }}</span>
                                    @endif
                                    <h2 class="text-3xl md:text-4xl font-extrabold mb-3 leading-tight tracking-tight line-clamp-2">
                                        {{ $heroEvent->title }}</h2>
                                    <p class="text-white/90 font-medium text-sm md:text-base flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $heroEvent->date->translatedFormat('l, d F Y') }}&nbsp;&bull;&nbsp;{{ $heroEvent->location }}
                                    </p>
                                </div>
                            </a>
                        @endforeach

                        {{-- Left / Right Navigation --}}
                        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full flex items-center justify-center text-white transition opacity-0 group-hover:opacity-100 z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full flex items-center justify-center text-white transition opacity-0 group-hover:opacity-100 z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        {{-- Dots Indicator --}}
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
                            @foreach($heroEvents as $index => $heroEvent)
                                <button @click="current = {{ $index }}"
                                    :class="current === {{ $index }} ? 'w-8 bg-white' : 'w-2.5 bg-white/50 hover:bg-white/70'"
                                    class="h-2.5 rounded-full transition-all duration-300"></button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            <!-- ── Kategori Event ── -->
            <section>
                <style>
                    /* Hide scrollbar for category carousel */
                    .hide-scrollbar::-webkit-scrollbar {
                        display: none;
                    }

                    .hide-scrollbar {
                        -ms-overflow-style: none;
                        scrollbar-width: none;
                    }
                </style>
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">event categories</h3>
                </div>

                @if($categories->isEmpty())
                    <p class="text-gray-500 text-sm lowercase">no categories yet.</p>
                @else
                    <div class="relative group/carousel">
                        <div class="flex gap-4 overflow-x-auto snap-x hide-scrollbar pb-4" id="categoryContainer"
                            style="scroll-behavior: smooth;">
                            @php
                                $categoryIcons = [
                                    'tech workshop' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z"/></svg>',
                                    'developer conference' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>',
                                    'startup networking' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/></svg>',
                                    'music concert' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/></svg>',
                                    'comedy show' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>',
                                    'art exhibition' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>',
                                    'virtual run' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1.001A3.75 3.75 0 0012 18z"/></svg>',
                                    'food festival' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.87c1.355 0 2.697.055 4.024.165C17.155 8.51 18 9.473 18 10.608v2.513m-3-4.87v-1.5m-6 1.5v-1.5m12 9.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0L3 16.5m15-3.38a48.474 48.474 0 00-6-.37c-2.032 0-4.034.126-6 .37m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.17c0 .62-.504 1.124-1.125 1.124H4.125A1.125 1.125 0 013 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 016 13.12M12.265 3.11a.375.375 0 11-.53 0L12 2.845l.265.265z"/></svg>',
                                    'e-sports tournament' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.491 48.491 0 01-4.163-.3c-1.07-.16-1.837-1.09-1.837-2.175v-.745c0-.505.408-.914.914-.914h.522a.75.75 0 00.72-.545c.159-.559.597-1.005 1.165-1.123 1.048-.218 2.12-.34 3.213-.36A48.398 48.398 0 0112.25 1.5V0m0 24v-1.5a48.398 48.398 0 011.623-.084c1.093.02 2.165.142 3.213.36.568.118 1.006.564 1.165 1.123a.75.75 0 00.72.545h.522c.506 0 .914-.409.914-.914v-.745c0-1.085-.767-2.015-1.837-2.175a48.477 48.477 0 00-4.163-.3.64.64 0 01-.657-.643v0c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959"/></svg>',
                                    'theater performance' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0016.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m4.015-9.492a48.354 48.354 0 00-16.57 0"/></svg>',
                                ];
                            @endphp
                            @foreach($categories as $category)
                                @php
                                    $key = strtolower($category->name ?? '');
                                    $icon = $categoryIcons[$key] ?? '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>';
                                    $categoryColors = [
                                        'tech workshop' => ['bg' => 'bg-blue-50', 'hover' => 'group-hover:bg-blue-100', 'text' => 'text-blue-500', 'hoverText' => 'group-hover:text-blue-600'],
                                        'developer conference' => ['bg' => 'bg-indigo-50', 'hover' => 'group-hover:bg-indigo-100', 'text' => 'text-indigo-500', 'hoverText' => 'group-hover:text-indigo-600'],
                                        'startup networking' => ['bg' => 'bg-violet-50', 'hover' => 'group-hover:bg-violet-100', 'text' => 'text-violet-500', 'hoverText' => 'group-hover:text-violet-600'],
                                        'music concert' => ['bg' => 'bg-rose-50', 'hover' => 'group-hover:bg-rose-100', 'text' => 'text-rose-500', 'hoverText' => 'group-hover:text-rose-600'],
                                        'comedy show' => ['bg' => 'bg-amber-50', 'hover' => 'group-hover:bg-amber-100', 'text' => 'text-amber-500', 'hoverText' => 'group-hover:text-amber-600'],
                                        'art exhibition' => ['bg' => 'bg-fuchsia-50', 'hover' => 'group-hover:bg-fuchsia-100', 'text' => 'text-fuchsia-500', 'hoverText' => 'group-hover:text-fuchsia-600'],
                                        'virtual run' => ['bg' => 'bg-orange-50', 'hover' => 'group-hover:bg-orange-100', 'text' => 'text-orange-500', 'hoverText' => 'group-hover:text-orange-600'],
                                        'food festival' => ['bg' => 'bg-red-50', 'hover' => 'group-hover:bg-red-100', 'text' => 'text-red-400', 'hoverText' => 'group-hover:text-red-500'],
                                        'e-sports tournament' => ['bg' => 'bg-purple-50', 'hover' => 'group-hover:bg-purple-100', 'text' => 'text-purple-500', 'hoverText' => 'group-hover:text-purple-600'],
                                        'theater performance' => ['bg' => 'bg-emerald-50', 'hover' => 'group-hover:bg-emerald-100', 'text' => 'text-emerald-500', 'hoverText' => 'group-hover:text-emerald-600'],
                                    ];
                                    $color = $categoryColors[$key] ?? ['bg' => 'bg-gray-50', 'hover' => 'group-hover:bg-gray-100', 'text' => 'text-gray-500', 'hoverText' => 'group-hover:text-gray-600'];
                                @endphp
                                <a href="{{ route('user.explore', ['category' => $category->id_category]) }}"
                                    class="snap-start flex-shrink-0 w-[130px] flex flex-col items-center justify-center gap-3 group">
                                    <div
                                        class="w-16 h-16 {{ $color['bg'] }} {{ $color['hover'] }} rounded-full flex items-center justify-center {{ $color['text'] }} {{ $color['hoverText'] }} transition-all duration-300 group-hover:scale-105 shadow-sm">
                                        {!! $icon !!}
                                    </div>
                                    <span
                                        class="text-[10px] font-bold text-[#555555] {{ $color['hoverText'] }} text-center uppercase tracking-widest line-clamp-2 w-full transition-colors"
                                        title="{{ $category->name }}">{{ $category->name }}</span>
                                </a>
                            @endforeach

                            <!-- Tombol Lihat Semua Kategori -->
                            <a href="{{ route('user.explore') }}"
                                class="snap-start flex-shrink-0 w-[140px] bg-white rounded-[1.5rem] p-4 flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md transition group border-2 border-dashed border-gray-200">
                                <div
                                    class="w-14 h-14 bg-[#F4F4F4] group-hover:bg-[#E5E5E3] transition rounded-full flex items-center justify-center text-[#777777]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold text-[#555555] text-center uppercase tracking-widest">all
                                    events</span>
                            </a>
                        </div>

                        <!-- Navigation Button (Left) -->
                        <button
                            onclick="document.getElementById('categoryContainer').scrollBy({left: -200, behavior: 'smooth'})"
                            class="absolute left-0 top-[calc(50%-8px)] -translate-y-1/2 -translate-x-4 w-10 h-10 bg-white shadow-md rounded-full items-center justify-center text-gray-600 hover:text-black hidden group-hover/carousel:flex z-10 border border-gray-100 hover:scale-105 transition">
                            <svg class="w-5 h-5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </button>

                        <!-- Navigation Button (Right) -->
                        <button
                            onclick="document.getElementById('categoryContainer').scrollBy({left: 200, behavior: 'smooth'})"
                            class="absolute right-0 top-[calc(50%-8px)] -translate-y-1/2 translate-x-4 w-10 h-10 bg-white shadow-md rounded-full items-center justify-center text-gray-600 hover:text-black hidden group-hover/carousel:flex z-10 border border-gray-100 hover:scale-105 transition">
                            <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                @endif
            </section>

            <!-- ── Featured Events ── -->
            <section>
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">featured events</h3>
                    <a href="{{ route('user.explore') }}"
                        class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">see all
                        &rarr;</a>
                </div>

                @if($featuredEvents->isEmpty())
                    <p class="text-gray-500 text-sm lowercase">no events available yet.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($featuredEvents as $event)
                            <a href="{{ route('events.show', $event->id_event) }}"
                                class="bg-white rounded-[2rem] p-3 shadow-sm hover:shadow-md transition flex flex-col group cursor-pointer block">
                                <!-- Cover Image -->
                                <div
                                    class="w-full h-40 bg-gray-200 rounded-[1.5rem] overflow-hidden relative mb-4 flex-shrink-0">
                                    @if($event->banner)
                                        <img src="{{ $event->banner_url }}" alt="{{ $event->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div
                                            class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center group-hover:scale-105 transition duration-500">
                                            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                    <!-- Floating Price Tag -->
                                    <div
                                        class="absolute bottom-3 right-3 bg-[#555555] text-white px-3 py-1 rounded-xl text-xs font-bold shadow backdrop-blur-sm">
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
                                    <h4
                                        class="text-[15px] font-bold text-[#444444] group-hover:text-[#38b2ac] transition leading-snug mb-1 line-clamp-2">
                                        {{ $event->title }}</h4>
                                    <p class="text-[11px] font-bold text-[#777777] mb-4">
                                        {{ $event->date->translatedFormat('d M Y') }}</p>
                                    <hr class="border-gray-100 mb-3">
                                    <div class="flex items-center gap-2 mt-auto">
                                        <div
                                            class="w-5 h-5 bg-[#E5E5E3] rounded-full flex-shrink-0 flex items-center justify-center text-[9px] font-bold text-gray-500">
                                            {{ substr($event->organizer?->name ?? '?', 0, 1) }}
                                        </div>
                                        <span
                                            class="text-[10px] font-semibold text-[#555555] truncate uppercase">{{ $event->organizer?->name ?? 'Unknown Organizer' }}</span>
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
                        <span
                            class="inline-block px-3 py-1 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg text-[10px] font-extrabold uppercase tracking-widest mb-3 shadow-sm">Flowtix</span>
                        <h2 class="text-xl md:text-3xl font-extrabold text-white mb-2 leading-tight">Discover the best
                            events,<br class="hidden md:block"> all in one place.</h2>
                        <p class="text-[#CCCCCC] font-medium text-xs md:text-sm max-w-lg">From concerts to workshops —
                            Flowtix makes it easy to find &amp; book tickets for your favorite events.</p>
                    </div>
                    <div
                        class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute -bottom-12 right-1/3 w-48 h-48 bg-orange-400/10 rounded-full blur-2xl pointer-events-none">
                    </div>
                </div>
            </section>

            <!-- ── My Upcoming Tickets ── -->
            <section>
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">my upcoming tickets</h3>
                    <a href="{{ route('user.my-tickets') }}"
                        class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">all tickets
                        &rarr;</a>
                </div>

                @if($upcomingTickets->isEmpty())
                    <div
                        class="bg-white/60 border-2 border-dashed border-gray-200 rounded-[2rem] p-10 flex flex-col items-center justify-center text-center gap-3">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                            </path>
                        </svg>
                        <p class="text-sm font-bold text-gray-400 lowercase">belum ada tiket yang dibeli.</p>
                        <a href="{{ route('user.explore') }}"
                            class="mt-1 px-5 py-2 bg-[#555555] text-white rounded-full text-xs font-bold hover:bg-black transition lowercase">cari
                            event sekarang</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($upcomingTickets as $ticket)
                            @php
                                $event = $ticket->eventTicketType->event;
                                $ticketTypeName = $ticket->eventTicketType->ticketType->name ?? 'Tiket';
                            @endphp
                            <a href="{{ route('user.ticket.detail', $ticket->unique_code) }}"
                                class="bg-white border border-gray-100 hover:border-gray-200 p-4 rounded-[2rem] flex items-center gap-4 hover:shadow-md transition group">
                                <!-- Event Banner -->
                                <div class="w-20 h-20 bg-gray-200 rounded-[1.2rem] overflow-hidden flex-shrink-0">
                                    @if($event->banner)
                                        <img src="{{ $event->banner_url }}" alt="{{ $event->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400"></div>
                                    @endif
                                </div>
                                <!-- Info -->
                                <div class="flex-grow overflow-hidden">
                                    <span
                                        class="inline-block px-2 py-0.5 bg-[#F4F4F4] text-[#555555] text-[9px] font-bold rounded-md mb-1.5 uppercase tracking-widest">
                                        {{ $event->date->format('M d, Y') }}
                                    </span>
                                    <h4
                                        class="text-sm font-bold text-[#444444] leading-tight mb-0.5 truncate group-hover:text-[#38b2ac] transition">
                                        {{ $event->title }}</h4>
                                    <p class="text-[11px] font-medium text-[#777777] truncate">{{ $event->location }}</p>
                                </div>
                                <!-- Ticket type badge & arrow -->
                                <div class="flex-shrink-0 flex flex-col items-end gap-2 pl-2">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-teal-50 text-teal-700 uppercase tracking-wide whitespace-nowrap">{{ $ticketTypeName }}</span>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-[#38b2ac] transition" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- ── Kreator Favorit ── -->
            <section class="pb-4">
                <div class="flex justify-between items-end mb-5">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">favorite creators</h3>
                </div>
                @php
                    $creatorGradients = [
                        'from-pink-500 to-rose-400',
                        'from-violet-500 to-purple-400',
                        'from-blue-500 to-cyan-400',
                        'from-emerald-500 to-teal-400',
                        'from-amber-500 to-orange-400',
                        'from-fuchsia-500 to-pink-400',
                        'from-indigo-500 to-blue-400',
                        'from-red-500 to-rose-400',
                    ];
                @endphp
                <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-4 md:gap-8">
                    @foreach($organizers->take(7) as $index => $organizer)
                        <a href="{{ route('user.organizer.profile', $organizer->id) }}"
                            class="flex flex-col items-center gap-2 group cursor-pointer">
                            <div
                                class="w-16 h-16 md:w-20 md:h-20 rounded-full p-[3px] bg-gradient-to-br {{ $creatorGradients[$index % count($creatorGradients)] }} shadow-sm group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                                <div class="w-full h-full rounded-full bg-white flex items-center justify-center">
                                    <span class="text-lg md:text-xl font-extrabold bg-gradient-to-br {{ $creatorGradients[$index % count($creatorGradients)] }} bg-clip-text text-transparent">
                                        {{ strtoupper(substr($organizer->name, 0, 2)) }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-[#555555] text-center w-full truncate px-1"
                                title="{{ $organizer->name }}">{{ $organizer->name }}</span>
                        </a>
                    @endforeach

                    <!-- Lihat Semua Button inside grid -->
                    <a href="{{ route('user.explore.creators') }}"
                        class="flex flex-col items-center gap-2 group cursor-pointer hover:bg-transparent">
                        <div
                            class="w-16 h-16 md:w-20 md:h-20 bg-white rounded-full flex items-center justify-center p-1 shadow-sm group-hover:shadow-md transition text-[#555555]">
                            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-[#555555] text-center w-full truncate px-1">View
                            All</span>
                    </a>
                </div>
            </section>
        </main>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Logic for Countdown Timers
                function updateCountdowns() {
                    const now = new Date();
                    document.querySelectorAll('.pending-countdown').forEach(el => {
                        const deadline = new Date(el.dataset.deadline);
                        const diff = deadline - now;

                        if (diff <= 0) {
                            el.innerText = "Expired";
                            el.closest('div.bg-white').classList.add('opacity-50');
                            return;
                        }

                        const mins = Math.floor(diff / 60000);
                        const secs = Math.floor((diff % 60000) / 1000);
                        el.innerText = `Expiring in ${mins}:${secs.toString().padStart(2, '0')}`;
                    });
                }

                setInterval(updateCountdowns, 1000);
                updateCountdowns();

                // Data semua event dikonversi dari collection ke Javascript Array
                const allEvents = {!! json_encode($allEventsLite) !!};

                const searchInput = document.getElementById('liveSearchInput');
                const searchDropdown = document.getElementById('searchResultsDropdown');
                const searchList = document.getElementById('searchResultsList');
                const clearBtn = document.getElementById('clearSearchBtn');
                const spinner = document.getElementById('searchSpinner');

                // Debounce function manual (mencegah search jalan setiap keystroke trmpan buffering)
                let timeoutId;

                searchInput.addEventListener('input', function () {
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

                clearBtn.addEventListener('click', function () {
                    clearTimeout(timeoutId); // Batalkan pencarian jika tombol clear diklik
                    searchInput.value = '';
                    searchInput.focus();
                    clearBtn.classList.add('hidden');
                    searchDropdown.classList.add('hidden');
                    spinner.classList.add('hidden');
                });

                // Sembunyikan dropdown kalau klik di luar area search
                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                        searchDropdown.classList.add('hidden');
                    }
                });

                // Munculkan lagi kalau klik input tapi teksnya ada isinya
                searchInput.addEventListener('click', function () {
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
                        if (results.length > 5) {
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