<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['transports', 'hotel_rooms', 'entry_tickets', 'meals', 'tourist_spots'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'currency')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('currency', 3)->default('MYR')->after(Schema::hasColumn($table->getTable(), 'base_price') ? 'base_price' : (Schema::hasColumn($table->getTable(), 'adult_price') ? 'adult_price' : 'id'));
                });
            }
        }

        // Update existing INR defaults to MYR in packages, attractions, services
        if (Schema::hasColumn('packages', 'currency')) {
            DB::table('packages')->where('currency', 'INR')->orWhereNull('currency')->update(['currency' => 'MYR']);
        }
        if (Schema::hasColumn('attractions', 'currency')) {
            DB::table('attractions')->where('currency', 'INR')->orWhereNull('currency')->update(['currency' => 'MYR']);
        }
        if (Schema::hasColumn('services', 'currency')) {
            DB::table('services')->where('currency', 'INR')->orWhereNull('currency')->update(['currency' => 'MYR']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = ['transports', 'hotel_rooms', 'entry_tickets', 'meals', 'tourist_spots'];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'currency')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('currency');
                });
            }
        }
    }
};
