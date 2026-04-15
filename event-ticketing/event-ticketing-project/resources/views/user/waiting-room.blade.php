<x-app-layout>
    <div class="min-h-screen bg-[#F0F2F5] font-sans antialiased flex flex-col items-center justify-center py-6 px-4">
        
        {{-- Main Container --}}
        <div class="w-full max-w-[1100px] bg-white shadow-2xl overflow-hidden flex flex-col md:flex-row relative">
            
            {{-- Left Column (Illustration & Ticket Availability) --}}
            <div class="w-full md:w-[60%] flex flex-col relative bg-[#EBEFF5]">
                
                {{-- Top Logo/Brand Area --}}
                <div class="p-6 md:p-8 flex items-center">
                    <div class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-[#1A237E]" fill="currentColor" viewBox="0 0 24 24"><path d="M19 19H5V5h14v14zM3 3v18h18V3H3z"/><path d="M7 7h10v2H7zm0 4h10v2H7zm0 4h7v2H7z"/></svg>
                        <h2 class="text-2xl font-black text-[#1A237E] uppercase tracking-wider">Flowtix</h2>
                    </div>
                </div>

                {{-- Mascot/Illustration --}}
                <div class="flex-grow flex items-center justify-center p-8 min-h-[350px] relative">
                    <div class="text-center z-10">
                        <div class="inline-flex items-center justify-center w-64 h-48 bg-transparent relative">
                            {{-- Placeholder for Mascot --}}
                            <div class="absolute inset-0 bg-[#34495E] rounded-3xl transform skew-y-3 opacity-10"></div>
                            <div class="w-40 h-40 bg-[#2980B9] rounded-2xl shadow-xl flex items-center justify-center relative border-b-8 border-[#1A5276]">
                                {{-- Sunglasses --}}
                                <div class="absolute top-10 w-24 h-8 flex justify-between gap-1 z-20">
                                    <div class="w-1/2 h-full bg-yellow-400 rounded-l-lg border-2 border-black flex items-center justify-center"><div class="w-2 h-4 bg-white/50 rounded-full rotate-45"></div></div>
                                    <div class="w-1/2 h-full bg-yellow-400 rounded-r-lg border-2 border-black flex items-center justify-center"><div class="w-2 h-4 bg-white/50 rounded-full rotate-45"></div></div>
                                </div>
                                {{-- Mouth --}}
                                <div class="w-4 h-4 bg-red-400 rounded-full border-2 border-black absolute top-24"></div>
                                {{-- Coffee Cup --}}
                                <div class="absolute -right-6 top-16 w-12 h-10 bg-orange-500 rounded-b-xl border-yellow-200">
                                   <div class="w-3 h-6 border-4 border-orange-500 rounded-full absolute -right-2 top-1"></div>
                                </div>
                                {{-- Steam --}}
                                <div class="absolute -right-4 top-8 text-xl text-gray-500 opacity-60">♨</div>
                                {{-- Legs --}}
                                <div class="absolute -bottom-8 left-6 w-4 h-12 border-l-4 border-b-4 border-black rounded-bl-lg"></div>
                                <div class="absolute -bottom-6 right-10 w-4 h-10 border-r-4 border-b-4 border-black rounded-br-lg"></div>
                                {{-- Arms --}}
                                <div class="absolute top-16 -left-8 w-10 h-10 border-l-4 border-t-4 border-black rounded-tl-xl opacity-80"></div>
                                <div class="absolute top-20 -right-4 w-8 h-4 border-t-4 border-black rotate-12 opacity-80"></div>
                            </div>
                        </div>
                        <div class="mt-4 text-[#7F8C8D] animate-pulse">
                            ♪ ♫ ♪
                        </div>
                    </div>
                </div>

                {{-- Ticket Availability Section --}}
                <div class="bg-[#1C203A] p-6 text-white rounded-tr-3xl relative z-10 w-[105%] md:w-full">
                    <div class="flex items-center gap-2 mb-4">
                        <h3 class="text-white font-extrabold text-sm tracking-wide">Ticket Availability Information</h3>
                        <svg class="w-4 h-4 text-gray-400 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    
                    <div id="wishlist-items" class="flex gap-3 overflow-x-auto pb-4 scrollbar-hide">
                        <!-- JS Injected Items -->
                    </div>
                    
                    <p class="text-gray-400 text-xs mt-2 italic font-medium">*this ticket availability information may be delayed</p>
                </div>
            </div>

            {{-- Right Column (Queue Info) --}}
            <div class="w-full md:w-[40%] bg-[#FDFDFD] flex flex-col relative min-h-screen md:min-h-0">
                
                {{-- Alert Banner --}}
                <div class="bg-[#FCD34D] p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-900 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-yellow-900 text-sm font-bold leading-tight">Don't close or leave this page, so your queue doesn't lost.</p>
                </div>

                <div class="p-8 flex flex-col flex-grow">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-6 tracking-tight">You are in the queue</h2>
                    
                    <div class="mb-6 pb-6 border-b border-gray-100">
                        <h3 class="text-gray-900 font-extrabold text-sm uppercase leading-relaxed">{{ $event->title }}</h3>
                        @if($event->date)
                        <p class="text-gray-600 text-sm font-semibold mt-1">{{ $event->date->format('d F Y - H:i') }}</p>
                        @endif
                    </div>

                    <p class="text-gray-700 text-sm leading-relaxed mb-8 font-medium">
                        Please wait to access the sales page. You will be given <strong class="text-black">15 minutes</strong> to complete your purchase form.
                    </p>

                    <div class="bg-[#F8F9FA] rounded-xl p-6 border border-gray-200 flex flex-col items-center justify-center mb-10 text-center relative overflow-hidden" id="queue-box">
                        <p class="text-gray-600 font-bold text-sm tracking-wide mb-4">Estimated Waiting Status</p>
                        
                        <div class="flex items-center justify-center gap-3">
                            <div class="bg-[#1E293B] text-white rounded-lg w-20 h-20 flex flex-col items-center justify-center shadow-lg">
                                <span id="queue-position" class="text-4xl font-black tabular-nums">{{ $myWait->position }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 font-bold mt-3 uppercase tracking-widest">Position</p>

                        <p id="queue-message" class="text-sm font-semibold text-gray-500 mt-6 min-h-[40px] px-2 flex items-center justify-center">
                            Retrieving live position data...
                        </p>
                    </div>

                    {{-- Bottom Status Bar --}}
                    <div class="mt-auto">
                        <div class="relative pt-1 border-b-2 border-dashed border-gray-300 pb-8">
                            <div class="overflow-hidden h-1 text-xs flex rounded bg-gray-200">
                                <div id="progress-bar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-[#1A237E] transition-all duration-1000"></div>
                            </div>
                            <div class="absolute -mt-3 -top-1" id="runner-icon" style="right: 100%; transition: right 0.5s ease;">
                                <svg class="w-6 h-6 text-[#1A237E]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                            </div>
                        </div>
                        <div class="flex justify-between items-end mt-4">
                            <p class="text-[10px] text-gray-500 font-semibold max-w-[70%] break-all leading-tight">
                                Queue ID: {{ Str::uuid()->toString() }}<br>
                                QPos-{{ $myWait->position }}
                            </p>
                            <a href="{{ route('events.show', $event->id_event) }}" class="text-xs text-gray-500 hover:text-red-600 underline transition font-bold">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    {{-- Custom Modal: Skip Category Confirmation --}}
    <div id="skip-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mb-6 mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Drop this ticket?</h3>
            <p class="text-gray-500 text-center text-sm mb-8">You will skip this ticket and proceed with your other selected tickets.</p>

            <div class="flex flex-col gap-3">
                <button id="confirm-skip-btn" onclick="submitSkip()" class="w-full py-3 bg-[#EE3D2F] hover:bg-red-700 text-white font-bold rounded-xl transition shadow-md">
                    Yes, Drop it
                </button>
                <button onclick="closeSkipModal()" class="w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- JavaScript Polling --}}
    <style>
        .waiting-dots::after {
            content: '.';
            animation: ellipsis 1.5s infinite steps(4, end);
            display: inline-block;
            width: 12px;
            text-align: left;
        }

        @keyframes ellipsis {
            0%   { content: ''; }
            25%  { content: '.'; }
            50%  { content: '..'; }
            75%  { content: '...'; }
            100% { content: ''; }
        }
    </style>

    <script>
        const eventId = {{ $event->id_event }};
        const statusUrlBase = `{{ route('queue.status', ':id') }}`.replace(':id', eventId);
        const skipUrl = `{{ route('queue.skip', ':id') }}`.replace(':id', eventId);

        const serverWishlist  = @json($serverWishlist);
        const serverTicketIds = @json($serverTicketIds);

        let ticketIds = serverTicketIds.map(id => String(id));
        let savedCart = {};
        serverWishlist.forEach(item => { savedCart[String(item.id)] = item; });

        if (ticketIds.length === 0) {
            const ss = JSON.parse(sessionStorage.getItem('flowtix_cart') || '{}');
            ticketIds = Object.keys(ss);
            savedCart = ss;
        }

        const wishlistList = document.getElementById('wishlist-items');

        function renderWishlistStatic() {
            wishlistList.innerHTML = '';
            if (ticketIds.length > 0) {
                ticketIds.forEach(id => {
                    const item = savedCart[id];
                    if (!item) return;
                    const safeName = item.name.length > 25 ? item.name.substring(0, 22) + '...' : item.name;
                    wishlistList.innerHTML += `
<div id="wishlist-item-${id}" class="bg-white rounded-lg w-40 flex-shrink-0 flex flex-col overflow-hidden relative shadow-md">
    <!-- Ticket circles -->
    <div class="absolute -left-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-[#1C203A] rounded-full"></div>
    <div class="absolute -right-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-[#1C203A] rounded-full"></div>
    
    <div class="p-4 border-b border-dashed border-gray-200 flex-grow flex items-center justify-center text-center">
        <h4 class="text-gray-800 font-black text-sm name-text line-clamp-2">${safeName}</h4>
    </div>
    <div class="py-3 px-4 text-center bg-gray-50 flex flex-col justify-center items-center min-h-[70px] card-bottom relative">
        <span class="status-badge text-xs font-black uppercase text-gray-400">Loading...</span>
        <button onclick="openSkipModal(${id})" class="skip-btn hidden mt-1.5 w-full bg-red-100 text-red-600 hover:bg-red-200 transition-colors rounded items-center justify-center py-1 text-[10px] font-bold uppercase gap-1 shadow-sm">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            DROP
        </button>
    </div>
</div>
                    `;
                });
            } else {
                wishlistList.innerHTML = `<p class="text-gray-400 text-sm py-4">No tickets selected.</p>`;
            }
        }

        renderWishlistStatic();

        // --- Modal Logic ---
        let currentSkipId = null;

        function getSkipModal() { return document.getElementById('skip-modal'); }
        function getSkipModalContent() { return getSkipModal()?.querySelector('div.bg-white'); }

        function openSkipModal(id) {
            currentSkipId = id;
            const skipModal = getSkipModal();
            const skipModalContent = getSkipModalContent();
            if (!skipModal) return;
            skipModal.classList.remove('hidden');
            setTimeout(() => {
                skipModal.classList.add('opacity-100');
                if (skipModalContent) skipModalContent.classList.add('scale-100');
            }, 10);
        }

        function closeSkipModal() {
            const skipModal = getSkipModal();
            const skipModalContent = getSkipModalContent();
            if (!skipModal) return;
            skipModal.classList.remove('opacity-100');
            if (skipModalContent) skipModalContent.classList.remove('scale-100');
            setTimeout(() => {
                skipModal.classList.add('hidden');
                currentSkipId = null;
            }, 300);
        }

        async function submitSkip() {
            if (!currentSkipId) return;

            const btn = document.getElementById('confirm-skip-btn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-block animate-spin mr-2">↻</span> Processing...';

            try {
                const res = await fetch(skipUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ticket_type_id: currentSkipId })
                });
                const data = await res.json();

                if (data.status === 'updated' || data.status === 'cancelled') {
                    delete savedCart[currentSkipId];
                    sessionStorage.setItem('flowtix_cart', JSON.stringify(savedCart));
                    ticketIds = Object.keys(savedCart);
                    renderWishlistStatic();
                    closeSkipModal();
                    checkStatus();
                }
            } catch (e) {
                console.error('Skip error:', e);
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }

        // Animate Runner
        const maxPos = parseInt('{{ $myWait->position }}');
        
        async function checkStatus() {
            try {
                const cleanIds = ticketIds.map(id => parseInt(id)).filter(id => !isNaN(id));
                let queryParams = cleanIds.map(id => `ticket_ids[]=${id}`).join('&');
                const fullUrl = statusUrlBase + (queryParams ? `?${queryParams}` : '');

                const res = await fetch(fullUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();

                if (data.wishlist_stock) {
                    data.wishlist_stock.forEach(item => {
                        const itemId = parseInt(item.id);
                        const itemRow = document.getElementById(`wishlist-item-${itemId}`);
                        if (itemRow) {
                            const badge = itemRow.querySelector('.status-badge');
                            const nameText = itemRow.querySelector('.name-text');
                            const skipBtn = itemRow.querySelector('.skip-btn');

                            if (data.status !== 'granted') {
                                if (item.is_sold_out) {
                                    hasSoldOut = true;
                                    badge.className = 'status-badge text-xs font-black uppercase text-red-700';
                                    badge.textContent = 'Sold Out';
                                    nameText.classList.add('opacity-40');

                                    const hasOthers = data.wishlist_stock.some(i => i.is_available);
                                    if (hasOthers) {
                                        skipBtn.classList.remove('hidden');
                                        skipBtn.classList.add('flex');
                                    }
                                } else if (!item.is_available) {
                                    hasSoldOut = true;
                                    // Change UI logic: yellow/orange badge with animated Waiting text
                                    badge.className = 'status-badge text-xs font-black uppercase text-amber-500 bg-amber-50 border border-amber-100';
                                    badge.innerHTML = 'Waiting<span class="waiting-dots"></span>';
                                    nameText.classList.add('opacity-40');

                                    const hasOthers = data.wishlist_stock.some(i => i.is_available);
                                    if (hasOthers) {
                                        skipBtn.classList.remove('hidden');
                                        skipBtn.classList.add('flex');
                                    }
                                } else {
                                    badge.className = 'status-badge text-xs font-black uppercase text-[#2ECC71]';
                                    badge.textContent = 'Available';
                                    nameText.classList.remove('opacity-40');
                                    skipBtn.classList.remove('flex');
                                    skipBtn.classList.add('hidden');
                                }
                            }
                        }
                    });

                    if (data.status !== 'granted') {
                        const hasSoldOut = data.wishlist_stock.some(i => !i.is_available);
                        if (hasSoldOut) {
                            const hasOthers = data.wishlist_stock.some(i => i.is_available);
                            document.getElementById('queue-message').innerHTML = hasOthers 
                                ? '<div class="flex flex-col items-center"><span class="text-red-500 font-extrabold">Some tickets are blocked/sold out!</span><span class="text-gray-500 text-xs mt-1">Click drop to continue.</span></div>' 
                                : '<div class="flex flex-col items-center"><span class="text-red-500 font-extrabold">All chosen tickets are blocked/sold out.</span><span class="text-gray-500 text-xs mt-1">Please wait for release.</span></div>';
                        } else {
                            document.getElementById('queue-message').innerHTML = 'Please hold on, looking for an open slot...';
                        }
                    }
                }

                if (data.status === 'granted') {
                    document.getElementById('queue-position').textContent = '✓';
                    document.getElementById('queue-message').textContent = 'Its your turn! Securing tickets...';
                    document.getElementById('queue-message').classList.add('text-green-600', 'font-black');
                    document.getElementById('queue-position').parentElement.classList.replace('bg-[#1E293B]', 'bg-[#2ECC71]');

                    ticketIds.forEach(id => {
                        const itemRow = document.getElementById(`wishlist-item-${id}`);
                        if (itemRow) {
                            itemRow.querySelector('.status-badge').className = 'status-badge text-xs font-black uppercase text-[#2ECC71]';
                            itemRow.querySelector('.status-badge').textContent = 'Reserved';
                        }
                    });

                    document.getElementById('runner-icon').style.right = '0%';
                    document.getElementById('progress-bar').style.width = '100%';

                    setTimeout(() => { window.location.href = data.redirect; }, 2000);
                    return;
                }

                if (data.status === 'sold_out') {
                    document.getElementById('queue-position').textContent = '✕';
                    document.getElementById('queue-message').textContent = data.message;
                    document.getElementById('queue-message').classList.add('text-red-600', 'font-black');
                    document.getElementById('queue-position').parentElement.classList.replace('bg-[#1E293B]', 'bg-red-600');
                    setTimeout(() => { window.location.href = data.redirect + '?error=sold_out'; }, 3500);
                    return;
                }

                if (data.status === 'waiting' && data.position !== undefined) {
                    document.getElementById('queue-position').textContent = data.position;
                    // Calculate mock progress bar based on position shrinking 
                    const currentPos = parseInt(data.position);
                    let progress = 0;
                    if(maxPos > 0) {
                         progress = ((maxPos - currentPos) / maxPos) * 100;
                    }
                    if(progress < 10) progress = 10;
                    document.getElementById('runner-icon').style.right = (100 - progress) + '%';
                    document.getElementById('progress-bar').style.width = progress + '%';
                }

            } catch (e) {
                console.error('Queue poll error:', e);
            }

            setTimeout(checkStatus, 4000);
        }

        setTimeout(checkStatus, 1000);
    </script>
</x-app-layout>