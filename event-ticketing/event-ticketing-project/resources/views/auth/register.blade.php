<x-guest-layout>
    <div class="min-h-screen flex flex-col bg-[#E5E5E3]">
        <nav class="flex justify-between items-center px-12 py-8 w-full">
            <div class="text-3xl font-bold text-[#555555]">Flowtix</div>
        </nav>

        <div class="flex-grow flex items-center justify-center pb-24">
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm w-full max-w-md">
                <h1 class="text-center text-3xl font-bold text-[#444444] mb-10">Create your account!</h1>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-[#555555] font-medium ml-1">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                               required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1 text-left">
                            <label class="block text-[#555555] font-medium ml-1">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" placeholder="Username"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                                   required />
                            <x-input-error :messages="$errors->get('username')" class="mt-1" />
                        </div>
                        <div class="flex-1 text-left">
                            <label class="block text-[#555555] font-medium ml-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                                   required />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[#555555] font-medium ml-1">Password</label>
                        <input type="password" name="password" placeholder="Password"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                               required />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-[#555555] font-medium ml-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm Password"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-gray-400 mt-1 placeholder-gray-300"
                               required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <button type="submit" class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all mt-4">
                        Register
                    </button>

                    <p class="text-center text-sm text-[#555555] mt-6">
                        Already have an account? <a href="{{ route('login') }}" class="font-bold text-[#333333] hover:underline">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
