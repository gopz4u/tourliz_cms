<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceFieldsToPersonalAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only add columns if they don't already exist
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('personal_access_tokens', 'device_name')) {
                    $table->string('device_name')->nullable()->after('name');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'device_id')) {
                    $table->string('device_id')->nullable()->after('device_name');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('device_id');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'user_agent')) {
                    $table->text('user_agent')->nullable()->after('ip_address');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['device_name', 'device_id', 'ip_address', 'user_agent']);
        });
    }
}
