<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInAppFollowupTrackingToB2cItineraries extends Migration
{
    public function up()
    {
        if (Schema::hasTable('b2c_itineraries') && !Schema::hasColumn('b2c_itineraries', 'last_followup_notification_at')) {
            Schema::table('b2c_itineraries', function (Blueprint $table) {
                $table->timestamp('last_followup_notification_at')->nullable()->after('last_followup_whatsapp_at');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('b2c_itineraries') && Schema::hasColumn('b2c_itineraries', 'last_followup_notification_at')) {
            Schema::table('b2c_itineraries', function (Blueprint $table) {
                $table->dropColumn('last_followup_notification_at');
            });
        }
    }
}
