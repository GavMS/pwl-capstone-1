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
        Schema::create('event_ticket_types', function (Blueprint $table) {
            $table->id();
            // Menggunakan unsignedInteger karena tabel 'event' menggunakan id_event (Integer increments)
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('ticket_type_id');

            $table->integer('price')->comment('Harga tiket untuk event ini');
            $table->integer('stock')->comment('Kuota tiket untuk event ini');
            $table->timestamps();
            // Definisi Foreign Key Manual agar sinkron
            $table->foreign('event_id')->references('id_event')->on('event')->onDelete('cascade');
            $table->foreign('ticket_type_id')->references('id_ticket_type')->on('ticket_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_ticket_types');
    }
};
