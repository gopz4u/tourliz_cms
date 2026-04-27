<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidAmountToItineraryExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itinerary_expenses', function (Blueprint $table) {
            $table->decimal('paid_amount', 15, 2)->default(0)->after('amount');
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
            $table->dropColumn('paid_amount');
        });
    }
}
