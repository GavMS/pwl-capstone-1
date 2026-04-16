<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\EventCategories;
use App\Models\Accounts;
use App\Models\TicketType;
use App\Models\ShoppingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * EventController
 *
 * Full CRUD operations for events (Admin) and read-only access (Organizer).
 * Also serves the public event detail page (User).
 *
 * Used by: admin.events.*, organizer.events.*, events.show routes.
 */
class EventController extends Controller
{
    /**
     * Determine route prefix based on current user's role.
     * Admin → 'admin', Organizer → 'organizer'.
     */
    private function routePrefix(): string
    {
        return auth()->user()->role === 'admin' ? 'admin' : 'organizer';
    }

    /**
     * List events — Admin sees all, Organizer sees only their own.
     */
    public function index()
    {
        $query = Events::latest();

        if (auth()->user()->role === 'organizer') {
            $query->where('organizer_id', auth()->id());
        }

        $events = $query->paginate(10);
        $prefix = $this->routePrefix();

        return view('events.index', compact('events', 'prefix'));
    }

    /**
     * Public event detail page.
     * Pre-loads ShoppingSession wishlist for users with an active queue slot,
     * allowing the checkout form to auto-populate ticket selections.
     */
    public function show($id)
    {
        $event = Events::with(['category', 'organizer', 'ticketTypes'])->findOrFail($id);

        // Hide non-published events from normal users
        if ($event->status !== 'published') {
            $user = auth()->user();
            $isPrivileged = $user && (
                $user->role === 'admin' ||
                ($user->role === 'organizer' && $event->organizer_id === $user->id)
            );

            if (!$isPrivileged) {
                abort(404, 'Event tidak ditemukan atau belum rilis.');
            }
        }

        // Load wishlist from active queue session (if user has one)
        $queueSessionWishlist = [];

        if (auth()->check()) {
            $activeSession = ShoppingSession::where('user_id', auth()->id())
                ->where('event_id', $event->id_event)
                ->where('expires_at', '>', now())
                ->first();

            // Drop session if user navigated here manually (without queue_granted flag).
            // This returns their reserved stock to the waiting list immediately.
            if ($activeSession && !session()->has('queue_granted') && !request()->has('queue_granted')) {
                $activeSession->delete();
                $activeSession = null;
            }

            if ($activeSession && !empty($activeSession->wishlist)) {
                $queueSessionWishlist = array_values(
                    array_map(fn ($item) => [
                        'id'    => (int)($item['id'] ?? 0),
                        'name'  => (string)($item['name'] ?? 'Ticket'),
                        'price' => (int)($item['price'] ?? 0),
                        'qty'   => (int)($item['qty'] ?? 1),
                    ], (array) $activeSession->wishlist)
                );
            }
        }

        return view('events.show', compact('event', 'queueSessionWishlist'));
    }

    /**
     * Show the create event form.
     */
    public function create()
    {
        $prefix      = $this->routePrefix();
        $categories  = EventCategories::all();
        $organizers  = Accounts::where('role', 'organizer')->get();
        $ticketTypes = TicketType::all();

        return view('events.create', compact('prefix', 'categories', 'organizers', 'ticketTypes'));
    }

    /**
     * Store a new event with ticket types. Sends notification email to assigned organizer.
     */
    public function store(Request $request)
    {
        $validated = $this->validateEventRequest($request);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $validated['banner'] = $bannerPath;
        }

        // Auto-assign organizer_id for organizer users
        if (auth()->user()->role === 'organizer') {
            $validated['organizer_id'] = auth()->id();
        }

