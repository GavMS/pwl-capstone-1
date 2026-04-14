<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\EventTicketType;
use App\Models\Transaction;

class CheckoutController extends Controller
{
    public function confirm(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer',
            'tickets' => 'required|array|min:1',
            'tickets.*.id' => 'required|integer',
            'tickets.*.quantity' => 'required|integer|min:1|max:10',
        ]);

        $eventId = $request->input('event_id');
        $ticketsInput = $request->input('tickets');

        $event = \App\Models\Events::findOrFail($eventId);
        $selectedTickets = [];
        $totalPrice = 0;

        foreach ($ticketsInput as $ticketData) {
            $ett = EventTicketType::with('ticketType')->findOrFail($ticketData['id']);
            if ($ett->stock < $ticketData['quantity']) {
                return back()->withErrors(['checkout' => "Stok tiket '{$ett->ticketType->name}' tidak mencukupi."]);
            }

            $totalPrice += ($ett->price * $ticketData['quantity']);
            $selectedTickets[] = [
                'id' => $ett->id,
                'name' => $ett->ticketType->name,
                'price' => $ett->price,
                'quantity' => $ticketData['quantity']
            ];
        }

        return view('user.order-details', compact('event', 'selectedTickets', 'totalPrice'));
    }

    public function process(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'event_id' => 'required|integer',
            'tickets' => 'required|json',
            'attendees' => 'required|array',
            'attendees.*.name' => 'required|string|max:255',
            'attendees.*.email' => 'required|email',
            'attendees.*.phone' => 'required|string|max:20',
            'attendees.*.id_card' => 'required|string|max:50',
            'agreement' => 'accepted',
        ]);

        $tickets = json_decode($request->input('tickets'), true);
        $totalPrice = 0;
        $ticketPayload = [];

        foreach ($tickets as $ticketData) {
            $ettId = $ticketData['id'];
            $qty = (int) $ticketData['quantity'];

            $ett = EventTicketType::with('ticketType')->findOrFail($ettId);
            if ($ett->stock < $qty) {
                return back()->withErrors(['checkout' => "Maaf, stok tiket '{$ett->ticketType->name}' sudah habis."]);
            }

            $totalPrice += ($ett->price * $qty);

            $ticketPayload[] = [
                'event_ticket_type_id' => $ettId,
                'event_title' => $ett->event->title ?? 'Event',
                'ticket_name' => $ett->ticketType->name,
                'price' => $ett->price,
                'quantity' => $qty
            ];
        }

        $orderId = 'TRX-' . time() . '-' . Str::random(5);
        $deadline = now()->addMinutes(15);

        $transaction = Transaction::create([
            'accounts_id' => $user->id,
            'order_id' => $orderId,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'ticket_payload' => $ticketPayload,
            'customer_details' => $request->input('attendees'),
            'deadline_payment' => $deadline,
        ]);

        $this->initMidtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->input('attendees')[0]['name'],
                'email' => $request->input('attendees')[0]['email'],
                'phone' => $request->input('attendees')[0]['phone'],
            ],
            'expiry' => [
                'unit' => 'second',
                'duration' => 900
            ],
            'callbacks' => [
                'finish' => route('user.my-tickets'),
                'unfinish' => route('checkout.payment', $orderId),
                'error' => route('user.my-tickets'),
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return redirect()->route('checkout.payment', $transaction->order_id);
        } catch (\Exception $e) {
            return back()->withErrors(['checkout' => 'Midtrans Error: ' . $e->getMessage()]);
        }
    }

    public function payment($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)
            ->where('accounts_id', Auth::id())
            ->firstOrFail();

        if ($transaction->status === 'success') {
            return redirect()->route('user.my-tickets')->with('success', 'Transaksi ini sudah lunas.');
        }

        return view('user.payment', compact('transaction'));
    }

    public function recreate(Request $request, $order_id)
    {
        $oldTransaction = Transaction::where('order_id', $order_id)
            ->where('accounts_id', Auth::id())
            ->firstOrFail();

        if ($oldTransaction->status === 'success') {
            return redirect()->route('user.my-tickets')->with('success', 'Transaksi ini sudah lunas.');
        }

        $deadline = $oldTransaction->deadline_payment
            ? \Carbon\Carbon::parse($oldTransaction->deadline_payment)
            : \Carbon\Carbon::parse($oldTransaction->created_at)->addMinutes(15);

        $timeLeftSeconds = (int) now()->diffInSeconds($deadline, false);

        if ($timeLeftSeconds <= 0) {
            $oldTransaction->update(['status' => 'expired']);
            return redirect()->route('user.my-tickets')->with('error', 'Payment time has expired.');
        }

        $oldTransaction->update(['status' => 'failed']);

        $newOrderId = 'TRX-' . time() . '-' . Str::random(5);

        $newTransaction = Transaction::create([
            'accounts_id' => $oldTransaction->accounts_id,
            'order_id' => $newOrderId,
            'total_price' => $oldTransaction->total_price,
            'status' => 'pending',
            'ticket_payload' => $oldTransaction->ticket_payload,
            'customer_details' => $oldTransaction->customer_details,
            'deadline_payment' => $deadline,
        ]);

        $this->initMidtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $newOrderId,
                'gross_amount' => $newTransaction->total_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'callbacks' => [
                'finish' => route('user.my-tickets'),
                'unfinish' => route('checkout.payment', $newOrderId),
                'error' => route('user.my-tickets'),
            ],
            'expiry' => [
                'unit' => 'second',
                'duration' => max(1, $timeLeftSeconds)
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $newTransaction->update(['snap_token' => $snapToken]);
            return redirect()->route('checkout.payment', $newTransaction->order_id);
        } catch (\Exception $e) {
            return back()->withErrors(['checkout' => 'Failed requesting new Snap Token: ' . $e->getMessage()]);
        }
    }

    public function mockSuccess($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)
            ->where('accounts_id', Auth::id())
            ->firstOrFail();

        if ($transaction->status === 'success') {
            return redirect()->route('user.my-tickets');
        }

        $transaction->status = 'success';
        $transaction->payment_method = 'developer_mock';
        $transaction->save();

        if ($transaction->issuedTickets()->count() == 0) {
            DB::beginTransaction();
            try {
                $payloadArray = $transaction->ticket_payload;
                $customerDetails = $transaction->customer_details;
                $attendeeIndex = 0;

                foreach ($payloadArray as $item) {
                    $ett = EventTicketType::lockForUpdate()->findOrFail($item['event_ticket_type_id']);
                    if ($ett->stock >= $item['quantity']) {
                        $ett->decrement('stock', $item['quantity']);
                        for ($i = 0; $i < $item['quantity']; $i++) {
                            $attendee = $customerDetails[$attendeeIndex] ?? null;

                            $ticket = \App\Models\IssuedTicket::create([
                                'user_id' => $transaction->accounts_id,
                                'event_ticket_type_id' => $ett->id,
                                'transaction_id' => $transaction->id,
                                'unique_code' => 'TIX-' . strtoupper(Str::random(10)),
                                'status' => 'active',
                                'attendee_name' => $attendee['name'] ?? null,
                                'attendee_email' => $attendee['email'] ?? null,
                                'attendee_phone' => $attendee['phone'] ?? null,
                                'attendee_id_card' => $attendee['id_card'] ?? null,
                                'attendee_dob' => $attendee['dob'] ?? null,
                                'attendee_gender' => $attendee['gender'] ?? null,
                            ]);

                            if ($attendee && !empty($attendee['email'])) {
                                try {
                                    \Illuminate\Support\Facades\Mail::to($attendee['email'])->send(new \App\Mail\TicketMailable($ticket));
                                } catch (\Exception $mailEx) {
                                    \Illuminate\Support\Facades\Log::error("Failed to send ticket email: " . $mailEx->getMessage());
                                }
                            }

                            $attendeeIndex++;
                        }
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['checkout' => 'Mock payment failed: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('user.my-tickets')->with('success', 'Mock Payment Success!');
    }

    private function initMidtrans()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        if (env('NGROK_URL')) {
            \Midtrans\Config::$overrideNotifUrl = env('NGROK_URL') . '/midtrans/callback';
        } else {
            \Midtrans\Config::$overrideNotifUrl = url('/midtrans/callback');
        }
    }
}
