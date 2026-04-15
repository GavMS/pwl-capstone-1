<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flowtix</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#E5E5E3] font-sans antialiased">
<div class="relative min-h-screen flex items-center justify-center p-6">

    <main class="w-full max-w-[400px]">
        <div class="bg-white p-12 rounded-[2.5rem] shadow-sm text-center">

            <div class="mb-6">
                <h1 class="text-4xl font-extrabold text-[#555555] tracking-tight lowercase">
                    Flowtix
                </h1>
            </div>

            <p class="text-[#777777] font-medium mb-10 lowercase tracking-tight">
                get your tickets. join the crowd. <br>keep it chill.
            </p>

            <div class="flex flex-col gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full py-4 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition">
                            go to dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full py-4 bg-[#555555] text-white rounded-2xl font-bold lowercase hover:bg-black transition">
                            log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full py-4 bg-[#F4F4F4] text-[#555555] rounded-2xl font-bold lowercase hover:bg-[#EBEBEB] transition">
                                register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <footer class="mt-12 text-[10px] text-[#AAAAAA] uppercase tracking-widest">
                &copy; {{ date('Y') }} built for the culture
            </footer>
        </div>
    </main>

</div>
</body>
</html>
