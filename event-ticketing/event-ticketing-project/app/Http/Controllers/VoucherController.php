<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    // API endpoint for User to apply voucher code at checkout step 2
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'event_id' => 'required|integer',
            'total_price' => 'required|numeric'
        ]);

        $code = strtoupper($request->code);
        $totalPrice = (float) $request->total_price;

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher tidak valid.'
            ]);
        }

        // Internal Validation Logic inside Voucher Model
        [$isValid, $message] = $voucher->isValidForOrder($request->event_id, $totalPrice);

        if (!$isValid) {
            return response()->json([
                'valid' => false,
                'message' => $message
            ]);
        }

        // Anti-Stack/Anti-Double Usage Constraint: Users who have already used it via successful transaction
        $hasUsed = VoucherUsage::where('voucher_id', $voucher->id)
            ->where('user_id', Auth::id())
            ->whereNotNull('transaction_id')
            ->exists();

        if ($hasUsed) {
            return response()->json([
                'valid' => false,
                'message' => 'Anda sudah pernah menggunakan kode voucher ini!'
            ]);
        }

        // If valid, calculate discount using model logically (applying caps if any)
        $discountAmount = $voucher->calculateDiscount($totalPrice);
        
        $finalPrice = max(0, $totalPrice - $discountAmount);

        return response()->json([
            'valid' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'discount_percent' => $voucher->discount_percent,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice
        ]);
    }
}
