<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGroupPackagesTableAddMissingFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('group_packages', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null')->after('destination_id');
            }
            if (!Schema::hasColumn('group_packages', 'supplier_ids')) {
                $table->json('supplier_ids')->nullable()->after('supplier_id');
            }
            if (!Schema::hasColumn('group_packages', 'categories')) {
                $table->json('categories')->nullable()->after('category');
            }
            if (!Schema::hasColumn('group_packages', 'net_price')) {
                $table->decimal('net_price', 15, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('group_packages', 'markup_percentage')) {
                $table->decimal('markup_percentage', 5, 2)->default(0)->after('net_price');
            }
            if (!Schema::hasColumn('group_packages', 'markup_amount')) {
                $table->decimal('markup_amount', 15, 2)->default(0)->after('markup_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_packages', function (Blueprint $table) {
            //
        });
    }
}
