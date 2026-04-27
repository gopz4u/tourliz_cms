<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateB2cItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b2c_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // Creator

            // Client Contact Info
            $table->string('client_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('secondary_phone')->nullable();

            // Proposal Basics
            $table->string('title');
            $table->date('start_date')->nullable();
            $table->integer('duration_days')->default(1);
            $table->json('itinerary')->nullable();

            // Financials
            $table->decimal('base_cost', 15, 2)->default(0);
            $table->decimal('markup_percentage', 5, 2)->default(0);
            $table->decimal('markup_amount', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->string('currency', 10)->default('INR');

            // Pax
            $table->integer('adults')->default(1);
            $table->integer('children_2_6')->default(0);
            $table->integer('children_6_11')->default(0);

            // CRM & Tracking
            $table->string('status')->default('draft');
            $table->string('payment_status')->default('pending');
            $table->decimal('total_amount_received', 15, 2)->default(0);
            $table->text('payment_details')->nullable();
            $table->string('followup_status')->default('leads');
            $table->dateTime('followed_up_at')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->string('lead_source')->default('walk_in'); // walk_in, call, social, web
            $table->text('special_requirements')->nullable();

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
        Schema::dropIfExists('b2c_itineraries');
    }
}
