<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxFieldsToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('gst_percentage', 5, 2)->nullable()->after('markup_amount');
            $table->decimal('tcs_percentage', 5, 2)->nullable()->after('gst_percentage');
            $table->decimal('tax_amount', 15, 2)->nullable()->after('tcs_percentage');
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['gst_percentage', 'tcs_percentage', 'tax_amount']);
        });
    }
}
