<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHighlightsAndDayInclusionsExclusions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Packages Table: Add highlights if not exists
        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                if (!Schema::hasColumn('packages', 'highlights')) {
                    $table->json('highlights')->nullable()->after('short_description');
                }
                if (!Schema::hasColumn('packages', 'inclusions')) {
                    $table->json('inclusions')->nullable();
                }
                if (!Schema::hasColumn('packages', 'exclusions')) {
                    $table->json('exclusions')->nullable();
                }
            });
        }

        // 2. Package Days Table: Add day highlights, inclusions, exclusions
        if (Schema::hasTable('package_days')) {
            Schema::table('package_days', function (Blueprint $table) {
                if (!Schema::hasColumn('package_days', 'highlights')) {
                    $table->json('highlights')->nullable()->after('description');
                }
                if (!Schema::hasColumn('package_days', 'inclusions')) {
                    $table->json('inclusions')->nullable()->after('highlights');
                }
                if (!Schema::hasColumn('package_days', 'exclusions')) {
                    $table->json('exclusions')->nullable()->after('inclusions');
                }
            });
        }

        // 3. Unified Itineraries Table: Add highlights
        if (Schema::hasTable('itineraries')) {
            Schema::table('itineraries', function (Blueprint $table) {
                if (!Schema::hasColumn('itineraries', 'highlights')) {
                    $table->json('highlights')->nullable()->after('description');
                }
            });
        }

        // 4. Unified Itinerary Days Table: Add day highlights, inclusions, exclusions
        if (Schema::hasTable('itinerary_days')) {
            Schema::table('itinerary_days', function (Blueprint $table) {
                if (!Schema::hasColumn('itinerary_days', 'highlights')) {
                    $table->json('highlights')->nullable()->after('description');
                }
                if (!Schema::hasColumn('itinerary_days', 'inclusions')) {
                    $table->json('inclusions')->nullable()->after('highlights');
                }
                if (!Schema::hasColumn('itinerary_days', 'exclusions')) {
                    $table->json('exclusions')->nullable()->after('inclusions');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn(['highlights']);
            });
        }
        if (Schema::hasTable('package_days')) {
            Schema::table('package_days', function (Blueprint $table) {
                $table->dropColumn(['highlights', 'inclusions', 'exclusions']);
            });
        }
        if (Schema::hasTable('itineraries')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->dropColumn(['highlights']);
            });
        }
        if (Schema::hasTable('itinerary_days')) {
            Schema::table('itinerary_days', function (Blueprint $table) {
                $table->dropColumn(['highlights', 'inclusions', 'exclusions']);
            });
        }
    }
}
