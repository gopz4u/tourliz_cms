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

class QuoteCurrencyPresentationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function customer_quote_uses_the_selected_currency_while_reporting_uses_its_myr_equivalent()
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

        $b2c = B2CItinerary::create([
            'user_id' => $admin->id,
            'destination_id' => $country->id,
            'client_name' => 'Ninu',
            'title' => 'Malaysia Escape',
            'duration_days' => 4,
            'itinerary' => [],
            'currency' => 'MYR',
            'quote_currency' => 'INR',
            'exchange_rate' => 20,
            'total_price' => 1250,
        ]);
        $b2b = CustomItinerary::create([
            'user_id' => $legacyUser->id,
            'client_name' => 'Agency Customer',
            'title' => 'Agency Malaysia Escape',
            'duration_days' => 4,
            'itinerary' => [],
            'currency' => 'MYR',
            'quote_currency' => 'INR',
            'exchange_rate' => 20,
            'total_price' => 2100,
        ]);

        $this->assertSame(25000.0, $b2c->customer_total_price);
        $this->assertSame(1250.0, $b2c->myr_quoted_total);
        $this->assertSame(42000.0, $b2b->customer_total_price);
        $this->assertSame(2100.0, $b2b->myr_quoted_total);

        $this->actingAs($admin)->get(route('admin.b2c-itineraries.index'))
            ->assertOk()
            ->assertSee('INR 25,000.00')
            ->assertSee('MYR 1,250.00');
    }
}
