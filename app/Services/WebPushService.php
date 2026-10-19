<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public function isConfigured(): bool
    {
        return (bool) config('services.webpush.public_key')
            && (bool) config('services.webpush.private_key')
            && (bool) config('services.webpush.subject');
    }

    public function sendToAdmins($adminIds, array $payload): int
    {
        if (!$this->isConfigured()) {
            return 0;
        }

        $subscriptions = PushSubscription::whereIn('admin_id', collect($adminIds)->filter()->unique()->values())->get();
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('services.webpush.subject'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ]);
        $json = json_encode($payload);
        $sent = 0;

        foreach ($subscriptions as $subscription) {
            try {
                $report = $webPush->sendOneNotification(Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding ?: 'aes128gcm',
                ]), $json, ['TTL' => 3600, 'urgency' => 'high']);

                if ($report->isSuccess()) {
                    $subscription->forceFill(['last_used_at' => now()])->save();
                    $sent++;
                } elseif ($report->isSubscriptionExpired()) {
                    $subscription->delete();
                } else {
                    Log::warning('Web push notification was not delivered.', [
                        'admin_id' => $subscription->admin_id,
                        'reason' => $report->getReason(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Web push notification failed.', [
                    'admin_id' => $subscription->admin_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sent;
    }
}
