<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            // Voucher applied to this transaction (nullable — not all orders use a voucher)
            $table->unsignedBigInteger('voucher_id')->nullable()->after('status');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');
            // The rupiah amount discounted
            $table->unsignedBigInteger('discount_amount')->default(0)->after('voucher_id');
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['voucher_id', 'discount_amount']);
        });
    }
};
