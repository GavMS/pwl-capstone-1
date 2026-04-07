<x-app-layout>
<style>
/* ── Filter Modal via CSS :target ── */
#filter-modal { display: none; position: fixed; inset: 0; z-index: 50; align-items: flex-end; justify-content: center; padding: 1rem; }
@media(min-width:640px){ #filter-modal { align-items: center; } }
#filter-modal:target { display: flex; }
.tab-panel { display: none; }
</style>

<div class="min-h-screen bg-[#F9F9F9] font-sans antialiased pb-24">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        {{-- Search Bar --}}
        <form action="{{ route('user.explore') }}" method="GET" class="mb-6" id="search-form">
            @foreach(request()->except(['search','_token']) as $k => $v)
                @if($v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endif
            @endforeach
            <div class="border-b border-gray-300 pb-2 flex gap-4 items-center">
                <svg class="w-6 h-6 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="search-input" type="text" name="search" value="{{ request('search') }}" placeholder="Search events..." autocomplete="off"
                    class="w-full bg-transparent border-none focus:ring-0 text-xl md:text-2xl font-bold placeholder-gray-400 p-0 outline-none">
            </div>
        </form>

        {{-- Tab Switcher --}}
        <div class="flex justify-center mb-8">
            <div class="inline-flex bg-white rounded-full p-1 border border-gray-200 shadow-sm">
                <a href="{{ route('user.explore') }}" class="px-8 py-2.5 rounded-full text-sm font-bold bg-[#f4fbfc] text-[#38b2ac] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Event
                </a>
                <a href="{{ route('user.explore.creators') }}" class="px-8 py-2.5 rounded-full text-sm font-bold text-gray-500 hover:text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Creators
                </a>
            </div>
        </div>

        {{-- Filter Pills Row --}}
        <div class="flex flex-wrap gap-2 mb-8 items-center">
            <a href="#filter-modal" class="px-4 py-2.5 rounded-full border border-gray-300 bg-white hover:bg-gray-50 flex items-center gap-2 text-sm font-semibold text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
                @if(request()->hasAny(['category','location','tanggal','waktu','harga','format']))
                    <span class="w-2 h-2 rounded-full bg-[#38b2ac] inline-block"></span>
                @endif
            </a>

            {{-- Active filter badges --}}
            @if(request('category'))
                <span class="px-3 py-1.5 bg-[#f0fdfa] text-[#38b2ac] border border-[#38b2ac] rounded-full text-xs font-bold">
                    {{ $categories->firstWhere('id_category', request('category'))?->name ?? 'Category' }}
                    <a href="{{ route('user.explore', array_merge(request()->except(['category','_token']), [])) }}" class="ml-1 opacity-60 hover:opacity-100">×</a>
                </span>
            @endif
            @if(request('location'))
                <span class="px-3 py-1.5 bg-[#f0fdfa] text-[#38b2ac] border border-[#38b2ac] rounded-full text-xs font-bold">
                    {{ request('location') }}
                    <a href="{{ route('user.explore', array_merge(request()->except(['location','_token']), [])) }}" class="ml-1 opacity-60 hover:opacity-100">×</a>
                </span>
            @endif
            @if(request('tanggal'))
                <span class="px-3 py-1.5 bg-[#f0fdfa] text-[#38b2ac] border border-[#38b2ac] rounded-full text-xs font-bold">
                    {{ request('tanggal') == 'custom' ? (request('custom_date') ?? 'Pick Date') : ucfirst(str_replace('_',' ',request('tanggal'))) }}
                    <a href="{{ route('user.explore', array_merge(request()->except(['tanggal','custom_date','_token']), [])) }}" class="ml-1 opacity-60 hover:opacity-100">×</a>
                </span>
            @endif
            @if(request('waktu'))
                <span class="px-3 py-1.5 bg-[#f0fdfa] text-[#38b2ac] border border-[#38b2ac] rounded-full text-xs font-bold">
                    {{ ucfirst(request('waktu')) }}
                    <a href="{{ route('user.explore', array_merge(request()->except(['waktu','_token']), [])) }}" class="ml-1 opacity-60 hover:opacity-100">×</a>
                </span>
            @endif
            @if(request('harga'))
                <span class="px-3 py-1.5 bg-[#f0fdfa] text-[#38b2ac] border border-[#38b2ac] rounded-full text-xs font-bold">
                    {{ ['gratis'=>'Free','under100'=>'<Rp100k','100to250'=>'Rp100-250k','251to500'=>'Rp251-500k','over500'=>'>Rp500k'][request('harga')] ?? request('harga') }}
                    <a href="{{ route('user.explore', array_merge(request()->except(['harga','_token']), [])) }}" class="ml-1 opacity-60 hover:opacity-100">×</a>
                </span>
            @endif
            @if(request('format'))
                <span class="px-3 py-1.5 bg-[#f0fdfa] text-[#38b2ac] border border-[#38b2ac] rounded-full text-xs font-bold">
                    {{ request('format') == 'online' ? 'Online' : 'Onsite' }}
                    <a href="{{ route('user.explore', array_merge(request()->except(['format','_token']), [])) }}" class="ml-1 opacity-60 hover:opacity-100">×</a>
                </span>
            @endif
            @if(request()->hasAny(['category','location','tanggal','waktu','harga','format']))
                <a href="{{ route('user.explore', request('search') ? ['search'=>request('search')] : []) }}"
                   class="px-4 py-2 text-red-500 hover:bg-red-50 text-sm font-bold rounded-full flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset
                </a>
            @endif
        </div>

        {{-- Event Count --}}
        <p class="text-gray-600 font-medium mb-6">{{ $events->total() }} {{ $events->total() === 1 ? 'event' : 'events' }} available</p>

        {{-- Events Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($events as $event)
                <a href="{{ route('events.show', $event->id_event) }}" class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-300 border border-gray-100 flex flex-col h-full">
                    <div class="relative h-48 overflow-hidden bg-gray-200 flex-shrink-0">
                        @if($event->banner)
                            <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-gray-200 to-gray-300"></div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 rounded-lg text-[10px] font-extrabold uppercase tracking-widest text-[#555555] shadow-sm">
                                {{ $event->category?->name ?? 'Uncategorized' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-extrabold text-lg text-[#333333] mb-2 line-clamp-2 leading-tight">{{ $event->title }}</h3>
                        <div class="mt-auto space-y-2 mb-4">
                            <p class="text-gray-500 text-xs font-semibold flex items-center gap-1.5 lowercase">
                                <svg class="w-4 h-4 text-[#AAAAAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="truncate">{{ $event->organizer->name }}</span>
                            </p>
                            <p class="text-gray-500 text-xs font-semibold flex items-center gap-1.5 lowercase">
                                <svg class="w-4 h-4 text-[#AAAAAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="truncate">{{ $event->location }}</span>
                            </p>
                            <p class="text-gray-500 text-xs font-semibold flex items-center gap-1.5 lowercase">
                                <svg class="w-4 h-4 text-[#AAAAAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $event->date->translatedFormat('d M \'y') }} &bull; {{ $event->date->translatedFormat('H:i') }} WIB
                            </p>
                            {{-- City & Format badges — below date, left-aligned --}}
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 uppercase tracking-wide">
                                    {{ $event->city }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $event->format === 'online' ? 'bg-green-50 text-green-700' : 'bg-orange-50 text-orange-700' }}">
                                    {{ $event->format === 'online' ? '🌐 Online' : '📍 Onsite' }}
                                </span>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-dashed border-gray-200 flex justify-between items-end">
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Starting from</span>
                            <span class="text-base font-extrabold text-[#e02424]">
                                @php $minPrice = $event->ticketTypes->min('pivot.price'); @endphp
                                @if($minPrice === null) TBA @elseif($minPrice == 0) Free @else Rp{{ number_format($minPrice, 0, ',', '.') }} @endif
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No events found.</h3>
                    <p class="text-gray-500 font-medium">Try using different filters or keywords.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">{{ $events->links() }}</div>
    </div>
</div>

{{-- ── FILTER MODAL (CSS :target trick) ── --}}
<div id="filter-modal">
    {{-- Backdrop --}}
    <a href="#" class="absolute inset-0 bg-gray-900/50" aria-label="Close filter"></a>

    {{-- Modal Panel --}}
    <div class="relative bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl w-full max-w-2xl flex flex-col" style="max-height:88vh;">

        {{-- Header --}}
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">Filter</h3>
            <a href="#" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>

        {{-- Body --}}
        <form action="{{ route('user.explore') }}" method="GET" class="flex flex-1 overflow-hidden min-h-0" id="filter-form">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

            {{-- Left Tab Nav --}}
            <div class="flex-shrink-0 w-32 bg-gray-50 border-r border-gray-100 flex flex-col" id="tab-nav">
                @foreach([
                    ['id'=>'tab-kat','label'=>'Category'],
                    ['id'=>'tab-lok','label'=>'Location'],
                    ['id'=>'tab-tgl','label'=>'Date'],
                    ['id'=>'tab-wkt','label'=>'Time'],
                    ['id'=>'tab-hrg','label'=>'Price'],
                    ['id'=>'tab-fmt','label'=>'Format'],
                ] as $tab)
                <button type="button" onclick="switchTab('{{ $tab['id'] }}')"
                    id="btn-{{ $tab['id'] }}"
                    class="tab-btn px-5 py-4 text-left font-bold text-sm text-gray-500 hover:bg-gray-100 transition border-l-4 border-transparent">
                    {{ $tab['label'] }}
                </button>
                @endforeach
            </div>

            {{-- Right Content --}}
            <div class="flex-1 overflow-y-auto p-6">

                {{-- Category --}}
                <div id="tab-kat" class="tab-panel">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Category</h4>
                    <div class="space-y-1">
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="w-5 h-5 accent-teal-500">
                            <span class="ml-3 font-medium text-gray-700">All Categories</span>
                        </label>
                        @foreach($categories as $cat)
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="category" value="{{ $cat->id_category }}" {{ request('category') == $cat->id_category ? 'checked' : '' }} class="w-5 h-5 accent-teal-500">
                            <span class="ml-3 font-medium text-gray-700">{{ $cat->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Location --}}
                <div id="tab-lok" class="tab-panel">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Location</h4>
                    <div class="space-y-1">
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="location" value="" {{ !request('location') ? 'checked' : '' }} class="w-5 h-5 accent-teal-500">
                            <span class="ml-3 font-medium text-gray-700">All Locations</span>
                        </label>
                        @foreach($popularLocations as $loc)
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="location" value="{{ $loc }}" {{ request('location') == $loc ? 'checked' : '' }} class="w-5 h-5 accent-teal-500">
                            <span class="ml-3 font-medium text-gray-700">{{ $loc }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Date --}}
                <div id="tab-tgl" class="tab-panel">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Date</h4>
                    <div class="space-y-1">
                        @foreach([
                            ['val'=>'','label'=>'All Dates'],
                            ['val'=>'today','label'=>'Today'],
                            ['val'=>'tomorrow','label'=>'Tomorrow'],
                            ['val'=>'this_week','label'=>'This Week'],
                            ['val'=>'this_weekend','label'=>'This Weekend'],
                            ['val'=>'next_week','label'=>'Next Week'],
                            ['val'=>'next_weekend','label'=>'Next Weekend'],
                            ['val'=>'this_month','label'=>'This Month'],
                        ] as $opt)
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="tanggal" value="{{ $opt['val'] }}" {{ request('tanggal')==$opt['val'] ? 'checked' : '' }} class="w-5 h-5 accent-teal-500">
                            <span class="ml-3 font-medium text-gray-700">{{ $opt['label'] }}</span>
                        </label>
                        @endforeach
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="tanggal" value="custom" id="ftgl-custom" {{ request('tanggal')=='custom' ? 'checked' : '' }} class="w-5 h-5 accent-teal-500" onchange="document.getElementById('custom-date-wrap').style.display='block'">
                            <span class="ml-3 font-medium text-gray-700">Pick a Date</span>
                        </label>
                        <div id="custom-date-wrap" class="px-3 pt-2" style="{{ request('tanggal')=='custom' ? '' : 'display:none' }}">
                            <input type="date" name="custom_date" value="{{ request('custom_date') }}" min="{{ today()->format('Y-m-d') }}"
                                class="w-full px-4 py-2.5 border border-[#38b2ac] text-[#38b2ac] rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#38b2ac] outline-none cursor-pointer">
                        </div>
                    </div>
                </div>

                {{-- Time --}}
                <div id="tab-wkt" class="tab-panel">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Time</h4>
                    <div class="space-y-1">
                        @foreach([
                            ['val'=>'','label'=>'All Times','sub'=>''],
                            ['val'=>'pagi','label'=>'Morning','sub'=>'05:00 – 10:00'],
                            ['val'=>'siang','label'=>'Afternoon','sub'=>'10:00 – 16:00'],
                            ['val'=>'malam','label'=>'Evening','sub'=>'16:00 – 05:00'],
                        ] as $opt)
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="waktu" value="{{ $opt['val'] }}" {{ request('waktu')==$opt['val'] ? 'checked' : '' }} class="w-5 h-5 accent-teal-500">
                            <span class="ml-3 font-medium text-gray-700">
                                {{ $opt['label'] }}
                                @if($opt['sub']) <span class="text-[#38b2ac] font-bold">({{ $opt['sub'] }})</span> @endif
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Price --}}
                <div id="tab-hrg" class="tab-panel">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Price</h4>
                    <div class="space-y-1">
                        @foreach([
                            ['val'=>'','label'=>'All Prices'],
                            ['val'=>'gratis','label'=>'Free'],
                            ['val'=>'under100','label'=>'Under Rp100,000'],
                            ['val'=>'100to250','label'=>'Rp100,000 – Rp250,000'],
                            ['val'=>'251to500','label'=>'Rp251,000 – Rp500,000'],
                            ['val'=>'over500','label'=>'Over Rp500,000'],
                        ] as $opt)
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="harga" value="{{ $opt['val'] }}" {{ request('harga')==$opt['val'] ? 'checked' : '' }} class="w-5 h-5 accent-teal-500">
                            <span class="ml-3 font-medium text-gray-700">{{ $opt['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Format --}}
                <div id="tab-fmt" class="tab-panel">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Format</h4>
                    <div class="space-y-1">
                        @foreach([
                            ['val'=>'','label'=>'All Formats'],
                            ['val'=>'onsite','label'=>'Onsite Event'],
                            ['val'=>'online','label'=>'Online Event'],
                        ] as $opt)
                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="format" value="{{ $opt['val'] }}" {{ request('format')==$opt['val'] ? 'checked' : '' }} class="w-5 h-5 accent-teal-500">
                            <span class="ml-3 font-medium text-gray-700">{{ $opt['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>

        {{-- Footer --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between rounded-b-3xl flex-shrink-0">
            <a href="{{ route('user.explore', request('search') ? ['search'=>request('search')] : []) }}"
               class="text-sm font-bold text-gray-500 hover:text-gray-900 transition">Reset</a>
            <button type="submit" form="filter-form"
                class="px-8 py-2.5 bg-[#38b2ac] hover:bg-teal-600 text-white font-bold rounded-full transition shadow-md shadow-teal-500/30">
                Apply
            </button>
        </div>
    </div>
</div>

{{-- Vanilla JS: tab switching + live search debounce --}}
<script>
// ── Tab switching ──
function switchTab(id) {
    document.querySelectorAll('.tab-panel').forEach(function(p){ p.style.display = 'none'; });
    document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('bg-white','text-gray-900','border-l-teal-500'); b.classList.add('text-gray-500','border-transparent'); });
    document.getElementById(id).style.display = 'block';
    var btn = document.getElementById('btn-' + id);
    btn.classList.add('bg-white','text-gray-900','border-l-teal-500');
    btn.classList.remove('text-gray-500','border-transparent');
}
// Show first tab by default on load
switchTab('tab-kat');

// ── Live search debounce (400ms) ──
var _st;
document.getElementById('search-input').addEventListener('input', function(){
    clearTimeout(_st);
    _st = setTimeout(function(){ document.getElementById('search-form').submit(); }, 400);
});
</script>

</x-app-layout>
