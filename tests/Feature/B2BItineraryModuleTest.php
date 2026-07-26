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
}
