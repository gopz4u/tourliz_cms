<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToItineraryExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itinerary_expenses', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('amount'); // pending, requested, confirmed, rejected
        });
    }

    public function down()
    {
        Schema::table('itinerary_expenses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
