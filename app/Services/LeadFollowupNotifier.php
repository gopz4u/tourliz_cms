<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\B2CItinerary;
use App\Notifications\B2CFollowupDueNotification;

class LeadFollowupNotifier
{
    private $webPush;

    public function __construct(WebPushService $webPush)
    {
        $this->webPush = $webPush;
    }

    /**
     * Send an in-app reminder to the owner and to super-admins for visibility.
     * When no owner is assigned, all active back-office users receive the alert.
     */
    public function notifyFollowupDue(B2CItinerary $lead, $dueDate): int
    {
        $recipientIds = collect();

        if ($lead->user_id && Admin::whereKey($lead->user_id)->exists()) {
            $recipientIds->push((int) $lead->user_id);
        }

        $superAdminIds = Admin::query()
            ->where(function ($query) {
                $query->whereIn('role', [Admin::ROLE_SUPER_ADMIN, 'admin'])
                    ->orWhereNull('role');
            })
            ->pluck('id');

        $recipientIds = $recipientIds->merge($superAdminIds)->unique()->values();

        if ($recipientIds->isEmpty()) {
            $recipientIds = Admin::query()
                ->whereIn('role', [Admin::ROLE_SUPER_ADMIN, 'admin', Admin::ROLE_EMPLOYEE])
                ->pluck('id');
        }

        $notification = new B2CFollowupDueNotification($lead, $dueDate);
        $recipients = Admin::whereIn('id', $recipientIds)->get();

        foreach ($recipients as $recipient) {
            $recipient->notify($notification);
        }

        $this->webPush->sendToAdmins($recipients->pluck('id'), [
            'title' => 'Follow-up due',
            'body' => ($lead->client_name ?: 'Customer') . ' needs a follow-up for ' . ($lead->title ?: 'their trip') . '.',
            'url' => route('admin.follow-ups.index'),
            'tag' => 'tourliz-followup-' . $lead->id,
        ]);

        return $recipients->count();
    }
}
