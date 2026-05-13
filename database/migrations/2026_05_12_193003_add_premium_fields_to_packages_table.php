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
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'is_trending')) {
                $table->boolean('is_trending')->default(false)->after('featured');
            }
            if (!Schema::hasColumn('packages', 'cancellation_policy')) {
                $table->text('cancellation_policy')->nullable()->after('excluded_services');
            }
            if (!Schema::hasColumn('packages', 'terms')) {
                $table->text('terms')->nullable()->after('cancellation_policy');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['is_trending', 'cancellation_policy', 'terms']);
        });
    }
};
