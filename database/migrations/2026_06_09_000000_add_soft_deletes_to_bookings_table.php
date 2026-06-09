<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the weird column bookings.deleted_at if it exists
            try {
                DB::statement("ALTER TABLE bookings DROP COLUMN `bookings.deleted_at`");
            } catch (\Throwable $e) {
                // Ignore if it doesn't exist
            }

            // Add standard deleted_at column if it doesn't exist
            if (!Schema::hasColumn('bookings', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
