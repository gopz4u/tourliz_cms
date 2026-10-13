<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\FixedItinerary;
use App\Models\GroupDeparture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupDepartureModuleTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $template;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'departure@test.com', 'password' => bcrypt('password'), 'role' => 'super_admin']);
        $this->template = FixedItinerary::create([
            'user_id' => $this->admin->id, 'title' => 'Malaysia Discovery', 'duration_days' => 4,
            'duration_nights' => 3, 'fixed_price' => 1200, 'currency' => 'MYR', 'status' => 'active',
            'days' => [['day_number' => 1, 'title' => 'Arrival', 'items' => []]],
        ]);
    }

    /** @test */
    public function admin_can_announce_a_group_departure_from_fixed_itinerary()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.group-departures.store'), [
            'fixed_itinerary_id' => $this->template->id, 'title' => 'December Malaysia Group',
            'departure_date' => '2026-12-10', 'return_date' => '2026-12-13',
            'adult_rate' => 1500, 'child_rate' => 1100, 'currency' => 'MYR', 'capacity' => 20, 'status' => 'announced',
        ]);

        $departure = GroupDeparture::first();
        $response->assertRedirect(route('admin.group-departures.show', $departure));
        $this->assertDatabaseHas('group_departures', ['title' => 'December Malaysia Group', 'capacity' => 20]);
    }

    /** @test */
    public function admin_can_add_and_manage_a_customer()
    {
        $departure = GroupDeparture::create([
            'fixed_itinerary_id' => $this->template->id, 'user_id' => $this->admin->id, 'title' => 'Group Trip',
            'departure_date' => '2026-12-10', 'adult_rate' => 1500, 'currency' => 'MYR', 'capacity' => 10, 'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.group-departures.customers.store', $departure), [
            'name' => 'Test Customer', 'phone' => '+60123456789', 'customer_type' => 'adult', 'pax' => 2,
            'rate' => 1500, 'amount_paid' => 500, 'payment_status' => 'partial', 'booking_status' => 'confirmed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('group_departure_customers', ['name' => 'Test Customer', 'pax' => 2, 'payment_status' => 'partial']);
        $this->assertSame(8, $departure->fresh()->available_seats);
    }

    /** @test */
    public function departure_prevents_overbooking()
    {
        $departure = GroupDeparture::create([
            'fixed_itinerary_id' => $this->template->id, 'title' => 'Small Group', 'departure_date' => '2026-12-10',
            'adult_rate' => 1500, 'currency' => 'MYR', 'capacity' => 2, 'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->from(route('admin.group-departures.show', $departure))->post(route('admin.group-departures.customers.store', $departure), [
            'name' => 'Large Family', 'customer_type' => 'adult', 'pax' => 3, 'rate' => 1500,
            'payment_status' => 'pending', 'booking_status' => 'reserved',
        ]);

        $response->assertRedirect(route('admin.group-departures.show', $departure));
        $response->assertSessionHasErrors('pax');
        $this->assertDatabaseCount('group_departure_customers', 0);
    }

    /** @test */
    public function admin_can_download_the_professional_group_trip_pdf()
    {
        $departure = GroupDeparture::create([
            'fixed_itinerary_id' => $this->template->id, 'title' => 'Downloadable Group Trip',
            'departure_date' => '2026-12-10', 'return_date' => '2026-12-13',
            'adult_rate' => 1500, 'child_rate' => 1000, 'currency' => 'MYR', 'capacity' => 20, 'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.group-departures.itinerary', $departure));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function whatsapp_share_contains_the_group_proposal_without_a_web_link()
    {
        $departure = GroupDeparture::create([
            'fixed_itinerary_id' => $this->template->id, 'title' => 'WhatsApp Group Trip',
            'departure_date' => '2026-12-10', 'adult_rate' => 1500, 'currency' => 'MYR', 'capacity' => 20, 'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.group-departures.whatsapp', $departure));
        $response->assertRedirect();
        $location = urldecode($response->headers->get('Location'));
        $this->assertStringContainsString('WhatsApp Group Trip', $location);
        $this->assertStringContainsString('GROUP TRIP RATE', $location);
        $this->assertStringNotContainsString('/admin/group-departures/', $location);
    }
}
