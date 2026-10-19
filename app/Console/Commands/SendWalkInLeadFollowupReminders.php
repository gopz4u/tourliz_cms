<?php

namespace App\Console\Commands;

use App\Models\B2CItinerary;
use App\Services\LeadFollowupNotifier;
use App\Services\LeadFollowupScheduleService;
use Illuminate\Console\Command;

class SendWalkInLeadFollowupReminders extends Command
{
    protected $signature = 'leads:send-followup-reminders';
    protected $description = 'Send due 12:30 PM India-time follow-up reminders for active walk-in leads';

    public function handle(
        LeadFollowupNotifier $inAppNotifier,
        LeadFollowupScheduleService $schedule
    ): int
    {
        $inAppAlerts = 0;
        $processed = 0;

        B2CItinerary::orderBy('id')
            ->chunkById(100, function ($leads) use ($inAppNotifier, $schedule, &$inAppAlerts, &$processed) {
                foreach ($leads as $lead) {
                    if ($schedule->isTerminal($lead)) {
                        continue;
                    }

                    $schedule->ensureScheduled($lead);
                    if (!$lead->next_followup_at || ($lead->followup_paused_until && $lead->followup_paused_until->isFuture())) {
                        $lead->save();
                        continue;
                    }

                    if ($lead->next_followup_at->isFuture()) {
                        $lead->save();
                        continue;
                    }

                    $dueAt = $lead->next_followup_at->copy();
                    $inAppAlerts += $inAppNotifier->notifyFollowupDue($lead, $dueAt);
                    $schedule->scheduleNext($lead);
                    $lead->forceFill([
                        'last_followup_notification_at' => now(),
                        'last_followup_reminded_at' => now(),
                        'followup_reminder_count' => ((int) $lead->followup_reminder_count) + 1,
                    ])->save();
                    $processed++;
                }
            });

        $this->info("Follow-up push cycle completed for {$processed} lead(s). In-app alerts created: {$inAppAlerts}.");
        return self::SUCCESS;
    }
}
