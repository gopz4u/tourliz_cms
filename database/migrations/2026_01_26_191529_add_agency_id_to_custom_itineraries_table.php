<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgencyIdToCustomItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->after('user_id')->constrained('agencies')->onDelete('cascade');

            // Make user_id nullable since it might be created by admin without specific user context, 
            // or we keep user_id as "created_by" (Admin)
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            //
        });
    }
}
