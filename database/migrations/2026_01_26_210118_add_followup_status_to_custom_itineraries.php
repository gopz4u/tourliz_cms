<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowupStatusToCustomItineraries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->string('followup_status')->default('leads')->after('payment_status');
            // leads, followed_up, waiting, interested, not_interested, converted, dead
            $table->dateTime('followed_up_at')->nullable()->after('followup_status');
            $table->date('next_followup_date')->nullable()->after('followed_up_at');
        });
    }

    public function down()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->dropColumn(['followup_status', 'followed_up_at', 'next_followup_date']);
        });
    }
}
