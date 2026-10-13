<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupDeparturesTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('group_departures')) {
            Schema::create('group_departures', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fixed_itinerary_id')->constrained('fixed_itineraries')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('admins')->nullOnDelete();
                $table->string('title');
                $table->date('announcement_date')->nullable();
                $table->date('departure_date');
                $table->date('return_date')->nullable();
                $table->decimal('adult_rate', 15, 2)->default(0);
                $table->decimal('child_rate', 15, 2)->nullable();
                $table->string('currency', 10)->default('MYR');
                $table->unsignedInteger('capacity')->default(20);
                $table->string('status')->default('announced');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['departure_date', 'status']);
            });
        }

        if (!Schema::hasTable('group_departure_customers')) {
            Schema::create('group_departure_customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_departure_id')->constrained('group_departures')->cascadeOnDelete();
                $table->string('name');
                $table->string('phone', 40)->nullable();
                $table->string('email')->nullable();
                $table->string('passport_number', 100)->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('customer_type')->default('adult');
                $table->unsignedInteger('pax')->default(1);
                $table->decimal('rate', 15, 2)->default(0);
                $table->decimal('amount_paid', 15, 2)->default(0);
                $table->string('payment_status')->default('pending');
                $table->string('booking_status')->default('reserved');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['group_departure_id', 'booking_status']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('group_departure_customers');
        Schema::dropIfExists('group_departures');
    }
}
