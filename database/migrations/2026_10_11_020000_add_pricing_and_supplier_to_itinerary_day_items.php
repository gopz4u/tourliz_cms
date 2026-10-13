<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingAndSupplierToItineraryDayItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('itinerary_day_items')) {
            Schema::table('itinerary_day_items', function (Blueprint $table) {
                if (!Schema::hasColumn('itinerary_day_items', 'price')) {
                    $table->decimal('price', 12, 2)->default(0.00)->after('description');
                }
                if (!Schema::hasColumn('itinerary_day_items', 'cost')) {
                    $table->decimal('cost', 12, 2)->default(0.00)->after('price');
                }
                if (!Schema::hasColumn('itinerary_day_items', 'supplier_id')) {
                    $table->unsignedBigInteger('supplier_id')->nullable()->after('cost');
                }
                if (!Schema::hasColumn('itinerary_day_items', 'room_type')) {
                    $table->string('room_type')->nullable()->after('supplier_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('itinerary_day_items')) {
            Schema::table('itinerary_day_items', function (Blueprint $table) {
                $columns = ['price', 'cost', 'supplier_id', 'room_type'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('itinerary_day_items', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
}
