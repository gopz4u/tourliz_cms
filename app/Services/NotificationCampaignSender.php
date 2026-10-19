<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\NotificationCampaign;
use App\Notifications\CustomCampaignNotification;
use Illuminate\Support\Collection;

class NotificationCampaignSender
{
    private $webPush;

    public function __construct(WebPushService $webPush)
    {
        $this->webPush = $webPush;
    }

    /**
     * Deliver a custom staff announcement immediately through both channels.
     * In-app notifications are always created; device delivery is attempted
     * only for staff members who have enabled PWA alerts on a device.
     */
    public function send(NotificationCampaign $campaign, Collection $recipients): array
    {
        $recipients = $recipients->unique('id')->values();
        $notification = new CustomCampaignNotification($campaign);

        foreach ($recipients as $recipient) {
            $recipient->notify($notification);
        }

        $devicePushes = $this->webPush->sendToAdmins($recipients->pluck('id'), [
            'title' => $campaign->title,
            'body' => $campaign->message,
            'url' => $campaign->url ?: route('admin.dashboard'),
            'tag' => 'tourliz-campaign-' . $campaign->id,
        ]);

        $campaign->forceFill([
            'recipient_count' => $recipients->count(),
            'in_app_count' => $recipients->count(),
            'push_count' => $devicePushes,
            'status' => 'sent',
            'sent_at' => now(),
        ])->save();

        return [
            'recipients' => $recipients->count(),
            'in_app' => $recipients->count(),
            'pushes' => $devicePushes,
        ];
    }
}
