<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Voucher Model
 *
 * Discount codes (percentage-based) that can be global or event-specific.
 * Supports limits: max usage, date validity, minimum purchase, max discount cap.
 *
 * Used by: Admin\VoucherController (CRUD), VoucherController (apply), CheckoutController.
 */
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
        'is_active'        => 'boolean',
        'discount_percent' => 'integer',
        'max_uses'         => 'integer',
        'used_count'       => 'integer',
        'valid_from'       => 'datetime',
        'valid_until'      => 'datetime',
        'min_purchase'     => 'integer',
        'max_discount'     => 'integer',
    ];

    // ─── Relationships ───────────────────────────────────

    /** Event scope (null = global voucher) */
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'id_event');
    }

    /** Usage records for tracking per-user redemptions */
    public function usages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    // ─── Business Logic ──────────────────────────────────

    /**
     * Check if this voucher is valid for a specific order.
     * Returns [bool $isValid, string $message].
     */
    public function isValidForOrder($eventId = null, $totalPrice = 0): array
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

    /**
     * Calculate the discount amount, capped by max_discount if set.
     */
    public function calculateDiscount($totalPrice): int
    {
        $discount = (int) floor(($this->discount_percent / 100) * $totalPrice);

        if ($this->max_discount !== null && $this->max_discount > 0 && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return $discount;
    }
}
