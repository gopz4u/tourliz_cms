<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RepairMissingSupplierIdsColumns extends Migration
{
    /**
     * Repair production databases where the multi-supplier feature was
     * deployed without all of its required JSON columns.
     *
     * @return void
     */
    public function up()
    {
        $this->addSupplierIdsColumn('fixed_itineraries', 'supplier_id');
        $this->addSupplierIdsColumn('itineraries', 'destination_id');
        $this->addSupplierIdsColumn('itinerary_b2b_details', 'supplier_id');
    }

    /**
     * This is a repair migration. Rolling it back must not remove columns
     * owned by the original feature migration or destroy production data.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally left blank.
    }

    private function addSupplierIdsColumn(string $tableName, string $afterColumn): void
    {
        if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'supplier_ids')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($afterColumn) {
            $table->json('supplier_ids')->nullable()->after($afterColumn);
        });
    }
}
