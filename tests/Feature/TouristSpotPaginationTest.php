<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Country;
use App\Models\Destination;
use App\Models\TouristSpot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TouristSpotPaginationTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private Country $country;
    private Destination $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'spots@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $this->country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        $this->destination = Destination::create([
            'name' => 'Kuala Lumpur',
            'country' => 'Malaysia',
            'slug' => 'kuala-lumpur-spots-test',
        ]);
    }

    /** @test */
    public function tourist_spots_can_be_searched_and_filtered(): void
    {
        $this->makeSpot('Petronas Towers', true);
        $this->makeSpot('Hidden Waterfall', false);

        $response = $this->actingAs($this->admin)->get(route('admin.tourist-spots.index', [
            'search' => 'Petronas',
            'country_id' => $this->country->id,
            'destination_id' => $this->destination->id,
            'status' => 'active',
        ]));

        $response->assertOk()
            ->assertSee('Petronas Towers')
            ->assertDontSee('Hidden Waterfall')
            ->assertSee('Filtered results');
    }

    /** @test */
    public function page_size_and_query_are_preserved_across_pagination(): void
    {
        foreach (range(1, 26) as $number) {
            $this->makeSpot(sprintf('City Spot %02d', $number), true);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.tourist-spots.index', [
            'search' => 'City Spot',
            'per_page' => 25,
        ]));

        $response->assertOk()
            ->assertSee('Showing <strong>1</strong>–<strong>25</strong>', false)
            ->assertSee('search=City%20Spot', false)
            ->assertSee('per_page=25', false);
    }

    private function makeSpot(string $name, bool $active): TouristSpot
    {
        return TouristSpot::create([
            'name' => $name,
            'country_id' => $this->country->id,
            'destination_id' => $this->destination->id,
            'description' => "Description for {$name}",
            'is_active' => $active,
        ]);
    }
}
