<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFixedItinerarySourceToGroupPackages extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('group_packages') || Schema::hasColumn('group_packages', 'fixed_itinerary_id')) {
            return;
        }

        Schema::table('group_packages', function (Blueprint $table) {
            // Intentionally no database foreign key: Fixed Itineraries can be
            // archived, while an already-published website tour must remain.
            $table->unsignedBigInteger('fixed_itinerary_id')->nullable()->index()->after('destination_id');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('group_packages') || !Schema::hasColumn('group_packages', 'fixed_itinerary_id')) {
            return;
        }

        Schema::table('group_packages', function (Blueprint $table) {
            $table->dropIndex(['fixed_itinerary_id']);
            $table->dropColumn('fixed_itinerary_id');
        });
    }
}
