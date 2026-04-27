<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCurrencyExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3)->unique(); // INR, USD, MYR, SGD, AED
            $table->string('currency_name')->nullable(); // Indian Rupee, US Dollar, etc.
            $table->decimal('rate_to_inr', 10, 4)->default(1.0000); // Exchange rate to INR (base currency)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Insert default currencies with initial rates (1 INR = 1 INR, approximate rates)
        DB::table('currency_exchange_rates')->insert([
            ['currency_code' => 'INR', 'currency_name' => 'Indian Rupee', 'rate_to_inr' => 1.0000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['currency_code' => 'USD', 'currency_name' => 'US Dollar', 'rate_to_inr' => 83.0000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['currency_code' => 'MYR', 'currency_name' => 'Malaysian Ringgit', 'rate_to_inr' => 17.5000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['currency_code' => 'SGD', 'currency_name' => 'Singapore Dollar', 'rate_to_inr' => 62.0000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['currency_code' => 'AED', 'currency_name' => 'UAE Dirham', 'rate_to_inr' => 22.6000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_exchange_rates');
    }
}
