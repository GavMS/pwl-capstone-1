<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Voucher;
use App\Models\VoucherUsage;

/**
 * VoucherController
 *
 * User-facing AJAX endpoint for applying voucher codes during checkout.
 *
 * Used by: voucher.apply route (called from order-details page via fetch/AJAX).
 */
class VoucherController extends Controller
{
    /**
     * Validate and apply a voucher code. Returns discount amount and final price.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code'        => 'required|string',
            'event_id'    => 'required|integer',
            'total_price' => 'required|numeric',
        ]);

        $code       = strtoupper($request->code);
        $totalPrice = (float) $request->total_price;

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Kode voucher tidak valid.']);
        }

        // Check voucher validity (active, dates, quota, event scope, min purchase)
        [$isValid, $message] = $voucher->isValidForOrder($request->event_id, $totalPrice);

        if (!$isValid) {
            return response()->json(['valid' => false, 'message' => $message]);
        }

        // Prevent double usage by the same user
        $hasUsed = VoucherUsage::where('voucher_id', $voucher->id)
            ->where('user_id', Auth::id())
            ->whereNotNull('transaction_id')
            ->exists();

        if ($hasUsed) {
            return response()->json(['valid' => false, 'message' => 'Anda sudah pernah menggunakan kode voucher ini!']);
        }

        $discountAmount = $voucher->calculateDiscount($totalPrice);
        $finalPrice     = max(0, $totalPrice - $discountAmount);

        return response()->json([
            'valid'            => true,
            'message'          => 'Voucher berhasil diterapkan!',
            'discount_percent' => $voucher->discount_percent,
            'discount_amount'  => $discountAmount,
            'final_price'      => $finalPrice,
        ]);
    }
}
