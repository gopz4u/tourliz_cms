<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketingCampaignTables extends Migration
{
    public function up()
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('fixed_itinerary_id')->index();
            $table->string('name');
            $table->string('message_mode', 20)->default('summary');
            $table->text('intro_message')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();
        });

        Schema::create('marketing_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marketing_campaign_id')->index();
            $table->string('source_type', 30)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone', 50);
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['marketing_campaign_id', 'phone'], 'marketing_campaign_phone_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketing_campaign_recipients');
        Schema::dropIfExists('marketing_campaigns');
    }
}
