<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingFieldsToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('net_price', 10, 2)->nullable()->after('price');
            $table->decimal('markup_percentage', 5, 2)->default(0)->after('net_price');
            $table->decimal('markup_amount', 10, 2)->default(0)->after('markup_percentage');
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
            $table->dropColumn(['net_price', 'markup_percentage', 'markup_amount']);
        });
    }
}
