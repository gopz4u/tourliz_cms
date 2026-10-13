<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('itineraries') && !Schema::hasColumn('itineraries', 'currency')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->string('currency', 10)->default('MYR')->after('status');
            });
        }

        if (Schema::hasTable('itinerary_b2c_details') && !Schema::hasColumn('itinerary_b2c_details', 'currency')) {
            Schema::table('itinerary_b2c_details', function (Blueprint $table) {
                $table->string('currency', 10)->default('MYR')->after('selling_price');
            });
        }

        if (Schema::hasTable('itinerary_b2b_details') && !Schema::hasColumn('itinerary_b2b_details', 'currency')) {
            Schema::table('itinerary_b2b_details', function (Blueprint $table) {
                $table->string('currency', 10)->default('MYR')->after('net_rate');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('itineraries') && Schema::hasColumn('itineraries', 'currency')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }

        if (Schema::hasTable('itinerary_b2c_details') && Schema::hasColumn('itinerary_b2c_details', 'currency')) {
            Schema::table('itinerary_b2c_details', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }

        if (Schema::hasTable('itinerary_b2b_details') && Schema::hasColumn('itinerary_b2b_details', 'currency')) {
            Schema::table('itinerary_b2b_details', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
};
