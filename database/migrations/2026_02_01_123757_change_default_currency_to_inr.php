<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDefaultCurrencyToInr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change default currency to INR for various tables

        Schema::table('packages', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });

        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });

        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });
    }
}
