<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ticketTypes = [
            [
                'name' => 'Normal',
                'description' => 'General admission with access to all main sessions.',
            ],
            [
                'name' => 'VIP',
                'description' => 'Priority seating, faster check-in, and exclusive networking access.',
            ],
            [
                'name' => 'Student Pass',
                'description' => 'Affordable pass with full session access for students.',
            ],
        ];

        foreach ($ticketTypes as $ticketType) {
            TicketType::updateOrCreate(
                ['name' => $ticketType['name']],
                $ticketType
            );
        }
    }
}