<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only add deleted_at column if it doesn't exist
        if (Schema::hasTable('services') && !Schema::hasColumn('services', 'deleted_at')) {
            Schema::table('services', function (Blueprint $table) {
                $table->softDeletes();
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
        if (Schema::hasTable('services') && Schema::hasColumn('services', 'deleted_at')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
}
