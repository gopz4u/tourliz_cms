<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFixedPricingToB2cItineraries extends Migration
{
    public function up()
    {
        Schema::table('b2c_itineraries', function (Blueprint $table) {
            $table->unsignedBigInteger('fixed_itinerary_id')->nullable()->index()->after('destination_id');
            $table->decimal('fixed_price', 15, 2)->nullable()->after('total_price');
        });
    }

    public function down()
    {
        Schema::table('b2c_itineraries', function (Blueprint $table) {
            $table->dropIndex(['fixed_itinerary_id']);
            $table->dropColumn(['fixed_itinerary_id', 'fixed_price']);
        });
    }
}
