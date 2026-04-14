<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event', function (Blueprint $table) {
            $table->increments('id_event');
            $table->string('title');
            $table->text('description');
            $table->string('banner')->nullable();
            $table->string('location');
            $table->string('city')->default('Jakarta');
            $table->enum('format', ['onsite', 'online'])->default('onsite');
            $table->dateTime('date');
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedBigInteger('organizer_id')->nullable();
            
            $table->timestamps();

            $table->foreign('category_id')->references('id_category')->on('event_categories')->onDelete('set null');
            $table->foreign('organizer_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
