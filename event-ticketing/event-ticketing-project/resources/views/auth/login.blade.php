<x-guest-layout>
    <div class="min-h-screen flex flex-col">
        <nav class="flex justify-between items-center px-12 py-8 w-full">
            <div class="text-3xl font-bold text-[#555555]">Flowtix</div>
        </nav>

        <div class="flex-grow flex items-center justify-center pb-24">
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm w-full max-w-md">
                <h1 class="text-center text-3xl font-bold text-[#444444] mb-10">Welcome back!</h1>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="username" :value="__('Username')" class="text-[#555555] font-medium ml-1" />
                        <x-text-input id="username"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                            type="text" name="username" :value="old('username')" placeholder="Username" required
                            autofocus />
                        <x-input-error :messages="$errors->get('username')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-[#555555] font-medium ml-1" />
                        <x-text-input id="password"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                            type="password" name="password" placeholder="Password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between px-1">


                        @if (Route::has('password.request'))
                            <a class="text-sm font-bold text-[#333333] hover:underline"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all mt-4">
                        {{ __('Sign In') }}
                    </button>

                    <p class="text-center text-sm text-[#555555] mt-6">
                        Don't have an account? <a href="{{ route('register') }}"
                            class="font-bold text-[#333333] hover:underline">Register now</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>