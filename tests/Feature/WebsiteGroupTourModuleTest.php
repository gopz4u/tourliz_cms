<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\FixedItinerary;
use App\Models\GroupDeparture;
use App\Models\GroupDepartureCustomer;
use App\Models\GroupPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteGroupTourModuleTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $fixedItinerary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Website Group Tour Admin',
            'email' => 'website-group-tour@example.test',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $this->fixedItinerary = FixedItinerary::create([
            'user_id' => $this->admin->id,
            'title' => 'Discover Malaysia',
            'duration_days' => 3,
            'duration_nights' => 2,
            'short_description' => 'A three-day group discovery tour.',
            'itinerary_description' => 'Kuala Lumpur and Genting group holiday.',
            'inclusions' => ['Hotel', 'Breakfast'],
            'exclusions' => ['Personal expenses'],
            'fixed_price' => 1299,
            'operational_cost' => 900,
            'markup_percentage' => 20,
            'currency' => 'MYR',
            'image' => 'uploads/fixed-cover.jpg',
            'status' => 'active',
            'days' => [[
                'day_number' => 1,
                'title' => 'Arrival in Kuala Lumpur',
                'description' => 'Airport transfer and city tour.',
                'items' => [
                    ['item_type' => 'hotel', 'title' => 'City Hotel', 'room_type' => 'Deluxe', 'price' => 200],
                    ['item_type' => 'spot', 'title' => 'Petronas Towers', 'description' => 'Photo stop'],
                    ['item_type' => 'meal', 'title' => 'Breakfast'],
                ],
            ]],
        ]);
    }

    /** @test */
    public function super_admin_can_import_a_fixed_itinerary_as_an_unpublished_website_group_tour(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.group-packages.import-fixed-itinerary'), [
            'fixed_itinerary_id' => $this->fixedItinerary->id,
        ]);

        $response->assertCreated()->assertJsonPath('package.fixed_itinerary_id', $this->fixedItinerary->id);

        $groupTour = GroupPackage::firstOrFail();
        $this->assertSame($this->fixedItinerary->id, $groupTour->fixed_itinerary_id);
        $this->assertFalse($groupTour->status);
        $this->assertSame('uploads/fixed-cover.jpg', $groupTour->image);
        $this->assertSame([], $groupTour->gallery);
        $this->assertSame('City Hotel', $groupTour->itinerary[0]['hotel']['name']);
        $this->assertSame('Included', $groupTour->itinerary[0]['meals']['breakfast']);
        $this->assertSame('Petronas Towers', $groupTour->itinerary[0]['places'][0]['name']);
        $response->assertJsonPath('redirect', route('admin.group-packages.edit', $groupTour->id));

        $showResponse = $this->actingAs($this->admin)->getJson(route('admin.group-packages.show', $groupTour->id));
        $showResponse->assertOk()
            ->assertJsonPath('fixed_itinerary.days.0.items.1.item_type', 'spot')
            ->assertJsonPath('fixed_itinerary.days.0.items.1.title', 'Petronas Towers');

        $updateResponse = $this->actingAs($this->admin)->putJson(route('admin.group-packages.update', $groupTour->id), [
            'name' => $groupTour->name,
            'price' => 1399,
            'currency' => 'MYR',
            'image' => 'uploads/new-group-hero.jpg',
            'gallery' => ['uploads/group-album.jpg'],
            'is_featured' => false,
            'is_active' => false,
        ]);
        $updateResponse->assertOk();

        $savedTour = $groupTour->fresh();
        $this->assertSame('uploads/new-group-hero.jpg', $savedTour->image);
        $this->assertSame('Petronas Towers', $savedTour->itinerary[0]['places'][0]['name']);
    }

    /** @test */
    public function public_group_tour_api_exposes_hero_album_and_upcoming_departure_highlight(): void
    {
        $groupTour = GroupPackage::create([
            'fixed_itinerary_id' => $this->fixedItinerary->id,
            'name' => 'Discover Malaysia Group Tour',
            'slug' => 'discover-malaysia-group-tour',
            'price' => 1299,
            'currency' => 'MYR',
            'image' => 'uploads/group-hero.jpg',
            'gallery' => ['uploads/group-1.jpg', 'uploads/group-2.jpg'],
            'status' => true,
        ]);

        $departure = GroupDeparture::create([
            'fixed_itinerary_id' => $this->fixedItinerary->id,
            'title' => 'December Malaysia Group',
            'departure_date' => now()->addDays(10)->toDateString(),
            'return_date' => now()->addDays(12)->toDateString(),
            'adult_rate' => 1499,
            'currency' => 'MYR',
            'capacity' => 20,
            'status' => 'open',
        ]);
        GroupDepartureCustomer::create([
            'group_departure_id' => $departure->id,
            'name' => 'Booked Guest',
            'customer_type' => 'adult',
            'pax' => 3,
            'rate' => 1499,
            'booking_status' => 'confirmed',
        ]);

        $response = $this->getJson('/api/v1/group-packages/discover-malaysia-group-tour');

        $response->assertOk()
            ->assertJsonPath('data.hero_image', getImageUrl('uploads/group-hero.jpg'))
            ->assertJsonPath('data.gallery.0', getImageUrl('uploads/group-1.jpg'))
            ->assertJsonPath('data.source_fixed_itinerary.days.0.items.1.item_type', 'spot')
            ->assertJsonPath('data.source_fixed_itinerary.days.0.items.1.title', 'Petronas Towers')
            ->assertJsonPath('data.upcoming_departures.0.title', 'December Malaysia Group')
            ->assertJsonPath('data.upcoming_departures.0.available_seats', 17)
            ->assertJsonPath('data.upcoming_departures.0.departure_date', now()->addDays(10)->toDateString());
    }
}
