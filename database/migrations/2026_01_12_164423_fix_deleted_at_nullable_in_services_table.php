<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixDeletedAtNullableInServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('services') && Schema::hasColumn('services', 'deleted_at')) {
            // Use raw SQL to alter the column to be nullable
            DB::statement('ALTER TABLE `services` MODIFY COLUMN `deleted_at` TIMESTAMP NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to reverse - nullable is the correct state
    }
}
