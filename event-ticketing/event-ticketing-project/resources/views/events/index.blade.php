<x-app-layout>
    <div class="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-container">
                <div>
                    <h2 class="lowercase" style="font-size: 1.875rem; font-weight: 800; color: var(--text-dark); margin: 0;">
                        manage events.
                    </h2>
                    <p class="lowercase" style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                        {{ $prefix }} panel — {{ $events->total() }} events total
                    </p>
                </div>
                @if(auth()->user()->role === 'admin')
                <div>
                    <a href="{{ route($prefix . '.events.create') }}" class="btn btn-primary lowercase">
                        + create new event
                    </a>
                </div>
                @endif
            </div>
        </header>

        <main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
            @if(session('success'))
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 1rem 1.5rem; border-radius: 1rem; margin-bottom: 2rem; font-weight: 700; font-size: 13px;" class="lowercase">
                    {{ session('success') }}
                </div>
            @endif

            <div style="background: white; border-radius: var(--radius-2xl); border: 1px solid var(--gray-border); overflow: hidden; box-shadow: var(--shadow-sm);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--gray-border);">
                                <th style="padding: 1.5rem 2rem; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em;">Event</th>
                                <th style="padding: 1.5rem 2rem; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em;">Category</th>
                                <th style="padding: 1.5rem 2rem; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em;">Organizer</th>
                                <th style="padding: 1.5rem 2rem; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em;">Date & Location</th>
                                <th style="padding: 1.5rem 2rem; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em;">Status</th>
                                @if(auth()->user()->role === 'admin')
                                <th style="padding: 1.5rem 2rem; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody style="text-transform: lowercase;">
                            @forelse ($events as $event)
                                <tr style="border-bottom: 1px solid #f9f9f9; transition: background 0.2s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='white'">
                                    <td style="padding: 1.5rem 2rem;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <div style="width: 3.5rem; height: 3.5rem; border-radius: 1rem; background: var(--gray-light); overflow: hidden; border: 1px solid var(--gray-border);">
                                                @if($event->banner)
                                                    <img src="{{ $event->banner_url }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc;">
                                                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-dark); text-transform: none;">{{ $event->title }}</h5>
                                                <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.5rem;">
                                                    @foreach($event->ticketTypes as $ticket)
                                                        <span style="font-size: 9px; font-weight: 700; color: var(--text-muted); background: var(--gray-light); padding: 2px 6px; border-radius: 4px; text-transform: uppercase;">{{ $ticket->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 1.5rem 2rem;">
                                        <span style="font-size: 10px; font-weight: 700; color: var(--text-main); background: #f0f0ef; padding: 4px 8px; border-radius: 6px; text-transform: uppercase;">{{ $event->category->name ?? 'none' }}</span>
                                    </td>
                                    <td style="padding: 1.5rem 2rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 12px; font-weight: 700;">
                                            <div class="avatar-circle" style="width: 1.5rem; height: 1.5rem; font-size: 8px;">{{ substr($event->organizer->name ?? 'PL', 0, 2) }}</div>
                                            <span>{{ $event->organizer->name ?? 'platform' }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 1.5rem 2rem;">
                                        <div style="font-size: 13px; font-weight: 700; color: var(--text-dark);">{{ $event->date->format('d M Y, H:i') }}</div>
                                        <div style="font-size: 10px; color: var(--text-muted); margin-top: 4px;">{{ $event->location }}</div>
                                        <div style="display: flex; gap: 0.4rem; margin-top: 6px; flex-wrap: wrap;">
                                            <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 50px; background: #eff6ff; color: #1d4ed8;">
                                                {{ $event->city }}
                                            </span>
                                            <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 50px; {{ $event->format === 'online' ? 'background: #f0fdf4; color: #15803d;' : 'background: #fff7ed; color: #c2410c;' }}">
                                                {{ $event->format }}
                                            </span>
                                        </div>
                                    </td>
                                    <td style="padding: 1.5rem 2rem;">
                                        @php
                                            $color = match($event->status) {
                                                'published' => 'color: #15803d; background: #f0fdf4;',
                                                'cancelled' => 'color: #b91c1c; background: #fef2f2;',
                                                default => 'color: #374151; background: #f3f4f6;'
                                            };
                                        @endphp
                                        <span style="font-size: 9px; font-weight: 800; text-transform: uppercase; padding: 4px 10px; border-radius: 50px; {!! $color !!}">{{ $event->status }}</span>
                                    </td>
                                    @if(auth()->user()->role === 'admin')
                                    <td style="padding: 1.5rem 2rem; text-align: right;">
                                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                            <a href="{{ route($prefix . '.events.edit', $event->id_event) }}" style="width: 2rem; height: 2rem; background: var(--gray-light); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: var(--text-dark); text-decoration: none;">
                                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route($prefix . '.events.destroy', $event->id_event) }}" method="POST" id="delete-form-{{ $event->id_event }}" style="margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('{{ $event->id_event }}')" style="width: 2rem; height: 2rem; background: #fef2f2; border: none; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #ef4444; cursor: pointer;">
                                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 6rem; text-align: center; color: var(--text-muted);">
                                        <div style="display: flex; flex-direction: column; items-center: center; gap: 1rem;">
                                            <svg style="width: 3rem; height: 3rem; margin: 0 auto; opacity: 0.2;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                                            <p style="font-weight: 700; margin: 0;">
                                                @if(auth()->user()->role === 'admin')
                                                    no events found. start by adding one to the platform.
                                                @else
                                                    no events have been assigned to your account yet.
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
                confirmButtonColor: '#000000',
                cancelButtonColor: '#F4F4F4',
                confirmButtonText: 'yes, delete it',
                cancelButtonText: 'cancel',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl font-bold px-6 py-3',
                    cancelButton: 'rounded-xl font-bold px-6 py-3 text-[#777777]'
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
