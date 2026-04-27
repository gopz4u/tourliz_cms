<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attractions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('place_id')->nullable()->constrained('places')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->boolean('status')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
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
        Schema::dropIfExists('attractions');
    }
}
