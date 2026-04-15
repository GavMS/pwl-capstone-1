<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'description',
        'discount_percent',
        'event_id',
        'is_active',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'min_purchase',
        'max_discount',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_percent' => 'integer',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'min_purchase' => 'integer',
        'max_discount' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'id_event');
    }

    public function usages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    // Business Logic: Check if valid globally or for specific event, applying new limits
    public function isValidForOrder($eventId = null, $totalPrice = 0)
    {
        if (!$this->is_active) {
            return [false, 'Voucher tidak aktif.'];
        }

        if ($this->valid_from && now()->lt($this->valid_from)) {
            return [false, 'Voucher belum bisa digunakan.'];
        }

        if ($this->valid_until && now()->gt($this->valid_until)) {
            return [false, 'Voucher sudah kadaluarsa.'];
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return [false, 'Kuota pemakaian voucher sudah habis.'];
        }

        if ($this->event_id !== null && $eventId !== null && $this->event_id != $eventId) {
            return [false, 'Voucher ini tidak berlaku untuk event ini.'];
        }

        if ($this->min_purchase > 0 && $totalPrice < $this->min_purchase) {
            return [false, 'Minimal transaksi untuk menggunakan voucher ini adalah Rp ' . number_format($this->min_purchase, 0, ',', '.')];
        }

        return [true, 'Voucher valid'];
    }

    public function calculateDiscount($totalPrice)
    {
        $discount = floor(($this->discount_percent / 100) * $totalPrice);

        // Cap nominal discount if max_discount is defined
        if ($this->max_discount !== null && $this->max_discount > 0 && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return $discount;
    }
}
