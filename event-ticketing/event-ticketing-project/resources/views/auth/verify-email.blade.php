<x-guest-layout>
    <div class="min-h-screen flex flex-col">
        <nav class="flex justify-between items-center px-12 py-8 w-full">
            <div class="text-3xl font-bold text-[#555555]">Flowtix</div>
            <div class="flex items-center gap-8">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[#555555] font-medium hover:text-black underline decoration-2 underline-offset-4">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </nav>

        <div class="flex-grow flex items-center justify-center pb-24">
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm w-full max-w-md text-center">
                <div class="flex justify-center mb-6">
                    <div class="bg-[#F4F4F4] p-5 rounded-full">
                        <svg class="w-12 h-12 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-[#444444] mb-4">Verify your email</h1>

                <p class="text-sm text-[#777777] mb-8 leading-relaxed px-4">
                    {{ __('Thanks for signing up! Could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive it, we will gladly send you another.') }}
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-green-50 rounded-xl font-medium text-sm text-green-600 border border-green-100">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </div>
                @endif

                <div class="flex flex-col gap-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all shadow-md active:scale-[0.98]">
                            {{ __('Resend Verification Email') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
