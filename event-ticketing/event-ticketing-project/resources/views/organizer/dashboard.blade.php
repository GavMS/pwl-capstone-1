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
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Tickets Sold</span>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2 relative z-10">8,240</h4>
                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mt-1 relative z-10"> across 3 active events</p>
                    </div>

                    <!-- Stat 2 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col gap-2 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#E5E5E3] rounded-full opacity-50 group-hover:scale-150 transition duration-700"></div>
                        <div class="flex items-center justify-between relative z-10">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Total Revenue</span>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2 relative z-10">Rp 4.2B</h4>
                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mt-1 relative z-10">estimated payout tomorrow</p>
                    </div>

                    <!-- Stat 3 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col gap-2 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#E5E5E3] rounded-full opacity-50 group-hover:scale-150 transition duration-700"></div>
                        <div class="flex items-center justify-between relative z-10">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Profile Views</span>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2 relative z-10">45.2K</h4>
                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mt-1 relative z-10">+24% this week</p>
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
                    
                    <!-- Event Row 1 -->
                    <div class="bg-white p-4 sm:p-6 rounded-[2rem] shadow-sm flex flex-col sm:flex-row items-center gap-6 hover:shadow-md transition">
                        <div class="w-full sm:w-32 h-32 bg-gray-200 rounded-2xl overflow-hidden flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1540039155732-d674ce313cb6?q=80&w=400&auto=format&fit=crop" alt="Event" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow w-full">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-lg uppercase tracking-widest">Published</span>
                                <span class="text-xs font-bold text-[#777777]">Dec 5, 2026</span>
                            </div>
                            <h4 class="text-xl font-bold text-[#444444] leading-tight mb-2">Jakarta Music Festival 2026</h4>
                            
                            <!-- Progress Bar -->
                            <div class="w-full mt-4">
                                <div class="flex justify-between text-[10px] font-bold text-[#777777] uppercase tracking-widest mb-1">
                                    <span>Tickets Sold: 4,500 / 5,000</span>
                                    <span>90%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#555555] rounded-full" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex sm:flex-col gap-2 w-full sm:w-auto mt-4 sm:mt-0">
                            <button class="flex-1 sm:flex-none px-6 py-3 bg-[#555555] text-white rounded-xl font-bold text-sm hover:bg-black transition lowercase flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                edit
                            </button>
                            <button class="flex-1 sm:flex-none px-6 py-3 bg-[#F4F4F4] text-[#555555] rounded-xl font-bold text-sm hover:bg-[#EBEBEB] transition lowercase flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                stats
                            </button>
                        </div>
                    </div>
                    
                    <!-- Event Row 2 -->
                    <div class="bg-white p-4 sm:p-6 rounded-[2rem] shadow-sm flex flex-col sm:flex-row items-center gap-6 hover:shadow-md transition">
                        <div class="w-full sm:w-32 h-32 bg-gray-200 rounded-2xl overflow-hidden flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=400&auto=format&fit=crop" alt="Event" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow w-full">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-lg uppercase tracking-widest">Selling Fast</span>
                                <span class="text-xs font-bold text-[#777777]">Jan 15, 2027</span>
                            </div>
                            <h4 class="text-xl font-bold text-[#444444] leading-tight mb-2">Tech Creator Workshop</h4>
                            
                            <!-- Progress Bar -->
                            <div class="w-full mt-4">
                                <div class="flex justify-between text-[10px] font-bold text-[#777777] uppercase tracking-widest mb-1">
                                    <span>Tickets Sold: 80 / 100</span>
                                    <span>80%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-400 rounded-full" style="width: 80%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex sm:flex-col gap-2 w-full sm:w-auto mt-4 sm:mt-0">
                            <button class="flex-1 sm:flex-none px-6 py-3 bg-[#555555] text-white rounded-xl font-bold text-sm hover:bg-black transition lowercase flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                edit
                            </button>
                            <button class="flex-1 sm:flex-none px-6 py-3 bg-[#F4F4F4] text-[#555555] rounded-xl font-bold text-sm hover:bg-[#EBEBEB] transition lowercase flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                stats
                            </button>
                        </div>
                    </div>

                </div>
            </section>
        </main>

    </div>
</x-app-layout>
