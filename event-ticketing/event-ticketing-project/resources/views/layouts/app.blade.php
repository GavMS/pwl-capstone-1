<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'logo.') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased text-[#555555] bg-[#E5E5E3]">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
            
            <!-- Mobile Overlay -->
            <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-transition.opacity style="display: none;"></div>

            <!-- Sidebar Fragment -->
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
                <!-- Mobile Top Nav -->
                <div class="lg:hidden bg-white/70 backdrop-blur-md border-b border-gray-200 p-4 flex items-center justify-between sticky top-0 z-30">
                    <span class="text-2xl font-extrabold tracking-tight lowercase text-[#555555]">logo.</span>
                    <button @click="sidebarOpen = true" class="p-2.5 bg-[#F4F4F4] rounded-xl text-[#555555] hover:bg-[#EBEBEB] transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>

                <main class="flex-1 w-full relative">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            function confirmLogout(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'are you sure?',
                    text: 'you will be logged out of your account.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#F4F4F4',
                    confirmButtonText: 'yes, sign out',
                    cancelButtonText: '<span class="text-[#555555] font-bold">cancel</span>',
                    background: '#ffffff',
                    color: '#444444',
                    customClass: {
                        popup: 'rounded-[2.5rem] p-6 shadow-xl',
                        title: 'text-2xl font-bold lowercase tracking-tight pt-4 text-[#444444]',
                        htmlContainer: 'text-[#777777] text-sm font-medium lowercase mb-4',
                        confirmButton: 'rounded-xl font-bold lowercase px-8 py-3.5 mx-2 text-sm',
                        cancelButton: 'rounded-xl font-bold lowercase px-8 py-3.5 mx-2 text-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                })
            }
        </script>
    </body>
</html>
