<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnifiedItinerariesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Master Itineraries Table
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('itinerary_type', ['b2c', 'b2b'])->default('b2c')->index();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->integer('duration_days')->default(1);
            $table->integer('duration_nights')->default(0);
            $table->text('description')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('status')->default('draft')->index(); // draft, published, archived
            $table->boolean('is_published')->default(false)->index();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['itinerary_type', 'is_published', 'status']);
            $table->index('created_at');
        });

        // 2. Itinerary Days Table
        Schema::create('itinerary_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained('itineraries')->onDelete('cascade');
            $table->integer('day_number');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('overnight_location')->nullable();
            $table->timestamps();

            $table->index(['itinerary_id', 'day_number']);
        });

        // 3. Itinerary Day Items Table
        Schema::create('itinerary_day_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_day_id')->constrained('itinerary_days')->onDelete('cascade');
            $table->string('item_type')->nullable(); // hotel, activity, transport, attraction, meal
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['itinerary_day_id', 'sort_order']);
        });

        // 4. B2B Channel Specific Details
        Schema::create('itinerary_b2b_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained('itineraries')->onDelete('cascade');
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->decimal('net_rate', 12, 2)->default(0.00);
            $table->decimal('agent_rate', 12, 2)->default(0.00);
            $table->decimal('markup_percentage', 8, 2)->default(0.00);
            $table->decimal('markup_amount', 12, 2)->default(0.00);
            $table->decimal('commission', 12, 2)->default(0.00);
            $table->integer('min_pax')->default(1);
            $table->integer('max_pax')->default(50);
            $table->string('hotel_category')->nullable();
            $table->string('room_category')->nullable();
            $table->string('vehicle_category')->nullable();
            $table->boolean('guide_required')->default(false);
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('validity_start')->nullable();
            $table->date('validity_end')->nullable();
            $table->text('operational_notes')->nullable();
            $table->text('agent_notes')->nullable();
            $table->timestamps();
        });

        // 5. B2C Channel Specific Details
        Schema::create('itinerary_b2c_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained('itineraries')->onDelete('cascade');
            $table->text('short_description')->nullable();
            $table->decimal('selling_price', 12, 2)->default(0.00);
            $table->decimal('child_price', 12, 2)->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
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
        Schema::dropIfExists('itinerary_b2c_details');
        Schema::dropIfExists('itinerary_b2b_details');
        Schema::dropIfExists('itinerary_day_items');
        Schema::dropIfExists('itinerary_days');
        Schema::dropIfExists('itineraries');
    }
}
