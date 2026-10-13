<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierIdsToItinerariesAndFixedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('fixed_itineraries') && !Schema::hasColumn('fixed_itineraries', 'supplier_ids')) {
            Schema::table('fixed_itineraries', function (Blueprint $table) {
                $table->json('supplier_ids')->nullable()->after('supplier_id');
            });
        }

        if (Schema::hasTable('itineraries') && !Schema::hasColumn('itineraries', 'supplier_ids')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->json('supplier_ids')->nullable()->after('destination_id');
            });
        }

        if (Schema::hasTable('itinerary_b2b_details') && !Schema::hasColumn('itinerary_b2b_details', 'supplier_ids')) {
            Schema::table('itinerary_b2b_details', function (Blueprint $table) {
                $table->json('supplier_ids')->nullable()->after('supplier_id');
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
        if (Schema::hasTable('fixed_itineraries') && Schema::hasColumn('fixed_itineraries', 'supplier_ids')) {
            Schema::table('fixed_itineraries', function (Blueprint $table) {
                $table->dropColumn('supplier_ids');
            });
        }

        if (Schema::hasTable('itineraries') && Schema::hasColumn('itineraries', 'supplier_ids')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->dropColumn('supplier_ids');
            });
        }

        if (Schema::hasTable('itinerary_b2b_details') && Schema::hasColumn('itinerary_b2b_details', 'supplier_ids')) {
            Schema::table('itinerary_b2b_details', function (Blueprint $table) {
                $table->dropColumn('supplier_ids');
            });
        }
    }
}
