<x-guest-layout>
    <div class="min-h-screen flex flex-col">
        <nav class="flex justify-between items-center px-12 py-8 w-full">
            <div class="text-3xl font-bold text-[#555555]">Logo</div>
            <div class="flex items-center gap-8">
                <a href="#" class="text-[#555555] font-medium hover:text-black">Events</a>
                <a href="{{ route('login') }}" class="bg-[#555555] text-white px-8 py-2.5 rounded-xl font-medium hover:bg-[#444444] transition">Sign In</a>
            </div>
        </nav>

        <div class="flex-grow flex items-center justify-center pb-24">
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm w-full max-w-md text-center">
                <div class="flex justify-center mb-6">
                    <svg class="w-12 h-12 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-[#444444] mb-4">Secure Area</h1>

                <p class="text-sm text-[#777777] mb-8 leading-relaxed px-2">
                    {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </p>

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

                    <div class="text-left">
                        <x-input-label for="password" :value="__('Password')" class="text-[#555555] font-medium ml-1" />
                        <x-text-input id="password" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                                      type="password" name="password" placeholder="Confirm your password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all shadow-md active:scale-[0.98]">
                        {{ __('Confirm') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
