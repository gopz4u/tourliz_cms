<?php

namespace App\Notifications;

use App\Models\B2CItinerary;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class B2CFollowupDueNotification extends Notification
{
    use Queueable;

    protected $lead;
    protected $dueDate;

    public function __construct(B2CItinerary $lead, $dueDate)
    {
        $this->lead = $lead;
        $this->dueDate = $dueDate;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $customer = $this->lead->client_name ?: 'Customer';
        $trip = $this->lead->title ?: 'Untitled trip';

        return [
            'kind' => 'b2c_followup_due',
            'title' => 'Follow-up due',
            'message' => $customer . ' needs a follow-up for ' . $trip . '.',
            'url' => route('admin.b2c-itineraries.edit', $this->lead->id),
            'lead_id' => $this->lead->id,
            'quote_id' => $this->lead->quote_id,
            'customer' => $customer,
            'due_date' => $this->dueDate->toDateString(),
        ];
    }
}
