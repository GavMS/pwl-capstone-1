<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issued_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_ticket_type_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            
            $table->string('unique_code')->unique();
            $table->string('qr_image')->nullable();
            $table->string('status')->default('valid');
            
            // Attendee Info
            $table->string('attendee_name')->nullable();
            $table->string('attendee_email')->nullable();
            $table->string('attendee_phone')->nullable();
            $table->string('attendee_id_card')->nullable();
            $table->date('attendee_dob')->nullable();
            $table->string('attendee_gender')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('event_ticket_type_id')->references('id')->on('event_ticket_types')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transaction')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issued_tickets');
    }
};
