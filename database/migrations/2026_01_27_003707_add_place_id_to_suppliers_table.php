<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlaceIdToSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('place_id')->nullable()->after('id');
            // We can make it a foreign key if places table exists, which it does.
            // But let's make it optional in case of 'global' suppliers? Or just nullable.
        });
    }

    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('place_id');
        });
    }
}
