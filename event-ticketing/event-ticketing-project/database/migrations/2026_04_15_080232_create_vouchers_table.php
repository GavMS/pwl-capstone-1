<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('description', 255)->nullable();
            $table->unsignedTinyInteger('discount_percent'); // 1-100
            // NULL = global (all events), filled = specific event only
            $table->unsignedInteger('event_id')->nullable();
            $table->foreign('event_id')->references('id_event')->on('event')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            // NULL = unlimited uses (only limited by time)
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
