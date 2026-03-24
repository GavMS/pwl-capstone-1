<?php

namespace Database\Seeders;

use App\Models\Accounts;
use Illuminate\Database\Seeder;

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
            'password' => '$2y$12$1UdQ9eiPACdYbU6ZTFwS8OC129.Q/K2L2xYZGPzyeskvSAP3byaLy', // 43434343
            'role' => 'admin',
        ]);

        Accounts::create([
            'name' => 'Yosua Juswandiputra',
            'username' => 'Yosua',
            'email' => '2472027@maranatha.ac.id',
            'password' => '$2y$12$/93V3Swcbhv9.qTxz9prDeYSv2gBiHV3KIqoxv9iRDLu8NgQOB6c.', // 43434343
            'role' => 'user',
        ]);

        Accounts::create([
            'name' => 'Archangela Sheilla',
            'username' => 'sourrossie',
            'email' => '2472014@maranatha.ac.id',
            'password' => '$2y$12$aOtI1PdMvi.i8vQ70Kn7Gep/4WkliKxsDsJJvgJC1wyuJwzr4pzFC', // 43434343
            'role' => 'organizer',
        ]);

    // Akun dummy tambahan menggunakan AccountFactory (10 user acak untuk testing)
    // Accounts::factory(10)->create();
    }
}
