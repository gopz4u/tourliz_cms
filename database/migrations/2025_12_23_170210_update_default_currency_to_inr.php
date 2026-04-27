<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateDefaultCurrencyToInr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if currency column exists before updating
        try {
            // Update existing records with USD or NULL to INR (only if column exists)
            if (Schema::hasColumn('packages', 'currency')) {
                DB::table('packages')->where('currency', 'USD')->orWhereNull('currency')->update(['currency' => 'INR']);
            }
            if (Schema::hasColumn('services', 'currency')) {
                DB::table('services')->where('currency', 'USD')->orWhereNull('currency')->update(['currency' => 'INR']);
            }
            if (Schema::hasColumn('attractions', 'currency')) {
                DB::table('attractions')->where('currency', 'USD')->orWhereNull('currency')->update(['currency' => 'INR']);
            }
        } catch (\Exception $e) {
            // Column might not exist yet, skip update
        }
        
        // Note: Default values for new records will be handled by model/controller defaults (INR)
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert existing records back to USD
        DB::table('packages')->where('currency', 'INR')->update(['currency' => 'USD']);
        DB::table('services')->where('currency', 'INR')->update(['currency' => 'USD']);
        DB::table('attractions')->where('currency', 'INR')->update(['currency' => 'USD']);
    }
}
