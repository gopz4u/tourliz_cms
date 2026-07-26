<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'duration')) {
                $table->string('duration')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('packages', 'status')) {
                $table->string('status')->default('active')->after('is_trending');
            }
            if (!Schema::hasColumn('packages', 'featured')) {
                $table->boolean('featured')->default(false);
            }
            if (!Schema::hasColumn('packages', 'included_services')) {
                $table->text('included_services')->nullable();
            }
            if (!Schema::hasColumn('packages', 'excluded_services')) {
                $table->text('excluded_services')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'duration')) {
                $table->dropColumn('duration');
            }
            if (Schema::hasColumn('packages', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('packages', 'featured')) {
                $table->dropColumn('featured');
            }
            if (Schema::hasColumn('packages', 'included_services')) {
                $table->dropColumn('included_services');
            }
            if (Schema::hasColumn('packages', 'excluded_services')) {
                $table->dropColumn('excluded_services');
            }
        });
    }
};
