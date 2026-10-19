<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\NotificationCampaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCampaignTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function super_admin_can_send_a_custom_notification_to_selected_staff()
    {
        $owner = Admin::create([
            'name' => 'Owner', 'email' => 'owner@example.test', 'password' => bcrypt('password'), 'role' => 'super_admin',
        ]);
        $staff = Admin::create([
            'name' => 'Sales Staff', 'email' => 'staff@example.test', 'password' => bcrypt('password'), 'role' => 'employee',
        ]);

        $response = $this->actingAs($owner)->post(route('admin.notification-campaigns.store'), [
            'title' => 'Meeting reminder',
            'message' => 'Please join the sales meeting at 10 AM.',
            'url' => '/admin/follow-ups',
            'recipient_scope' => 'selected',
            'recipient_ids' => [$staff->id],
        ]);

        $campaign = NotificationCampaign::firstOrFail();
        $response->assertRedirect(route('admin.notification-campaigns.show', $campaign));
        $this->assertSame(1, $campaign->recipient_count);
        $this->assertSame(1, $campaign->in_app_count);
        $this->assertSame(0, $campaign->push_count);
        $this->assertSame('sent', $campaign->status);
        $this->assertSame(1, $staff->fresh()->unreadNotifications()->count());
        $this->assertSame(0, $owner->fresh()->unreadNotifications()->count());

        $notification = $staff->fresh()->unreadNotifications()->first();
        $this->assertSame('custom_campaign', $notification->data['kind']);
        $this->assertSame('/admin/follow-ups', $notification->data['url']);
    }

    /** @test */
    public function employee_cannot_access_notification_campaigns()
    {
        $employee = Admin::create([
            'name' => 'Sales Staff', 'email' => 'staff@example.test', 'password' => bcrypt('password'), 'role' => 'employee',
        ]);

        $this->actingAs($employee)
            ->get(route('admin.notification-campaigns.index'))
            ->assertForbidden();
    }

    /** @test */
    public function notification_link_must_stay_within_tourliz()
    {
        $owner = Admin::create([
            'name' => 'Owner', 'email' => 'owner@example.test', 'password' => bcrypt('password'), 'role' => 'super_admin',
        ]);
        $staff = Admin::create([
            'name' => 'Sales Staff', 'email' => 'staff@example.test', 'password' => bcrypt('password'), 'role' => 'employee',
        ]);

        $this->actingAs($owner)->from(route('admin.notification-campaigns.create'))
            ->post(route('admin.notification-campaigns.store'), [
                'title' => 'External link',
                'message' => 'This should not be sent.',
                'url' => 'https://example.test/not-safe',
                'recipient_scope' => 'selected',
                'recipient_ids' => [$staff->id],
            ])
            ->assertRedirect(route('admin.notification-campaigns.create'))
            ->assertSessionHasErrors('url');

        $this->assertSame(0, NotificationCampaign::count());
        $this->assertSame(0, $staff->fresh()->unreadNotifications()->count());
    }
}
