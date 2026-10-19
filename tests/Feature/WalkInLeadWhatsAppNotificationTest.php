<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\B2CItinerary;
use App\Models\Country;
use App\Models\Destination;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalkInLeadWhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_walk_in_lead_schedules_its_first_push_followup()
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'notify@test.com', 'password' => bcrypt('password'), 'role' => 'super_admin']);
        $user = User::create(['name' => 'Staff User', 'email' => 'staff-notify@test.com', 'password' => bcrypt('password')]);
        $country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        Destination::create(['name' => 'Kuala Lumpur', 'slug' => 'kuala-lumpur']);
        $response = $this->actingAs($admin)->post(route('admin.b2c-itineraries.store'), [
            'user_id' => $user->id, 'destination_id' => $country->id, 'title' => 'Malaysia Walk-in', 'client_name' => 'New Customer',
            'duration_days' => 4, 'phone' => '+60123456789', 'lead_source' => 'walk_in',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('b2c_itineraries', ['client_name' => 'New Customer']);
        $this->assertNotNull(B2CItinerary::where('client_name', 'New Customer')->first()->next_followup_at);
    }

    /** @test */
    public function reminder_command_sends_only_for_active_leads_due_at_their_scheduled_time()
    {
        Carbon::setTestNow(Carbon::parse('2026-10-18 07:05:00', 'UTC'));
        $admin = Admin::create(['name' => 'Admin', 'email' => 'reminder@test.com', 'password' => bcrypt('password'), 'role' => 'super_admin']);
        $user = User::create(['name' => 'Reminder User', 'email' => 'staff-reminder@test.com', 'password' => bcrypt('password')]);
        $country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        Destination::create(['name' => 'Kuala Lumpur', 'slug' => 'kuala-lumpur']);
        $due = B2CItinerary::create([
            'user_id' => $user->id, 'destination_id' => $country->id, 'client_name' => 'Due Customer',
            'title' => 'Due Lead', 'duration_days' => 3, 'itinerary' => [], 'status' => 'draft', 'followup_status' => 'leads',
        ]);
        $due->forceFill([
            'next_followup_at' => now()->subMinute(),
            'next_followup_date' => now()->toDateString(),
        ])->save();
        $closed = B2CItinerary::create([
            'user_id' => $user->id, 'destination_id' => $country->id, 'client_name' => 'Closed Customer',
            'title' => 'Closed Lead', 'duration_days' => 3, 'itinerary' => [], 'status' => 'draft', 'followup_status' => 'converted',
        ]);
        $closed->forceFill([
            'next_followup_at' => now()->subMinute(),
            'next_followup_date' => now()->toDateString(),
        ])->save();

        $this->artisan('leads:send-followup-reminders')->assertExitCode(0);
        $this->assertNotNull($due->fresh()->last_followup_notification_at);
        $this->assertNull($closed->fresh()->last_followup_notification_at);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
