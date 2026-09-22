<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateExistingItinerariesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            // 1. Migrate B2C itineraries from b2c_itineraries table if exists
            if (DB::getSchemaBuilder()->hasTable('b2c_itineraries')) {
                $b2cRows = DB::table('b2c_itineraries')->get();
                foreach ($b2cRows as $b2c) {
                    $slug = !empty($b2c->slug) ? $b2c->slug : Str::slug($b2c->title ?? 'B2C Itinerary ' . $b2c->id);

                    // Ensure slug uniqueness
                    $origSlug = $slug;
                    $c = 1;
                    while (DB::table('itineraries')->where('slug', $slug)->exists()) {
                        $slug = "{$origSlug}-{$c}";
                        $c++;
                    }

                    $itineraryId = DB::table('itineraries')->insertGetId([
                        'title' => $b2c->title ?? 'B2C Itinerary ' . $b2c->id,
                        'slug' => $slug,
                        'itinerary_type' => 'b2c',
                        'destination_id' => $b2c->destination_id ?? null,
                        'duration_days' => $b2c->duration_days ?? 1,
                        'duration_nights' => $b2c->duration_nights ?? 0,
                        'description' => $b2c->description ?? null,
                        'inclusions' => $b2c->inclusions ?? null,
                        'exclusions' => $b2c->exclusions ?? null,
                        'terms_conditions' => $b2c->terms ?? null,
                        'status' => $b2c->status ?? 'published',
                        'is_published' => true,
                        'created_at' => $b2c->created_at ?? now(),
                        'updated_at' => $b2c->updated_at ?? now(),
                    ]);

                    DB::table('itinerary_b2c_details')->insert([
                        'itinerary_id' => $itineraryId,
                        'short_description' => $b2c->short_description ?? null,
                        'selling_price' => $b2c->total_price ?? $b2c->price ?? 0,
                        'child_price' => $b2c->child_price ?? null,
                        'image' => $b2c->image ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Migrate days if itinerary JSON column exists
                    if (!empty($b2c->itinerary_data)) {
                        $days = is_string($b2c->itinerary_data) ? json_decode($b2c->itinerary_data, true) : $b2c->itinerary_data;
                        if (is_array($days)) {
                            foreach ($days as $idx => $dayData) {
                                $dayNum = $dayData['day_number'] ?? ($idx + 1);
                                $dayId = DB::table('itinerary_days')->insertGetId([
                                    'itinerary_id' => $itineraryId,
                                    'day_number' => $dayNum,
                                    'title' => $dayData['title'] ?? "Day {$dayNum}",
                                    'description' => $dayData['description'] ?? null,
                                    'overnight_location' => $dayData['overnight_location'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);

                                if (!empty($dayData['activities']) && is_array($dayData['activities'])) {
                                    foreach ($dayData['activities'] as $itemIdx => $act) {
                                        DB::table('itinerary_day_items')->insert([
                                            'itinerary_day_id' => $dayId,
                                            'item_type' => 'activity',
                                            'title' => is_string($act) ? $act : ($act['title'] ?? 'Activity'),
                                            'description' => is_array($act) ? ($act['description'] ?? null) : null,
                                            'sort_order' => $itemIdx,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // 2. Migrate B2B custom itineraries from custom_itineraries table if exists
            if (DB::getSchemaBuilder()->hasTable('custom_itineraries')) {
                $b2bRows = DB::table('custom_itineraries')->get();
                foreach ($b2bRows as $b2b) {
                    $slug = !empty($b2b->slug) ? $b2b->slug : Str::slug($b2b->title ?? 'B2B Proposal ' . $b2b->id);

                    $origSlug = $slug;
                    $c = 1;
                    while (DB::table('itineraries')->where('slug', $slug)->exists()) {
                        $slug = "{$origSlug}-{$c}";
                        $c++;
                    }

                    $itineraryId = DB::table('itineraries')->insertGetId([
                        'title' => $b2b->title ?? 'B2B Proposal ' . $b2b->id,
                        'slug' => $slug,
                        'itinerary_type' => 'b2b',
                        'destination_id' => $b2b->destination_id ?? null,
                        'duration_days' => $b2b->duration_days ?? 1,
                        'duration_nights' => $b2b->duration_nights ?? 0,
                        'description' => $b2b->description ?? null,
                        'inclusions' => $b2b->inclusions ?? null,
                        'exclusions' => $b2b->exclusions ?? null,
                        'status' => $b2b->status ?? 'draft',
                        'is_published' => false,
                        'created_at' => $b2b->created_at ?? now(),
                        'updated_at' => $b2b->updated_at ?? now(),
                    ]);

                    DB::table('itinerary_b2b_details')->insert([
                        'itinerary_id' => $itineraryId,
                        'agency_id' => $b2b->agency_id ?? null,
                        'supplier_id' => $b2b->supplier_id ?? null,
                        'net_rate' => $b2b->total_cost ?? $b2b->net_price ?? 0,
                        'agent_rate' => $b2b->grand_total ?? $b2b->selling_price ?? 0,
                        'markup_percentage' => $b2b->markup_percentage ?? 0,
                        'markup_amount' => $b2b->markup_amount ?? 0,
                        'min_pax' => $b2b->pax_count ?? 1,
                        'max_pax' => $b2b->max_pax ?? 50,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
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
        DB::table('itineraries')->truncate();
        DB::table('itinerary_days')->truncate();
        DB::table('itinerary_day_items')->truncate();
        DB::table('itinerary_b2b_details')->truncate();
        DB::table('itinerary_b2c_details')->truncate();
    }
}
