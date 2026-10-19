<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\B2CItinerary;
use App\Models\Country;
use App\Models\Destination;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowupQueueTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function completing_a_followup_moves_the_next_alert_to_1230_india_time_two_days_later()
    {
        Carbon::setTestNow(Carbon::parse('2026-10-18 07:05:00', 'UTC'));
        $admin = Admin::create(['name' => 'Owner', 'email' => 'queue-owner@example.test', 'password' => bcrypt('password'), 'role' => 'super_admin']);
        $country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        Destination::create(['name' => 'Kuala Lumpur', 'slug' => 'kuala-lumpur']);
        $lead = $this->activeLead($country->id);

        $this->actingAs($admin)
            ->post(route('admin.follow-ups.complete', $lead->id), ['followup_status' => 'waiting'])
            ->assertRedirect();

        $fresh = $lead->fresh();
        $this->assertSame('waiting', $fresh->followup_status);
        $this->assertSame('2026-10-20 12:30:00', $fresh->next_followup_at->copy()->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function confirming_or_cancelling_a_lead_stops_future_reminders()
    {
        $admin = Admin::create(['name' => 'Owner', 'email' => 'queue-close@example.test', 'password' => bcrypt('password'), 'role' => 'super_admin']);
        $country = Country::create(['name' => 'Malaysia', 'code' => 'MY']);
        Destination::create(['name' => 'Kuala Lumpur', 'slug' => 'kuala-lumpur']);
        $lead = $this->activeLead($country->id);

        $this->actingAs($admin)
            ->post(route('admin.follow-ups.close', $lead->id), ['status' => 'confirmed'])
            ->assertRedirect();

        $fresh = $lead->fresh();
        $this->assertSame('confirmed', $fresh->followup_status);
        $this->assertNull($fresh->next_followup_at);
        $this->assertNull($fresh->next_followup_date);
    }

    private function activeLead(int $countryId): B2CItinerary
    {
        return B2CItinerary::create([
            'destination_id' => $countryId,
            'client_name' => 'Queue Customer',
            'title' => 'Queue Trip',
            'duration_days' => 3,
            'itinerary' => [],
            'status' => 'draft',
            'followup_status' => 'leads',
            'next_followup_date' => now()->toDateString(),
            'next_followup_at' => now()->subMinute(),
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
