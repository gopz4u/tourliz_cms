<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToPackagesTableIfNotExists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('packages', 'currency')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->string('currency', 3)->default('INR')->after('discount_price');
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
        if (Schema::hasColumn('packages', 'currency')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
}
