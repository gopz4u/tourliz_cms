<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCustomItinerariesUserIdForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            Schema::table('custom_itineraries', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }
    }

    public function down()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}
