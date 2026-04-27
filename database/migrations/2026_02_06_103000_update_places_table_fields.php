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
            // Rename location to country
            if (Schema::hasColumn('places', 'location')) {
                $table->renameColumn('location', 'country');
            }
            // Rename region to location
            if (Schema::hasColumn('places', 'region')) {
                $table->renameColumn('region', 'location');
            }
            // Add city column
            if (!Schema::hasColumn('places', 'city')) {
                $table->string('city')->nullable()->after('image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            if (Schema::hasColumn('places', 'country')) {
                $table->renameColumn('country', 'location');
            }
            if (Schema::hasColumn('places', 'location')) {
                $table->renameColumn('location', 'region');
            }
            if (Schema::hasColumn('places', 'city')) {
                $table->dropColumn('city');
            }
        });
    }
};
