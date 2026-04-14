<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->decimal('total_price', 12, 2);
            $table->string('payment_method')->default('midtrans');
            $table->dateTime('deadline_payment')->nullable();
            $table->unsignedBigInteger('accounts_id'); 
            
            $table->string('order_id')->unique();
            $table->string('snap_token')->nullable();
            $table->json('ticket_payload')->nullable();
            $table->json('customer_details')->nullable();
            
            $table->timestamps();

            $table->foreign('accounts_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
