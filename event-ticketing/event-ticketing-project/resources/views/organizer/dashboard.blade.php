<x-app-layout>
    <div class="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-container">
                <div>
                    <h2 class="lowercase" style="font-size: 1.875rem; font-weight: 800; color: var(--text-dark); margin: 0;">
                        organizer hub.
                    </h2>
                    <p class="lowercase" style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                        manage your events easily, {{ $user->name }}
                    </p>
                </div>
            </div>
        </header>

        <!-- Quick Stats -->
        <section class="stats-grid">
            <div class="stat-card">
                <span class="text-xs font-bold uppercase tracking-widest text-muted" style="color: var(--text-muted);">Total Events</span>
                <h4>{{ number_format($stats['total_events']) }}</h4>
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #16a34a; margin: 0;">managed events.</p>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold uppercase tracking-widest text-muted" style="color: var(--text-muted);">Active Events</span>
                <h4>{{ number_format($stats['active_events']) }}</h4>
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #16a34a; margin: 0;">published live.</p>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold uppercase tracking-widest text-muted" style="color: var(--text-muted);">Drafts</span>
                <h4>{{ number_format($stats['draft_events']) }}</h4>
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #ca8a04; margin: 0;">waiting to publish.</p>
            </div>
        </section>

        <!-- Events List -->
        <section class="event-section">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
                <h3 class="lowercase" style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0;">
                    {{ $status === 'active' ? 'my active events' : ($status === 'draft' ? 'my drafts' : 'past events') }}
                </h3>
                <div class="tabs-container">
                    <a href="{{ route('organizer.dashboard', ['status' => 'active']) }}" class="tab-item lowercase {{ $status === 'active' ? 'active' : '' }}">active</a>
                    <a href="{{ route('organizer.dashboard', ['status' => 'draft']) }}" class="tab-item lowercase {{ $status === 'draft' ? 'active' : '' }}">drafts</a>
                    <a href="{{ route('organizer.dashboard', ['status' => 'past']) }}" class="tab-item lowercase {{ $status === 'past' ? 'active' : '' }}">past</a>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @forelse ($events as $event)
                    <div class="event-card">
                        <div class="event-banner-container">
                            @if($event->banner)
                                <img src="{{ asset('storage/' . $event->banner) }}" class="event-banner">
                            @else
                                <div class="event-banner" style="display: flex; align-items: center; justify-content: center; color: #bbbbbb;">
                                    <svg style="width: 2rem; height: 2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                                </div>
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                <span class="uppercase tracking-widest font-bold" style="font-size: 10px; padding: 0.25rem 0.5rem; background: var(--gray-light); border-radius: 4px;">{{ $event->status }}</span>
                                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">{{ $event->date->format('M d, Y') }}</span>
                            </div>
                            <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--text-dark); margin: 0 0 0.25rem 0;">{{ $event->title }}</h4>
                            <p class="lowercase" style="font-size: 12px; color: var(--text-muted); margin: 0;">{{ $event->location }}</p>
                        </div>
                        <div style="display: flex; align-items: center;">
                             <span class="uppercase font-bold tracking-widest" style="font-size: 10px; color: #999999; border: 1px solid var(--gray-border); padding: 0.5rem 1rem; border-radius: 0.75rem;">view only</span>
                        </div>
                    </div>
                @empty
                    <div style="background: white; padding: 4rem; border-radius: var(--radius-2xl); text-align: center; border: 1px solid var(--gray-border);">
                        <p class="lowercase" style="font-weight: 700; color: var(--text-muted); margin: 0;">no events assigned to you yet.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
