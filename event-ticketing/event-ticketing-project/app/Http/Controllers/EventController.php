<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Determine the route prefix based on the current user's role.
     */
    private function routePrefix(): string
    {
        return auth()->user()->role === 'admin' ? 'admin' : 'organizer';
    }

    /**
     * Display a listing of events.
     */
    public function index()
    {
        $events = Events::latest()->paginate(10);
        $prefix = $this->routePrefix();
        return view('events.index', compact('events', 'prefix'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $prefix = $this->routePrefix();
        return view('events.create', compact('prefix'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'banner'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location'    => 'required|string|max:255',
            'date'        => 'required|date',
            'status'      => 'required|in:draft,published,cancelled,completed',
        ]);

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        Events::create($validated);

        $prefix = $this->routePrefix();
        return redirect()->route("{$prefix}.events.index")
            ->with('success', 'Event berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Events $event)
    {
        $prefix = $this->routePrefix();
        return view('events.edit', compact('event', 'prefix'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Events $event)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'banner'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location'    => 'required|string|max:255',
            'date'        => 'required|date',
            'status'      => 'required|in:draft,published,cancelled,completed',
        ]);

        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($event->banner) {
                Storage::disk('public')->delete($event->banner);
            }
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $event->update($validated);

        $prefix = $this->routePrefix();
        return redirect()->route("{$prefix}.events.index")
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Events $event)
    {
        if ($event->banner) {
            Storage::disk('public')->delete($event->banner);
        }

        $event->delete();

        $prefix = $this->routePrefix();
        return redirect()->route("{$prefix}.events.index")
            ->with('success', 'Event berhasil dihapus!');
    }
}
