<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountAndCountryToPackageOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_offers', function (Blueprint $table) {
            $table->string('discount_type')->nullable(); // fixed, percentage
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->text('countries')->nullable(); // JSON array of country names
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_offers', function (Blueprint $table) {
            //
        });
    }
}
