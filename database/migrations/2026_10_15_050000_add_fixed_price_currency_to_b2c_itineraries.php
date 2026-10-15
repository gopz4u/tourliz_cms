<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFixedPriceCurrencyToB2cItineraries extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('b2c_itineraries', 'fixed_price_currency')) {
            Schema::table('b2c_itineraries', function (Blueprint $table) {
                $table->string('fixed_price_currency', 10)->nullable()->after('fixed_price');
            });
        }

        DB::table('b2c_itineraries')
            ->whereNotNull('fixed_price')->where('fixed_price', '>', 0)
            ->orderBy('id')->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $fixedCurrency = $row->fixed_price_currency ?: ($row->currency ?: 'MYR');
                    DB::table('b2c_itineraries')->where('id', $row->id)->update([
                        'fixed_price_currency' => $fixedCurrency,
                        'currency' => 'MYR',
                        'quote_currency' => $fixedCurrency,
                    ]);
                }
            });
    }

    public function down()
    {
        if (Schema::hasColumn('b2c_itineraries', 'fixed_price_currency')) {
            Schema::table('b2c_itineraries', fn (Blueprint $table) => $table->dropColumn('fixed_price_currency'));
        }
    }
}
