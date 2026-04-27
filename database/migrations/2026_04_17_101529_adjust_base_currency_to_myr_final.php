<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AdjustBaseCurrencyToMyrFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Update rates in currency_exchange_rates table
        // These rates are MYR based (1 MYR = X Other Currency)
        // Wait! If 1 MYR = 17.5 INR, then the rate should be 17.5 if we want to multiply by it?
        // Actually, usually exchange_rate is "how much of this currency for 1 base currency".
        // If MYR is base (1.0), and 1 MYR = 17.5 INR, then INR rate is 17.5.
        // Let's check the old USD rate. It was 83.0 (1 USD = 83 INR).
        // Since my previous logic did: amount_in_base = amount / fromRate.
        // If amount is 83 INR, and rate is 1.0, baseAmount is 83. Correct.
        // If amount is 1 USD, and rate is 83, baseAmount is 1/83. Correct.
        // Wait! If I want INR to be THE BASE, everything is relative to INR.
        
        // NOW: MYR is base.
        // 1 MYR = 1.0
        // 1 INR = (1/17.5) MYR = 0.0571
        // 1 USD = (83/17.5) MYR = 4.7428
        // 1 SGD = (62/17.5) MYR = 3.5428
        // 1 AED = (22.6/17.5) MYR = 1.2914
        
        DB::table('currency_exchange_rates')->where('code', 'MYR')->update(['exchange_rate' => 1.0000, 'is_default' => 1]);
        DB::table('currency_exchange_rates')->where('code', 'INR')->update(['exchange_rate' => 0.0571, 'is_default' => 0]);
        DB::table('currency_exchange_rates')->where('code', 'USD')->update(['exchange_rate' => 4.7428]);
        DB::table('currency_exchange_rates')->where('code', 'SGD')->update(['exchange_rate' => 3.5428]);
        DB::table('currency_exchange_rates')->where('code', 'AED')->update(['exchange_rate' => 1.2914]);

        // 2. Update defaults in other tables
        Schema::table('packages', function (Blueprint $table) {
            $table->string('currency', 3)->default('MYR')->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('currency', 3)->default('MYR')->change();
        });

        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->string('currency', 3)->default('MYR')->change();
        });
        
        Schema::table('b2c_itineraries', function (Blueprint $table) {
            $table->string('currency', 3)->default('MYR')->change();
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->string('currency', 3)->default('MYR')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to INR as base
        DB::table('currency_exchange_rates')->where('code', 'INR')->update(['exchange_rate' => 1.0000, 'is_default' => 1]);
        DB::table('currency_exchange_rates')->where('code', 'MYR')->update(['exchange_rate' => 17.5000, 'is_default' => 0]);
        DB::table('currency_exchange_rates')->where('code', 'USD')->update(['exchange_rate' => 83.0000]);
        DB::table('currency_exchange_rates')->where('code', 'SGD')->update(['exchange_rate' => 62.0000]);
        DB::table('currency_exchange_rates')->where('code', 'AED')->update(['exchange_rate' => 22.6000]);

        Schema::table('packages', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });

        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });
        
        Schema::table('b2c_itineraries', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->change();
        });
    }
}
