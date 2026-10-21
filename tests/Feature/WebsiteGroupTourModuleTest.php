<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\FixedItinerary;
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
            'departure_date' => now()->addDays(10)->toDateString(),
            'return_date' => now()->addDays(12)->toDateString(),
            'total_pax' => 20,
            'status' => true,
        ]);

        $response = $this->getJson('/api/v1/group-packages/discover-malaysia-group-tour');

        $response->assertOk()
            ->assertJsonPath('data.hero_image', getImageUrl('uploads/group-hero.jpg'))
            ->assertJsonPath('data.gallery.0', getImageUrl('uploads/group-1.jpg'))
            ->assertJsonPath('data.source_fixed_itinerary.days.0.items.1.item_type', 'spot')
            ->assertJsonPath('data.source_fixed_itinerary.days.0.items.1.title', 'Petronas Towers')
            ->assertJsonPath('data.upcoming_departures.0.title', 'Discover Malaysia Group Tour')
            ->assertJsonPath('data.upcoming_departures.0.available_seats', 20)
            ->assertJsonPath('data.upcoming_departures.0.departure_date', now()->addDays(10)->toDateString());
    }

    /** @test */
    public function published_group_tour_with_a_future_departure_is_visible_on_the_website(): void
    {
        $groupTour = GroupPackage::create([
            'fixed_itinerary_id' => $this->fixedItinerary->id,
            'name' => 'Malaysia Explorer Group Tour',
            'slug' => 'malaysia-explorer-group-tour',
            'description' => 'Explore Malaysia with a scheduled group.',
            'duration' => '3 Days 2 Nights',
            'price' => 1299,
            'currency' => 'MYR',
            'image' => 'uploads/group-hero.jpg',
            'gallery' => ['uploads/group-album.jpg'],
            'departure_date' => now()->addDays(14)->toDateString(),
            'return_date' => now()->addDays(16)->toDateString(),
            'total_pax' => 12,
            'status' => true,
        ]);

        $this->get(route('group-tours.index'))
            ->assertOk()
            ->assertSee('Malaysia Explorer Group Tour')
            ->assertSee('12 seats');

        $this->get(route('group-tours.show', $groupTour->slug))
            ->assertOk()
            ->assertSee('Petronas Towers')
            ->assertSee('Tourist Spot')
            ->assertSee('12 seats')
            ->assertSee('group-album.jpg');

        $this->get(route('landing'))
            ->assertOk()
            ->assertSee('Upcoming Group Departures')
            ->assertSee('Malaysia Explorer Group Tour');
    }

    /** @test */
    public function drafts_and_tours_without_a_bookable_future_departure_stay_hidden(): void
    {
        GroupPackage::create([
            'fixed_itinerary_id' => $this->fixedItinerary->id,
            'name' => 'Draft Group Tour',
            'slug' => 'draft-group-tour',
            'price' => 999,
            'currency' => 'MYR',
            'departure_date' => now()->addWeek()->toDateString(),
            'status' => false,
        ]);

        GroupPackage::create([
            'fixed_itinerary_id' => $this->fixedItinerary->id,
            'name' => 'Expired Group Tour',
            'slug' => 'expired-group-tour',
            'departure_date' => now()->subDay()->toDateString(),
            'price' => 999,
            'currency' => 'MYR',
            'status' => true,
        ]);

        $this->get(route('group-tours.index'))
            ->assertOk()
            ->assertDontSee('Draft Group Tour')
            ->assertDontSee('Expired Group Tour');

        $this->get(route('group-tours.show', 'draft-group-tour'))->assertNotFound();
    }

    /** @test */
    public function publishing_an_updated_group_tour_makes_it_live_in_cms_and_public_api(): void
    {
        $groupTour = GroupPackage::create([
            'fixed_itinerary_id' => $this->fixedItinerary->id,
            'name' => 'Integrated Departure Tour',
            'slug' => 'integrated-departure-tour',
            'price' => 1800,
            'currency' => 'MYR',
            'total_pax' => 24,
            'status' => false,
        ]);

        $departureDate = now()->addMonth()->toDateString();
        $returnDate = now()->addMonth()->addDays(3)->toDateString();

        $this->actingAs($this->admin)->putJson(route('admin.group-packages.update', $groupTour->id), [
            'name' => $groupTour->name,
            'price' => 1800,
            'child_price' => 1200,
            'currency' => 'MYR',
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'total_pax' => 24,
            'is_active' => true,
        ])->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('departure_date', $departureDate . 'T00:00:00.000000Z');

        $this->assertDatabaseHas('group_packages', [
            'id' => $groupTour->id,
            'status' => true,
        ]);
        $this->assertSame($departureDate, $groupTour->fresh()->departure_date->toDateString());

        $this->actingAs($this->admin)->getJson(route('admin.group-packages.index'))
            ->assertOk()
            ->assertJsonPath('data.0.status', true)
            ->assertJsonPath('data.0.departure_date', $departureDate . 'T00:00:00.000000Z');

        $this->getJson('/api/v1/group-packages/integrated-departure-tour')
            ->assertOk()
            ->assertJsonPath('data.upcoming_departures.0.departure_date', $departureDate)
            ->assertJsonPath('data.upcoming_departures.0.adult_rate', '1800.00')
            ->assertJsonPath('data.upcoming_departures.0.child_rate', '1200.00')
            ->assertJsonPath('data.upcoming_departures.0.available_seats', 24);
    }

    /** @test */
    public function departure_dates_belong_to_one_website_tour_not_every_tour_using_the_template(): void
    {
        $firstTour = GroupPackage::create([
            'fixed_itinerary_id' => $this->fixedItinerary->id,
            'name' => 'First Website Tour',
            'slug' => 'first-website-tour',
            'price' => 1000,
            'currency' => 'MYR',
            'departure_date' => now()->addDays(20)->toDateString(),
            'status' => true,
        ]);
        $secondTour = GroupPackage::create([
            'fixed_itinerary_id' => $this->fixedItinerary->id,
            'name' => 'Second Website Tour',
            'slug' => 'second-website-tour',
            'price' => 1200,
            'currency' => 'MYR',
            'status' => true,
        ]);

        $this->getJson('/api/v1/group-packages/first-website-tour')
            ->assertOk()
            ->assertJsonCount(1, 'data.upcoming_departures');
        $this->getJson('/api/v1/group-packages/second-website-tour')
            ->assertNotFound();
    }
}
