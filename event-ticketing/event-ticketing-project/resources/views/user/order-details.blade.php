<x-app-layout>
    <div class="min-h-screen bg-[#F0F2F5] font-sans antialiased pb-24">
        {{-- Progress Header --}}
        <div class="bg-white border-b border-gray-200 py-4 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <form action="{{ route('queue.release', $event->id_event) }}" method="POST" id="cancel-form">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-gray-500 hover:text-red-600 transition font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Batalkan Sesi
                    </button>
                </form>
                <div class="flex items-center gap-4">
                    <span class="text-[10px] uppercase tracking-widest font-black text-gray-400">Step 2 of 3</span>
                    <div class="h-1.5 w-32 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-teal-500 w-2/3"></div>
                    </div>
                    <span class="text-sm font-extrabold text-teal-600 lowercase tracking-tighter">order details</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Main Form Column --}}
                <div class="lg:col-span-8 space-y-6">
                    <div class="mb-2">
                        <h1 class="text-2xl font-black text-gray-900 lowercase tracking-tight">{{ $event->title }}</h1>
                        <p class="text-sm text-gray-500 font-medium">
                            {{ $event->date->format('d M Y, H:i') }} &bull; {{ $event->location }}
                        </p>
                    </div>

                    <form action="{{ route('checkout.process') }}" method="POST" id="main-checkout-form" class="space-y-6">
                        @csrf
                        <input type="hidden" name="event_id" id="event_id" value="{{ $event->id_event }}">
                        <input type="hidden" name="tickets" value="{{ json_encode($selectedTickets) }}">
                        <input type="hidden" name="voucher_code" id="hidden_voucher_code" value="">

                        {{-- Attendee Info Sections --}}
                        @php $attendeeIndex = 0; @endphp
                        @foreach($selectedTickets as $ticket)
                            @for($i = 1; $i <= $ticket['quantity']; $i++)
                                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 animate-in fade-in slide-in-from-bottom-4 duration-500 delay-{{ $attendeeIndex * 100 }}">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-lg font-black text-gray-900 border-l-4 border-teal-500 pl-4">
                                            Attendee Info ({{ $ticket['name'] }} #{{ $i }})
                                        </h3>
                                        <span class="text-[10px] bg-gray-50 px-2 py-1 rounded-md text-gray-400 font-bold uppercase tracking-widest">Required</span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Full Name --}}
                                        <div class="space-y-2">
                                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Full Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="attendees[{{ $attendeeIndex }}][name]" required
                                                value="{{ $attendeeIndex === 0 ? auth()->user()->name : '' }}"
                                                placeholder="Enter full name"
                                                class="w-full bg-gray-50 border-gray-200 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition text-sm font-bold placeholder:text-gray-300">
                                        </div>

                                        {{-- Email --}}
                                        <div class="space-y-2">
                                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Email Address <span class="text-red-500">*</span></label>
                                            <input type="email" name="attendees[{{ $attendeeIndex }}][email]" required
                                                data-validate="email"
                                                value="{{ $attendeeIndex === 0 ? auth()->user()->email : '' }}"
                                                placeholder="example@mail.com"
                                                class="attendee-input w-full bg-gray-50 border-gray-200 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition text-sm font-bold placeholder:text-gray-300">
                                            <p class="validation-error text-red-500 text-xs font-bold mt-1 hidden"></p>
                                        </div>

                                        {{-- Phone Number --}}
                                        <div class="space-y-2">
                                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Phone Number <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">+62</span>
                                                <input type="text" name="attendees[{{ $attendeeIndex }}][phone]" required
                                                    data-validate="phone"
                                                    inputmode="numeric"
                                                    placeholder="812345678"
                                                    class="attendee-input w-full bg-gray-50 border-gray-200 rounded-2xl pl-14 pr-5 py-3.5 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition text-sm font-bold placeholder:text-gray-300">
                                            </div>
                                            <p class="validation-error text-red-500 text-xs font-bold mt-1 hidden"></p>
                                        </div>

                                        {{-- ID Number --}}
                                        <div class="space-y-2">
                                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">ID Number (KTP/Passport) <span class="text-red-500">*</span></label>
                                            <input type="text" name="attendees[{{ $attendeeIndex }}][id_card]" required
                                                data-validate="id_number"
                                                inputmode="numeric"
                                                placeholder="32000xxxxxxxx"
                                                class="attendee-input w-full bg-gray-50 border-gray-200 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition text-sm font-bold placeholder:text-gray-300">
                                            <p class="validation-error text-red-500 text-xs font-bold mt-1 hidden"></p>
                                        </div>

                                        {{-- Date of Birth --}}
                                        <div class="space-y-2">
                                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Date of Birth <span class="text-red-500">*</span></label>
                                            <input type="date" name="attendees[{{ $attendeeIndex }}][dob]" required
                                                max="{{ now()->format('Y-m-d') }}"
                                                class="w-full bg-gray-50 border-gray-200 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition text-sm font-bold">
                                        </div>

                                        {{-- Gender --}}
                                        <div class="space-y-2">
                                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Gender <span class="text-red-500">*</span></label>
                                            <div class="flex items-center gap-6 py-3.5">
                                                <label class="flex items-center gap-3 cursor-pointer group">
                                                    <input type="radio" name="attendees[{{ $attendeeIndex }}][gender]" value="male" required
                                                        class="w-5 h-5 text-teal-500 border-gray-200 focus:ring-teal-500/20">
                                                    <span class="text-sm font-bold text-gray-600 group-hover:text-gray-900 transition">Male</span>
                                                </label>
                                                <label class="flex items-center gap-3 cursor-pointer group">
                                                    <input type="radio" name="attendees[{{ $attendeeIndex }}][gender]" value="female" required
                                                        class="w-5 h-5 text-teal-500 border-gray-200 focus:ring-teal-500/20">
                                                    <span class="text-sm font-bold text-gray-600 group-hover:text-gray-900 transition">Female</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php $attendeeIndex++; @endphp
                            @endfor
                        @endforeach
                    </form>
                </div>

                {{-- Sidebar Summary Column --}}
                <div class="lg:col-span-4 sticky top-24 space-y-4">
                    {{-- Timer Widget --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase text-amber-500 tracking-widest leading-none mb-1">expires in</p>
                                <p class="text-lg font-black text-amber-700 leading-none" id="order-timer">15:00</p>
                            </div>
                        </div>
                    </div>

                    {{-- Voucher Section --}}
                    <div class="bg-white rounded-3xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-6">
                        <h3 class="text-lg font-black text-gray-900 mb-4 lowercase tracking-tight">apply voucher</h3>
                        <div class="flex items-stretch gap-3 relative">
                            <input type="text" id="voucher_input" placeholder="e.g. DISCOUNT20" class="flex-1 w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-3 text-sm font-bold placeholder:text-gray-300 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition uppercase">
                            <button type="button" id="btn-apply-voucher" class="px-6 flex items-center justify-center border border-transparent bg-[#444444] text-white rounded-2xl font-bold lowercase hover:bg-black transition shadow-sm whitespace-nowrap">
                                apply
                            </button>
                        </div>
                        <div id="voucher-message" class="hidden mt-3 p-3 rounded-xl text-xs font-bold flex items-start gap-2"></div>
                    </div>

                    {{-- Order Summary --}}
                    <div class="bg-white rounded-3xl shadow-xl shadow-teal-500/5 border border-teal-100 p-6">
                        <h3 class="text-lg font-black text-gray-900 mb-6 border-b border-dashed border-gray-100 pb-4 lowercase tracking-tight">order summary</h3>
                        
                        <div class="space-y-4 mb-8">
                            @foreach($selectedTickets as $ticket)
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-extrabold text-gray-800 leading-tight">{{ $ticket['name'] }}</p>
                                        <p class="text-[11px] font-bold text-gray-400 tracking-wider">Rp {{ number_format($ticket['price'], 0, ',', '.') }} x {{ $ticket['quantity'] }}</p>
                                    </div>
                                    <p class="text-sm font-black text-gray-900">Rp {{ number_format($ticket['price'] * $ticket['quantity'], 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-3 pt-6 border-t border-gray-100">
                            <div class="flex justify-between text-sm font-bold text-gray-500 lowercase tracking-tight">
                                <span>subtotal</span>
                                <span id="summary-subtotal" data-value="{{ $totalPrice }}">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                            <div id="discount-row" class="flex justify-between text-sm font-bold text-teal-600 lowercase tracking-tight hidden">
                                <span>discount <span id="discount-percent-badge" class="bg-teal-100 text-teal-800 px-1.5 py-0.5 rounded-md text-[10px] ml-1"></span></span>
                                <span id="summary-discount">- Rp 0</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-gray-500 lowercase tracking-tight">
                                <span>tax (0%)</span>
                                <span>Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center pt-2">
                                <span class="text-base font-black text-gray-900 lowercase tracking-tight">total amount</span>
                                <span class="text-2xl font-black text-teal-600 tracking-tighter" id="summary-total">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Agreements --}}
                        <div class="mt-8 space-y-4">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" form="main-checkout-form" name="agreement" required
                                    class="mt-1 w-5 h-5 text-teal-500 border-gray-200 rounded focus:ring-teal-500/20">
                                <span class="text-[11px] font-bold text-gray-500 group-hover:text-gray-800 transition leading-relaxed">
                                    I agree to Flowtix's <a href="#" class="text-teal-600 underline">Terms & Conditions</a> and <a href="#" class="text-teal-600 underline">Privacy Policy</a>
                                </span>
                            </label>
                        </div>

                        @if($errors->any())
                            <div class="mt-6 p-4 bg-red-50 border border-red-100 rounded-2xl text-[11px] font-bold text-red-600">
                                <ul class="list-disc ml-4 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <button type="submit" form="main-checkout-form" class="mt-8 w-full py-4 rounded-2xl bg-[#38b2ac] hover:bg-teal-600 text-white font-black text-lg transition shadow-xl shadow-teal-500/30 flex justify-center items-center gap-3 group lowercase tracking-tight">
                            Proceed to Payment
                            <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>

                    <div class="px-6 py-4 bg-gray-100/50 rounded-2xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-snug">secure checkout with standard encryption</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let timeLeft = 900; // 15 minutes
            const timerEl = document.getElementById('order-timer');

            const timer = setInterval(() => {
                const actualMins = Math.floor(timeLeft / 60);
                const secs = timeLeft % 60;
                
                timerEl.innerText = `${actualMins}:${secs.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    alert('Session expired. Please restart your booking.');
                    window.location.href = "{{ route('events.show', $event->id_event) }}";
                }
                timeLeft--;
            }, 1000);

            // --- AUTO RELEASE SYSTEM (Zero-Ghosting) ---
            const releaseUrl = "{{ route('queue.release', $event->id_event) }}";
            const csrfToken = "{{ csrf_token() }}";
            let submitted = false;

            // Jangan rilis jika user sedang memproses pembayaran (submit form)
            const checkoutForm = document.getElementById('main-checkout-form');
            checkoutForm.addEventListener('submit', function(e) {
                // --- Attendee Validation ---
                let isValid = true;
                let firstError = null;

                // Reset all previous errors
                document.querySelectorAll('.validation-error').forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });
                document.querySelectorAll('.attendee-input').forEach(el => {
                    el.classList.remove('border-red-400', 'bg-red-50/50');
                });

                // Validate each field
                document.querySelectorAll('.attendee-input').forEach(input => {
                    const type = input.dataset.validate;
                    const value = input.value.trim();
                    const errorEl = input.closest('.space-y-2').querySelector('.validation-error');

                    if (type === 'phone') {
                        if (value && !/^\d+$/.test(value)) {
                            showFieldError(input, errorEl, 'Phone number harus diisi dengan angka saja.');
                            isValid = false;
                            if (!firstError) firstError = input;
                        } else if (value && value.length < 8) {
                            showFieldError(input, errorEl, 'Phone number minimal 8 digit.');
                            isValid = false;
                            if (!firstError) firstError = input;
                        }
                    }

                    if (type === 'id_number') {
                        if (value && !/^[a-zA-Z0-9]+$/.test(value)) {
                            showFieldError(input, errorEl, 'ID Number harus diisi dengan angka atau huruf saja (tanpa spasi/simbol).');
                            isValid = false;
                            if (!firstError) firstError = input;
                        } else if (value && value.length < 6) {
                            showFieldError(input, errorEl, 'ID Number minimal 6 karakter.');
                            isValid = false;
                            if (!firstError) firstError = input;
                        }
                    }

                    if (type === 'email') {
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (value && !emailPattern.test(value)) {
                            showFieldError(input, errorEl, 'Format email tidak valid. Gunakan format: example@mail.com');
                            isValid = false;
                            if (!firstError) firstError = input;
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }

                submitted = true;
            });

            function showFieldError(input, errorEl, message) {
                input.classList.add('border-red-400', 'bg-red-50/50');
                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.classList.remove('hidden');
                }
            }

            // Real-time: clear error when user starts correcting
            document.querySelectorAll('.attendee-input').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('border-red-400', 'bg-red-50/50');
                    const errorEl = this.closest('.space-y-2').querySelector('.validation-error');
                    if (errorEl) {
                        errorEl.textContent = '';
                        errorEl.classList.add('hidden');
                    }
                });
            });

            function performRelease() {
                if (submitted) return;
                const blob = new Blob([JSON.stringify({ _token: csrfToken })], { type: 'application/json' });
                navigator.sendBeacon(releaseUrl, blob);
            }

            // Picu pelepasan jika user navigasi keluar secara eksplisit (tombol Back/Close Tab)
            window.addEventListener('pagehide', performRelease);

            // --- VOUCHER AJAX SYSTEM ---
            const btnApplyVoucher = document.getElementById('btn-apply-voucher');
            const voucherInput = document.getElementById('voucher_input');
            const messageEl = document.getElementById('voucher-message');
            const hiddenVoucher = document.getElementById('hidden_voucher_code');
            const originalTotal = parseFloat(document.getElementById('summary-subtotal').getAttribute('data-value'));
            const eventId = document.getElementById('event_id').value;

btnApplyVoucher.addEventListener('click', function() {
                const code = voucherInput.value.trim();
                
                if (!code) {
                    messageEl.innerHTML = '<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> <span>Please enter a voucher code.</span>';
                    messageEl.className = 'mt-3 p-3 rounded-xl text-xs font-bold flex items-start gap-2 bg-red-50 text-red-600 block';
                    return;
                }

                btnApplyVoucher.disabled = true;
                btnApplyVoucher.innerHTML = '<svg class="animate-spin w-5 h-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';

                fetch("{{ route('voucher.apply') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: code,
                        event_id: eventId,
                        total_price: originalTotal
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btnApplyVoucher.disabled = false;
                    btnApplyVoucher.innerHTML = 'apply';

                    if (data.valid) {
                        messageEl.innerHTML = '<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>' + data.message + '</span>';
                        messageEl.className = 'mt-3 p-3 rounded-xl text-xs font-bold flex items-start gap-2 bg-teal-50 text-teal-700 block';
                        
                        hiddenVoucher.value = code;
                        voucherInput.disabled = true;
                        btnApplyVoucher.style.display = 'none'; // hide apply button

                        document.getElementById('discount-row').classList.remove('hidden');
                        document.getElementById('discount-percent-badge').textContent = data.discount_percent + '%';
                        document.getElementById('summary-discount').textContent = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.discount_amount);
                        document.getElementById('summary-total').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.final_price);

                    } else {
                        messageEl.innerHTML = '<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> <span>' + (data.message || 'Invalid voucher.') + '</span>';
                        messageEl.className = 'mt-3 p-3 rounded-xl text-xs font-bold flex items-start gap-2 bg-red-50 text-red-600 block';
                        hiddenVoucher.value = '';
                    }
                })
                .catch(err => {
                    btnApplyVoucher.disabled = false;
                    btnApplyVoucher.innerHTML = 'apply';
                    messageEl.innerHTML = '<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> <span>Failed to verify voucher. Check your connection.</span>';
                    messageEl.className = 'mt-3 p-3 rounded-xl text-xs font-bold flex items-start gap-2 bg-red-50 text-red-600 block';
                    console.error(err);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
