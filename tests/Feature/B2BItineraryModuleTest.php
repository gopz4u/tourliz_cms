<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Agency;
use App\Models\Country;
use App\Models\Destination;
use App\Models\CustomItinerary;
use App\Models\ItineraryExpense;
use App\Models\Supplier;

class B2BItineraryModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $agency;
    protected $country;
    protected $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'B2B Admin',
            'email' => 'b2badmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $this->user = User::create([
            'name' => 'B2B User',
            'email' => 'b2buser@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->agency = Agency::create([
            'agency_name' => 'Test Travel Agency',
            'company_name' => 'Test Agency Co',
            'contact_person' => 'Agent Smith',
            'email' => 'agent@agency.com',
            'phone' => '1234567890',
            'status' => 'active',
        ]);

        $this->country = Country::create([
            'name' => 'Malaysia',
            'code' => 'MY',
        ]);

        $this->destination = Destination::create([
            'name' => 'Kuala Lumpur',
            'slug' => 'kuala-lumpur',
        ]);
    }

    /** @test */
    public function admin_can_view_b2b_itineraries_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.b2b-itineraries.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_b2b_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.b2b-itineraries.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_b2b_itinerary()
    {
        $payload = [
            'user_id' => $this->user->id,
            'agency_id' => $this->agency->id,
            'destination_id' => $this->country->id,
            'title' => 'Malaysia 5D4N B2B Tour',
            'client_name' => 'Corporate Client',
            'duration_days' => 5,
            'start_date' => now()->addDays(15)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.b2b-itineraries.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('custom_itineraries', [
            'title' => 'Malaysia 5D4N B2B Tour',
            'agency_id' => $this->agency->id,
        ]);
    }

    /** @test */
    public function admin_can_view_b2b_kanban_board()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.b2b-itineraries.kanban'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_b2b_itinerary_status()
    {
        $itinerary = CustomItinerary::create([
            'user_id' => $this->user->id,
            'agency_id' => $this->agency->id,
            'destination_id' => $this->destination->id,
            'title' => 'Sample B2B Itinerary',
            'client_name' => 'Sample Client',
            'duration_days' => 3,
            'status' => 'draft',
            'followup_status' => 'leads',
            'type' => 'b2b',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.b2b-itineraries.updateStatus', $itinerary->id), [
            'status' => 'interested',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('custom_itineraries', [
            'id' => $itinerary->id,
            'followup_status' => 'interested',
        ]);
    }

    /** @test */
    public function admin_can_delete_b2b_itinerary()
    {
        $itinerary = CustomItinerary::create([
            'user_id' => $this->user->id,
            'agency_id' => $this->agency->id,
            'destination_id' => $this->destination->id,
            'title' => 'Itinerary To Delete',
            'duration_days' => 2,
            'type' => 'b2b',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.b2b-itineraries.destroy', $itinerary->id));
        $response->assertRedirect(route('admin.b2b-itineraries.index'));
        $this->assertSoftDeleted('custom_itineraries', ['id' => $itinerary->id]);
    }

    /** @test */
    public function b2b_customer_proposal_contains_full_imported_style_content()
    {
        $itinerary = CustomItinerary::create([
            'user_id' => $this->admin->id,
            'agency_id' => $this->agency->id,
            'destination_id' => $this->country->id,
            'title' => 'Malaysia Agency Escape',
            'client_name' => 'Agency Guest',
            'duration_days' => 1,
            'adults' => 2,
            'children_2_6' => 1,
            'currency' => 'MYR',
            'total_price' => 2250,
            'short_description' => 'A curated Malaysian experience for agency guests.',
            'highlights' => ['Private city discovery'],
            'inclusions' => ['Private airport transfers'],
            'exclusions' => ['International flights'],
            'terms_conditions' => 'Agency quotation valid for seven days.',
            'itinerary' => [[
                'day' => 1,
                'title' => 'Kuala Lumpur Icons',
                'notes' => 'Meet the guide and discover the capital.',
                'spots' => [['name' => 'Petronas Twin Towers']],
            ]],
            'status' => 'draft',
        ]);

        $edit = $this->actingAs($this->admin)->get(route('admin.b2b-itineraries.edit', $itinerary->id));
        $edit->assertOk()->assertSee('Import Fixed Itinerary');

        $message = $this->actingAs($this->admin)
            ->get(route('admin.b2b-itineraries.whatsapp', $itinerary->id))
            ->assertOk()->json('text');
        $this->assertStringContainsString('Agency Travel Proposal', $message);
        $this->assertStringContainsString('Private city discovery', $message);
        $this->assertStringContainsString('Meet the guide and discover the capital.', $message);
        $this->assertStringContainsString('Private airport transfers', $message);
        $this->assertStringContainsString('International flights', $message);
        $this->assertStringContainsString('TOTAL PACKAGE PRICE', $message);
        $this->assertStringNotContainsString('/admin/b2b-itineraries/', $message);

        $this->actingAs($this->admin)
            ->get(route('admin.b2b-itineraries.pdf', $itinerary->id) . '?public=1')
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function b2b_proposal_can_save_customer_content()
    {
        $itinerary = CustomItinerary::create([
            'user_id' => $this->admin->id,
            'agency_id' => $this->agency->id,
            'destination_id' => $this->country->id,
            'title' => 'Draft Agency Proposal',
            'client_name' => 'Agency Guest',
            'duration_days' => 1,
            'adults' => 2,
            'itinerary' => [],
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.b2b-itineraries.update', $itinerary->id), [
            'itinerary' => json_encode([['day' => 1, 'title' => 'Imported Day', 'notes' => 'Imported day description', 'places' => [], 'activities' => [], 'spots' => [], 'transport' => [], 'meals' => [], 'hotels' => []]]),
            'title' => 'Saved Agency Proposal',
            'client_name' => 'Agency Guest',
            'adults' => 2,
            'short_description' => 'Saved journey overview',
            'highlights' => "First highlight\nSecond highlight",
            'inclusions' => "Hotel\nTransfers",
            'exclusions' => "Flights\nInsurance",
            'terms_conditions' => 'Saved agency terms.',
            'quote_currency' => 'USD',
            'exchange_rate' => 0.25,
            'exchange_rate_date' => '2026-10-15',
            'round_quote' => '0',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect();
        $itinerary->refresh();
        $this->assertSame('Saved Agency Proposal', $itinerary->title);
        $this->assertSame(['First highlight', 'Second highlight'], $itinerary->highlights);
        $this->assertSame('Imported day description', $itinerary->itinerary[0]['notes']);
        $this->assertSame('USD', $itinerary->customer_currency);
        $this->assertSame(0.25, (float) $itinerary->exchange_rate);
    }

    /** @test */
    public function b2b_children_are_added_without_reducing_the_adult_rate()
    {
        $itinerary = new CustomItinerary([
            'currency' => 'MYR',
            'adults' => 2,
            'children_2_6' => 1,
            'children_6_11' => 1,
            'markup_percentage' => 20,
            'itinerary' => [[
                'day' => 1,
                'hotels' => [['price_per_night' => 100, 'quantity' => 1]],
            ]],
        ]);

        $itinerary->calculatePricing();

        $this->assertSame(165.0, (float) $itinerary->total_price);
        $this->assertSame(60.0, (float) $itinerary->total_price / (2 + 0.25 + 0.50));
    }

    /** @test */
    public function b2b_retains_multiple_vendors_and_transport_voucher_lists_days_and_places()
    {
        $transportVendor = Supplier::create(['name' => 'Agency Wheels', 'type' => 'Transport', 'phone' => '60111111111']);
        $hotelVendor = Supplier::create(['name' => 'Agency Hotel', 'type' => 'Hotel', 'phone' => '60222222222']);
        $itinerary = CustomItinerary::create([
            'user_id' => $this->admin->id,
            'agency_id' => $this->agency->id,
            'destination_id' => $this->country->id,
            'title' => 'B2B Vendor Trip',
            'client_name' => 'Agency Guest',
            'duration_days' => 1,
            'adults' => 2,
            'itinerary' => [],
            'currency' => 'MYR',
            'status' => 'draft',
        ]);
        $dayPlan = [[
            'day' => 1,
            'title' => 'Kuala Lumpur Discovery',
            'notes' => 'Airport pickup followed by city sightseeing.',
            'places' => [['name' => 'Royal Palace']],
            'spots' => [['name' => 'Petronas Twin Towers']],
            'transport' => [['name' => 'Private Van', 'vehicle_type' => '12 Seater Van', 'supplier_id' => $transportVendor->id]],
            'hotels' => [['name' => 'Agency Hotel', 'type' => 'Deluxe', 'supplier_id' => $hotelVendor->id]],
        ]];

        $this->actingAs($this->admin)->put(route('admin.b2b-itineraries.update', $itinerary->id), [
            'itinerary' => json_encode($dayPlan),
            'title' => $itinerary->title,
            'client_name' => $itinerary->client_name,
            'adults' => 2,
            'supplier_id' => $transportVendor->id,
            'involved_vendors' => json_encode([$transportVendor->id, $hotelVendor->id]),
        ])->assertSessionHasNoErrors();

        $expenses = ItineraryExpense::where('itinerary_id', $itinerary->id)
            ->where('itinerary_type', 'b2b')->where('description', 'Involved Vendor')->get();
        $this->assertCount(2, $expenses);
        $this->assertSame('Transport', $expenses->firstWhere('supplier_id', $transportVendor->id)->category);
        $this->assertSame('Hotel', $expenses->firstWhere('supplier_id', $hotelVendor->id)->category);

        $transportExpense = $expenses->firstWhere('supplier_id', $transportVendor->id);
        $message = $this->actingAs($this->admin)
            ->get(route('admin.expenses.whatsapp-vendor', $transportExpense->id))
            ->assertOk()->json('text');
        $this->assertStringContainsString('Day 1: Kuala Lumpur Discovery', $message);
        $this->assertStringContainsString('Places: Royal Palace, Petronas Twin Towers', $message);
        $this->assertStringContainsString('12 Seater Van', $message);

        $this->actingAs($this->admin)
            ->get(route('admin.expenses.pdf-vendor', $transportExpense->id))
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }
}
