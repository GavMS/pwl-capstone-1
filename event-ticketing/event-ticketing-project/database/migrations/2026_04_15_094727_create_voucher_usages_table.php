<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_id');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('accounts')->onDelete('cascade');
            // Filled after payment is confirmed
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('transaction')->onDelete('set null');
            // Amount saved in rupiah (e.g. 10% of 100000 = 10000)
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->timestamps();

            // Prevent 1 user from using the same voucher more than once
            $table->unique(['voucher_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
    }
};
