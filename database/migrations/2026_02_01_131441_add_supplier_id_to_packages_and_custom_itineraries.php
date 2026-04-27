<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierIdToPackagesAndCustomItineraries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('place_id');
            // We won't add foreign key constraint to avoid potential issues with existing data 
            // or if the supplier table engine differs (e.g. MyISAM vs InnoDB), though usually safe.
            // Keeping it simple as requested.
        });

        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('agency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
        });

        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
        });
    }
}
