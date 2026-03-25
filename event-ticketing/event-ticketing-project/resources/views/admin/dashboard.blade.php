<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                        admin overview.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        system status & analytics
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="#" class="inline-block px-5 py-2.5 bg-[#F4F4F4] text-[#555555] rounded-xl font-bold lowercase hover:bg-[#EBEBEB] transition shadow-sm text-sm">
                        settings
                    </a>
                    <a href="#" class="inline-block px-5 py-2.5 bg-[#555555] text-white rounded-xl font-bold lowercase hover:bg-black transition shadow-sm text-sm">
                        generate report
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 flex flex-col gap-14">
            
            <!-- Quick Stats -->
            <section>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Stat 1 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Total Users</span>
                            <div class="w-8 h-8 rounded-full bg-[#F4F4F4] flex items-center justify-center text-[#555555]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2">{{ number_format($stats['total_users']) }}</h4>
                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mt-1">active accounts</p>
                    </div>

                    <!-- Stat 2 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Active Events</span>
                            <div class="w-8 h-8 rounded-full bg-[#F4F4F4] flex items-center justify-center text-[#555555]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2">{{ number_format($stats['active_events']) }}</h4>
                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mt-1">published events</p>
                    </div>

                    <!-- Stat 3 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#777777] uppercase tracking-widest">Platform Revenue</span>
                            <div class="w-8 h-8 rounded-full bg-[#F4F4F4] flex items-center justify-center text-[#555555]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <h4 class="text-4xl font-extrabold text-[#444444] tracking-tight mt-2">{{ number_format($stats['total_events']) }}</h4>
                        <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mt-1">all created events</p>
                    </div>
                </div>
            </section>

            <!-- Pending Approvals & Logs -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Pending Organizers -->
                <section>
                    <div class="flex justify-between items-end mb-6">
                        <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">pending organizers</h3>
                        <a href="#" class="text-sm font-bold text-[#777777] hover:text-black lowercase transition">view all</a>
                    </div>
                    
                    <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden flex flex-col gap-2 p-3">
                        
                        <!-- Item 1 -->
                        <div class="flex items-center justify-between p-4 bg-[#F4F4F4] rounded-2xl">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-[#555555] font-bold text-sm">
                                    IS
                                </div>
                                <div>
                                    <h5 class="font-bold text-[#444444] leading-tight">Ismaya Live</h5>
                                    <p class="text-xs font-medium text-[#777777]">Requested 2 hours ago</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button class="w-8 h-8 bg-green-100 text-green-700 rounded-lg flex items-center justify-center hover:bg-green-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                                <button class="w-8 h-8 bg-red-100 text-red-700 rounded-lg flex items-center justify-center hover:bg-red-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="flex items-center justify-between p-4 hover:bg-[#F4F4F4] rounded-2xl transition cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-[#555555] font-bold text-sm">
                                    SG
                                </div>
                                <div>
                                    <h5 class="font-bold text-[#444444] leading-tight">Soundrenaline Group</h5>
                                    <p class="text-xs font-medium text-[#777777]">Requested 5 hours ago</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button class="w-8 h-8 bg-green-100 text-green-700 rounded-lg flex items-center justify-center hover:bg-green-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                                <button class="w-8 h-8 bg-red-100 text-red-700 rounded-lg flex items-center justify-center hover:bg-red-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </section>

                <!-- System Activity -->
                <section>
                    <div class="flex justify-between items-end mb-6">
                        <h3 class="text-2xl font-bold text-[#444444] lowercase tracking-tight">recent activity</h3>
                    </div>
                    
                    <div class="bg-white rounded-[2rem] shadow-sm p-6 relative">
                        <div class="absolute left-10 top-6 bottom-6 w-px bg-gray-200"></div>
                        
                        <div class="flex flex-col gap-6 relative">
                            <!-- Log 1 -->
                            <div class="flex gap-4">
                                <div class="w-8 h-8 bg-[#E5E5E3] rounded-full border-4 border-white flex-shrink-0 z-10 flex items-center justify-center text-[#555555]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                                <div class="pt-1">
                                    <p class="text-sm font-bold text-[#444444]">System Alert: High Traffic</p>
                                    <p class="text-xs font-medium text-[#777777] mt-1">Over 5,000 users concurrent during Coldplay ticket sale.</p>
                                    <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest mt-2 block">10 mins ago</span>
                                </div>
                            </div>
                            
                            <!-- Log 2 -->
                            <div class="flex gap-4">
                                <div class="w-8 h-8 bg-[#E5E5E3] rounded-full border-4 border-white flex-shrink-0 z-10 flex items-center justify-center text-[#555555]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="pt-1">
                                    <p class="text-sm font-bold text-[#444444]">Payment Gateway Synced</p>
                                    <p class="text-xs font-medium text-[#777777] mt-1">Midtrans batch settlement completed for 3,240 transactions.</p>
                                    <span class="text-[10px] font-bold text-[#777777] uppercase tracking-widest mt-2 block">1 hour ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            
        </main>

    </div>
</x-app-layout>
