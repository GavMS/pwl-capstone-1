<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col">
    <!-- Logo -->
    <div class="h-24 flex items-center px-8 shrink-0">
        <a href="/" class="text-3xl font-extrabold text-[#555555] tracking-tight hover:text-black transition lowercase">
            logo.
        </a>
    </div>

    <!-- Navigation List -->
    <div class="flex-1 px-4 space-y-2 overflow-y-auto">
        <p class="px-4 text-[10px] font-bold text-[#777777] uppercase tracking-widest mb-4 mt-2">menu</p>

        @if (Auth::user()->role === 'admin')
            <!-- ADMIN LINKS -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.dashboard') ? 'bg-[#E5E5E3] text-[#444444] font-bold' : 'text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium' }} lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.users.*') ? 'bg-[#E5E5E3] text-[#444444] font-bold' : 'text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium' }} lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                manage users
            </a>
            <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.events.index') ? 'bg-[#E5E5E3] text-[#444444] font-bold' : 'text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium' }} lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                all events
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                financials
            </a>
            
        @elseif (Auth::user()->role === 'organizer')
            <!-- ORGANIZER LINKS -->
            <a href="{{ route('organizer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('organizer.dashboard') ? 'bg-[#E5E5E3] text-[#444444] font-bold' : 'text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium' }} lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                dashboard
            </a>
            <a href="{{ route('organizer.events.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('organizer.events.index') ? 'bg-[#E5E5E3] text-[#444444] font-bold' : 'text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium' }} lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                my events
            </a>
            <a href="{{ route('organizer.events.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('organizer.events.create') ? 'bg-[#E5E5E3] text-[#444444] font-bold' : 'text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium' }} lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                create event
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                sales & payouts
            </a>

        @else
            <!-- USER LINKS -->
            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('user.dashboard') ? 'bg-[#E5E5E3] text-[#444444] font-bold' : 'text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium' }} lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                dashboard
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                browse events
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                my tickets
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#777777] hover:bg-[#F4F4F4] hover:text-[#555555] font-medium lowercase transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                wishlist
            </a>
        @endif
    </div>

    <!-- User Profile & Logout Box at Bottom -->
    <div class="px-6 py-6 border-t border-gray-100 shrink-0">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 group cursor-pointer p-2 -ml-2 rounded-2xl {{ request()->routeIs('profile.edit') ? 'bg-[#F4F4F4]' : 'hover:bg-[#F4F4F4]' }} transition">
            <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center font-bold text-[#555555] uppercase shadow-sm group-hover:bg-[#E5E5E3] transition shrink-0">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <p class="font-bold text-[#444444] text-sm truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] font-medium text-[#777777] truncate uppercase tracking-widest mt-0.5">{{ Auth::user()->role }}</p>
            </div>
        </a>
        
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="button" onclick="confirmLogout(event)" class="w-full py-3.5 bg-red-50 text-red-500 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-red-500 hover:text-white transition shadow-sm">
                sign out
            </button>
        </form>
    </div>
</aside>
