<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduledFollowupFieldsToB2cItineraries extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('b2c_itineraries')) {
            return;
        }

        Schema::table('b2c_itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('b2c_itineraries', 'next_followup_at')) {
                $table->timestamp('next_followup_at')->nullable()->index()->after('next_followup_date');
            }
            if (!Schema::hasColumn('b2c_itineraries', 'last_followup_reminded_at')) {
                $table->timestamp('last_followup_reminded_at')->nullable()->after('last_followup_notification_at');
            }
            if (!Schema::hasColumn('b2c_itineraries', 'followup_reminder_count')) {
                $table->unsignedInteger('followup_reminder_count')->default(0)->after('last_followup_reminded_at');
            }
            if (!Schema::hasColumn('b2c_itineraries', 'followup_paused_until')) {
                $table->timestamp('followup_paused_until')->nullable()->after('followup_reminder_count');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('b2c_itineraries')) {
            return;
        }

        $columns = array_filter([
            'next_followup_at',
            'last_followup_reminded_at',
            'followup_reminder_count',
            'followup_paused_until',
        ], function ($column) {
            return Schema::hasColumn('b2c_itineraries', $column);
        });

        if ($columns) {
            Schema::table('b2c_itineraries', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
}
