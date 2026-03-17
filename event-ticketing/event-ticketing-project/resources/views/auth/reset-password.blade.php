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
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm w-full max-w-md">
                <h1 class="text-center text-3xl font-bold text-[#444444] mb-10">Reset Password</h1>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <x-input-label for="email" :value="__('Email Address')" class="text-[#555555] font-medium ml-1" />
                        <x-text-input id="email" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                                      type="email" name="email" :value="old('email', $request->email)" placeholder="Your email" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('New Password')" class="text-[#555555] font-medium ml-1" />
                        <x-text-input id="password" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                                      type="password" name="password" placeholder="New Password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="text-[#555555] font-medium ml-1" />
                        <x-text-input id="password_confirmation" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                                      type="password" name="password_confirmation" placeholder="Confirm your new password" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <button type="submit" class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all mt-6 shadow-md active:scale-[0.98]">
                        {{ __('Reset Password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
