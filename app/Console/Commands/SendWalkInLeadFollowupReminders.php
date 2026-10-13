<?php

namespace App\Console\Commands;

use App\Models\B2CItinerary;
use App\Services\LeadWhatsAppNotifier;
use Illuminate\Console\Command;

class SendWalkInLeadFollowupReminders extends Command
{
    protected $signature = 'leads:send-followup-reminders';
    protected $description = 'Send seven-day WhatsApp follow-up reminders for active walk-in leads';

    public function handle(LeadWhatsAppNotifier $notifier): int
    {
        $cutoff = now()->subDays(7);
        $sentLeads = 0;

        B2CItinerary::whereNotIn('followup_status', ['converted', 'dead', 'confirmed', 'travelled'])
            ->where('created_at', '<=', $cutoff)
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_followup_whatsapp_at')->orWhere('last_followup_whatsapp_at', '<=', $cutoff);
            })
            ->orderBy('id')
            ->chunkById(100, function ($leads) use ($notifier, &$sentLeads) {
                foreach ($leads as $lead) {
                    if ($notifier->notifyFollowupDue($lead) > 0) {
                        $lead->forceFill(['last_followup_whatsapp_at' => now()])->save();
                        $sentLeads++;
                    }
                }
            });

        $this->info("Follow-up reminders sent for {$sentLeads} lead(s).");
        return self::SUCCESS;
    }
}
