@forelse($events as $event)
    <a href="#" class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-300 border border-gray-100 flex flex-col h-full relative">
        <div class="relative h-48 overflow-hidden bg-gray-200 flex-shrink-0">
            @if($event->banner)
                <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @else
                <div class="w-full h-full bg-gradient-to-tr from-gray-200 to-gray-300"></div>
            @endif
            <div class="absolute top-4 left-4">
                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-[10px] font-extrabold uppercase tracking-widest text-[#555555] shadow-sm">
                    {{ $event->category?->name ?? 'Uncategorized' }}
                </span>
            </div>
        </div>
        <div class="p-5 flex-1 flex flex-col">
            <h3 class="font-extrabold text-lg text-[#333333] mb-2 line-clamp-2 leading-tight group-hover:text-black">{{ $event->title }}</h3>
            <div class="mt-auto space-y-2 mb-4">
                <p class="text-gray-500 text-xs font-semibold flex items-center gap-1.5 lowercase">
                    <svg class="w-4 h-4 text-[#AAAAAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="truncate">{{ $event->organizer->name }}</span>
                </p>
                <p class="text-gray-500 text-xs font-semibold flex items-center gap-1.5 lowercase">
                    <svg class="w-4 h-4 text-[#AAAAAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="truncate">{{ $event->location }}</span>
                </p>
                <p class="text-gray-500 text-xs font-semibold flex items-center gap-1.5 lowercase">
                    <svg class="w-4 h-4 text-[#AAAAAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ $event->date->translatedFormat('d M \'y') }} &bull; {{ $event->date->translatedFormat('H:i') }} WIB
                </p>
                {{-- City & Format badges --}}
                <div class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 uppercase tracking-wide">
                        {{ $event->city }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $event->format === 'online' ? 'bg-green-50 text-green-700' : 'bg-orange-50 text-orange-700' }}">
                        {{ $event->format === 'online' ? '🌐 Online' : '📍 Onsite' }}
                    </span>
                </div>
            </div>
            <div class="pt-4 border-t border-dashed border-gray-200 mt-auto flex justify-between items-end">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Mulai dari</span>
                <span class="text-base font-extrabold text-[#e02424]">
                    @php $minPrice = $event->ticketTypes->min('pivot.price'); @endphp
                    @if($minPrice === null) TBA
                    @elseif($minPrice == 0) Gratis
                    @else Rp{{ number_format($minPrice, 0, ',', '.') }}
                    @endif
                </span>
            </div>
        </div>
    </a>
@empty
    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 shadow-sm">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak ada event ditemukan.</h3>
        <p class="text-gray-500 font-medium">Coba gunakan filter atau kata kunci lain.</p>
    </div>
@endforelse
