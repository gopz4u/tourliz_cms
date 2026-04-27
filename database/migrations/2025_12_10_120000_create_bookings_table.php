<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable(); // who booked
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('travel_date')->nullable();
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

