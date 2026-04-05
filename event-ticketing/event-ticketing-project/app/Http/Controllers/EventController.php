<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\EventCategories;
use App\Models\Accounts;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Exception;

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
        $ticketTypes = TicketType::all();
        
        return view('events.create', compact('prefix', 'categories', 'organizers', 'ticketTypes'));
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
            'description' => 'required|string|max:5000',
            'banner'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location'    => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'format'      => 'required|in:onsite,online',
            'date'        => 'required|date|after_or_equal:' . now()->addDays(30)->toDateString(),
            'status'      => 'required|in:draft,published,cancelled,completed',
            'tickets'     => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id_ticket_type',
            'tickets.*.price' => 'required|integer|min:0',
            'tickets.*.stock' => 'required|integer|min:1',
        ], [
            'date.after_or_equal' => 'tanggal event minimal 30 hari dari sekarang (D-30) untuk persiapan ticketing.',
            'tickets.required' => 'minimal satu jenis tiket harus ditambahkan.',
            'description.max' => 'deskripsi terlalu panjang (maksimal 5000 karakter).',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $validated['banner'] = $bannerPath;
        }

        if (auth()->user()->role === 'organizer') {
            $validated['organizer_id'] = auth()->id();
        }

        DB::beginTransaction();
        try {
            $event = Events::create($validated);

            $tickets = [];
            foreach ($request->tickets as $ticket) {
                $tickets[$ticket['ticket_type_id']] = [
                    'price' => $ticket['price'],
                    'stock' => $ticket['stock'],
                ];
            }
            $event->ticketTypes()->sync($tickets);

            if (!empty($validated['organizer_id'])) {
                $organizer = Accounts::find($validated['organizer_id']);
                if ($organizer) {
                    Mail::send('emails.organizer_assigned', [
                        'organizer' => $organizer,
                        'event'     => $event,
                    ], function ($message) use ($organizer) {
                        $message->to($organizer->email, $organizer->name)
                                ->subject('You Have Been Assigned to an Event – Flowtix');
                    });
                }
            }

            DB::commit();
            
            $prefix = $this->routePrefix();
            return redirect()->route("{$prefix}.events.index")
                ->with('success', 'Event berhasil dibuat!');

        } catch (Exception $e) {
            DB::rollBack();
            // Hapus file banner yang terlanjur di-upload jika database error
            if ($bannerPath) {
                Storage::disk('public')->delete($bannerPath);
            }
            
            return back()->withInput()->withErrors(['error' => 'gagal menyimpan data. silakan periksa kembali input anda.']);
        }
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
        $ticketTypes = TicketType::all();
        $event->load('ticketTypes');
        
        return view('events.edit', compact('event', 'prefix', 'categories', 'organizers', 'ticketTypes'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Events $event)
    {
        if (auth()->user()->role === 'organizer' && $event->organizer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'category_id'  => 'nullable|exists:event_categories,id_category',
            'organizer_id' => 'nullable|exists:accounts,id',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string|max:5000',
            'banner'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location'     => 'required|string|max:255',
            'city'         => 'required|string|max:100',
            'format'       => 'required|in:onsite,online',
            'date'         => 'required|date|after_or_equal:' . now()->addDays(30)->toDateString(),
            'status'       => 'required|in:draft,published,cancelled,completed',
            'tickets'      => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id_ticket_type',
            'tickets.*.price' => 'required|integer|min:0',
            'tickets.*.stock' => 'required|integer|min:1',
        ], [
            'date.after_or_equal' => 'tanggal event minimal 30 hari dari sekarang (D-30) untuk persiapan ticketing.',
            'tickets.required' => 'minimal satu jenis tiket harus ditambahkan.',
            'description.max' => 'deskripsi terlalu panjang (maksimal 5000 karakter).',
        ]);

        $newBannerPath = null;
        if ($request->hasFile('banner')) {
            $newBannerPath = $request->file('banner')->store('banners', 'public');
            $validated['banner'] = $newBannerPath;
        } elseif ($request->input('delete_banner') == '1') {
            // User explicitly deleted the existing banner
            $validated['banner'] = null;
        }

        if (auth()->user()->role === 'organizer') {
            unset($validated['organizer_id']);
        }

        $oldOrganizerId = $event->organizer_id;
        $oldBannerPath = $event->banner;

        DB::beginTransaction();
        try {
            $event->update($validated);

            $tickets = [];
            foreach ($request->tickets as $ticket) {
                $tickets[$ticket['ticket_type_id']] = [
                    'price' => $ticket['price'],
                    'stock' => $ticket['stock'],
                ];
            }
            $event->ticketTypes()->sync($tickets);

            $newOrganizerId = $event->fresh()->organizer_id;
            if ($newOrganizerId && $newOrganizerId !== $oldOrganizerId) {
                $organizer = Accounts::find($newOrganizerId);
                if ($organizer) {
                    Mail::send('emails.organizer_assigned', [
                        'organizer' => $organizer,
                        'event'     => $event->fresh(),
                    ], function ($message) use ($organizer) {
                        $message->to($organizer->email, $organizer->name)
                                ->subject('You Have Been Assigned to an Event – Flowtix');
                    });
                }
            }

            DB::commit();

            // Jika update sukses dan ada banner baru / banner dihapus, hapus file lama
            if ($oldBannerPath && ($newBannerPath || $request->input('delete_banner') == '1')) {
                Storage::disk('public')->delete($oldBannerPath);
            }

            $prefix = $this->routePrefix();
            return redirect()->route("{$prefix}.events.index")
                ->with('success', 'Event berhasil diperbarui!');

        } catch (Exception $e) {
            DB::rollBack();
            // Jika gagal, hapus file banner baru yang terlanjur di-upload
            if ($newBannerPath) {
                Storage::disk('public')->delete($newBannerPath);
            }

            return back()->withInput()->withErrors(['error' => 'gagal memperbarui data. silakan periksa kembali input anda.']);
        }
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
