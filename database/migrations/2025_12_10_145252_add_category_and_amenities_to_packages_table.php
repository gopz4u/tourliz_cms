<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryAndAmenitiesToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('category')->nullable()->after('place_id')->comment('Package category');
            $table->integer('star_rating')->nullable()->after('category')->comment('Hotel star rating (1-5)');
            $table->string('vehicle_type')->nullable()->after('star_rating')->comment('Transportation vehicle type');
            $table->string('accommodation_type')->nullable()->after('vehicle_type')->comment('Accommodation type');
            $table->integer('ticket_count')->nullable()->after('accommodation_type')->comment('Entry tickets count');
            $table->string('ticket_name')->nullable()->after('ticket_count')->comment('Entry ticket name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['category', 'star_rating', 'vehicle_type', 'accommodation_type', 'ticket_count', 'ticket_name']);
        });
    }
}
