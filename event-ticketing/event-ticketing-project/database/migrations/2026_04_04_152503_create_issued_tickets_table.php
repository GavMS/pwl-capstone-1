<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issued_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign Key ke accounts
            $table->unsignedBigInteger('event_ticket_type_id'); // Foreign Key ke event_ticket_types
            $table->string('unique_code')->unique();
            $table->string('qr_image');
            $table->enum('status', ['active', 'used', 'cancelled'])->default('active');
            $table->timestamps();

            // Mendefinisikan Relasi
            $table->foreign('user_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('event_ticket_type_id')->references('id')->on('event_ticket_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issued_tickets');
    }
};
