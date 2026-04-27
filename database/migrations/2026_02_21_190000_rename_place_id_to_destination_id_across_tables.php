<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePlaceIdToDestinationIdAcrossTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'packages',
            'suppliers',
            'b2c_itineraries',
            'tourist_spots',
            'meals',
            'hotels',
            'activities',
            'transports',
            'agencies',
            'custom_itineraries',
            'services',
            'group_packages',
            'attractions'
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'place_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('place_id', 'destination_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'packages',
            'suppliers',
            'b2c_itineraries',
            'tourist_spots',
            'meals',
            'hotels',
            'activities',
            'transports',
            'agencies',
            'custom_itineraries',
            'services',
            'group_packages',
            'attractions'
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'destination_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('destination_id', 'place_id');
                });
            }
        }
    }
}
