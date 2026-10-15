<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Country;
use App\Models\Destination;
use App\Models\B2CItinerary;
use App\Models\Supplier;
use App\Models\ItineraryExpense;
use App\Models\FixedItinerary;

class B2CItineraryModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $country;
    protected $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'B2C Admin',
            'email' => 'b2cadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $this->user = User::create([
            'name' => 'B2C User',
            'email' => 'b2cuser@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->country = Country::create([
            'name' => 'Thailand',
            'code' => 'TH',
        ]);

        $this->destination = Destination::create([
            'name' => 'Bangkok',
            'slug' => 'bangkok',
        ]);
    }

    /** @test */
    public function admin_can_view_b2c_itineraries_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.b2c-itineraries.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_b2c_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.b2c-itineraries.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_b2c_itinerary()
    {
        $payload = [
            'user_id' => $this->user->id,
            'destination_id' => $this->country->id,
            'title' => 'Bangkok 4D3N Vacation',
            'client_name' => 'Alice Smith',
            'phone' => '9876543210',
            'duration_days' => 4,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.b2c-itineraries.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('b2c_itineraries', [
            'title' => 'Bangkok 4D3N Vacation',
            'client_name' => 'Alice Smith',
        ]);
    }

    /** @test */
    public function walk_in_lead_can_import_an_existing_b2c_itinerary_without_customer_data()
    {
        $source = B2CItinerary::create([
            'user_id' => $this->admin->id,
            'destination_id' => $this->country->id,
            'title' => 'Reusable Thailand Plan',
            'client_name' => 'Previous Private Customer',
            'phone' => '60111111111',
            'email' => 'previous@example.com',
            'duration_days' => 1,
            'short_description' => 'A reusable curated journey.',
            'highlights' => ['Private city discovery'],
            'inclusions' => ['Airport transfer'],
            'exclusions' => ['Flights'],
            'terms_conditions' => 'Rates subject to availability.',
            'itinerary' => [[
                'day' => 1,
                'title' => 'Bangkok Discovery',
                'notes' => 'Visit the capital highlights.',
                'hotels' => [], 'transport' => [], 'places' => [], 'spots' => [], 'activities' => [], 'meals' => [],
            ]],
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.b2c-itineraries.store'), [
            'import_source' => 'b2c',
            'b2c_source_id' => $source->id,
            'user_id' => $this->admin->id,
            'destination_id' => $this->country->id,
            'title' => 'New Customer Thailand Plan',
            'client_name' => 'New Walk-in Customer',
            'phone' => '60222222222',
            'email' => 'new@example.com',
            'duration_days' => 1,
            'lead_source' => 'walk_in',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect();
        $copy = B2CItinerary::where('title', 'New Customer Thailand Plan')->firstOrFail();
        $this->assertSame('New Walk-in Customer', $copy->client_name);
        $this->assertSame('60222222222', $copy->phone);
        $this->assertSame('new@example.com', $copy->email);
        $this->assertSame('Bangkok Discovery', $copy->itinerary[0]['title']);
        $this->assertSame('A reusable curated journey.', $copy->short_description);
        $this->assertSame(['Private city discovery'], $copy->highlights);
        $this->assertNotSame($source->client_name, $copy->client_name);
        $this->assertNotSame($source->phone, $copy->phone);
    }

    /** @test */
    public function fixed_itinerary_price_is_retained_in_the_lead_and_customer_quote()
    {
        $fixed = FixedItinerary::create([
            'user_id' => $this->admin->id,
            'title' => 'Locked Thailand Package',
            'duration_days' => 1,
            'duration_nights' => 0,
            'days' => [[
                'day_number' => 1, 'title' => 'Bangkok Arrival',
                'items' => [['item_type' => 'transport', 'title' => 'Transfer', 'room_type' => 'Private', 'price' => 100]],
            ]],
            'fixed_price' => 2500,
            'currency' => 'MYR',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.b2c-itineraries.store'), [
            'import_source' => 'fixed',
            'fixed_itinerary_id' => $fixed->id,
            'destination_id' => $this->country->id,
            'title' => 'Customer Fixed Quote',
            'client_name' => 'Direct Customer',
            'phone' => '+60123456789',
            'duration_days' => 1,
        ]);
        $response->assertSessionHasNoErrors()->assertRedirect();

        $lead = B2CItinerary::where('title', 'Customer Fixed Quote')->firstOrFail();
        $this->assertSame($fixed->id, $lead->fixed_itinerary_id);
        $this->assertSame('2500.00', $lead->fixed_price);
        $this->assertSame('fixed', $lead->pricing_mode);
        $this->assertSame('2500.00', $lead->total_price);
        $this->assertSame(2500.0, $lead->customer_total_price);

        $message = $this->actingAs($this->admin)
            ->get(route('admin.b2c-itineraries.whatsapp', $lead->id))
            ->json('text');
        $this->assertStringContainsString('MYR 2,500.00', $message);

        $this->actingAs($this->admin)
            ->get(route('admin.b2c-itineraries.pdf', $lead->id) . '?public=1')
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function an_empty_fixed_price_keeps_dynamic_item_pricing()
    {
        $itinerary = new B2CItinerary([
            'currency' => 'MYR', 'adults' => 1, 'markup_percentage' => 0,
            'fixed_price' => null,
            'itinerary' => [[
                'day' => 1,
                'hotels' => [['name' => 'Hotel', 'price_per_night' => 350, 'quantity' => 1]],
            ]],
        ]);

        $itinerary->calculatePricing();

        $this->assertSame('350.00', $itinerary->total_price);
    }

    /** @test */
    public function live_mode_keeps_the_fixed_parameter_but_uses_calculated_pricing()
    {
        $itinerary = new B2CItinerary([
            'currency' => 'MYR', 'adults' => 1, 'markup_percentage' => 0,
            'fixed_price' => 2500, 'pricing_mode' => 'live',
            'itinerary' => [[
                'day' => 1,
                'hotels' => [['name' => 'Hotel', 'price_per_night' => 350, 'quantity' => 1]],
            ]],
        ]);

        $itinerary->calculatePricing();

        $this->assertSame('2500.00', $itinerary->fixed_price);
        $this->assertSame('350.00', $itinerary->total_price);
    }

    /** @test */
    public function fixed_mode_never_multiplies_the_package_total_by_exchange_rate_or_passengers()
    {
        $itinerary = new B2CItinerary([
            'currency' => 'INR', 'quote_currency' => 'INR', 'exchange_rate' => 12,
            'adults' => 12, 'children_2_6' => 3, 'markup_percentage' => 50,
            'fixed_price' => 21200, 'pricing_mode' => 'fixed',
            'itinerary' => [[
                'day' => 1,
                'hotels' => [['name' => 'Hotel', 'price_per_night' => 5000, 'quantity' => 12]],
                'transport' => [['name' => 'Coach', 'price' => 10000]],
            ]],
        ]);

        $itinerary->calculatePricing();

        $this->assertSame('21200.00', $itinerary->total_price);
        $this->assertSame(21200.0, $itinerary->customer_total_price);
        $this->assertSame('INR', $itinerary->customer_currency);
    }

    /** @test */
    public function admin_can_view_b2c_kanban_board()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.b2c-itineraries.kanban'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_b2c_itinerary_status()
    {
        $itinerary = B2CItinerary::create([
            'user_id' => $this->user->id,
            'destination_id' => $this->destination->id,
            'title' => 'Sample B2C Itinerary',
            'client_name' => 'Bob Brown',
            'duration_days' => 3,
            'followup_status' => 'leads',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.b2c-itineraries.updateStatus', $itinerary->id), [
            'status' => 'interested',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('b2c_itineraries', [
            'id' => $itinerary->id,
            'followup_status' => 'interested',
        ]);
    }

    /** @test */
    public function admin_can_delete_b2c_itinerary()
    {
        $itinerary = B2CItinerary::create([
            'user_id' => $this->user->id,
            'destination_id' => $this->destination->id,
            'title' => 'B2C Itinerary To Delete',
            'client_name' => 'Delete Customer',
            'duration_days' => 2,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.b2c-itineraries.destroy', $itinerary->id));
        $response->assertRedirect(route('admin.b2c-itineraries.index'));
        $this->assertSoftDeleted('b2c_itineraries', ['id' => $itinerary->id]);
    }

    /** @test */
    public function customer_quote_uses_the_locked_selected_currency()
    {
        $itinerary = B2CItinerary::create([
            'user_id' => $this->user->id,
            'destination_id' => $this->country->id,
            'title' => 'USD Customer Quote',
            'client_name' => 'Currency Customer',
            'duration_days' => 2,
            'itinerary' => [[
                'day' => 1,
                'title' => 'Bangkok Discovery',
                'notes' => 'Meet your guide and explore the historic old town.',
            ]],
            'short_description' => 'A private city escape designed for this customer.',
            'highlights' => ['Private old town experience', 'Riverside evening'],
            'inclusions' => ['Private airport transfer', 'English-speaking guide'],
            'exclusions' => ['International airfare', 'Personal expenses'],
            'terms_conditions' => 'Quotation valid for seven days.',
            'adults' => 2,
            'currency' => 'MYR',
            'quote_currency' => 'USD',
            'exchange_rate' => 0.235,
            'base_cost' => 1000,
            'markup_amount' => 200,
            'total_price' => 1200,
        ]);

        $this->assertSame('USD', $itinerary->customer_currency);
        $this->assertSame(282.0, $itinerary->customer_total_price);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.b2c-itineraries.whatsapp', $itinerary->id));

        $response->assertOk();
        $message = $response->json('text');
        $this->assertStringContainsString('TOURLIZ HOLIDAYS', $message);
        $this->assertStringContainsString('DAY-BY-DAY ITINERARY', $message);
        $this->assertStringContainsString('PACKAGE INCLUSIONS', $message);
        $this->assertStringContainsString('A private city escape designed for this customer.', $message);
        $this->assertStringContainsString('Private old town experience', $message);
        $this->assertStringContainsString('Meet your guide and explore the historic old town.', $message);
        $this->assertStringContainsString('Private airport transfer', $message);
        $this->assertStringContainsString('International airfare', $message);
        $this->assertStringContainsString('Quotation valid for seven days.', $message);
        $this->assertStringContainsString('TOTAL PACKAGE PRICE', $message);
        $this->assertStringContainsString('Adult: *USD 141.00 per person*', $message);
        $this->assertStringContainsString('USD 282.00', $message);
        $this->assertStringNotContainsString('/admin/b2c-itineraries/', $message);

        $pdfResponse = $this->actingAs($this->admin)
            ->get(route('admin.b2c-itineraries.pdf', $itinerary->id) . '?public=1');
        $pdfResponse->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function customer_quote_can_round_the_converted_total()
    {
        $itinerary = new B2CItinerary([
            'currency' => 'MYR',
            'quote_currency' => 'SGD',
            'exchange_rate' => 0.3149,
            'total_price' => 1234.56,
            'round_quote' => true,
        ]);

        $this->assertSame(389.0, $itinerary->customer_total_price);
    }

    /** @test */
    public function children_are_added_to_the_established_adult_package_price()
    {
        $itinerary = new B2CItinerary([
            'currency' => 'MYR',
            'adults' => 2,
            'children_2_6' => 1,
            'children_6_11' => 1,
            'markup_percentage' => 20,
            'itinerary' => [[
                'day' => 1,
                'hotels' => [[
                    'name' => 'Test Hotel',
                    'price_per_night' => 100,
                    'add_on_price' => 0,
                    'quantity' => 1,
                ]],
            ]],
        ]);

        $itinerary->calculatePricing();

        // Adult subtotal: 100 + 20% = 120; adult rate = 60.
        // Child add-ons: 15 (25%) + 30 (50%) = 45; final = 165.
        $this->assertSame(100.0, (float) $itinerary->base_cost);
        $this->assertSame(20.0, (float) $itinerary->markup_amount);
        $this->assertSame(165.0, (float) $itinerary->total_price);
        $adultRate = (float) $itinerary->total_price / (2 + 0.25 + 0.50);
        $this->assertSame(60.0, $adultRate);
    }

    /** @test */
    public function multiple_involved_vendors_are_retained_and_transport_copy_is_route_focused()
    {
        $this->withoutExceptionHandling();
        $transportVendor = Supplier::create(['name' => 'City Wheels', 'type' => 'Transport', 'phone' => '60111111111']);
        $hotelVendor = Supplier::create(['name' => 'Grand Stay', 'type' => 'Hotel', 'phone' => '60222222222']);
        $itinerary = B2CItinerary::create([
            'user_id' => $this->admin->id,
            'destination_id' => $this->country->id,
            'title' => 'Vendor Test Trip',
            'client_name' => 'Vendor Guest',
            'duration_days' => 1,
            'adults' => 2,
            'itinerary' => [],
            'currency' => 'MYR',
            'status' => 'draft',
        ]);
        $dayPlan = [[
            'day' => 1,
            'title' => 'City Discovery',
            'notes' => 'Morning pickup followed by the city tour.',
            'places' => [['name' => 'Royal Palace']],
            'transport' => [['name' => 'Private Van', 'vehicle_type' => '12 Seater Van', 'supplier_id' => $transportVendor->id]],
            'hotels' => [['name' => 'Grand Stay', 'type' => 'Deluxe', 'supplier_id' => $hotelVendor->id]],
            'meals' => [['name' => 'Lunch']],
        ]];

        $response = $this->actingAs($this->admin)->put(route('admin.b2c-itineraries.update', $itinerary->id), [
            'itinerary' => json_encode($dayPlan),
            'title' => $itinerary->title,
            'client_name' => $itinerary->client_name,
            'adults' => 2,
            'duration_days' => 1,
            'supplier_id' => $transportVendor->id,
            'lead_source' => 'walk_in',
            'involved_vendors' => json_encode([$transportVendor->id, $hotelVendor->id]),
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertCount(2, ItineraryExpense::where('itinerary_id', $itinerary->id)->where('description', 'Involved Vendor')->get());

        $transportExpense = ItineraryExpense::where('itinerary_id', $itinerary->id)
            ->where('supplier_id', $transportVendor->id)->firstOrFail();
        $message = $this->actingAs($this->admin)
            ->get(route('admin.expenses.whatsapp-vendor', $transportExpense->id))
            ->assertOk()->json('text');

        $this->assertStringContainsString('Day 1: City Discovery', $message);
        $this->assertStringContainsString('Morning pickup followed by the city tour.', $message);
        $this->assertStringContainsString('Places: Royal Palace', $message);
        $this->assertStringContainsString('12 Seater Van', $message);
        $this->assertStringNotContainsString('Grand Stay', $message);
        $this->assertStringNotContainsString('Meals:', $message);
    }
}
