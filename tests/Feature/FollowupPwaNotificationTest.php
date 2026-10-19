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

class FollowupPwaNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function due_followups_create_one_in_app_alert_for_the_assignee_and_super_admin()
    {
        Carbon::setTestNow(Carbon::parse('2026-10-18 07:05:00', 'UTC'));
        // The legacy b2c_itineraries foreign key still targets users, while
        // the CMS resolves the assigned staff member from admins. Matching
        // identifiers reproduce the production compatibility path.
        $legacyUser = User::create([
            'name' => 'Sales Staff', 'email' => 'legacy-staff@example.test', 'password' => bcrypt('password'),
        ]);
        $assignee = Admin::forceCreate([
            'id' => $legacyUser->id,
            'name' => 'Sales Staff', 'email' => 'staff@example.test', 'password' => bcrypt('password'), 'role' => 'employee',
        ]);
        $superAdmin = Admin::create([
            'name' => 'Owner', 'email' => 'owner@example.test', 'password' => bcrypt('password'), 'role' => 'super_admin',
        ]);
        $country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        Destination::create(['name' => 'Kuala Lumpur', 'slug' => 'kuala-lumpur']);
        $lead = B2CItinerary::create([
            'user_id' => $assignee->id,
            'destination_id' => $country->id,
            'client_name' => 'Asha Kumar',
            'title' => 'Langkawi Escape',
            'duration_days' => 3,
            'itinerary' => [],
            'status' => 'draft',
            'followup_status' => 'interested',
            'next_followup_date' => now()->toDateString(),
            'next_followup_at' => now()->subMinute(),
        ]);

        $this->artisan('leads:send-followup-reminders')->assertExitCode(0);

        $this->assertSame(1, $assignee->fresh()->unreadNotifications()->count());
        $this->assertSame(1, $superAdmin->fresh()->unreadNotifications()->count());
        $this->assertNotNull($lead->fresh()->last_followup_notification_at);

        $response = $this->actingAs($assignee)->getJson(route('admin.notifications.unread'));
        $response->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('notifications.0.kind', 'b2c_followup_due')
            ->assertJsonPath('notifications.0.title', 'Follow-up due');

        $notification = $assignee->fresh()->unreadNotifications()->first();
        $this->actingAs($assignee)
            ->postJson(route('admin.notifications.read', $notification->id))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame(0, $assignee->fresh()->unreadNotifications()->count());

        // A due lead is not re-alerted every scheduler run for the same date.
        $this->artisan('leads:send-followup-reminders')->assertExitCode(0);
        $this->assertSame(1, $superAdmin->fresh()->unreadNotifications()->count());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
