<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\IssuedTicket;
use App\Models\EventTicketType;

/**
 * TicketScanController
 *
 * Handles QR-code ticket scanning / check-in for Organizers.
 * Provides event selection, scan validation (AJAX), and scan history.
 *
 * Used by: organizer.scan, organizer.scan.process, organizer.scan.history routes.
 */
class TicketScanController extends Controller
{
    /**
     * Scan page — select an event, view stats, scan QR codes.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $events = Events::where('organizer_id', $user->id)
            ->where('status', 'published')
            ->orderBy('date', 'desc')
            ->get();

        $selectedEventId = $request->query('event');
        $selectedEvent   = null;
        $stats           = null;
        $recentScans     = collect();

        if ($selectedEventId) {
            $selectedEvent = Events::where('id_event', $selectedEventId)
                ->where('organizer_id', $user->id)
                ->first();

            if ($selectedEvent) {
                $stats       = $this->getEventScanStats($selectedEvent->id_event);
                $recentScans = $this->getRecentScans($selectedEvent->id_event, 10);
            }
        }

        return view('organizer.scan', compact(
            'user', 'events', 'selectedEvent', 'selectedEventId', 'stats', 'recentScans'
        ));
    }

    /**
     * Process a ticket scan (AJAX POST).
     * Validates the unique_code against the selected event and marks check-in.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'unique_code' => 'required|string',
            'event_id'    => 'required|integer',
        ]);

        $user       = auth()->user();
        $uniqueCode = trim($request->unique_code);
        $eventId    = $request->event_id;

        // Verify event belongs to this organizer
        $event = Events::where('id_event', $eventId)
            ->where('organizer_id', $user->id)
            ->first();

        if (!$event) {
            return response()->json([
                'status'  => 'error',
                'type'    => 'invalid_event',
                'message' => 'Event tidak ditemukan atau bukan milik Anda.',
            ], 403);
        }

        // Find ticket by unique code
        $ticket = IssuedTicket::where('unique_code', $uniqueCode)
            ->with(['eventTicketType.ticketType', 'eventTicketType.event', 'user'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'status'  => 'error',
                'type'    => 'not_found',
                'message' => 'Tiket tidak ditemukan. Kode tidak valid.',
            ], 404);
        }

        // Verify ticket belongs to the selected event
        if ($ticket->eventTicketType->event->id_event != $eventId) {
            return response()->json([
                'status'  => 'error',
                'type'    => 'wrong_event',
                'message' => 'Tiket ini bukan untuk event "' . $event->title . '".',
            ], 422);
        }

        // Reject if already scanned
        if ($ticket->scanned_at) {
            return response()->json([
                'status'   => 'error',
                'type'     => 'already_scanned',
                'message'  => 'Tiket sudah digunakan (check-in)',
                'attendee' => [
                    'name'        => $ticket->attendee_name ?? $ticket->user->name ?? '-',
                    'email'       => $ticket->attendee_email ?? $ticket->user->email ?? '-',
                    'ticket_type' => $ticket->eventTicketType->ticketType->name ?? '-',
                    'scanned_at'  => $ticket->scanned_at->format('d M Y, H:i'),
                ],
            ], 409);
        }

        // Mark check-in
        $ticket->scanned_at = now();
        $ticket->save();

        $stats = $this->getEventScanStats($eventId);

        return response()->json([
            'status'   => 'success',
            'type'     => 'checked_in',
            'message'  => 'Check-in berhasil!',
            'attendee' => [
                'name'        => $ticket->attendee_name ?? $ticket->user->name ?? '-',
                'email'       => $ticket->attendee_email ?? $ticket->user->email ?? '-',
                'phone'       => $ticket->attendee_phone ?? '-',
                'ticket_type' => $ticket->eventTicketType->ticketType->name ?? '-',
                'unique_code' => $ticket->unique_code,
                'scanned_at'  => now()->format('d M Y, H:i'),
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Scan history for an event (AJAX GET).
     */
    public function history($eventId)
    {
        $user = auth()->user();

        Events::where('id_event', $eventId)
            ->where('organizer_id', $user->id)
            ->firstOrFail();

        $scannedTickets = $this->getRecentScans($eventId, 20)
            ->map(fn ($ticket) => [
                'name'        => $ticket->attendee_name ?? $ticket->user->name ?? '-',
                'email'       => $ticket->attendee_email ?? $ticket->user->email ?? '-',
                'ticket_type' => $ticket->eventTicketType->ticketType->name ?? '-',
                'unique_code' => $ticket->unique_code,
                'scanned_at'  => $ticket->scanned_at->format('d M Y, H:i'),
            ])->values();

        return response()->json([
            'status' => 'success',
            'data'   => $scannedTickets,
        ]);
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Get scan statistics for an event.
     */
    private function getEventScanStats(int $eventId): array
    {
        $ettIds = EventTicketType::where('event_id', $eventId)->pluck('id');

        $total   = IssuedTicket::whereIn('event_ticket_type_id', $ettIds)->count();
        $scanned = IssuedTicket::whereIn('event_ticket_type_id', $ettIds)->whereNotNull('scanned_at')->count();

        return [
            'total'     => $total,
            'scanned'   => $scanned,
            'remaining' => $total - $scanned,
        ];
    }

    /**
     * Get recently scanned tickets for an event.
     */
    private function getRecentScans(int $eventId, int $limit)
    {
        $ettIds = EventTicketType::where('event_id', $eventId)->pluck('id');

        return IssuedTicket::whereIn('event_ticket_type_id', $ettIds)
            ->whereNotNull('scanned_at')
            ->with(['eventTicketType.ticketType', 'user'])
            ->orderBy('scanned_at', 'desc')
            ->orderBy('id', 'desc')
            ->take($limit)
            ->get();
    }
}
