<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAgencyPlacesToAgencyDestinations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('agency_places')) {
            Schema::rename('agency_places', 'agency_destinations');
        }

        if (Schema::hasTable('agency_destinations')) {
            Schema::table('agency_destinations', function (Blueprint $table) {
                if (Schema::hasColumn('agency_destinations', 'place_id')) {
                    $table->renameColumn('place_id', 'destination_id');
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
        if (Schema::hasTable('agency_destinations')) {
            Schema::table('agency_destinations', function (Blueprint $table) {
                if (Schema::hasColumn('agency_destinations', 'destination_id')) {
                    $table->renameColumn('destination_id', 'place_id');
                }
            });
            Schema::rename('agency_destinations', 'agency_places');
        }
    }
}
