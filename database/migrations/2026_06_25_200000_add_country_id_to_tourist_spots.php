<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryIdToTouristSpots extends Migration
{
    public function up()
    {
        Schema::table('tourist_spots', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('destination_id')->constrained('countries')->onDelete('set null');
            // Make destination optional — spots can be country-level
            $table->unsignedBigInteger('destination_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tourist_spots', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
            $table->unsignedBigInteger('destination_id')->nullable(false)->change();
        });
    }
}
