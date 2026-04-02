<?php

namespace Database\Seeders;

use App\Models\Accounts;
use App\Models\EventCategories;
use App\Models\Events;
use App\Models\TicketType;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizer = Accounts::where('role', 'organizer')->first() ?? Accounts::first();

        $categories = EventCategories::pluck('id_category', 'slug');
        $ticketTypes = TicketType::pluck('id_ticket_type', 'name');

        $eventDateBase = now()->addDays(30)->setTime(9, 0, 0);

        $events = [
            [
                'lookup' => ['title' => 'DevFest Bandung 2026'],
                'data' => [
                    'category_id' => $categories['developer-conference'] ?? null,
                    'organizer_id' => $organizer?->id,
                    'description' => 'Developer conference featuring practical talks, coding demos, and networking.',
                    'location' => 'Universitas Kristen Maranatha',
                    'date' => $eventDateBase->copy(),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 150000, 'stock' => 250],
                    'VIP' => ['price' => 400000, 'stock' => 60],
                    'Student Pass' => ['price' => 90000, 'stock' => 200],
                ],
            ],
            [
                'lookup' => ['title' => 'AI Hands-on Workshop'],
                'data' => [
                    'category_id' => $categories['tech-workshop'] ?? null,
                    'organizer_id' => $organizer?->id,
                    'description' => 'Workshop session covering AI fundamentals with guided mini projects.',
                    'location' => 'Institut Teknologi Bandung (ITB)',
                    'date' => $eventDateBase->copy()->addDays(7),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 120000, 'stock' => 180],
                    'VIP' => ['price' => 300000, 'stock' => 40],
                    'Student Pass' => ['price' => 70000, 'stock' => 220],
                ],
            ],
            [
                'lookup' => ['title' => 'Startup Connect Night'],
                'data' => [
                    'category_id' => $categories['startup-networking'] ?? null,
                    'organizer_id' => $organizer?->id,
                    'description' => 'Networking event to connect students, founders, and industry mentors.',
                    'location' => 'Bandung Creative Hub',
                    'date' => $eventDateBase->copy()->addDays(14),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 100000, 'stock' => 200],
                    'VIP' => ['price' => 250000, 'stock' => 50],
                    'Student Pass' => ['price' => 50000, 'stock' => 250],
                ],
            ],
        ];

        foreach ($events as $eventConfig) {
            $event = Events::updateOrCreate(
                $eventConfig['lookup'],
                array_merge($eventConfig['lookup'], $eventConfig['data'])
            );

            $ticketSyncData = [];
            foreach ($eventConfig['tickets'] as $ticketName => $pivot) {
                if (! isset($ticketTypes[$ticketName])) {
                    continue;
                }

                $ticketSyncData[$ticketTypes[$ticketName]] = $pivot;
            }

            $event->ticketTypes()->sync($ticketSyncData);
        }
    }
}