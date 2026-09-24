<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->default('Meta Ads');
            $table->string('campaign_name')->nullable();
            $table->string('form_id')->nullable();
            $table->string('leadgen_id')->nullable()->unique();
            $table->string('status')->default('New');
            $table->text('notes')->nullable();
            $table->json('raw_data')->nullable(); // To store any extra fields from the lead form
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
        Schema::dropIfExists('leads');
    }
}
