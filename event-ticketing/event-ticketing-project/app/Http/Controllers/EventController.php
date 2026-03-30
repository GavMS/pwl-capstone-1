<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\EventCategories;
use App\Models\Accounts;
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
        $query = Events::latest();
        
        // Filter by organizer if the user is an organizer
        if (auth()->user()->role === 'organizer') {
            $query->where('organizer_id', auth()->id());
        }

        $events = $query->paginate(10);
        $prefix = $this->routePrefix();
        return view('events.index', compact('events', 'prefix'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $prefix     = $this->routePrefix();
        $categories = EventCategories::all();
        $organizers = Accounts::where('role', 'organizer')->get();
        
        return view('events.create', compact('prefix', 'categories', 'organizers'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'nullable|exists:event_categories,id_category',
            'organizer_id' => 'nullable|exists:accounts,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'banner'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location'    => 'required|string|max:255',
            'date'        => 'required|date|after_or_equal:today',
            'status'      => 'required|in:draft,published,cancelled,completed',
        ], [
            'date.after_or_equal' => 'tanggal event tidak boleh tanggal yang sudah lewat.',
        ]);

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        // If the user is an organizer, force the organizer_id to be their own ID
        if (auth()->user()->role === 'organizer') {
            $validated['organizer_id'] = auth()->id();
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
        // Authorization check for organizers
        if (auth()->user()->role === 'organizer' && $event->organizer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $prefix     = $this->routePrefix();
        $categories = EventCategories::all();
        $organizers = Accounts::where('role', 'organizer')->get();
        
        return view('events.edit', compact('event', 'prefix', 'categories', 'organizers'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Events $event)
    {
        // Authorization check for organizers
        if (auth()->user()->role === 'organizer' && $event->organizer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'category_id'  => 'nullable|exists:event_categories,id_category',
            'organizer_id' => 'nullable|exists:accounts,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'banner'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location'    => 'required|string|max:255',
            'date'        => 'required|date|after_or_equal:today',
            'status'      => 'required|in:draft,published,cancelled,completed',
        ], [
            'date.after_or_equal' => 'tanggal event tidak boleh tanggal yang sudah lewat.',
        ]);

        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($event->banner) {
                Storage::disk('public')->delete($event->banner);
            }
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        // Prevent organizers from changing the organizer_id
        if (auth()->user()->role === 'organizer') {
            unset($validated['organizer_id']);
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
        // Authorization check for organizers
        if (auth()->user()->role === 'organizer' && $event->organizer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($event->banner) {
            Storage::disk('public')->delete($event->banner);
        }

        $event->delete();

        $prefix = $this->routePrefix();
        return redirect()->route("{$prefix}.events.index")
            ->with('success', 'Event berhasil dihapus!');
    }
}
