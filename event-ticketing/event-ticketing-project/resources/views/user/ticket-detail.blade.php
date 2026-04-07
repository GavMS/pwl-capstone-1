<x-app-layout>
    <div class="min-h-screen bg-[#F9F9F9] font-sans antialiased pb-24">

        <div class="max-w-xl mx-auto px-4 sm:px-6 py-10" id="print-area">
            {{-- Back button --}}
            <a href="{{ route('user.my-tickets') }}" class="no-print inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-800 transition mb-8">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to My Tickets
            </a>

            @php
                $ett   = $ticket->eventTicketType;
                $event = $ett?->event;
                $type  = $ett?->ticketType;
            @endphp

            {{-- E-Ticket Card --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                {{-- Header Gradient --}}
                <div class="bg-gradient-to-br from-[#38b2ac] to-teal-700 p-8 text-white text-center relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white/10 rounded-full"></div>
                    <p class="text-teal-200 font-bold text-xs uppercase tracking-widest mb-2">E-Ticket</p>
                    <h1 class="text-xl md:text-2xl font-extrabold leading-tight mb-1">{{ $event?->title ?? 'Event' }}</h1>
                    <p class="text-teal-100 text-sm font-semibold">{{ $type?->name ?? 'Ticket' }}</p>
                </div>

                {{-- Perforated line --}}
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-[#F9F9F9] rounded-full -ml-3 flex-shrink-0"></div>
                    <div class="flex-1 border-t-2 border-dashed border-gray-200 mx-2"></div>
                    <div class="w-6 h-6 bg-[#F9F9F9] rounded-full -mr-3 flex-shrink-0"></div>
                </div>

                {{-- QR Code Section --}}
                <div class="flex flex-col items-center px-8 py-8">
                    <div class="p-4 bg-white border-2 border-gray-100 rounded-2xl shadow-sm mb-4">
                        {!! QrCode::size(200)->margin(1)->generate($ticket->unique_code) !!}
                    </div>
                    <p class="font-mono text-sm font-bold text-gray-600 tracking-widest mb-1">{{ $ticket->unique_code }}</p>
                    <p class="text-xs font-semibold text-gray-400">Scan this QR Code at the event entrance</p>
                </div>

                {{-- Perforated line --}}
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-[#F9F9F9] rounded-full -ml-3 flex-shrink-0"></div>
                    <div class="flex-1 border-t-2 border-dashed border-gray-200 mx-2"></div>
                    <div class="w-6 h-6 bg-[#F9F9F9] rounded-full -mr-3 flex-shrink-0"></div>
                </div>

                {{-- Detail Info --}}
                <div class="px-8 py-6 space-y-4">
                    @if($event)
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Date & Time</span>
                        <span class="text-sm font-extrabold text-gray-900 text-right">{{ $event->date->format('l, d F Y') }}<br>{{ $event->date->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Location</span>
                        <span class="text-sm font-extrabold text-gray-900 text-right">{{ $event->location }}<br>{{ $event->city }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ticket Price</span>
                        <span class="text-sm font-extrabold text-teal-600">
                            {{ $ett && $ett->price == 0 ? 'Free' : 'Rp' . number_format($ett->price ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</span>
                        @if($ticket->status === 'active')
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-extrabold rounded-full">✓ Active</span>
                        @elseif($ticket->status === 'used')
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-extrabold rounded-full">Used</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-600 text-xs font-extrabold rounded-full">Cancelled</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ticket Holder</span>
                        <span class="text-sm font-extrabold text-gray-900">{{ auth()->user()->name }}</span>
                    </div>
                </div>

                {{-- Print Button --}}
                <div class="px-8 pb-8 no-print">
                    <button onclick="window.print()" class="w-full py-3.5 rounded-xl bg-gray-900 hover:bg-gray-700 text-white font-extrabold text-sm transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print / Save as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        @media print {
            /* Sembunyikan elemen bawaan dari layot (seperti sidebar dan navbar mobile) */
            aside, .sidebar, header, .no-print {
                display: none !important;
            }

            /* Reset margin/padding container web dan paksa background putih */
            body, html, .min-h-screen, main, .layout-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                background-color: white !important;
            }
            .layout-wrapper {
                display: block !important; 
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                background: white !important;
            }

            /* Atur ulang ukuran kontainer agar di tengah pas diprint */
            #print-area {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
                background: white !important;
            }

            /* Hilangkan bayangan dan sisa-sisa overlay gelap */
            * {
                box-shadow: none !important;
                text-shadow: none !important;
                filter: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .shadow-xl {
                border: 1px solid #e5e7eb !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
