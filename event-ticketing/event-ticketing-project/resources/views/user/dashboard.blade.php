<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased">

        <header class="bg-white/50 backdrop-blur-sm shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto py-8 px-12">
                <h2 class="text-3xl font-bold text-[#555555] leading-tight">
                    {{ __('User Dashboard') }}
                </h2>
            </div>
        </header>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm overflow-hidden">
                    <div class="text-[#444444]">
                        <h3 class="text-2xl font-bold mb-4">
                            Selamat datang, <span class="text-[#555555]">{{ $user->name }}</span>!
                        </h3>

                        <p class="text-lg text-[#777777] leading-relaxed">
                            Anda login dengan role:
                            <span class="ml-2 font-bold px-4 py-1.5 bg-[#F4F4F4] text-[#555555] rounded-xl border border-gray-200 shadow-sm">
                                {{ ucfirst($user->role) }}
                            </span>
                        </p>

                        <div class="mt-12 p-8 border-2 border-dashed border-gray-100 rounded-[2rem] text-center">
                            <p class="text-gray-400 italic">Konten tiket atau aktivitas Anda akan muncul di sini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
