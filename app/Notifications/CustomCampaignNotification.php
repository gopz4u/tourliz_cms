<?php

namespace App\Notifications;

use App\Models\NotificationCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomCampaignNotification extends Notification
{
    use Queueable;

    protected $campaign;

    public function __construct(NotificationCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'kind' => 'custom_campaign',
            'campaign_id' => $this->campaign->id,
            'title' => $this->campaign->title,
            'message' => $this->campaign->message,
            'url' => $this->campaign->url ?: route('admin.dashboard'),
        ];
    }
}
