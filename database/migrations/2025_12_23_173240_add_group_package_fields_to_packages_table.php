<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupPackageFieldsToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('price_2_6', 10, 2)->nullable()->after('discount_price')->comment('Price for kids age 2-6');
            $table->decimal('price_6_10', 10, 2)->nullable()->after('price_2_6')->comment('Price for kids age 6-10');
            $table->date('announcement_date')->nullable()->after('duration')->comment('Date when package is announced/available');
            $table->integer('total_pax')->nullable()->after('announcement_date')->comment('Total passengers/people');
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
            $table->dropColumn(['price_2_6', 'price_6_10', 'announcement_date', 'total_pax']);
        });
    }
}
