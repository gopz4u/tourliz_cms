<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaffWhatsappAndLeadReminderTracking extends Migration
{
    public function up()
    {
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (!Schema::hasColumn('admins', 'whatsapp_number')) $table->string('whatsapp_number', 40)->nullable()->after('email');
                if (!Schema::hasColumn('admins', 'whatsapp_notifications')) $table->boolean('whatsapp_notifications')->default(true)->after('whatsapp_number');
            });
        }
        if (Schema::hasTable('b2c_itineraries')) {
            Schema::table('b2c_itineraries', function (Blueprint $table) {
                if (!Schema::hasColumn('b2c_itineraries', 'last_followup_whatsapp_at')) $table->timestamp('last_followup_whatsapp_at')->nullable()->after('next_followup_date');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('b2c_itineraries') && Schema::hasColumn('b2c_itineraries', 'last_followup_whatsapp_at')) Schema::table('b2c_itineraries', fn (Blueprint $table) => $table->dropColumn('last_followup_whatsapp_at'));
        if (Schema::hasTable('admins')) Schema::table('admins', function (Blueprint $table) {
            $columns = array_filter(['whatsapp_number', 'whatsapp_notifications'], fn ($column) => Schema::hasColumn('admins', $column));
            if ($columns) $table->dropColumn($columns);
        });
    }
}
