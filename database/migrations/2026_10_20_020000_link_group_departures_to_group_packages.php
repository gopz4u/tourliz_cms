<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LinkGroupDeparturesToGroupPackages extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('group_departures') || !Schema::hasTable('group_packages')) {
            return;
        }

        if (!Schema::hasColumn('group_departures', 'group_package_id')) {
            Schema::table('group_departures', function (Blueprint $table) {
                // No database foreign key is intentional: Website Group Tours
                // are soft-deleted and historical departure/customer data must remain.
                $table->unsignedBigInteger('group_package_id')->nullable()->index()->after('id');
            });
        }

        // Preserve legacy departures. Only link automatically when one active
        // Website Group Tour uniquely owns the Fixed Itinerary.
        $matches = DB::table('group_packages')
            ->select('fixed_itinerary_id', DB::raw('MIN(id) as group_package_id'), DB::raw('COUNT(*) as package_count'))
            ->whereNotNull('fixed_itinerary_id')
            ->whereNull('deleted_at')
            ->groupBy('fixed_itinerary_id')
            ->get();

        foreach ($matches as $match) {
            if ((int) $match->package_count !== 1) {
                continue;
            }

            DB::table('group_departures')
                ->whereNull('group_package_id')
                ->where('fixed_itinerary_id', $match->fixed_itinerary_id)
                ->update(['group_package_id' => $match->group_package_id]);
        }
    }

    public function down()
    {
        if (!Schema::hasTable('group_departures') || !Schema::hasColumn('group_departures', 'group_package_id')) {
            return;
        }

        Schema::table('group_departures', function (Blueprint $table) {
            $table->dropIndex(['group_package_id']);
            $table->dropColumn('group_package_id');
        });
    }
}
