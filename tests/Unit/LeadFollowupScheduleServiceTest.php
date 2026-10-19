<?php

namespace Tests\Unit;

use App\Services\LeadFollowupScheduleService;
use Carbon\Carbon;
use Tests\TestCase;

class LeadFollowupScheduleServiceTest extends TestCase
{
    /** @test */
    public function first_reminder_is_at_1230_india_time_after_at_least_24_hours()
    {
        $schedule = new LeadFollowupScheduleService();
        $createdAt = Carbon::parse('2026-10-18 14:00:00', 'Asia/Kolkata');

        $dueAt = $schedule->initialDueAt($createdAt);

        $this->assertSame('2026-10-20 12:30:00', $dueAt->copy()->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function repeat_reminder_is_two_calendar_days_later_at_1230_india_time()
    {
        $schedule = new LeadFollowupScheduleService();
        $afterFollowup = Carbon::parse('2026-10-18 16:00:00', 'Asia/Kolkata');

        $dueAt = $schedule->nextDueAt($afterFollowup);

        $this->assertSame('2026-10-20 12:30:00', $dueAt->copy()->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'));
    }
}
