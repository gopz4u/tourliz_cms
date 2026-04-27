<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVehicleTypeToItineraryExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itinerary_expenses', function (Blueprint $table) {
            $table->string('vehicle_type')->nullable()->after('category');
        });
    }

    public function down()
    {
        Schema::table('itinerary_expenses', function (Blueprint $table) {
            $table->dropColumn('vehicle_type');
        });
    }
}
