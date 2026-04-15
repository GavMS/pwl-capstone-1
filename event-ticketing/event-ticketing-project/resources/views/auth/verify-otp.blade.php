<x-guest-layout>
    <div class="min-h-screen flex flex-col bg-[#E5E5E3]">
        <nav class="flex justify-between items-center px-12 py-8 w-full">
            <div class="text-3xl font-bold text-[#555555]">Flowtix</div>
        </nav>

        <div class="flex-grow flex items-center justify-center pb-24">
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm w-full max-w-md">

                {{-- Icon --}}
                <div class="flex justify-center mb-6">
                    <div class="bg-[#f3f4f6] rounded-full p-5">
                        <svg class="w-10 h-10 text-[#555555]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-center text-2xl font-bold text-[#444444] mb-2">Check your email</h1>
                <p class="text-center text-[#888888] text-sm mb-8">
                    We sent a 6-digit verification code to<br>
                    <span class="font-semibold text-[#555555]">{{ $email }}</span>
                </p>

                {{-- Success: Resent --}}
                @if (session('resent'))
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm mb-5 text-center">
                        ✅ A new OTP has been sent to your email.
                    </div>
                @endif

                {{-- OTP Form --}}
                <form method="POST" action="{{ route('register.otp.verify') }}" class="space-y-5" id="otp-form">
                    @csrf

                    <div>
                        <label class="block text-[#555555] font-medium ml-1 mb-1">Enter OTP Code</label>

                        {{-- 6-digit OTP boxes --}}
                        <div class="flex gap-2 justify-center" id="otp-inputs">
                            @for ($i = 0; $i < 6; $i++)
                                <input
                                    type="text"
                                    maxlength="1"
                                    inputmode="numeric"
                                    pattern="[0-9]"
                                    class="otp-digit w-12 h-14 text-center text-2xl font-bold border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#555555] focus:border-transparent transition-all"
                                    id="otp-{{ $i }}"
                                    autocomplete="off"
                                />
                            @endfor
                        </div>

                        {{-- Hidden input that aggregates OTP --}}
                        <input type="hidden" name="otp" id="otp-combined" />

                        @error('otp')
                            <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Countdown Timer --}}
                    <p class="text-center text-sm text-[#888888]" id="timer-text">
                        Code expires in <span id="countdown" class="font-semibold text-[#444444]">10:00</span>
                    </p>

                    <button type="submit" id="submit-btn"
                        class="w-full bg-[#555555] text-white font-bold py-4 rounded-xl hover:bg-[#444444] transition-all mt-2">
                        Verify Email
                    </button>
                </form>

                {{-- Resend --}}
                <form method="POST" action="{{ route('register.otp.resend') }}" class="mt-4">
                    @csrf
                    <button type="submit"
                        class="w-full text-sm text-[#555555] font-medium py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-all">
                        Resend OTP
                    </button>
                </form>

                <p class="text-center text-sm text-[#888888] mt-5">
                    Wrong email?
                    <a href="{{ route('register') }}" class="font-bold text-[#333333] hover:underline">Go back</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // --- OTP Input Navigation ---
        const inputs = document.querySelectorAll('.otp-digit');
        const combined = document.getElementById('otp-combined');
        const form = document.getElementById('otp-form');

        inputs.forEach((input, idx) => {
            input.addEventListener('input', (e) => {
                // Allow only numbers
                input.value = input.value.replace(/[^0-9]/g, '');

                if (input.value && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                }
                updateCombined();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && idx > 0) {
                    inputs[idx - 1].focus();
                }
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                [...pasted].forEach((char, i) => {
                    if (inputs[idx + i]) {
                        inputs[idx + i].value = char;
                    }
                });
                const nextEmpty = [...inputs].findIndex(inp => !inp.value);
                if (nextEmpty !== -1) inputs[nextEmpty].focus();
                else inputs[inputs.length - 1].focus();
                updateCombined();
            });
        });

        function updateCombined() {
            combined.value = [...inputs].map(i => i.value).join('');
        }

        // Auto-submit when all 6 boxes filled
        form.addEventListener('input', () => {
            const allFilled = [...inputs].every(i => i.value !== '');
            if (allFilled) {
                updateCombined();
            }
        });

        // --- Countdown Timer (10 minutes) ---
        let seconds = 10 * 60;
        const countdownEl = document.getElementById('countdown');
        const timerText = document.getElementById('timer-text');

        const interval = setInterval(() => {
            seconds--;
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            countdownEl.textContent = `${m}:${s}`;

            if (seconds <= 0) {
                clearInterval(interval);
                timerText.innerHTML = '<span class="text-red-500 font-medium">OTP expired. Please resend.</span>';
                document.getElementById('submit-btn').disabled = true;
                document.getElementById('submit-btn').classList.add('opacity-50', 'cursor-not-allowed');
            }
        }, 1000);
    </script>
</x-guest-layout>
