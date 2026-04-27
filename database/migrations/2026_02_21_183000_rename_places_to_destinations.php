<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename places table to destinations
        Schema::rename('places', 'destinations');

        // Note: Renaming foreign keys in all other tables might be complex.
        // For now, we will keep place_id column names but they will point to destinations table.
        // If the user wants to rename columns too, we can add that.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('destinations', 'places');
    }
};
