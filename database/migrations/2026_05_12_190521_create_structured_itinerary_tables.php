<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStructuredItineraryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Package Days
        Schema::create('package_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->integer('day_number');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('destination_id')->nullable()->constrained(); // Overnight stay location
            $table->json('meal_plan')->nullable(); // ['breakfast' => true, 'lunch' => false, 'dinner' => true]
            $table->timestamps();
        });

        // 2. Package Day Hotels
        Schema::create('package_day_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained();
            $table->foreignId('room_type_id')->nullable()->constrained('hotel_rooms');
            $table->string('meal_plan_code')->nullable(); // EP, CP, MAP, AP
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
        });

        // 3. Package Day Transports
        Schema::create('package_day_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('transport_id')->constrained();
            $table->string('pickup_point')->nullable();
            $table->string('drop_point')->nullable();
            $table->timestamps();
        });

        // 4. Package Day Activities
        Schema::create('package_day_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->constrained();
            $table->timestamps();
        });

        // 5. Package Day Attractions (Entry Tickets)
        Schema::create('package_day_attractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('attraction_id')->constrained('entry_tickets');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_day_attractions');
        Schema::dropIfExists('package_day_activities');
        Schema::dropIfExists('package_day_transports');
        Schema::dropIfExists('package_day_hotels');
        Schema::dropIfExists('package_days');
    }
}
