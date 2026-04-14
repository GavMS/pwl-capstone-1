<x-app-layout>
    <div class="min-h-screen bg-[#F9F9F9] font-sans antialiased pb-24">

        {{-- Header --}}
        <div class="bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">My Tickets 🎟️</h1>
                        <p class="text-gray-500 font-medium mt-1">All your e-tickets are right here.</p>
                    </div>
                    <a href="{{ route('user.explore') }}" class="hidden md:inline-flex items-center gap-2 px-5 py-2.5 bg-[#38b2ac] text-white rounded-full font-bold text-sm hover:bg-teal-600 transition shadow-md shadow-teal-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Find More Events
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- Success Toast Notification --}}
            @if(session('success'))
                <div id="success-toast" class="fixed top-24 right-5 transform translate-x-full opacity-0 transition-all duration-700 ease-in-out z-50 flex items-center bg-white border border-green-100 p-5 rounded-2xl shadow-2xl max-w-sm">
                    <div class="w-12 h-12 bg-green-50 text-green-500 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="ml-4 mr-8">
                        <p class="font-extrabold text-gray-900 leading-tight">Great News!</p>
                        <p class="text-gray-500 text-xs font-semibold mt-1">{{ session('success') }}</p>
                    </div>
                    <button onclick="document.getElementById('success-toast').classList.add('translate-x-full', 'opacity-0')" class="absolute top-3 right-3 text-gray-300 hover:text-gray-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toast = document.getElementById('success-toast');
                        setTimeout(() => {
                            toast.classList.remove('translate-x-full', 'opacity-0');
                            toast.classList.add('translate-x-0', 'opacity-100');
                        }, 300);

                        setTimeout(() => {
                            toast.classList.add('translate-x-full', 'opacity-0');
                        }, 6000);
                    });
                </script>
            @endif

            @if($tickets->isEmpty())
                {{-- Empty State --}}
                <div class="text-center py-24">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <h2 class="text-xl font-extrabold text-gray-800 mb-2">You Don't Have Any Tickets Yet</h2>
                    <p class="text-gray-500 font-medium mb-8">Discover exciting events and get your first ticket now!</p>
                    <a href="{{ route('user.explore') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-[#38b2ac] text-white rounded-full font-bold hover:bg-teal-600 transition shadow-md shadow-teal-500/20">
                        Explore Events
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tickets as $ticket)
                        @php
                            $ett   = $ticket->eventTicketType;
                            $event = $ett?->event;
                            $type  = $ett?->ticketType;
                        @endphp
                        <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
                            {{-- Event Banner --}}
                            <div class="h-36 relative overflow-hidden bg-gray-200 flex-shrink-0">
                                @if($event?->banner)
                                    <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-teal-400 to-blue-500"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                {{-- Status Badge --}}
                                <div class="absolute top-3 right-3">
                                    @if($ticket->status === 'active')
                                        <span class="px-3 py-1 bg-green-500 text-white text-[10px] font-extrabold uppercase tracking-widest rounded-full shadow">✓ Active</span>
                                    @elseif($ticket->status === 'used')
                                        <span class="px-3 py-1 bg-gray-500 text-white text-[10px] font-extrabold uppercase tracking-widest rounded-full shadow">Used</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-500 text-white text-[10px] font-extrabold uppercase tracking-widest rounded-full shadow">Cancelled</span>
                                    @endif
                                </div>
                                <div class="absolute bottom-3 left-4 right-4">
                                    <p class="text-white font-extrabold text-sm line-clamp-1 drop-shadow">{{ $event?->title ?? 'Event not found' }}</p>
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-5 flex flex-col flex-1">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">Ticket Type</p>
                                        <p class="font-extrabold text-gray-900">{{ $type?->name ?? '-' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">Unit Price</p>
                                        <p class="font-extrabold text-teal-600">
                                            {{ $ett && $ett->price == 0 ? 'Free' : 'Rp' . number_format($ett->price ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                @if($event)
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-4">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $event->date->format('d F Y, H:i') }}
                                </div>
                                @endif

                                {{-- QR Code Preview --}}
                                <div class="bg-gray-50 rounded-2xl p-4 flex flex-col items-center mb-4 border border-gray-100">
                                    <div class="mb-2">
                                        {!! QrCode::size(120)->margin(1)->generate($ticket->unique_code) !!}
                                    </div>
                                    <p class="text-[11px] font-mono font-bold text-gray-500 tracking-widest">{{ $ticket->unique_code }}</p>
                                </div>

                                <a href="{{ route('user.ticket.detail', $ticket->unique_code) }}" class="mt-auto block w-full text-center py-3 rounded-xl border-2 border-[#38b2ac] text-[#38b2ac] font-extrabold text-sm hover:bg-[#38b2ac] hover:text-white transition">
                                    View Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
