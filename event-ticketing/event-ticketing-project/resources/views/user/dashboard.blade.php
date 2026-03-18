<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
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
                    <a href="#" class="inline-block px-6 py-3 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition shadow-sm text-sm">
                        browse events
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 flex flex-col gap-14">
            
            <!-- My Tickets Section -->
            <section>
                <div class="flex justify-between items-end mb-6">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">my upcoming tickets</h3>
                    <a href="#" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">view all</a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dummy Ticket 1 -->
                    <div class="bg-white p-5 rounded-[2rem] shadow-sm flex items-center gap-5 hover:shadow-md transition">
                        <div class="w-24 h-24 bg-gray-200 rounded-2xl overflow-hidden flex-shrink-0">
                            <!-- Placeholder image -->
                            <img src="https://images.unsplash.com/photo-1540039155732-d674ce313cb6?q=80&w=400&auto=format&fit=crop" alt="Event" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow">
                            <span class="inline-block px-3 py-1 bg-[#F4F4F4] text-[#555555] text-[10px] font-bold rounded-lg mb-2 uppercase tracking-widest">
                                Oct 24, 2026
                            </span>
                            <h4 class="text-lg font-bold text-[#444444] leading-tight mb-1">Neon Lights Festival</h4>
                            <p class="text-xs font-medium text-[#777777] flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                GBK Stadium, Jakarta
                            </p>
                        </div>
                        <div class="text-right hidden sm:block">
                            <button class="px-5 py-2.5 bg-[#F4F4F4] text-[#555555] rounded-xl font-bold text-sm hover:bg-[#EBEBEB] transition lowercase">
                                view
                            </button>
                        </div>
                    </div>
                    
                    <!-- Dummy Ticket 2 -->
                    <div class="bg-white p-5 rounded-[2rem] shadow-sm flex items-center gap-5 hover:shadow-md transition">
                        <div class="w-24 h-24 bg-gray-200 rounded-2xl overflow-hidden flex-shrink-0">
                            <!-- Placeholder image -->
                            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=400&auto=format&fit=crop" alt="Event" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow">
                            <span class="inline-block px-3 py-1 bg-[#F4F4F4] text-[#555555] text-[10px] font-bold rounded-lg mb-2 uppercase tracking-widest">
                                Nov 12, 2026
                            </span>
                            <h4 class="text-lg font-bold text-[#444444] leading-tight mb-1">Startup Summit '26</h4>
                            <p class="text-xs font-medium text-[#777777] flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                ICE BSD, Tangerang
                            </p>
                        </div>
                        <div class="text-right hidden sm:block">
                            <button class="px-5 py-2.5 bg-[#F4F4F4] text-[#555555] rounded-xl font-bold text-sm hover:bg-[#EBEBEB] transition lowercase">
                                view
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Events -->
            <section>
                <div class="flex justify-between items-end mb-6">
                    <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">discover events</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <!-- Event 1 -->
                    <div class="bg-white rounded-[2.5rem] p-5 shadow-sm hover:shadow-md transition flex flex-col group cursor-pointer">
                        <div class="w-full h-56 bg-gray-200 rounded-[2rem] overflow-hidden relative mb-5">
                            <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=600&auto=format&fit=crop" alt="Event cover" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-xl text-xs font-bold text-[#555555]">
                                Rp 450.000
                            </div>
                        </div>
                        <div class="px-2 flex-grow flex flex-col">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-[10px] font-bold text-[#777777] uppercase tracking-widest">Concert</span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span class="text-[10px] font-bold text-[#777777] uppercase tracking-widest">Dec 5, 2026</span>
                            </div>
                            <h4 class="text-xl font-bold text-[#444444] leading-snug mb-2">Jazz Night Under The Stars</h4>
                            <p class="text-sm font-medium text-[#777777] line-clamp-2 mb-6 leading-relaxed">Experience a magical evening of smooth jazz performances by top international artists in an open-air amphitheater.</p>
                            
                            <button class="mt-auto w-full py-4 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition">
                                get tickets
                            </button>
                        </div>
                    </div>

                    <!-- Event 2 -->
                    <div class="bg-white rounded-[2.5rem] p-5 shadow-sm hover:shadow-md transition flex flex-col group cursor-pointer">
                        <div class="w-full h-56 bg-gray-200 rounded-[2rem] overflow-hidden relative mb-5">
                            <img src="https://images.unsplash.com/photo-1523580494112-071d16940a43?q=80&w=600&auto=format&fit=crop" alt="Event cover" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-xl text-xs font-bold text-[#555555]">
                                Free
                            </div>
                        </div>
                        <div class="px-2 flex-grow flex flex-col">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-[10px] font-bold text-[#777777] uppercase tracking-widest">Workshop</span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span class="text-[10px] font-bold text-[#777777] uppercase tracking-widest">Jan 15, 2027</span>
                            </div>
                            <h4 class="text-xl font-bold text-[#444444] leading-snug mb-2">Creative Typography Class</h4>
                            <p class="text-sm font-medium text-[#777777] line-clamp-2 mb-6 leading-relaxed">Learn the art of pairing fonts and designing beautiful web typography with industry experts.</p>
                            
                            <button class="mt-auto w-full py-4 bg-[#F4F4F4] text-[#555555] rounded-2xl font-bold lowercase hover:bg-[#EBEBEB] transition">
                                register now
                            </button>
                        </div>
                    </div>

                    <!-- Event 3 -->
                    <div class="bg-white rounded-[2.5rem] p-5 shadow-sm hover:shadow-md transition flex flex-col group cursor-pointer lg:block md:hidden">
                        <div class="w-full h-56 bg-gray-200 rounded-[2rem] overflow-hidden relative mb-5">
                            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=600&auto=format&fit=crop" alt="Event cover" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-xl text-xs font-bold text-[#555555]">
                                Rp 1.200.000
                            </div>
                        </div>
                        <div class="px-2 flex-grow flex flex-col">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-[10px] font-bold text-[#777777] uppercase tracking-widest">Conference</span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span class="text-[10px] font-bold text-[#777777] uppercase tracking-widest">Feb 20, 2027</span>
                            </div>
                            <h4 class="text-xl font-bold text-[#444444] leading-snug mb-2">Global UI/UX Design Summit</h4>
                            <p class="text-sm font-medium text-[#777777] line-clamp-2 mb-6 leading-relaxed">Join hundreds of designers to discuss the future of digital interfaces, AI integration, and user-centric flows.</p>
                            
                            <button class="mt-auto w-full py-4 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition">
                                get tickets
                            </button>
                        </div>
                    </div>

                </div>
            </section>
        </main>

    </div>
</x-app-layout>
