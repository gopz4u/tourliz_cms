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
        Schema::table('transports', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_id')->nullable()->after('supplier_id');
            $table->foreign('destination_id')->references('id')->on('destinations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropForeign(['destination_id']);
            $table->dropColumn('destination_id');
        });
    }
};
