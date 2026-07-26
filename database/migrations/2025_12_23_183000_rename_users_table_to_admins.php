<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameUsersTableToAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if users table exists and admins table doesn't
        if (Schema::hasTable('users') && !Schema::hasTable('admins')) {
            // Rename the table
            Schema::rename('users', 'admins');
        } elseif (Schema::hasTable('users') && Schema::hasTable('admins')) {
            if (DB::getDriverName() === 'mysql') {
                try {
                    DB::statement('INSERT IGNORE INTO admins (id, name, email, email_verified_at, password, remember_token, created_at, updated_at) 
                                  SELECT id, name, email, email_verified_at, password, remember_token, created_at, updated_at 
                                  FROM users');
                } catch (\Exception $e) {
                    // If insert fails, continue
                }
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Schema::dropIfExists('users');
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } else {
                Schema::dropIfExists('users');
            }
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique('users_new_email_unique');
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
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
        // Check if admins table exists and users table doesn't
        if (Schema::hasTable('admins') && !Schema::hasTable('users')) {
            // Rename back to users
            Schema::rename('admins', 'users');
        }
    }
}

