<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPricingModeToB2cItineraries extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('b2c_itineraries', 'pricing_mode')) {
            Schema::table('b2c_itineraries', function (Blueprint $table) {
                $table->string('pricing_mode', 20)->default('live')->after('fixed_price');
            });

            DB::table('b2c_itineraries')
                ->whereNotNull('fixed_price')->where('fixed_price', '>', 0)
                ->update(['pricing_mode' => 'fixed']);
        }
    }

    public function down()
    {
        if (Schema::hasColumn('b2c_itineraries', 'pricing_mode')) {
            Schema::table('b2c_itineraries', fn (Blueprint $table) => $table->dropColumn('pricing_mode'));
        }
    }
}
