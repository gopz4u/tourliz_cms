<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidByToItineraryExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itinerary_expenses', function (Blueprint $table) {
            $table->string('paid_by')->nullable()->after('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itinerary_expenses', function (Blueprint $table) {
            $table->dropColumn('paid_by');
        });
    }
}
