<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Agency/User ID');
            $table->string('title');
            $table->string('client_name')->nullable();
            $table->foreignId('place_id')->nullable()->constrained('places')->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->integer('duration_days')->default(1);
            $table->json('itinerary')->nullable();
            $table->decimal('base_cost', 10, 2)->default(0)->comment('Total cost of itinerary components');
            $table->decimal('markup_percentage', 5, 2)->default(0)->comment('Agency markup %');
            $table->decimal('markup_amount', 10, 2)->default(0)->comment('Agency markup amount');
            $table->decimal('total_price', 10, 2)->default(0)->comment('Final price to client');
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'proposed', 'confirmed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('custom_itineraries');
    }
}
