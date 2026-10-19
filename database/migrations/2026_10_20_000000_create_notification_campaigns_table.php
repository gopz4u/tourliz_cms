<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationCampaignsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('notification_campaigns')) {
            return;
        }

        Schema::create('notification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('title', 120);
            $table->text('message');
            $table->string('url', 2048)->nullable();
            $table->string('recipient_scope', 20)->default('selected');
            $table->unsignedInteger('recipient_count')->default(0);
            $table->unsignedInteger('in_app_count')->default(0);
            $table->unsignedInteger('push_count')->default(0);
            $table->string('status', 20)->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_campaigns');
    }
}
