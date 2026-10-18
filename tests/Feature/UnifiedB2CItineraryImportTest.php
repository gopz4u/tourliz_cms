<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Destination;
use App\Models\FixedItinerary;
use App\Models\GroupPackage;
use App\Models\Itinerary;
use App\Models\ItineraryB2cDetail;
use App\Models\ItineraryDay;
use App\Models\ItineraryDayItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnifiedB2CItineraryImportTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Itinerary Admin',
            'email' => 'itinerary-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $this->destination = Destination::create([
            'name' => 'Kuala Lumpur',
            'slug' => 'kuala-lumpur',
        ]);
    }

    /** @test */
    public function unified_b2c_create_page_exposes_the_safe_import_choices()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.itineraries.b2c.create'));

        $response->assertOk();
        $response->assertSee('Start from an Existing Itinerary');
        $response->assertSee('Fixed-Price Itinerary');
        $response->assertSee('Group Tour Master');
        $response->assertSee('Legacy B2C Itinerary');
        $response->assertSee('Unified B2C Template / Quote');
    }

    /** @test */
    public function it_imports_a_fixed_itinerary_as_a_normalized_reusable_template()
    {
        $source = FixedItinerary::create([
            'title' => 'KL Discovery',
            'destination_id' => $this->destination->id,
            'duration_days' => 3,
            'duration_nights' => 2,
            'short_description' => 'A curated Kuala Lumpur getaway.',
            'fixed_price' => 1299,
            'currency' => 'MYR',
            'status' => 'active',
            'days' => [[
                'day_number' => 1,
                'title' => 'Arrival in Kuala Lumpur',
                'description' => 'Airport pickup and hotel check-in.',
                'overnight_location' => 'Kuala Lumpur',
                'items' => [[
                    'item_type' => 'hotel',
                    'item_id' => 101,
                    'supplier_id' => 55,
                    'title' => 'City Centre Hotel',
                    'room_type' => 'Deluxe',
                    'price' => 220,
                ]],
            ]],
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.itineraries.b2c.import'), [
            'source_type' => 'fixed',
            'source_id' => $source->id,
            'destination_id' => $this->destination->id,
        ]);

        $itinerary = Itinerary::where('title', 'Copy of KL Discovery')->firstOrFail();

        $response->assertRedirect(route('admin.itineraries.b2c.edit', $itinerary->id));
        $this->assertSame('b2c', $itinerary->itinerary_type);
        $this->assertSame($this->destination->id, (int) $itinerary->destination_id);
        $this->assertSame(3, $itinerary->duration_days);
        $this->assertSame('draft', $itinerary->status);
        $this->assertTrue((bool) $itinerary->b2cDetail->is_template);
        $this->assertSame('1299.00', $itinerary->b2cDetail->selling_price);

        $this->assertDatabaseHas('itinerary_days', [
            'itinerary_id' => $itinerary->id,
            'day_number' => 1,
            'title' => 'Arrival in Kuala Lumpur',
        ]);
        $this->assertDatabaseHas('itinerary_day_items', [
            'item_type' => 'hotel',
            'item_id' => 101,
            'supplier_id' => 55,
            'title' => 'City Centre Hotel',
        ]);
    }

    /** @test */
    public function it_imports_a_group_master_as_a_customer_quote_when_requested()
    {
        $source = GroupPackage::create([
            'name' => 'KL Group Escape',
            'slug' => 'kl-group-escape',
            'destination_id' => $this->destination->id,
            'duration' => '2 Days / 1 Night',
            'description' => 'A group-ready Kuala Lumpur plan.',
            'price' => 850,
            'currency' => 'MYR',
            'status' => true,
            'itinerary' => [[
                'day' => 1,
                'title' => 'KL Arrival',
                'hotel' => [
                    'name' => 'Bukit Bintang Hotel',
                    'hotel_id' => 34,
                    'supplier_id' => 12,
                    'price_per_night' => 180,
                    'type' => 'Standard',
                ],
                'transport' => [[
                    'name' => 'Private Airport Transfer',
                    'transport_id' => 77,
                    'supplier_id' => 13,
                    'price' => 90,
                ]],
                'activities' => [[
                    'name' => 'KL City Tour',
                    'activity_id' => 45,
                    'entry_ticket' => ['adult_price' => 50],
                ]],
                'meals' => ['breakfast' => 'Included'],
            ]],
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.itineraries.b2c.import'), [
            'source_type' => 'group',
            'source_id' => $source->id,
            'destination_id' => $this->destination->id,
            'as_quote' => 1,
        ]);

        $itinerary = Itinerary::where('title', 'KL Group Escape Quotation')->firstOrFail();

        $response->assertRedirect(route('admin.itineraries.b2c.edit', $itinerary->id));
        $this->assertFalse((bool) $itinerary->b2cDetail->is_template);
        $this->assertSame(2, $itinerary->duration_days);
        $this->assertDatabaseHas('itinerary_day_items', [
            'item_type' => 'hotel',
            'item_id' => 34,
            'title' => 'Bukit Bintang Hotel',
        ]);
        $this->assertDatabaseHas('itinerary_day_items', [
            'item_type' => 'transport',
            'item_id' => 77,
            'title' => 'Private Airport Transfer',
        ]);
        $this->assertDatabaseHas('itinerary_day_items', [
            'item_type' => 'activity',
            'item_id' => 45,
            'title' => 'KL City Tour',
        ]);
        $this->assertDatabaseHas('itinerary_day_items', [
            'item_type' => 'meal',
            'title' => 'Breakfast',
        ]);
    }

    /** @test */
    public function it_clones_an_existing_unified_b2c_template_without_changing_the_source()
    {
        $source = Itinerary::create([
            'title' => 'Existing Langkawi Template',
            'slug' => 'existing-langkawi-template',
            'itinerary_type' => 'b2c',
            'destination_id' => $this->destination->id,
            'duration_days' => 2,
            'duration_nights' => 1,
            'currency' => 'MYR',
            'status' => 'published',
            'is_published' => true,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);
        ItineraryB2cDetail::create([
            'itinerary_id' => $source->id,
            'currency' => 'MYR',
            'selling_price' => 900,
            'is_template' => true,
        ]);
        $day = ItineraryDay::create([
            'itinerary_id' => $source->id,
            'day_number' => 1,
            'title' => 'Island Arrival',
        ]);
        ItineraryDayItem::create([
            'itinerary_day_id' => $day->id,
            'item_type' => 'activity',
            'item_id' => 20,
            'title' => 'Mangrove Tour',
            'price' => 80,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.itineraries.b2c.import'), [
            'source_type' => 'unified_b2c',
            'source_id' => $source->id,
            'destination_id' => $this->destination->id,
        ]);

        $copy = Itinerary::where('title', 'Copy of Existing Langkawi Template')->firstOrFail();

        $response->assertRedirect(route('admin.itineraries.b2c.edit', $copy->id));
        $this->assertNotSame($source->id, $copy->id);
        $this->assertSame('published', $source->fresh()->status);
        $this->assertDatabaseHas('itinerary_day_items', [
            'itinerary_day_id' => $copy->days()->first()->id,
            'item_type' => 'activity',
            'item_id' => 20,
            'title' => 'Mangrove Tour',
        ]);
    }
}
