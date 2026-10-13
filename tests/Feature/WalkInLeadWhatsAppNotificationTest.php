<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\B2CItinerary;
use App\Models\Country;
use App\Models\Destination;
use App\Models\User;
use App\Services\LeadWhatsAppNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class WalkInLeadWhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_walk_in_lead_triggers_the_staff_notifier()
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'notify@test.com', 'password' => bcrypt('password'), 'role' => 'super_admin']);
        $user = User::create(['name' => 'Staff User', 'email' => 'staff-notify@test.com', 'password' => bcrypt('password')]);
        $country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        Destination::create(['name' => 'Kuala Lumpur', 'slug' => 'kuala-lumpur']);
        $notifier = Mockery::mock(LeadWhatsAppNotifier::class);
        $notifier->shouldReceive('notifyNewLead')->once()->with(Mockery::type(B2CItinerary::class))->andReturn(1);
        $this->app->instance(LeadWhatsAppNotifier::class, $notifier);

        $response = $this->actingAs($admin)->post(route('admin.b2c-itineraries.store'), [
            'user_id' => $user->id, 'destination_id' => $country->id, 'title' => 'Malaysia Walk-in', 'client_name' => 'New Customer',
            'duration_days' => 4, 'phone' => '+60123456789', 'lead_source' => 'walk_in',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('b2c_itineraries', ['client_name' => 'New Customer']);
    }

    /** @test */
    public function reminder_command_sends_only_for_leads_due_after_seven_days()
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'reminder@test.com', 'password' => bcrypt('password'), 'role' => 'super_admin']);
        $user = User::create(['name' => 'Reminder User', 'email' => 'staff-reminder@test.com', 'password' => bcrypt('password')]);
        $country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        Destination::create(['name' => 'Kuala Lumpur', 'slug' => 'kuala-lumpur']);
        $due = B2CItinerary::create([
            'user_id' => $user->id, 'destination_id' => $country->id, 'client_name' => 'Due Customer',
            'title' => 'Due Lead', 'duration_days' => 3, 'itinerary' => [], 'status' => 'draft', 'followup_status' => 'leads',
        ]);
        $due->forceFill(['created_at' => now()->subDays(8), 'updated_at' => now()->subDays(8)])->save();
        $closed = B2CItinerary::create([
            'user_id' => $user->id, 'destination_id' => $country->id, 'client_name' => 'Closed Customer',
            'title' => 'Closed Lead', 'duration_days' => 3, 'itinerary' => [], 'status' => 'draft', 'followup_status' => 'converted',
        ]);
        $closed->forceFill(['created_at' => now()->subDays(8), 'updated_at' => now()->subDays(8)])->save();

        $notifier = Mockery::mock(LeadWhatsAppNotifier::class);
        $notifier->shouldReceive('notifyFollowupDue')->once()->with(Mockery::on(fn ($lead) => $lead->id === $due->id))->andReturn(1);
        $this->app->instance(LeadWhatsAppNotifier::class, $notifier);

        $this->artisan('leads:send-followup-reminders')->assertExitCode(0);
        $this->assertNotNull($due->fresh()->last_followup_whatsapp_at);
        $this->assertNull($closed->fresh()->last_followup_whatsapp_at);
    }
}
