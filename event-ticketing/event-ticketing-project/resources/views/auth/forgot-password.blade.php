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
                <h1 class="text-2xl font-bold text-[#444444] mb-4">Forgot Password?</h1>

                <p class="text-sm text-[#777777] mb-8 leading-relaxed">
                    {{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}
                </p>

                <x-auth-session-status class="mb-4 font-medium text-sm text-green-600" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div class="text-left">
                        <x-input-label for="email" :value="__('Email')" class="text-[#555555] font-medium ml-1" />
                        <x-text-input id="email" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                                      type="email" name="email" :value="old('email')" placeholder="Enter your email" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all shadow-md active:scale-[0.98]">
                        {{ __('Send Reset Link') }}
                    </button>

                    <p class="text-center text-sm text-[#555555] mt-6">
                        Remember your password? <a href="{{ route('login') }}" class="font-bold text-[#333333] hover:underline">Back to Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
