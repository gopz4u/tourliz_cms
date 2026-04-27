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
            // If both exist, migrate data from users to admins (skip duplicates)
            try {
                DB::statement('INSERT IGNORE INTO admins (id, name, email, email_verified_at, password, remember_token, created_at, updated_at) 
                              SELECT id, name, email, email_verified_at, password, remember_token, created_at, updated_at 
                              FROM users');
            } catch (\Exception $e) {
                // If insert fails, continue
            }
            
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Drop users table
            Schema::dropIfExists('users');
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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

