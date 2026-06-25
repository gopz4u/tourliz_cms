<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryIdsToPackages extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->json('country_ids')->nullable()->after('country_id');
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('country_ids');
        });
    }
}
