<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\B2CItinerary;
use App\Models\Country;
use App\Models\CustomItinerary;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRevenueBreakdownTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dashboard_separates_live_b2b_and_b2c_quotes_and_excludes_deleted_customers()
    {
        $legacyUser = User::create([
            'name' => 'Owner', 'email' => 'owner-user@example.test', 'password' => bcrypt('password'),
        ]);
        $admin = Admin::forceCreate([
            'id' => $legacyUser->id,
            'name' => 'Owner', 'email' => 'owner@example.test', 'password' => bcrypt('password'), 'role' => 'super_admin',
        ]);
        $country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        Destination::create(['name' => 'Kuala Lumpur', 'slug' => 'kuala-lumpur']);

        B2CItinerary::create([
            'user_id' => $admin->id,
            'destination_id' => $country->id,
            'client_name' => 'Current B2C Guest',
            'title' => 'Current B2C Quote',
            'duration_days' => 3,
            'itinerary' => [],
            'total_price' => 1250,
        ]);
        $deletedB2c = B2CItinerary::create([
            'user_id' => $admin->id,
            'destination_id' => $country->id,
            'client_name' => 'Deleted B2C Guest',
            'title' => 'Deleted B2C Quote',
            'duration_days' => 2,
            'itinerary' => [],
            'total_price' => 800,
        ]);
        $deletedB2c->delete();

        CustomItinerary::create([
            'user_id' => $legacyUser->id,
            'client_name' => 'Current B2B Customer',
            'title' => 'Current B2B Quote',
            'duration_days' => 4,
            'itinerary' => [],
            'total_price' => 2100,
        ]);
        $deletedB2b = CustomItinerary::create([
            'user_id' => $legacyUser->id,
            'client_name' => 'Deleted B2B Customer',
            'title' => 'Deleted B2B Quote',
            'duration_days' => 4,
            'itinerary' => [],
            'total_price' => 600,
        ]);
        $deletedB2b->delete();

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('salesStats', function ($stats) {
                return $stats['b2c_quoted'] === '1,250.00'
                    && $stats['b2b_quoted'] === '2,100.00';
            })
            ->assertSee('B2C Quoted Revenue')
            ->assertSee('B2B Quoted Revenue');
    }
}
