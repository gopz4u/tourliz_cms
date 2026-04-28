<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currency_exchange_rates', function (Blueprint $table) {
            // Rename currency_code to code
            if (Schema::hasColumn('currency_exchange_rates', 'currency_code') && !Schema::hasColumn('currency_exchange_rates', 'code')) {
                $table->renameColumn('currency_code', 'code');
            }

            // Rename currency_name to name
            if (Schema::hasColumn('currency_exchange_rates', 'currency_name') && !Schema::hasColumn('currency_exchange_rates', 'name')) {
                $table->renameColumn('currency_name', 'name');
            }

            // Rename rate_to_inr to exchange_rate
            if (Schema::hasColumn('currency_exchange_rates', 'rate_to_inr') && !Schema::hasColumn('currency_exchange_rates', 'exchange_rate')) {
                $table->renameColumn('rate_to_inr', 'exchange_rate');
            }

            // Add missing columns if they don't exist
            if (!Schema::hasColumn('currency_exchange_rates', 'symbol')) {
                $table->string('symbol', 10)->nullable()->after('name');
            }
            if (!Schema::hasColumn('currency_exchange_rates', 'country_code')) {
                $table->string('country_code', 2)->nullable()->after('symbol');
            }
            if (!Schema::hasColumn('currency_exchange_rates', 'flag_emoji')) {
                $table->string('flag_emoji')->nullable()->after('country_code');
            }
            if (!Schema::hasColumn('currency_exchange_rates', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('exchange_rate');
            }
            if (!Schema::hasColumn('currency_exchange_rates', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currency_exchange_rates', function (Blueprint $table) {
            // Reverse renames if necessary (optional, but good practice)
            if (Schema::hasColumn('currency_exchange_rates', 'code') && !Schema::hasColumn('currency_exchange_rates', 'currency_code')) {
                $table->renameColumn('code', 'currency_code');
            }
            if (Schema::hasColumn('currency_exchange_rates', 'name') && !Schema::hasColumn('currency_exchange_rates', 'currency_name')) {
                $table->renameColumn('name', 'currency_name');
            }
            if (Schema::hasColumn('currency_exchange_rates', 'exchange_rate') && !Schema::hasColumn('currency_exchange_rates', 'rate_to_inr')) {
                $table->renameColumn('exchange_rate', 'rate_to_inr');
            }
            
            // Drop columns we added
            $table->dropColumn(['symbol', 'country_code', 'flag_emoji', 'is_default', 'sort_order']);
        });
    }
};
