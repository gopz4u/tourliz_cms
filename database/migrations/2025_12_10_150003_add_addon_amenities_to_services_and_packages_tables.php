<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddonAmenitiesToServicesAndPackagesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->json('addon_amenities')->nullable()->after('ticket_name')->comment('Array of add-on amenities');
        });
        
        Schema::table('packages', function (Blueprint $table) {
            $table->json('addon_amenities')->nullable()->after('ticket_name')->comment('Array of add-on amenities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('addon_amenities');
        });
        
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('addon_amenities');
        });
    }
}
