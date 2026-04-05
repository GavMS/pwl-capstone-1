<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\EventTicketType;
use App\Models\IssuedTicket;

class CheckoutController extends Controller
{
    /**
     * Proses pembelian tiket dengan race-condition safe DB Transaction.
     */
    public function process(Request $request)
    {
        $user = Auth::user();

        // Validasi input dari form
        $request->validate([
            'event_id'  => 'required|integer',
            'tickets'   => 'required|array|min:1',
            'tickets.*.id'       => 'required|integer',
            'tickets.*.quantity' => 'required|integer|min:1|max:10',
        ]);

        $tickets = $request->input('tickets'); // ['id' => ..., 'quantity' => ...]

        try {
            DB::transaction(function () use ($tickets, $user) {
                foreach ($tickets as $ticketData) {
                    $ettId   = $ticketData['id'];
                    $qty     = (int) $ticketData['quantity'];

                    // Lock baris event_ticket_types agar transaksi concurrent menunggu
                    $ett = EventTicketType::lockForUpdate()->findOrFail($ettId);

                    // Validasi stok
                    if ($ett->stock < $qty) {
                        throw new \Exception("Stok tiket '{$ett->ticketType->name}' tidak mencukupi. Tersisa: {$ett->stock}.");
                    }

                    // Decrement stok
                    $ett->decrement('stock', $qty);

                    // Generate tiket per unit
                    for ($i = 0; $i < $qty; $i++) {
                        IssuedTicket::create([
                            'user_id'              => $user->id,
                            'event_ticket_type_id' => $ett->id,
                            'unique_code'          => 'TIX-' . strtoupper(Str::random(10)),
                            'status'               => 'active',
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }

        return redirect()->route('user.my-tickets')
            ->with('success', 'Pembelian berhasil! Tiket kamu sudah siap di bawah ini. 🎉');
    }
}
