<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTravelDateStatusToLeadItineraries extends Migration
{
    public function up()
    {
        foreach (['custom_itineraries', 'b2c_itineraries'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'travel_date_status')) {
                    $table->string('travel_date_status', 20)->default('tbd')->after('start_date')->index();
                }
                if (!Schema::hasColumn($tableName, 'preferred_travel_month')) {
                    $table->string('preferred_travel_month', 7)->nullable()->after('travel_date_status');
                }
                if (!Schema::hasColumn($tableName, 'travel_date_notes')) {
                    $table->string('travel_date_notes', 255)->nullable()->after('preferred_travel_month');
                }
            });

            // Existing real dates are exact. Empty dates remain explicitly TBD.
            DB::table($tableName)
                ->whereNotNull('start_date')
                ->update(['travel_date_status' => 'exact']);
        }
    }

    public function down()
    {
        foreach (['custom_itineraries', 'b2c_itineraries'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'travel_date_status')) {
                    $table->dropIndex($tableName . '_travel_date_status_index');
                    $table->dropColumn('travel_date_status');
                }
                if (Schema::hasColumn($tableName, 'preferred_travel_month')) {
                    $table->dropColumn('preferred_travel_month');
                }
                if (Schema::hasColumn($tableName, 'travel_date_notes')) {
                    $table->dropColumn('travel_date_notes');
                }
            });
        }
    }
}
