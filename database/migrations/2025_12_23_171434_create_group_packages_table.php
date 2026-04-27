<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->nullable()->constrained('places')->onDelete('set null');
            $table->string('category')->nullable()->comment('Package category');
            $table->string('package_category')->nullable()->comment('Honeymoon, Budget, Standard, Premium, Platinum');
            $table->boolean('includes_flight')->default(false);
            $table->integer('star_rating')->nullable()->comment('Hotel star rating (1-5)');
            $table->string('vehicle_type')->nullable()->comment('Transportation vehicle type');
            $table->string('accommodation_type')->nullable()->comment('Accommodation type');
            $table->integer('ticket_count')->nullable()->comment('Entry tickets count');
            $table->string('ticket_name')->nullable()->comment('Entry ticket name');
            $table->json('addon_amenities')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2)->comment('Adult price');
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('price_2_6', 10, 2)->nullable()->comment('Price for kids age 2-6');
            $table->decimal('price_6_10', 10, 2)->nullable()->comment('Price for kids age 6-10');
            $table->string('currency', 3)->default('INR');
            $table->string('duration')->nullable()->comment('Duration string like "3 Days 2 Nights"');
            $table->date('announcement_date')->nullable()->comment('Date when package is announced/available');
            $table->integer('total_pax')->nullable()->comment('Total passengers/people');
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('included_services')->nullable();
            $table->json('excluded_services')->nullable();
            $table->json('itinerary')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('status')->default(true);
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
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
        Schema::dropIfExists('group_packages');
    }
}
