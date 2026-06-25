<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixedItinerariesTable extends Migration
{
    public function up()
    {
        Schema::create('fixed_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->json('country_ids')->nullable();
            $table->string('title');
            $table->text('itinerary_description')->nullable();
            $table->decimal('fixed_price', 15, 2)->default(0);
            $table->string('currency', 10)->default('MYR');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fixed_itineraries');
    }
}
