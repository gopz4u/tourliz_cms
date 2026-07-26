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
        Schema::table('places', function (Blueprint $table) {
            // Add city column if not exists
            if (!Schema::hasColumn('places', 'city')) {
                $table->string('city')->nullable()->after('location');
            }
            // Add country column if not exists (in case original migration didn't have it)
            if (!Schema::hasColumn('places', 'country')) {
                $table->string('country')->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            if (Schema::hasColumn('places', 'city')) {
                $table->dropColumn('city');
            }
            // Only drop country if it was added by this migration (not if it existed originally)
            // The original create_places_table migration already has a country column,
            // so we don't drop it here to avoid data loss.
        });
    }
};
