<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageDayMealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_day_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_day_meals');
    }
}
