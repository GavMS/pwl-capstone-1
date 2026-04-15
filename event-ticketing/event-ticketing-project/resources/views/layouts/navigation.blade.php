<nav x-data="{ open: false }" class="bg-white/70 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex justify-between h-20">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @php
                        $dashboardRoute = 'user.dashboard';
                        if (Auth::user()->role === 'admin') $dashboardRoute = 'admin.dashboard';
                        elseif (Auth::user()->role === 'organizer') $dashboardRoute = 'organizer.dashboard';
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="text-2xl font-bold text-[#555555] tracking-tight hover:text-black transition">
                        Flowtix
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-12 sm:flex border-none">
                    <x-nav-link :href="route($dashboardRoute)" :active="request()->routeIs($dashboardRoute)"
                                class="text-[#555555] font-semibold lowercase tracking-wide border-none pt-1">
                        {{ __('dashboard') }}
                    </x-nav-link>

                    <a href="#" class="inline-flex items-center px-1 pt-1 text-sm font-semibold leading-5 text-[#555555]/60 hover:text-[#555555] transition lowercase">
                        events
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm leading-4 font-bold rounded-xl text-[#555555] bg-[#F4F4F4] hover:bg-[#EBEBEB] focus:outline-none transition ease-in-out duration-150">
                            <div class="lowercase">{{ Auth::user()->name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-xl">
                            <x-dropdown-link :href="route('profile.edit')" class="lowercase font-medium py-3">
                                {{ __('profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 class="lowercase font-medium py-3 text-red-500 hover:bg-red-50"
                                                 onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('log out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-[#555555] hover:bg-gray-100 focus:outline-none transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 backdrop-blur-lg">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route($dashboardRoute)" :active="request()->routeIs($dashboardRoute)"
                                   class="rounded-xl lowercase font-bold">
                {{ __('dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-6 border-t border-gray-100">
            <div class="px-6 flex items-center gap-3">
                <div class="h-10 w-10 bg-[#F4F4F4] rounded-full flex items-center justify-center font-bold text-[#555555] uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-base text-[#444444] lowercase">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-[#777777]">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-1 px-4">
                <x-responsive-nav-link :href="route('profile.edit')" class="rounded-xl lowercase font-medium">
                    {{ __('profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                                           class="rounded-xl lowercase font-bold text-red-500"
                                           onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('log out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
