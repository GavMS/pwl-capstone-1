<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'logo.') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <div x-data="{ sidebarOpen: false }" class="layout-wrapper">
            
            <!-- Mobile Overlay -->
            <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-transition.opacity style="display: none;"></div>

            <!-- Sidebar Fragment -->
            @include('layouts.sidebar')

            <div class="main-content">
                {{ $slot }}
            </div>
        </div>

        <script>
            // Global SweetAlert2 notification listeners
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'success.',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#000000',
                    customClass: {
                        popup: 'rounded-[2rem] p-6',
                        title: 'text-2xl font-bold lowercase tracking-tight text-[#444444]',
                        htmlContainer: 'text-[#777777] text-sm font-medium lowercase'
                    }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'ups.',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-[2rem] p-6',
                        title: 'text-2xl font-bold lowercase tracking-tight text-[#444444]',
                        htmlContainer: 'text-[#777777] text-sm font-medium lowercase'
                    }
                });
            @endif

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
        @stack('scripts')
    </body>
</html>
