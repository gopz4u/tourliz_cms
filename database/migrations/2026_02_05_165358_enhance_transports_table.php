<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade')->after('id');
            $table->string('destination')->nullable()->after('vehicle_type'); // e.g. Airport Drop, Hotel Transfer, City Tour
            $table->string('duration')->nullable()->after('destination'); // e.g. 4 Hours, 8 Hours, Transfer
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'destination', 'duration']);
        });
    }
}
