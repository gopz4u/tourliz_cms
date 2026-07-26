<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Country;
use App\Models\Destination;
use App\Models\Package;
use App\Models\Service;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Supplier;

class CatalogAndInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $country;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $this->country = Country::create([
            'name' => 'Malaysia',
            'code' => 'MY',
        ]);
    }

    /** Helper: create a destination with required fields */
    protected function makeDestination(array $overrides = []): Destination
    {
        return Destination::create(array_merge([
            'name' => 'Test Destination ' . uniqid(),
            'country' => 'Malaysia',
            'location' => 'West Malaysia',
            'city' => 'Kuala Lumpur',
            'slug' => 'test-destination-' . uniqid(),
        ], $overrides));
    }

    // ─── DESTINATIONS ────────────────────────────────────────────

    /** @test */
    public function admin_can_view_destinations_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.destinations.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_destinations_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.destinations.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_destination()
    {
        $data = [
            'name' => 'Test City',
            'country' => 'Malaysia',
            'location' => 'West Malaysia',
            'city' => 'Kuala Lumpur',
            'description' => 'A vibrant capital city',
            'rating' => 4,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.destinations.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Test City']);
        $this->assertDatabaseHas('destinations', ['name' => 'Test City', 'city' => 'Kuala Lumpur']);
    }

    /** @test */
    public function admin_can_show_a_destination()
    {
        $destination = $this->makeDestination([
            'name' => 'Show City',
            'country' => 'Thailand',
            'location' => 'Central',
            'city' => 'Bangkok',
            'slug' => 'show-city',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.destinations.show', $destination->id));
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Show City']);
    }

    /** @test */
    public function admin_can_update_a_destination()
    {
        $destination = $this->makeDestination([
            'name' => 'Old City',
            'country' => 'Malaysia',
            'location' => 'East',
            'city' => 'Kota Kinabalu',
            'slug' => 'old-city',
        ]);

        $updateData = [
            'name' => 'Updated City',
            'country' => 'Malaysia',
            'location' => 'East',
            'city' => 'Kota Kinabalu',
            'rating' => 5,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.destinations.update', $destination->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated City', 'rating' => 5]);
        $this->assertDatabaseHas('destinations', ['id' => $destination->id, 'name' => 'Updated City']);
    }

    /** @test */
    public function admin_can_soft_delete_a_destination()
    {
        $destination = $this->makeDestination([
            'name' => 'Delete City',
            'country' => 'Singapore',
            'location' => 'Central',
            'city' => 'Singapore',
            'slug' => 'delete-city',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.destinations.destroy', $destination->id));

        $response->assertRedirect(route('admin.destinations.index'));
        $this->assertSoftDeleted('destinations', ['id' => $destination->id]);
    }

    // ─── PACKAGES ────────────────────────────────────────────────

    /** @test */
    public function admin_can_view_packages_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.packages.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_packages_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.packages.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_package()
    {
        $destination = $this->makeDestination([
            'name' => 'Package Destination',
            'slug' => 'package-destination',
        ]);

        $data = [
            'country_id' => $this->country->id,
            'destination_ids' => [$destination->id],
            'name' => 'Penang 3D2N Tour',
            'slug' => 'penang-3d2n-tour',
            'description' => 'Explore beautiful Penang',
            'price' => 299,
            'currency' => 'MYR',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.packages.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Penang 3D2N Tour']);
        $this->assertDatabaseHas('packages', [
            'name' => 'Penang 3D2N Tour',
            'destination_id' => $destination->id,
        ]);
    }

    /** @test */
    public function admin_can_update_a_package()
    {
        $destination = $this->makeDestination([
            'name' => 'Update Dest',
            'slug' => 'update-dest',
        ]);

        $package = Package::create([
            'country_id' => $this->country->id,
            'destination_id' => $destination->id,
            'name' => 'Original Package',
            'slug' => 'original-package',
            'price' => 100,
            'is_active' => true,
        ]);

        $updateData = [
            'name' => 'Updated Package Name',
            'country_id' => $this->country->id,
            'destination_ids' => [$destination->id],
            'price' => 150,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.packages.update', $package->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated Package Name']);
        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'name' => 'Updated Package Name',
        ]);
    }

    /** @test */
    public function admin_can_soft_delete_a_package()
    {
        $package = Package::create([
            'name' => 'Delete Package',
            'slug' => 'delete-package',
            'price' => 200,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.packages.destroy', $package->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Package deleted successfully']);
        $this->assertSoftDeleted('packages', ['id' => $package->id]);
    }

    /** @test */
    public function admin_can_view_package_edit_page()
    {
        $package = Package::create([
            'name' => 'Edit Package',
            'slug' => 'edit-package',
            'price' => 300,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.packages.edit', $package->id));
        $response->assertStatus(200);
    }

    // ─── SERVICES ─────────────────────────────────────────────────

    /** @test */
    public function admin_can_view_services_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.services.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_services_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.services.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_service()
    {
        $data = [
            'name' => 'Airport Transfer Service',
            'category' => 'Transport',
            'description' => 'Comfortable airport transfers',
            'price' => 150,
            'currency' => 'MYR',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.services.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Airport Transfer Service']);
        $this->assertDatabaseHas('services', ['name' => 'Airport Transfer Service', 'category' => 'Transport']);
    }

    /** @test */
    public function admin_can_update_a_service()
    {
        $service = Service::create([
            'name' => 'Old Service',
            'slug' => 'old-service',
            'category' => 'Hotels',
            'price' => 100,
        ]);

        $updateData = [
            'name' => 'Updated Service',
            'category' => 'Hotels',
            'price' => 200,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.services.update', $service->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated Service']);
        $this->assertDatabaseHas('services', ['id' => $service->id, 'name' => 'Updated Service']);
    }

    /** @test */
    public function admin_can_soft_delete_a_service()
    {
        $service = Service::create([
            'name' => 'Delete Service',
            'slug' => 'delete-service',
            'category' => 'Activities',
            'price' => 50,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.services.destroy', $service->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Service deleted successfully']);
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    // ─── HOTELS ───────────────────────────────────────────────────

    /** @test */
    public function admin_can_view_hotels_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.hotels.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_hotels_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.hotels.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_hotel()
    {
        $destination = $this->makeDestination([
            'name' => 'Hotel City',
            'slug' => 'hotel-city',
        ]);

        $data = [
            'name' => 'Grand Hyatt KL',
            'destination_id' => $destination->id,
            'star_rating' => 5,
            'address' => '123 Jalan Sultan Ismail',
            'description' => 'Luxury hotel in city center',
            'rooms' => [
                ['room_type' => 'Deluxe', 'base_price' => 500, 'capacity' => 2],
                ['room_type' => 'Suite', 'base_price' => 1000, 'capacity' => 4],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.hotels.store'), $data);

        $response->assertRedirect(route('admin.hotels.index'));
        $this->assertDatabaseHas('hotels', ['name' => 'Grand Hyatt KL', 'star_rating' => 5]);
        $this->assertDatabaseHas('hotel_rooms', ['room_type' => 'Deluxe']);
        $this->assertDatabaseHas('hotel_rooms', ['room_type' => 'Suite']);
    }

    /** @test */
    public function admin_can_update_a_hotel()
    {
        $destination = $this->makeDestination([
            'name' => 'Update Hotel City',
            'slug' => 'update-hotel-city',
        ]);

        $hotel = Hotel::create([
            'name' => 'Old Hotel',
            'destination_id' => $destination->id,
            'star_rating' => 3,
        ]);

        $updateData = [
            'name' => 'Grand Kinabalu Hotel',
            'destination_id' => $destination->id,
            'star_rating' => 4,
            'rooms' => [
                ['room_type' => 'Standard', 'base_price' => 200, 'capacity' => 2],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.hotels.update', $hotel->id), $updateData);

        $response->assertRedirect(route('admin.hotels.index'));
        $this->assertDatabaseHas('hotels', ['id' => $hotel->id, 'name' => 'Grand Kinabalu Hotel']);
        $this->assertDatabaseHas('hotel_rooms', ['room_type' => 'Standard']);
    }

    /** @test */
    public function admin_can_soft_delete_a_hotel()
    {
        $destination = $this->makeDestination([
            'slug' => 'delete-hotel-city',
        ]);

        $hotel = Hotel::create([
            'name' => 'Delete Hotel',
            'destination_id' => $destination->id,
            'star_rating' => 2,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.hotels.destroy', $hotel->id));

        $response->assertRedirect(route('admin.hotels.index'));
        $this->assertSoftDeleted('hotels', ['id' => $hotel->id]);
    }

    /** @test */
    public function admin_can_view_hotel_edit_page()
    {
        $destination = $this->makeDestination([
            'slug' => 'edit-hotel-city',
        ]);

        $hotel = Hotel::create([
            'name' => 'Edit Hotel',
            'destination_id' => $destination->id,
            'star_rating' => 3,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.hotels.edit', $hotel->id));
        $response->assertStatus(200);
    }

    // ─── SUPPLIERS ────────────────────────────────────────────────

    /** @test */
    public function admin_can_view_suppliers_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.suppliers.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_supplier()
    {
        $data = [
            'name' => 'ABC Travel Agency',
            'type' => 'Hotel',
            'contact_person' => 'John Supplier',
            'phone' => '60123456789',
            'email' => 'john@abctravel.com',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.suppliers.store'), $data);

        $response->assertRedirect(route('admin.suppliers.index'));
        $this->assertDatabaseHas('suppliers', [
            'name' => 'ABC Travel Agency',
            'type' => 'Hotel',
        ]);
    }

    /** @test */
    public function admin_can_update_a_supplier()
    {
        $supplier = Supplier::create([
            'name' => 'Old Supplier',
            'type' => 'Transport',
            'is_active' => true,
        ]);

        $updateData = [
            'name' => 'Updated Supplier Co',
            'type' => 'Transport',
            'contact_person' => 'Jane Supplier',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.suppliers.update', $supplier->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Updated Supplier Co']);
    }

    /** @test */
    public function admin_can_delete_a_supplier()
    {
        $supplier = Supplier::create([
            'name' => 'Delete Supplier',
            'type' => 'Activities',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.suppliers.destroy', $supplier->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    // ─── ATTRACTIONS ──────────────────────────────────────────────

    /** @test */
    public function admin_can_view_attractions_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.attractions.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_attractions_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.attractions.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_an_attraction()
    {
        $destination = $this->makeDestination([
            'name' => 'Attraction City',
            'slug' => 'attraction-city',
        ]);

        $data = [
            'name' => 'A Famosa Fort',
            'destination_id' => $destination->id,
            'description' => 'Historical Portuguese fortress',
            'price' => 10,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.attractions.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('attractions', ['name' => 'A Famosa Fort']);
    }

    /** @test */
    public function admin_can_update_an_attraction()
    {
        $destination = $this->makeDestination([
            'name' => 'Update Attraction Dest',
            'slug' => 'update-attraction-dest',
        ]);

        $attraction = \App\Models\Attraction::create([
            'name' => 'Old Attraction',
            'slug' => 'old-attraction',
            'destination_id' => $destination->id,
        ]);

        $updateData = [
            'name' => 'Updated Attraction Name',
            'destination_id' => $destination->id,
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.attractions.update', $attraction->id), $updateData);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('attractions', ['id' => $attraction->id, 'name' => 'Updated Attraction Name']);
    }

    /** @test */
    public function admin_can_delete_an_attraction()
    {
        $attraction = \App\Models\Attraction::create([
            'name' => 'Delete Attraction',
            'slug' => 'delete-attraction',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.attractions.destroy', $attraction->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Attraction deleted successfully']);
        $this->assertSoftDeleted('attractions', ['id' => $attraction->id]);
    }

    /** @test */
    public function admin_can_view_attraction_edit_page()
    {
        $attraction = \App\Models\Attraction::create([
            'name' => 'Edit Attraction',
            'slug' => 'edit-attraction',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.attractions.edit', $attraction->id));
        $response->assertStatus(200);
    }

    // ─── GROUP PACKAGES ──────────────────────────────────────────

    /** @test */
    public function admin_can_view_group_packages_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.group-packages.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_group_packages_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.group-packages.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_group_package()
    {
        $destination = $this->makeDestination([
            'name' => 'Group Package Dest',
            'slug' => 'group-package-dest',
        ]);

        $data = [
            'destination_id' => $destination->id,
            'name' => 'Cameron Group Tour 3D2N',
            'slug' => 'cameron-group-tour-3d2n',
            'description' => 'Group tour package for Cameron Highlands',
            'price' => 350,
            'min_pax' => 10,
            'max_pax' => 30,
            'currency' => 'MYR',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.group-packages.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('group_packages', ['name' => 'Cameron Group Tour 3D2N']);
    }

    /** @test */
    public function admin_can_update_a_group_package()
    {
        $destination = $this->makeDestination([
            'name' => 'Update Group Dest',
            'slug' => 'update-group-dest',
        ]);

        $groupPackage = \App\Models\GroupPackage::create([
            'destination_id' => $destination->id,
            'name' => 'Old Group Package',
            'slug' => 'old-group-package',
            'price' => 200,
        ]);

        $updateData = [
            'destination_id' => $destination->id,
            'name' => 'Updated Group Package',
            'price' => 250,
        ];

        $response = $this->actingAs($this->admin)->put(
            route('admin.group-packages.update', $groupPackage->id),
            $updateData
        );

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('group_packages', ['id' => $groupPackage->id, 'name' => 'Updated Group Package']);
    }

    /** @test */
    public function admin_can_delete_a_group_package()
    {
        $groupPackage = \App\Models\GroupPackage::create([
            'name' => 'Delete Group Package',
            'slug' => 'delete-group-package',
            'price' => 100,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.group-packages.destroy', $groupPackage->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Group package deleted successfully']);
        $this->assertSoftDeleted('group_packages', ['id' => $groupPackage->id]);
    }

    /** @test */
    public function admin_can_view_group_package_edit_page()
    {
        $groupPackage = \App\Models\GroupPackage::create([
            'name' => 'Edit Group Package',
            'slug' => 'edit-group-package',
            'price' => 150,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.group-packages.edit', $groupPackage->id));
        $response->assertStatus(200);
    }
}
