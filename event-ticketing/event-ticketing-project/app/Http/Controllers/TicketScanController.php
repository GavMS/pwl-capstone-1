<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\IssuedTicket;
use App\Models\EventTicketType;

class TicketScanController extends Controller
{
    /**
     * Halaman utama scan tiket — pilih event, scan QR, lihat statistik.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua event milik organizer
        $events = Events::where('organizer_id', $user->id)
            ->where('status', 'published')
            ->orderBy('date', 'desc')
            ->get();

        $selectedEventId = $request->query('event');
        $selectedEvent = null;
        $stats = null;
        $recentScans = collect();

        if ($selectedEventId) {
            $selectedEvent = Events::where('id_event', $selectedEventId)
                ->where('organizer_id', $user->id)
                ->first();

            if ($selectedEvent) {
                // Hitung statistik
                $eventTicketTypeIds = EventTicketType::where('event_id', $selectedEvent->id_event)
                    ->pluck('id');

                $totalTickets = IssuedTicket::whereIn('event_ticket_type_id', $eventTicketTypeIds)->count();
                $scannedTickets = IssuedTicket::whereIn('event_ticket_type_id', $eventTicketTypeIds)
                    ->whereNotNull('scanned_at')
                    ->count();

                $stats = [
                    'total' => $totalTickets,
                    'scanned' => $scannedTickets,
                    'remaining' => $totalTickets - $scannedTickets,
                ];

                // 10 scan terbaru
                $recentScans = IssuedTicket::whereIn('event_ticket_type_id', $eventTicketTypeIds)
                    ->whereNotNull('scanned_at')
                    ->with(['eventTicketType.ticketType', 'user'])
                    ->orderBy('scanned_at', 'desc')
                    ->take(10)
                    ->get();
            }
        }

        return view('organizer.scan', compact(
            'user',
            'events',
            'selectedEvent',
            'selectedEventId',
            'stats',
            'recentScans'
        ));
    }

    /**
     * Proses scan tiket (AJAX POST).
     * Menerima unique_code, validasi, dan update scanned_at.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'unique_code' => 'required|string',
            'event_id' => 'required|integer',
        ]);

        $user = auth()->user();
        $uniqueCode = trim($request->unique_code);
        $eventId = $request->event_id;

        // Pastikan event milik organizer ini
        $event = Events::where('id_event', $eventId)
            ->where('organizer_id', $user->id)
            ->first();

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'type' => 'invalid_event',
                'message' => 'Event tidak ditemukan atau bukan milik Anda.',
            ], 403);
        }

        // Cari tiket berdasarkan unique_code
        $ticket = IssuedTicket::where('unique_code', $uniqueCode)
            ->with(['eventTicketType.ticketType', 'eventTicketType.event', 'user'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'type' => 'not_found',
                'message' => 'Tiket tidak ditemukan. Kode tidak valid.',
            ], 404);
        }

        // Pastikan tiket untuk event yang dipilih
        $ticketEventId = $ticket->eventTicketType->event->id_event;
        if ($ticketEventId != $eventId) {
            return response()->json([
                'status' => 'error',
                'type' => 'wrong_event',
                'message' => 'Tiket ini bukan untuk event "' . $event->title . '".',
            ], 422);
        }

        // Cek apakah sudah di-scan
        if ($ticket->scanned_at) {
            return response()->json([
                'status' => 'error',
                'type' => 'already_scanned',
                'message' => 'Tiket sudah digunakan (check-in)',
                'attendee' => [
                    'name' => $ticket->attendee_name ?? $ticket->user->name ?? '-',
                    'email' => $ticket->attendee_email ?? $ticket->user->email ?? '-',
                    'ticket_type' => $ticket->eventTicketType->ticketType->name ?? '-',
                    'scanned_at' => $ticket->scanned_at->format('d M Y, H:i'),
                ],
            ], 409);
        }

        // Proses check-in
        $ticket->scanned_at = now();
        $ticket->save();

        // Hitung statistik terbaru
        $eventTicketTypeIds = EventTicketType::where('event_id', $eventId)->pluck('id');
        $totalTickets = IssuedTicket::whereIn('event_ticket_type_id', $eventTicketTypeIds)->count();
        $scannedTickets = IssuedTicket::whereIn('event_ticket_type_id', $eventTicketTypeIds)
            ->whereNotNull('scanned_at')
            ->count();

        return response()->json([
            'status' => 'success',
            'type' => 'checked_in',
            'message' => 'Check-in berhasil!',
            'attendee' => [
                'name' => $ticket->attendee_name ?? $ticket->user->name ?? '-',
                'email' => $ticket->attendee_email ?? $ticket->user->email ?? '-',
                'phone' => $ticket->attendee_phone ?? '-',
                'ticket_type' => $ticket->eventTicketType->ticketType->name ?? '-',
                'unique_code' => $ticket->unique_code,
                'scanned_at' => now()->format('d M Y, H:i'),
            ],
            'stats' => [
                'total' => $totalTickets,
                'scanned' => $scannedTickets,
                'remaining' => $totalTickets - $scannedTickets,
            ],
        ]);
    }

    /**
     * Riwayat scan per event (AJAX GET).
     */
    public function history($eventId)
    {
        $user = auth()->user();

        $event = Events::where('id_event', $eventId)
            ->where('organizer_id', $user->id)
            ->firstOrFail();

        $eventTicketTypeIds = EventTicketType::where('event_id', $event->id_event)->pluck('id');

        $scannedTickets = IssuedTicket::whereIn('event_ticket_type_id', $eventTicketTypeIds)
            ->whereNotNull('scanned_at')
            ->with(['eventTicketType.ticketType', 'user'])
            ->orderBy('scanned_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($ticket) {
                return [
                    'name' => $ticket->attendee_name ?? $ticket->user->name ?? '-',
                    'email' => $ticket->attendee_email ?? $ticket->user->email ?? '-',
                    'ticket_type' => $ticket->eventTicketType->ticketType->name ?? '-',
                    'unique_code' => $ticket->unique_code,
                    'scanned_at' => $ticket->scanned_at->format('d M Y, H:i'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $scannedTickets,
        ]);
    }
}
