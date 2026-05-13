<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to change column types and remove JSON constraints in MariaDB/MySQL
        \DB::statement("ALTER TABLE packages MODIFY included_services TEXT NULL");
        \DB::statement("ALTER TABLE packages MODIFY excluded_services TEXT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to JSON might be tricky if data is not valid JSON, so we just set them back to TEXT
        // (Laravel's json type is just an alias for longtext + constraint)
    }
};
