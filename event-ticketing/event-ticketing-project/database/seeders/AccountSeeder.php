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
        // Akun fixed (data nyata dengan password & role spesifik)
        Accounts::create([
            'name' => 'Gavin Malik Setiawan',
            'username' => 'GavMS',
            'email' => '2472042@maranatha.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Accounts::create([
            'name' => 'Yosua Juswandiputra',
            'username' => 'Yosua',
            'email' => '2472027@maranatha.ac.id',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        Accounts::create([
            'name' => 'Archangela Sheilla',
            'username' => 'sourrossie',
            'email' => '2472014@maranatha.ac.id',
            'password' => Hash::make('password'),
            'role' => 'organizer',
        ]);

        // Akun dummy tambahan menggunakan AccountFactory (10 user acak untuk testing)
        Accounts::factory(10)->create();
    }
}
