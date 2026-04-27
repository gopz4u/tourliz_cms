<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Hotels
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->integer('star_rating')->default(3);
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Hotel Room Types
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('room_type'); // Deluxe, Suite, etc.
            $table->decimal('base_price', 10, 2);
            $table->integer('capacity')->default(2);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // 3. Activity Master
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('duration')->nullable(); // e.g. 2 hours
            $table->decimal('base_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Transport Master
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Private SUV, Shared Van
            $table->string('vehicle_type'); // Sedan, SUV, etc.
            $table->integer('capacity')->default(4);
            $table->decimal('base_price', 10, 2)->default(0); // Price per day/trip
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Entry Tickets
        Schema::create('entry_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('attraction_name');
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->decimal('adult_price', 10, 2);
            $table->decimal('child_price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_tickets');
        Schema::dropIfExists('transports');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotels');
    }
}
