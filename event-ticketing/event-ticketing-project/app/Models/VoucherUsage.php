<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    protected $fillable = [
        'voucher_id',
        'user_id',
        'transaction_id',
        'discount_amount'
    ];

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
