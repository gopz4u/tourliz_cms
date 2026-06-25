<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryIdsToB2cItineraries extends Migration
{
    public function up()
    {
        Schema::table('b2c_itineraries', function (Blueprint $table) {
            $table->json('country_ids')->nullable()->after('destination_id');
        });
    }

    public function down()
    {
        Schema::table('b2c_itineraries', function (Blueprint $table) {
            $table->dropColumn('country_ids');
        });
    }
}
