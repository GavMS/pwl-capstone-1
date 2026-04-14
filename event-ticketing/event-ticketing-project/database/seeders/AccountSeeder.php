<?php

namespace Database\Seeders;

use App\Models\Accounts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Akun Fixed ──────────────────────────────────────────────
        Accounts::create([
            'name' => 'Gavin Malik Setiawan',
            'username' => 'GavMS',
            'email' => '2472042@maranatha.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Accounts::create([
            'name' => 'Yosua Juswandiputra',
            'username' => 'yosua',
            'email' => '2472027@maranatha.ac.id',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // ── Organizer Accounts ──────────────────────────────────────
        $organizers = [
            [
                'name' => 'Archangela Sheilla',
                'username' => 'sourrossie',
                'email' => '2472014@maranatha.ac.id',
            ],
            [
                'name' => 'Scent of Indonesia',
                'username' => 'scentofindo',
                'email' => 'organizer.scentofindo@flowtix.id',
            ],
            [
                'name' => 'MAS Live Entertainment',
                'username' => 'maslive',
                'email' => 'organizer.maslive@flowtix.id',
            ],
            [
                'name' => 'Boss Creator Studio',
                'username' => 'bosscreator',
                'email' => 'organizer.bosscreator@flowtix.id',
            ],
            [
                'name' => 'Comika Event',
                'username' => 'comikaevent',
                'email' => 'organizer.comika@flowtix.id',
            ],
            [
                'name' => 'Voluntrip Indonesia',
                'username' => 'voluntrip',
                'email' => 'organizer.voluntrip@flowtix.id',
            ],
            [
                'name' => 'Bengkel Space',
                'username' => 'bengkelspace',
                'email' => 'organizer.bengkelspace@flowtix.id',
            ],
            [
                'name' => 'Mantappu Jiwa Events',
                'username' => 'mantappujiwa',
                'email' => 'organizer.mantappu@flowtix.id',
            ],
            [
                'name' => 'Ismaya Live',
                'username' => 'ismayalive',
                'email' => 'organizer.ismaya@flowtix.id',
            ],
            [
                'name' => 'PK Entertainment',
                'username' => 'pkent',
                'email' => 'organizer.pkent@flowtix.id',
            ],
        ];

        foreach ($organizers as $org) {
            Accounts::updateOrCreate(
                ['email' => $org['email']],
                array_merge($org, [
                    'password' => Hash::make('password'),
                    'role' => 'organizer',
                ])
            );
        }

        // ── Akun dummy tambahan (10 user acak untuk testing) ────────
        Accounts::factory(10)->create();
    }
}
