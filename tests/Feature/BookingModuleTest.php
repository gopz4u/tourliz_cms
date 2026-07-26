<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\Package;

class BookingModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Super Admin user
        $this->admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);
    }

    /** @test */
    public function admin_can_view_bookings_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.bookings.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_create_booking_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.bookings.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_a_booking()
    {
        $destination = \App\Models\Destination::create([
            'name' => 'Test Destination',
            'slug' => 'test-destination',
        ]);

        $package = Package::create([
            'destination_id' => $destination->id,
            'name' => 'Test Package',
            'slug' => 'test-package',
            'price' => 500,
        ]);

        $bookingData = [
            'package_id' => $package->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'travel_date' => now()->addDays(10)->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'price' => 500,
            'total_amount' => 500,
            'status' => 'pending',
            'followup_status' => 'leads',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.bookings.store'), $bookingData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertDatabaseHas('bookings', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function admin_can_view_a_booking_details()
    {
        $booking = Booking::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
            'travel_date' => now()->addDays(5)->format('Y-m-d'),
            'adults' => 1,
            'price' => 750,
            'total_amount' => 750,
            'status' => 'confirmed',
            'followup_status' => 'leads',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.bookings.show', $booking->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_booking_status()
    {
        $booking = Booking::create([
            'name' => 'Mark Wilson',
            'email' => 'mark@example.com',
            'phone' => '1122334455',
            'travel_date' => now()->addDays(3)->format('Y-m-d'),
            'adults' => 2,
            'price' => 300,
            'total_amount' => 300,
            'status' => 'pending',
            'followup_status' => 'leads',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.bookings.updateStatus', $booking->id), [
            'status' => 'confirmed',
            'followup_status' => 'converted',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'followup_status' => 'converted',
        ]);
    }

    /** @test */
    public function admin_can_soft_delete_and_restore_a_booking()
    {
        $booking = Booking::create([
            'name' => 'Delete Test User',
            'email' => 'delete@example.com',
            'phone' => '9998887776',
            'travel_date' => now()->addDays(4)->format('Y-m-d'),
            'adults' => 2,
            'price' => 400,
            'total_amount' => 400,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.bookings.destroy', $booking->id));
        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertSoftDeleted('bookings', ['id' => $booking->id]);

        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.bookings.restore', $booking->id));
        $restoreResponse->assertRedirect();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'deleted_at' => null]);
    }
}
