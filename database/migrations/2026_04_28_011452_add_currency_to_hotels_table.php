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
        if (Schema::hasTable('hotels') && !Schema::hasColumn('hotels', 'currency')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->string('currency', 3)->default('MYR')->after('id');
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
        if (Schema::hasColumn('hotels', 'currency')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
};
