<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerCurrencyToCustomItineraries extends Migration
{
    public function up()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->string('quote_currency', 10)->nullable()->after('currency');
            $table->decimal('exchange_rate', 18, 8)->default(1)->after('quote_currency');
            $table->date('exchange_rate_date')->nullable()->after('exchange_rate');
            $table->boolean('round_quote')->default(false)->after('exchange_rate_date');
        });
    }
    public function down()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->dropColumn(['quote_currency', 'exchange_rate', 'exchange_rate_date', 'round_quote']);
        });
    }
}
