<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * VoucherUsage Model
 *
 * Records each user's redemption of a voucher.
 * Prevents double-usage: one user can only use each voucher once.
 *
 * Used by: CheckoutController, PaymentCallbackController, VoucherController.
 */
class VoucherUsage extends Model
{
    protected $fillable = [
        'voucher_id',
        'user_id',
        'transaction_id',
        'discount_amount',
    ];

    // ─── Relationships ───────────────────────────────────

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function user()
    {
        return $this->belongsTo(Accounts::class, 'user_id', 'id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
