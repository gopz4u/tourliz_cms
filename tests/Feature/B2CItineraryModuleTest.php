<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Country;
use App\Models\Destination;
use App\Models\B2CItinerary;

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
}
