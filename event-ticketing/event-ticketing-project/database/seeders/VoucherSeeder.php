<?php

namespace Database\Seeders;

use App\Models\Events;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devFestEventId = Events::where('title', 'DevFest Bandung 2026')->value('id_event');

        $vouchers = [
            [
                'code' => 'GLOBAL10',
                'description' => 'Diskon global 10% untuk semua event.',
                'discount_percent' => 10,
                'event_id' => null,
                'min_purchase' => 100000,
                'max_discount' => 50000,
                'max_uses' => 300,
                'used_count' => 0,
                'is_active' => true,
                'valid_from' => now()->subDay(),
                'valid_until' => now()->addMonths(3),
            ],
            [
                'code' => 'DEVFEST25',
                'description' => 'Diskon 25% khusus event DevFest Bandung 2026.',
                'discount_percent' => 25,
                'event_id' => $devFestEventId,
                'min_purchase' => 50000,
                'max_discount' => 150000,
                'max_uses' => 120,
                'used_count' => 0,
                'is_active' => true,
                'valid_from' => now()->subDay(),
                'valid_until' => now()->addMonths(2),
            ],
            [
                'code' => 'STUDENTPASSFREE',
                'description' => 'Student Pass gratis (potongan 100%).',
                'discount_percent' => 100,
                'event_id' => $devFestEventId,
                'min_purchase' => 0,
                'max_discount' => null,
                'max_uses' => 30,
                'used_count' => 0,
                'is_active' => true,
                'valid_from' => now()->subDay(),
                'valid_until' => now()->addMonth(),
            ],
        ];

        foreach ($vouchers as $voucher) {
            if ($voucher['event_id'] === null || $voucher['event_id']) {
                Voucher::updateOrCreate(
                    ['code' => $voucher['code']],
                    $voucher
                );
            }
        }
    }
}