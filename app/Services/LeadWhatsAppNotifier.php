<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\B2CItinerary;
use Illuminate\Support\Facades\Log;

class LeadWhatsAppNotifier
{
    private $whatsapp;

    public function __construct(TwilioWhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function notifyNewLead(B2CItinerary $lead): int
    {
        $lead->loadMissing(['destination', 'user']);
        $message = "🔔 *NEW WALK-IN LEAD*\n\n"
            . "*Customer:* {$lead->client_name}\n"
            . "*Phone:* " . ($lead->phone ?: 'Not provided') . "\n"
            . "*Trip:* {$lead->title}\n"
            . "*Destination:* " . ($lead->destination->name ?? 'Not specified') . "\n"
            . "*Travel Date:* " . ($lead->start_date ? $lead->start_date->format('d M Y') : 'Not confirmed') . "\n"
            . "*Assigned To:* " . ($lead->user->name ?? 'Unassigned') . "\n"
            . "*Lead ID:* {$lead->quote_id}\n\n"
            . "Please contact the customer and update the follow-up status.";

        return $this->sendToStaff($message, $lead->id, 'new_lead');
    }

    public function notifyFollowupDue(B2CItinerary $lead): int
    {
        $lead->loadMissing(['destination', 'user']);
        $age = $lead->created_at->diffInDays(now());
        $message = "⏰ *7-DAY FOLLOW-UP REMINDER*\n\n"
            . "*Customer:* {$lead->client_name}\n"
            . "*Phone:* " . ($lead->phone ?: 'Not provided') . "\n"
            . "*Trip:* {$lead->title}\n"
            . "*Destination:* " . ($lead->destination->name ?? 'Not specified') . "\n"
            . "*Current Stage:* " . ucwords(str_replace('_', ' ', $lead->followup_status ?: 'leads')) . "\n"
            . "*Lead Age:* {$age} days\n"
            . "*Assigned To:* " . ($lead->user->name ?? 'Unassigned') . "\n"
            . "*Lead ID:* {$lead->quote_id}\n\n"
            . "Please follow up today and update the lead status.";

        return $this->sendToStaff($message, $lead->id, 'followup');
    }

    private function sendToStaff(string $message, int $leadId, string $type): int
    {
        $recipients = Admin::whereIn('role', ['super_admin', 'admin', 'employee'])
            ->where('whatsapp_notifications', true)
            ->whereNotNull('whatsapp_number')
            ->where('whatsapp_number', '!=', '')
            ->get();

        $sent = 0;
        foreach ($recipients as $recipient) {
            if ($this->whatsapp->sendMessage($recipient->whatsapp_number, $message)) {
                $sent++;
            } else {
                Log::warning('Staff WhatsApp notification was not delivered.', ['lead_id' => $leadId, 'admin_id' => $recipient->id, 'type' => $type]);
            }
        }

        return $sent;
    }
}
