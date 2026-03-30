<x-app-layout>
    <div class="min-h-screen bg-[#E5E5E3] font-sans antialiased pb-24">
        <!-- Header -->
        <header class="bg-white/50 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#555555] tracking-tight lowercase">
                        manage events.
                    </h2>
                    <p class="text-[#777777] font-medium text-sm lowercase mt-1">
                        {{ $prefix }} panel — {{ $events->total() }} events total
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route($prefix . '.events.create') }}" class="inline-block px-5 py-2.5 bg-[#555555] text-white rounded-xl font-bold lowercase hover:bg-black transition shadow-sm text-sm">
                        + create new event
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 mt-12">
            @if(session('success'))
                <div class="mb-8 p-4 bg-green-100 border border-green-200 text-green-700 rounded-2xl font-bold lowercase text-sm shadow-sm transition-all animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[2.5rem] shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-8 py-6 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Event</th>
                                <th class="px-8 py-6 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Category</th>
                                <th class="px-8 py-6 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Organizer</th>
                                <th class="px-8 py-6 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Date & Location</th>
                                <th class="px-8 py-6 text-[10px] font-bold text-[#777777] uppercase tracking-widest">Status</th>
                                <th class="px-8 py-6 text-[10px] font-bold text-[#777777] uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 uppercase tracking-tight">
                            @forelse ($events as $event)
                                <tr class="hover:bg-[#F4F4F4]/50 transition group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 rounded-2xl bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-200 shadow-sm relative group-hover:shadow-md transition">
                                                @if($event->banner)
                                                    <img src="{{ asset('storage/' . $event->banner) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-[#999999] bg-[#F4F4F4]">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-[#444444] leading-tight text-lg lowercase tracking-tight">{{ $event->title }}</h5>
                                                <p class="text-[10px] font-medium text-[#777777] mt-1 lowercase">{{ Str::limit($event->description, 50) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="inline-flex px-3 py-1 bg-[#F4F4F4] text-[#555555] rounded-lg text-[10px] font-bold uppercase tracking-widest leading-none border border-gray-100">
                                            {{ $event->category->name ?? 'uncategorized' }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[8px] font-bold text-[#777777] uppercase">
                                                {{ substr($event->organizer->name ?? 'PL', 0, 2) }}
                                            </div>
                                            <span class="text-xs font-bold text-[#555555] lowercase tracking-tight">
                                                {{ $event->organizer->name ?? 'platform' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-1 lowercase">
                                            <div class="flex items-center gap-2 text-sm font-bold text-[#555555]">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $event->date->format('d M Y, H:i') }}
                                            </div>
                                            <div class="flex items-center gap-2 text-[10px] font-bold text-[#999999] tracking-wider">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                {{ $event->location }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $color = match($event->status) {
                                                'published' => 'bg-green-100 text-green-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                                'completed' => 'bg-blue-100 text-blue-700',
                                                default => 'bg-gray-100 text-gray-600'
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1 rounded-full {{ $color }} text-[9px] font-extrabold uppercase tracking-widest leading-none">
                                            {{ $event->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2 transition-opacity">
                                            <a href="{{ route($prefix . '.events.edit', $event->id_event) }}" class="w-9 h-9 flex items-center justify-center bg-[#F4F4F4] text-[#555555] rounded-xl hover:bg-black hover:text-white transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route($prefix . '.events.destroy', $event->id_event) }}" method="POST" id="delete-form-{{ $event->id_event }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('{{ $event->id_event }}')" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-32 text-center bg-[#F9F9F8]">
                                        <div class="flex flex-col items-center gap-4 text-[#999999]">
                                            <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                                            <p class="font-bold lowercase tracking-tight">no events found. start by creating one.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($events->hasPages())
                    <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-center">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'are you sure?',
                text: 'this event will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#F4F4F4',
                confirmButtonText: 'yes, delete it',
                cancelButtonText: '<span class="text-[#555555] font-bold">cancel</span>',
                background: '#ffffff',
                color: '#444444',
                customClass: {
                    popup: 'rounded-[2.5rem] p-6 shadow-xl',
                    title: 'text-2xl font-bold lowercase tracking-tight pt-4 text-[#444444]',
                    htmlContainer: 'text-[#777777] text-sm font-medium lowercase mb-4',
                    confirmButton: 'rounded-xl font-bold lowercase px-8 py-3.5 mx-2 text-sm',
                    cancelButton: 'rounded-xl font-bold lowercase px-8 py-3.5 mx-2 text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
