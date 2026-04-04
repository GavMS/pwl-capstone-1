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
        Schema::create('event', function (Blueprint $table) {
            $table->increments('id_event');
            $table->string('title');
            $table->string('description');
            $table->string('banner')->nullable();
            $table->string('location');
            $table->string('city')->default('Jakarta');
            $table->enum('format', ['onsite', 'online'])->default('onsite');
            $table->dateTime('date');
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
