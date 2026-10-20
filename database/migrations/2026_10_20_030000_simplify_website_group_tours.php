<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SimplifyWebsiteGroupTours extends Migration
{
    /**
     * Website Group Tours own their public date and prices. They may have been
     * imported from a Fixed Itinerary, but never depend on an operational
     * Group Departure to be visible on the website.
     */
    public function up()
    {
        if (!Schema::hasTable('group_packages')) {
            return;
        }

        Schema::table('group_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('group_packages', 'child_price')) {
                $table->decimal('child_price', 12, 2)->nullable()->after('price')->comment('Website group-tour child price');
            }

            if (!Schema::hasColumn('group_packages', 'departure_date')) {
                $table->date('departure_date')->nullable()->after('announcement_date')->index();
            }

            if (!Schema::hasColumn('group_packages', 'return_date')) {
                $table->date('return_date')->nullable()->after('departure_date');
            }
        });

        // Keep current website tours visible after this change by copying the
        // first already-linked operational date once. From here onward the
        // Website Group Tour owns these values independently.
        if (!Schema::hasTable('group_departures') || !Schema::hasColumn('group_departures', 'group_package_id')) {
            return;
        }

        DB::table('group_packages')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunkById(100, function ($packages) {
                foreach ($packages as $package) {
                    $departure = DB::table('group_departures')
                        ->where('group_package_id', $package->id)
                        ->whereNull('deleted_at')
                        ->orderBy('departure_date')
                        ->first();

                    if (!$departure) {
                        continue;
                    }

                    $updates = [];
                    if (empty($package->departure_date) && !empty($departure->departure_date)) {
                        $updates['departure_date'] = $departure->departure_date;
                    }
                    if (empty($package->return_date) && !empty($departure->return_date)) {
                        $updates['return_date'] = $departure->return_date;
                    }
                    if (empty($package->child_price) && !empty($departure->child_rate)) {
                        $updates['child_price'] = $departure->child_rate;
                    }

                    if ($updates) {
                        DB::table('group_packages')->where('id', $package->id)->update($updates);
                    }
                }
            });

        // Existing age-band prices become the single simple child price.
        DB::table('group_packages')
            ->whereNull('child_price')
            ->whereNotNull('price_6_10')
            ->update(['child_price' => DB::raw('price_6_10')]);

        DB::table('group_packages')
            ->whereNull('child_price')
            ->whereNotNull('price_2_6')
            ->update(['child_price' => DB::raw('price_2_6')]);
    }

    public function down()
    {
        if (!Schema::hasTable('group_packages')) {
            return;
        }

        Schema::table('group_packages', function (Blueprint $table) {
            if (Schema::hasColumn('group_packages', 'return_date')) {
                $table->dropColumn('return_date');
            }
            if (Schema::hasColumn('group_packages', 'departure_date')) {
                $table->dropIndex(['departure_date']);
                $table->dropColumn('departure_date');
            }
            if (Schema::hasColumn('group_packages', 'child_price')) {
                $table->dropColumn('child_price');
            }
        });
    }
}
