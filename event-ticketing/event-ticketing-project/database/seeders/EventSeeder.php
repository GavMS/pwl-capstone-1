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
        $organizerId = Accounts::where('role', 'organizer')->first()->id ?? Accounts::first()->id ?? null;

        // Pilih sourrossie sebagai organizer khusus DevFest jika ada
        $sourrossieId = Accounts::where('name', 'like', '%sourrossie%')->first()->id ?? $organizerId;

        $categories = EventCategories::pluck('id_category', 'slug');
        $ticketTypes = TicketType::pluck('id_ticket_type', 'name');

        $eventDateBase = now()->addDays(30)->setTime(9, 0, 0);

        $events = [
            [
                'lookup' => ['title' => 'DevFest Bandung 2026'],
                'data' => [
                    'category_id' => $categories['developer-conference'] ?? null,
                    'organizer_id' => $sourrossieId,
                    'banner' => 'https://visi.news/wp-content/uploads/2025/11/Screenshot-2025-11-24-190050.webp',
                    'description' => 'Developer conference featuring practical talks, coding demos, and networking.',
                    'location' => 'Universitas Kristen Maranatha',
                    'city' => 'Bandung',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy(),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 150000, 'stock' => 250],
                    'VIP' => ['price' => 400000, 'stock' => 60],
                    'Student Pass' => ['price' => 90000, 'stock' => 1],
                ],
            ],
            [
                'lookup' => ['title' => 'AI Hands-on Workshop'],
                'data' => [
                    'category_id' => $categories['tech-workshop'] ?? null,
                    'organizer_id' => $organizerId,
                    'banner' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=800',
                    'description' => 'Workshop session covering AI fundamentals with guided mini projects.',
                    'location' => 'Institut Teknologi Bandung (ITB)',
                    'city' => 'Bandung',
                    'format' => 'onsite',
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
                    'organizer_id' => $organizerId,
                    'banner' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=800',
                    'description' => 'Networking event to connect students, founders, and industry mentors.',
                    'location' => 'Bandung Creative Hub',
                    'city' => 'Bandung',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy()->addDays(14),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 100000, 'stock' => 200],
                    'VIP' => ['price' => 250000, 'stock' => 50],
                    'Student Pass' => ['price' => 50000, 'stock' => 250],
                ],
            ],
            [
                'lookup' => ['title' => 'ASIAN KUNG-FU GENERATION Live in Jakarta'],
                'data' => [
                    'category_id' => $categories['music-concert'] ?? null,
                    'organizer_id' => $organizerId,
                    'banner' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQYfHqDZVPkzEBS3q78YPbkBsqwgNoLxBRhFA&s',
                    'description' => 'The legendary Japanese rock band is coming to Jakarta for a one-night show.',
                    'location' => 'Basket Hall GBK Senayan',
                    'city' => 'Jakarta',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy()->addDays(18)->setTime(19, 0, 0),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 850000, 'stock' => 1500],
                    'VIP' => ['price' => 2500000, 'stock' => 200],
                ],
            ],
            [
                'lookup' => ['title' => 'Marine Actions Expo (MAX) 2026'],
                'data' => [
                    'category_id' => $categories['art-exhibition'] ?? null,
                    'organizer_id' => $organizerId,
                    'banner' => 'https://images.unsplash.com/photo-1582967788606-a171c1080cb0?q=80&w=800',
                    'description' => 'Exhibition showcasing marine life conservation efforts through digital art.',
                    'location' => 'Jakarta Convention Center',
                    'city' => 'Jakarta',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy()->addDays(24),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 25000, 'stock' => 5000],
                ],
            ],
            [
                'lookup' => ['title' => 'Raditya Dika - Cerita Cintaku Tour'],
                'data' => [
                    'category_id' => $categories['comedy-show'] ?? null,
                    'organizer_id' => $organizerId,
                    'banner' => 'https://i.ytimg.com/vi/Z3kpDgsA9xs/maxresdefault.jpg',
                    'description' => 'Stand up comedy special tour covering love and relationships.',
                    'location' => 'Balai Sarbini',
                    'city' => 'Jakarta',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy()->addDays(40)->setTime(20, 0, 0),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 250000, 'stock' => 1000],
                    'VIP' => ['price' => 600000, 'stock' => 250],
                ],
            ],
            [
                'lookup' => ['title' => 'Pestapora 2026 Festival Pass'],
                'data' => [
                    'category_id' => $categories['music-concert'] ?? null,
                    'organizer_id' => $organizerId,
                    'banner' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=800',
                    'description' => 'Annual multi-genre music festival featuring local and international acts.',
                    'location' => 'Gambir Expo Kemayoran',
                    'city' => 'Jakarta',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy()->addDays(60),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 600000, 'stock' => 20000],
                    'VIP' => ['price' => 1200000, 'stock' => 2000],
                ],
            ],
            [
                'lookup' => ['title' => 'KARD 2026 WORLD TOUR <DRIFT>'],
                'data' => [
                    'category_id' => $categories['music-concert'] ?? null,
                    'organizer_id' => $organizerId,
                    'banner' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?q=80&w=800',
                    'description' => 'Co-ed K-Pop group KARD brings their DRIFT tour to Indonesia.',
                    'location' => 'The Kasablanka Hall',
                    'city' => 'Jakarta',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy()->addDays(85)->setTime(18, 30, 0),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 1350000, 'stock' => 800],
                    'VIP' => ['price' => 2800000, 'stock' => 150],
                ],
            ],
            [
                'lookup' => ['title' => 'Bandung Food & Culture Fest'],
                'data' => [
                    'category_id' => $categories['food-festival'] ?? null,
                    'organizer_id' => $organizerId,
                    'banner' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?q=80&w=800',
                    'description' => 'A weekend celebrating local cuisines, street food, and traditional performances.',
                    'location' => 'Kiara Artha Park',
                    'city' => 'Bandung',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy()->addDays(12),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 0, 'stock' => 10000],
                ],
            ],
            [
                'lookup' => ['title' => 'MLBB Championship Series - Finals'],
                'data' => [
                    'category_id' => $categories['esports-tournament'] ?? null,
                    'organizer_id' => $organizerId,
                    'banner' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=800',
                    'description' => 'The grand finals of the regional Mobile Legends bang bang tournament.',
                    'location' => 'Tennis Indoor Senayan',
                    'city' => 'Jakarta',
                    'format' => 'onsite',
                    'date' => $eventDateBase->copy()->addDays(20),
                    'status' => 'published',
                ],
                'tickets' => [
                    'Normal' => ['price' => 150000, 'stock' => 2000],
                    'VIP' => ['price' => 350000, 'stock' => 500],
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
                if (!isset($ticketTypes[$ticketName])) {
                    continue;
                }

                $ticketSyncData[$ticketTypes[$ticketName]] = $pivot;
            }

            $event->ticketTypes()->sync($ticketSyncData);
        }
    }
}