<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MigrateLegacyInventoryToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $emptyJson = json_encode([]);

        $getActiveRows = function ($table) {
            if (!Schema::hasTable($table)) return collect();
            $query = DB::table($table);
            if (Schema::hasColumn($table, 'deleted_at')) {
                $query->whereNull('deleted_at');
            }
            return $query->get();
        };

        // 1. Migrate Transports
        if (Schema::hasTable('transports')) {
            $transports = $getActiveRows('transports');
            foreach ($transports as $t) {
                // Determine destination_id
                $destId = $t->destination_id;
                if (!$destId && $t->destination) {
                    $dest = DB::table('destinations')->where('name', $t->destination)->first();
                    if ($dest) {
                        $destId = $dest->id;
                    }
                }

                // Check if already migrated
                $exists = DB::table('services')
                    ->where('category', 'Transport')
                    ->where('name', $t->name)
                    ->where('vehicle_type', $t->vehicle_type)
                    ->exists();

                if (!$exists) {
                    DB::table('services')->insert([
                        'destination_id' => $destId,
                        'name' => $t->name,
                        'slug' => Str::slug($t->name) . '-' . uniqid(),
                        'price' => $t->base_price,
                        'currency' => $t->currency ?? 'MYR',
                        'category' => 'Transport',
                        'vehicle_type' => $t->vehicle_type,
                        'total_pax' => $t->capacity,
                        'supplier_id' => $t->supplier_id,
                        'is_active' => $t->is_active ?? 1,
                        'is_featured' => 0,
                        'sort_order' => 0,
                        'short_description' => $t->duration ?? null,
                        'gallery' => $emptyJson,
                        'addon_amenities' => $emptyJson,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 2. Migrate Hotels & Rooms
        if (Schema::hasTable('hotels') && Schema::hasTable('hotel_rooms')) {
            $hotels = $getActiveRows('hotels');
            foreach ($hotels as $h) {
                $roomsQuery = DB::table('hotel_rooms')->where('hotel_id', $h->id);
                if (Schema::hasColumn('hotel_rooms', 'deleted_at')) {
                    $roomsQuery->whereNull('deleted_at');
                }
                $rooms = $roomsQuery->get();

                foreach ($rooms as $r) {
                    $name = $h->name . ' - ' . $r->room_type;
                    $exists = DB::table('services')
                        ->where('category', 'Hotels')
                        ->where('name', $name)
                        ->exists();

                    if (!$exists) {
                        DB::table('services')->insert([
                            'destination_id' => $h->destination_id,
                            'name' => $name,
                            'slug' => Str::slug($name) . '-' . uniqid(),
                            'price' => $r->base_price,
                            'currency' => $h->currency ?? 'MYR',
                            'category' => 'Hotels',
                            'star_rating' => $h->star_rating ?? 5,
                            'accommodation_type' => $r->room_type,
                            'supplier_id' => $h->supplier_id,
                            'is_active' => $h->is_active ?? 1,
                            'is_featured' => 0,
                            'sort_order' => 0,
                            'description' => $h->description ?? null,
                            'gallery' => $emptyJson,
                            'addon_amenities' => $emptyJson,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        // 3. Migrate Entry Tickets
        if (Schema::hasTable('entry_tickets')) {
            $tickets = $getActiveRows('entry_tickets');
            foreach ($tickets as $t) {
                $exists = DB::table('services')
                    ->where('category', 'Entry Tickets')
                    ->where('name', $t->attraction_name)
                    ->exists();

                if (!$exists) {
                    DB::table('services')->insert([
                        'destination_id' => $t->destination_id,
                        'name' => $t->attraction_name,
                        'slug' => Str::slug($t->attraction_name) . '-' . uniqid(),
                        'price' => $t->adult_price,
                        'price_2_6' => $t->child_price,
                        'price_6_10' => $t->child_price,
                        'currency' => $t->currency ?? 'MYR',
                        'category' => 'Entry Tickets',
                        'supplier_id' => $t->supplier_id,
                        'is_active' => $t->is_active ?? 1,
                        'is_featured' => 0,
                        'sort_order' => 0,
                        'gallery' => $emptyJson,
                        'addon_amenities' => $emptyJson,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 4. Migrate Activities
        if (Schema::hasTable('activities')) {
            $activities = $getActiveRows('activities');
            foreach ($activities as $a) {
                $exists = DB::table('services')
                    ->where('category', 'Activities')
                    ->where('name', $a->name)
                    ->exists();

                if (!$exists) {
                    DB::table('services')->insert([
                        'destination_id' => $a->destination_id,
                        'name' => $a->name,
                        'slug' => Str::slug($a->name) . '-' . uniqid(),
                        'price' => $a->base_price,
                        'price_2_6' => null,
                        'price_6_10' => null,
                        'currency' => $a->currency ?? 'MYR',
                        'category' => 'Activities',
                        'supplier_id' => $a->supplier_id,
                        'is_active' => $a->is_active ?? 1,
                        'is_featured' => 0,
                        'sort_order' => 0,
                        'description' => $a->description ?? null,
                        'short_description' => $a->duration ?? null,
                        'gallery' => $emptyJson,
                        'addon_amenities' => $emptyJson,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 5. Migrate Meals
        if (Schema::hasTable('meals')) {
            $meals = $getActiveRows('meals');
            foreach ($meals as $m) {
                $exists = DB::table('services')
                    ->where('category', 'Meals')
                    ->where('name', $m->name)
                    ->where('accommodation_type', $m->type)
                    ->exists();

                if (!$exists) {
                    DB::table('services')->insert([
                        'destination_id' => $m->destination_id,
                        'name' => $m->name,
                        'slug' => Str::slug($m->name) . '-' . uniqid(),
                        'price' => $m->price,
                        'currency' => $m->currency ?? 'MYR',
                        'category' => 'Meals',
                        'accommodation_type' => $m->type,
                        'supplier_id' => $m->supplier_id,
                        'is_active' => $m->is_active ?? 1,
                        'is_featured' => 0,
                        'sort_order' => 0,
                        'description' => $m->description ?? null,
                        'gallery' => $emptyJson,
                        'addon_amenities' => $emptyJson,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 6. Migrate Tourist Spots
        if (Schema::hasTable('tourist_spots')) {
            $spots = $getActiveRows('tourist_spots');
            foreach ($spots as $s) {
                $exists = DB::table('services')
                    ->where('category', 'Other Services')
                    ->where('name', $s->name)
                    ->exists();

                if (!$exists) {
                    DB::table('services')->insert([
                        'destination_id' => $s->destination_id,
                        'name' => $s->name,
                        'slug' => Str::slug($s->name) . '-' . uniqid(),
                        'price' => 0,
                        'currency' => $s->currency ?? 'MYR',
                        'category' => 'Other Services',
                        'supplier_id' => $s->supplier_id,
                        'is_active' => $s->is_active ?? 1,
                        'is_featured' => 0,
                        'sort_order' => 0,
                        'description' => $s->description ?? null,
                        'gallery' => $emptyJson,
                        'addon_amenities' => $emptyJson,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Don't delete migrated data in case of rollback
    }
}