        DB::beginTransaction();
        try {
            $event = Events::create($validated);
            $this->syncTicketTypes($event, $request->tickets);
            $this->notifyOrganizer($validated['organizer_id'] ?? null, $event);

            DB::commit();

            return redirect()->route("{$this->routePrefix()}.events.index")
                ->with('success', 'Event berhasil dibuat!');
        } catch (Exception $e) {
            DB::rollBack();
            if ($bannerPath) {
                Storage::disk('public')->delete($bannerPath);
            }
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data. Silakan periksa kembali input Anda.']);
        }
    }

    /**
     * Show the edit event form. Organizers can only edit their own events.
     */
    public function edit(Events $event)
    {
        $this->authorizeOrganizer($event);

        $prefix      = $this->routePrefix();
        $categories  = EventCategories::all();
        $organizers  = Accounts::where('role', 'organizer')->get();
        $ticketTypes = TicketType::all();
        $event->load('ticketTypes');

        return view('events.edit', compact('event', 'prefix', 'categories', 'organizers', 'ticketTypes'));
    }

    /**
     * Update an existing event. Handles banner replacement/deletion and organizer re-assignment.
     */
    public function update(Request $request, Events $event)
    {
        $this->authorizeOrganizer($event);

        $validated = $this->validateEventRequest($request);

        // Handle banner upload / deletion
        $newBannerPath = null;
        if ($request->hasFile('banner')) {
            $newBannerPath = $request->file('banner')->store('banners', 'public');
            $validated['banner'] = $newBannerPath;
        } elseif ($request->input('delete_banner') == '1') {
            $validated['banner'] = null;
        }

        // Organizers cannot change the organizer_id field
        if (auth()->user()->role === 'organizer') {
            unset($validated['organizer_id']);
        }

        $oldOrganizerId = $event->organizer_id;
        $oldBannerPath  = $event->banner;

        DB::beginTransaction();
        try {
            $event->update($validated);
            $this->syncTicketTypes($event, $request->tickets);

            // Notify new organizer if changed
            $newOrganizerId = $event->fresh()->organizer_id;
            if ($newOrganizerId && $newOrganizerId !== $oldOrganizerId) {
                $this->notifyOrganizer($newOrganizerId, $event->fresh());
            }

            DB::commit();

            // Clean up old banner after successful commit
            if ($oldBannerPath && ($newBannerPath || $request->input('delete_banner') == '1')) {
                Storage::disk('public')->delete($oldBannerPath);
            }

            return redirect()->route("{$this->routePrefix()}.events.index")
                ->with('success', 'Event berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            if ($newBannerPath) {
                Storage::disk('public')->delete($newBannerPath);
            }
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui data. Silakan periksa kembali input Anda.']);
        }
    }

    /**
     * Delete an event and its banner file. Organizers can only delete their own events.
     */
    public function destroy(Events $event)
    {
        $this->authorizeOrganizer($event);

        if ($event->banner) {
            Storage::disk('public')->delete($event->banner);
        }

        $event->delete();

        return redirect()->route("{$this->routePrefix()}.events.index")
            ->with('success', 'Event berhasil dihapus!');
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Shared validation rules for store() and update().
     */
    private function validateEventRequest(Request $request): array
    {
        return $request->validate([
            'category_id'              => 'nullable|exists:event_categories,id_category',
            'organizer_id'             => 'nullable|exists:accounts,id',
            'title'                    => 'required|string|max:255',
            'description'              => 'required|string|max:5000',
            'banner'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location'                 => 'required|string|max:255',
            'city'                     => 'required|string|max:100',
            'format'                   => 'required|in:onsite,online',
            'date'                     => 'required|date|after_or_equal:' . now()->toDateString(),
            'status'                   => 'required|in:draft,published,cancelled,completed',
            'tickets'                  => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id_ticket_type',
            'tickets.*.price'          => 'required|integer|min:0',
            'tickets.*.stock'          => 'required|integer|min:1',
        ], [
            'date.after_or_equal' => 'Tanggal event minimal hari ini.',
            'tickets.required'    => 'Minimal satu jenis tiket harus ditambahkan.',
            'description.max'     => 'Deskripsi terlalu panjang (maksimal 5000 karakter).',
        ]);
    }

    /**
     * Sync ticket types for an event using pivot data.
     */
    private function syncTicketTypes(Events $event, array $tickets): void
    {
        $pivotData = [];
        foreach ($tickets as $ticket) {
            $pivotData[$ticket['ticket_type_id']] = [
                'price' => $ticket['price'],
                'stock' => $ticket['stock'],
            ];
        }
        $event->ticketTypes()->sync($pivotData);
    }

    /**
     * Send email notification when an organizer is assigned to an event.
     */
    private function notifyOrganizer(?int $organizerId, Events $event): void
    {
        if (!$organizerId) return;

        $organizer = Accounts::find($organizerId);
        if (!$organizer) return;

        Mail::send('emails.organizer_assigned', [
            'organizer' => $organizer,
            'event'     => $event,
        ], function ($message) use ($organizer) {
            $message->to($organizer->email, $organizer->name)
                ->subject('You Have Been Assigned to an Event – Flowtix');
        });
    }

    /**
     * Abort 403 if an organizer tries to access another organizer's event.
     */
    private function authorizeOrganizer(Events $event): void
    {
        if (auth()->user()->role === 'organizer' && $event->organizer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
