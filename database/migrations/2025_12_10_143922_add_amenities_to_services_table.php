<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmenitiesToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
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
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['star_rating', 'vehicle_type', 'accommodation_type', 'ticket_count', 'ticket_name']);
        });
    }
}
