<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackageIdToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('services') && !Schema::hasColumn('services', 'package_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->foreignId('package_id')->nullable()->after('place_id')->constrained('packages')->onDelete('set null');
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
        if (Schema::hasTable('services') && Schema::hasColumn('services', 'package_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropForeign(['package_id']);
                $table->dropColumn('package_id');
            });
        }
    }
}
