<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItineraryExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itinerary_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itinerary_id');
            $table->string('itinerary_type'); // 'b2b' or 'b2c'

            $table->string('category'); // Hotel, Transport, Activity, Meal, Other
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('INR');
            $table->string('description')->nullable();
            $table->string('supplier_name')->nullable();
            $table->date('expense_date');

            $table->foreignId('created_by')->nullable()->constrained('admins');
            $table->timestamps();

            $table->index(['itinerary_id', 'itinerary_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itinerary_expenses');
    }
}
