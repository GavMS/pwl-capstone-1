<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IssuedTicket;

class UserTicketController extends Controller
{
    /**
     * Halaman "Tiket Saya" — tampilkan semua tiket yang dimiliki user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Jika user baru kembali dari pembayaran sukses
        if ($request->query('success') == 'true') {
            session()->flash('success', 'Your transaction was successful! Your tickets have been issued.');
        }

        $tickets = IssuedTicket::where('user_id', $user->id)
            ->with([
                'eventTicketType.event',
                'eventTicketType.ticketType',
            ])
            ->latest()
            ->paginate(9);

        return view('user.my-tickets', compact('tickets'));
    }

    /**
     * Detail satu tiket (untuk print / lihat QR).
     */
    public function show($uniqueCode)
    {
        $user = Auth::user();
        $ticket = IssuedTicket::where('unique_code', $uniqueCode)
            ->where('user_id', $user->id) // Security: pastikan milik user ini
            ->with(['eventTicketType.event', 'eventTicketType.ticketType'])
            ->firstOrFail();

        return view('user.ticket-detail', compact('ticket'));
    }
}
