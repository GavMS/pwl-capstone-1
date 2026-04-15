<?php

namespace Database\Seeders;

use App\Models\Accounts;
use App\Models\EventTicketType;
use App\Models\IssuedTicket;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = Accounts::where('role', 'user')->get();
        if ($users->isEmpty()) {
            return;
        }

        $eventTicketTypes = EventTicketType::with(['event', 'ticketType'])->get();
        if ($eventTicketTypes->isEmpty()) {
            return;
        }

        // 1. Create 30 successful transactions with issued tickets
        for ($i = 0; $i < 30; $i++) {
            $user = $users->random();
            $ticketType = $eventTicketTypes->random();
            $quantity = rand(1, 4);
            $totalPrice = $ticketType->price * $quantity;
            
            // Random past date for statistics variation
            $transactionDate = now()->subDays(rand(0, 45))->subHours(rand(0, 23));

            $transaction = Transaction::create([
                'status' => 'success',
                'total_price' => $totalPrice,
                'payment_method' => 'midtrans',
                'deadline_payment' => $transactionDate->copy()->addDay(),
                'accounts_id' => $user->id,
                'order_id' => 'TRX-' . strtoupper(Str::random(10)),
                'snap_token' => Str::random(20),
                'ticket_payload' => [
                    [
                        'event_ticket_type_id' => $ticketType->id,
                        'quantity' => $quantity,
                        'price' => $ticketType->price,
                        'event_name' => $ticketType->event->title ?? 'Event',
                        'ticket_name' => $ticketType->ticketType->name ?? 'Ticket'
                    ]
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'last_name' => '',
                    'email' => $user->email,
                    'phone' => '08' . rand(1000000000, 9999999999),
                ],
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ]);

            // Issue tickets for this successful transaction
            for ($j = 0; $j < $quantity; $j++) {
                $isScanned = rand(1, 100) <= 40; // 40% chance the ticket has been scanned
                
                IssuedTicket::create([
                    'user_id' => $user->id,
                    'event_ticket_type_id' => $ticketType->id,
                    'transaction_id' => $transaction->id,
                    'unique_code' => strtoupper((string) Str::uuid()),
                    'qr_image' => null,
                    'status' => $isScanned ? 'scanned' : 'valid',
                    'scanned_at' => $isScanned ? $transactionDate->copy()->addDays(rand(1, 10)) : null,
                    'attendee_name' => $user->name . ($quantity > 1 ? ' - Guest ' . ($j + 1) : ''),
                    'attendee_email' => $user->email,
                    'attendee_phone' => $transaction->customer_details['phone'] ?? '081234567890',
                    'attendee_id_card' => (string) rand(3200000000000000, 3600000000000000),
                    'attendee_dob' => now()->subYears(rand(18, 40))->format('Y-m-d'),
                    'attendee_gender' => rand(0, 1) ? 'male' : 'female',
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ]);
            }
        }

        // 2. Create 10 pending transactions (unpaid)
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $ticketType = $eventTicketTypes->random();
            $quantity = rand(1, 3);
            $totalPrice = $ticketType->price * $quantity;
            $transactionDate = now()->subMinutes(rand(10, 1400));

            Transaction::create([
                'status' => 'pending',
                'total_price' => $totalPrice,
                'payment_method' => 'midtrans',
                'deadline_payment' => $transactionDate->copy()->addDay(),
                'accounts_id' => $user->id,
                'order_id' => 'TRX-' . strtoupper(Str::random(10)),
                'snap_token' => Str::random(20),
                'ticket_payload' => [
                    [
                        'event_ticket_type_id' => $ticketType->id,
                        'quantity' => $quantity,
                        'price' => $ticketType->price,
                        'event_name' => $ticketType->event->title ?? 'Event',
                        'ticket_name' => $ticketType->ticketType->name ?? 'Ticket'
                    ]
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'last_name' => '',
                    'email' => $user->email,
                    'phone' => '08' . rand(1000000000, 9999999999),
                ],
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ]);
        }
        
        // 3. Create 5 failed/expired transactions
        for ($i = 0; $i < 5; $i++) {
            $user = $users->random();
            $ticketType = $eventTicketTypes->random();
            $quantity = rand(1, 2);
            $totalPrice = $ticketType->price * $quantity;
            $transactionDate = now()->subDays(rand(2, 20));

            Transaction::create([
                'status' => rand(0, 1) ? 'expired' : 'failed',
                'total_price' => $totalPrice,
                'payment_method' => 'midtrans',
                'deadline_payment' => $transactionDate->copy()->addDay(),
                'accounts_id' => $user->id,
                'order_id' => 'TRX-' . strtoupper(Str::random(10)),
                'snap_token' => Str::random(20),
                'ticket_payload' => [
                    [
                        'event_ticket_type_id' => $ticketType->id,
                        'quantity' => $quantity,
                        'price' => $ticketType->price,
                        'event_name' => $ticketType->event->title ?? 'Event',
                        'ticket_name' => $ticketType->ticketType->name ?? 'Ticket'
                    ]
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'last_name' => '',
                    'email' => $user->email,
                    'phone' => '08' . rand(1000000000, 9999999999),
                ],
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ]);
        }
    }
}
