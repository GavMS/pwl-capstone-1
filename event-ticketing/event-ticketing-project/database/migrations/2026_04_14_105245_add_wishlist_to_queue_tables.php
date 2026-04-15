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
        Schema::table('shopping_sessions', function (Blueprint $table) {
            $table->json('wishlist')->nullable()->after('event_id');
        });

        Schema::table('waiting_lists', function (Blueprint $table) {
            $table->json('wishlist')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('shopping_sessions', function (Blueprint $table) {
            $table->dropColumn('wishlist');
        });

        Schema::table('waiting_lists', function (Blueprint $table) {
            $table->dropColumn('wishlist');
        });
    }
};
