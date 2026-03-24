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
                @if (session('status'))
                    <h1 class="text-2xl font-bold text-[#444444] mb-4">Email Sent</h1>
                    
                    <div class="py-4 px-4 bg-green-50 rounded-xl border border-green-200 mb-6 mt-4">
                        <p class="font-bold text-green-700">{{ session('status') }}</p>
                    </div>

                    <p class="text-sm text-[#777777] mb-8 leading-relaxed">
                        Please check your inbox for the password reset link. <br>
                        If you don't see it, be sure to check your spam folder.
                    </p>

                    <a href="{{ route('login') }}" class="block w-full text-center bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all shadow-md active:scale-[0.98] mb-6">
                        Back to Sign in
                    </a>

                    <a href="{{ route('password.request') }}" class="block text-center text-sm font-bold text-[#333333] hover:underline">
                        Search another account
                    </a>
                @else
                    <h1 class="text-2xl font-bold text-[#444444] mb-4">Forgot Password?</h1>

                    @if (session('confirm_email'))
                        <p class="text-sm text-[#777777] mb-8 leading-relaxed text-center">
                            Is this your email address? We will send a password reset link to this email.
                        </p>

                        <x-auth-session-status class="mb-4 font-medium text-sm text-green-600" :status="session('status')" />

                        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                            @csrf
                            <input type="hidden" name="username" value="{{ session('username') }}">
                            <input type="hidden" name="confirm" value="1">
                            
                            <div class="py-4 bg-gray-50 rounded-xl text-center border border-gray-200">
                                <span class="text-lg font-bold text-[#444444]">{{ session('confirm_email') }}</span>
                            </div>

                            <button type="submit" class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all shadow-md active:scale-[0.98]">
                                {{ __('Send Reset Link') }}
                            </button>
                            
                            <div class="text-center mt-6">
                                <a href="{{ route('password.request') }}" class="text-sm font-bold text-[#333333] hover:underline">
                                    Search another account
                                </a>
                            </div>
                        </form>
                    @else
                        <p class="text-sm text-[#777777] mb-8 leading-relaxed">
                            {{ __('No problem. Just let us know your username and we will find your account.') }}
                        </p>

                        <x-auth-session-status class="mb-4 font-medium text-sm text-green-600" :status="session('status')" />

                        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                            @csrf

                            <div class="text-left">
                                <x-input-label for="username" :value="__('Username')" class="text-[#555555] font-medium ml-1" />
                                <x-text-input id="username" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                                              type="text" name="username" :value="old('username')" placeholder="Enter your username" required autofocus />
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            <button type="submit" class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all shadow-md active:scale-[0.98]">
                                {{ __('Search Account') }}
                            </button>

                            <p class="text-center text-sm text-[#555555] mt-6">
                                Remember your password? <a href="{{ route('login') }}" class="font-bold text-[#333333] hover:underline">Back to Sign in</a>
                            </p>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
