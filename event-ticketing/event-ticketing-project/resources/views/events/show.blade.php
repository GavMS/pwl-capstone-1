<x-app-layout>
    <div class="min-h-screen bg-[#F9F9F9] font-sans antialiased pb-24">
        {{-- Banner Section --}}
        <div class="w-full h-[40vh] md:h-[50vh] relative overflow-hidden bg-gray-900">
            @if($event->banner)
                <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}"
                    class="w-full h-full object-cover opacity-70">
            @else
                <div class="w-full h-full bg-gradient-to-r from-teal-500 to-blue-600 opacity-80"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-[#F9F9F9] via-transparent to-transparent"></div>
        </div>

        {{-- Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-32 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Main Column (Informations & Ticket Selection) --}}
                <div class="lg:col-span-8 space-y-8">
                    
                    {{-- Event Info Card --}}
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 text-xs font-extrabold uppercase tracking-widest rounded-lg mb-4 inline-block">
                            {{ $event->category?->name ?? 'Uncategorized' }}
                        </span>
                        
                        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
                            {{ $event->title }}
                        </h1>

                        {{-- Organizer Link --}}
                        <a href="{{ route('user.organizer.profile', $event->organizer_id) }}" class="inline-flex items-center gap-3 mb-8 hover:bg-gray-50 p-2 pr-4 rounded-full transition border border-transparent hover:border-gray-200 group">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-500 to-teal-400 flex items-center justify-center text-white font-bold text-lg shadow-inner uppercase">
                                {{ substr($event->organizer->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider leading-none mb-1">Penyelenggara</p>
                                <p class="text-sm font-extrabold text-gray-900 leading-none group-hover:text-teal-600 transition">{{ $event->organizer->name }}</p>
                            </div>
                        </a>

                        <div class="flex flex-wrap gap-6 mb-8 text-sm font-semibold text-gray-600 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center border border-gray-200 shadow-sm">
                                    <svg class="w-6 h-6 text-[#38b2ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Tanggal & Waktu</p>
                                    <p class="text-gray-900 text-sm md:text-base font-bold">{{ $event->date->translatedFormat('l, d F Y') }} &bull; {{ $event->date->translatedFormat('H:i') }} WIB</p>
                                </div>
                            </div>
                            <div class="w-full h-px bg-gray-200 md:hidden"></div>
                            <div class="hidden md:block w-px h-10 bg-gray-200"></div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center border border-gray-200 shadow-sm">
                                    <svg class="w-6 h-6 text-[#38b2ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Lokasi Event</p>
                                    <p class="text-gray-900 text-sm md:text-base font-bold">{{ $event->location }}, {{ $event->city }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="prose prose-teal max-w-none text-gray-600">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Deskripsi Event</h3>
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    {{-- Ticket Selection Layout --}}
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100" id="ticket-selection">
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">Pilih Tiket</h3>
                        <p class="text-gray-500 font-medium mb-6 text-sm">Pilih tiket yang tersedia secara bersamaan.</p>

                        @if($event->ticketTypes->isEmpty())
                            <div class="text-center p-6 bg-red-50 rounded-2xl border border-red-100">
                                <p class="text-red-500 font-bold">Tiket belum tersedia saat ini.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($event->ticketTypes as $ticket)
                                    <div class="border {{ $ticket->pivot->stock > 0 ? 'border-gray-200 hover:border-[#38b2ac]' : 'border-red-100 bg-red-50/30' }} rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-6 transition">
                                        <div class="flex-1">
                                            <h4 class="font-extrabold text-gray-900 text-lg mb-1">{{ $ticket->name }}</h4>
                                            <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $ticket->description ?? 'Tidak ada deskripsi' }}</p>
                                            <span class="text-[11px] px-2.5 py-1 bg-gray-100 {{ $ticket->pivot->stock < 10 ? 'text-red-500' : 'text-gray-500' }} rounded-md font-bold uppercase tracking-wider">
                                                Sisa: {{ $ticket->pivot->stock }} Tiket
                                            </span>
                                        </div>
                                        <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-3">
                                            <span class="font-black text-teal-600 text-xl whitespace-nowrap">
                                                {{ $ticket->pivot->price == 0 ? 'Gratis' : 'Rp' . number_format($ticket->pivot->price, 0, ',', '.') }}
                                            </span>
                                            @if($ticket->pivot->stock > 0)
                                                <div class="flex flex-col items-end">
                                                    <div class="flex items-center gap-3">
                                                        <button type="button" onclick="changeQty({{ $ticket->pivot->id }}, -1, '{{ $ticket->name }}', {{ $ticket->pivot->price }})" 
                                                            class="w-8 h-8 rounded-full bg-white border border-gray-200 text-teal-600 hover:bg-teal-50 hover:border-teal-300 font-bold flex items-center justify-center transition shadow-sm select-none">&minus;</button>
                                                        <input type="text" id="qty-{{ $ticket->pivot->id }}" value="0" readonly 
                                                            class="w-6 text-center border-none bg-transparent font-extrabold text-lg p-0 focus:ring-0 text-gray-900 select-none cursor-default">
                                                        <button type="button" onclick="changeQty({{ $ticket->pivot->id }}, 1, '{{ $ticket->name }}', {{ $ticket->pivot->price }}, {{ min(10, $ticket->pivot->stock) }})" 
                                                            class="w-8 h-8 rounded-full bg-[#38b2ac] hover:bg-teal-600 text-white font-bold flex items-center justify-center transition shadow-sm select-none">&plus;</button>
                                                    </div>
                                                    <p class="text-[10px] text-gray-400 font-semibold mt-1">Maks. {{ min(10, $ticket->pivot->stock) }} tix</p>
                                                </div>
                                            @else
                                                <span class="px-4 py-2 bg-red-100 text-red-600 font-black text-xs uppercase tracking-widest rounded-lg">Habis</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar Column (Your Order) --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl shadow-teal-500/5 border border-teal-100 p-6 sticky top-24">
                        <h3 class="text-lg font-extrabold text-gray-900 mb-4">Pesanan Anda</h3>
                        
                        <div id="empty-cart" class="text-center py-10">
                            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <p class="text-gray-500 text-sm font-semibold">Belum ada tiket yang dipilih.</p>
                        </div>

                        <!-- Checkout Form —> posts to CheckoutController -->
                        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form" class="hidden flex-col gap-4">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id_event }}">
                            {{-- Hidden inputs untuk cart; diisi oleh JavaScript --}}
                            <div id="hidden-inputs"></div>
                            <div id="cart-items" class="space-y-4 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                                <!-- JS Injected Content -->
                            </div>

                            <div class="pt-5 border-t border-dashed border-gray-200 mt-2">
                                @if ($errors->has('checkout'))
                                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
                                        ⚠️ {{ $errors->first('checkout') }}
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-semibold">
                                        ✅ {{ session('success') }}
                                    </div>
                                @endif
                                <div class="flex justify-between items-center mb-6">
                                    <span class="text-gray-500 font-semibold text-sm" id="total-tickets-label">Total (0 Tiket)</span>
                                    <span class="text-2xl font-black text-[#38b2ac]" id="total-price-label">Rp0</span>
                                </div>

                                <button type="button" onclick="attemptCheckout()" class="w-full py-3.5 rounded-xl bg-[#38b2ac] hover:bg-teal-600 text-white font-extrabold text-lg transition shadow-md shadow-teal-500/30 flex justify-center items-center gap-2 group">
                                    Beli Tiket
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    </style>

    <script>
        // State management
        const cart = {};

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        function changeQty(id, change, name, price, max = 10) {
            let input = document.getElementById('qty-' + id);
            let currentQty = parseInt(input.value) || 0;
            let newQty = currentQty + change;
            
            if (newQty < 0) newQty = 0;
            if (newQty > max) newQty = max;

            input.value = newQty;

            if (newQty === 0) {
                delete cart[id];
            } else {
                cart[id] = { id, name, price, qty: newQty, max };
            }

            renderCart();
        }

        function removeTicket(id) {
            let input = document.getElementById('qty-' + id);
            if(input) input.value = 0;
            delete cart[id];
            renderCart();
        }

        function renderCart() {
            const emptyState = document.getElementById('empty-cart');
            const cartForm = document.getElementById('checkout-form');
            const cartItemsList = document.getElementById('cart-items');
            
            cartItemsList.innerHTML = '';
            
            let totalQty = 0;
            let totalPrice = 0;
            
            const itemKeys = Object.keys(cart);

            if (itemKeys.length === 0) {
                emptyState.classList.remove('hidden');
                cartForm.classList.add('hidden');
                cartForm.classList.remove('flex');
            } else {
                emptyState.classList.add('hidden');
                cartForm.classList.remove('hidden');
                cartForm.classList.add('flex');

                itemKeys.forEach(id => {
                    let item = cart[id];
                    totalQty += item.qty;
                    totalPrice += (item.qty * item.price);

                    // Buat tampilan cart item
                    let html = `
                        <div class="flex items-start justify-between gap-3 bg-gray-50/50 p-3 rounded-xl border border-gray-100">
                            <div class="flex-1">
                                <h5 class="text-sm font-extrabold text-gray-900 leading-snug mb-1">${item.name}</h5>
                                <div class="text-xs font-bold text-teal-600 mb-2">${item.price === 0 ? 'Gratis' : formatRupiah(item.price)}</div>
                                
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="changeQty(${item.id}, -1, '${item.name}', ${item.price})" class="w-6 h-6 rounded-full bg-white border border-gray-200 text-teal-600 hover:bg-teal-50 font-bold flex items-center justify-center transition shadow-sm leading-none select-none">&minus;</button>
                                    <span class="font-extrabold text-sm text-gray-900 w-4 text-center select-none">${item.qty}</span>
                                    <button type="button" onclick="changeQty(${item.id}, 1, '${item.name}', ${item.price}, ${item.max})" class="w-6 h-6 rounded-full bg-teal-50 border border-teal-100 text-teal-600 hover:bg-teal-100 font-bold flex items-center justify-center transition shadow-sm leading-none select-none">&plus;</button>
                                </div>
                            </div>
                            <button type="button" onclick="removeTicket(${item.id})" class="text-gray-300 hover:text-red-500 transition p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    `;
                    cartItemsList.insertAdjacentHTML('beforeend', html);
                });
            }

            document.getElementById('total-tickets-label').innerText = `Total (${totalQty} Tiket)`;
            document.getElementById('total-price-label').innerText = totalPrice === 0 ? 'Gratis' : formatRupiah(totalPrice);
        }

        function attemptCheckout() {
            if (Object.keys(cart).length === 0) {
                alert('Silakan pilih minimal 1 tiket.');
                return false;
            }

            // Bangun hidden inputs dari cart state ke dalam form
            const hiddenContainer = document.getElementById('hidden-inputs');
            hiddenContainer.innerHTML = '';
            Object.values(cart).forEach(item => {
                hiddenContainer.innerHTML +=
                    `<input type="hidden" name="tickets[${item.id}][id]" value="${item.id}">
                     <input type="hidden" name="tickets[${item.id}][quantity]" value="${item.qty}">`;
            });

            // Submit form ke CheckoutController
            document.getElementById('checkout-form').submit();
        }
    </script>
</x-app-layout>