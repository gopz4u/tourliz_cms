<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\NotificationCampaign;
use App\Services\NotificationCampaignSender;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NotificationCampaignController extends Controller
{
    public function index()
    {
        $campaigns = NotificationCampaign::with('creator')
            ->latest('sent_at')
            ->latest('id')
            ->paginate(20);

        return view('admin.notification-campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $admins = Admin::orderBy('name')->get(['id', 'name', 'email', 'role']);

        return view('admin.notification-campaigns.create', compact('admins'));
    }

    public function store(Request $request, NotificationCampaignSender $sender)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'message' => 'required|string|max:1000',
            'url' => 'nullable|string|max:2048',
            'recipient_scope' => 'required|in:all,selected',
            'recipient_ids' => 'required_if:recipient_scope,selected|array',
            'recipient_ids.*' => 'integer|exists:admins,id',
        ]);

        $url = $this->normaliseInternalUrl($validated['url'] ?? null);
        $recipients = $validated['recipient_scope'] === 'all'
            ? Admin::orderBy('name')->get()
            : Admin::whereIn('id', collect($validated['recipient_ids'] ?? [])->unique())->orderBy('name')->get();

        if ($recipients->isEmpty()) {
            throw ValidationException::withMessages([
                'recipient_ids' => 'Select at least one staff member.',
            ]);
        }

        $campaign = DB::transaction(function () use ($validated, $url) {
            return NotificationCampaign::create([
                'user_id' => auth()->id(),
                'title' => trim($validated['title']),
                'message' => trim($validated['message']),
                'url' => $url,
                'recipient_scope' => $validated['recipient_scope'],
                'status' => 'sending',
            ]);
        });

        $result = $sender->send($campaign, $recipients);

        $message = "Notification sent to {$result['in_app']} staff member(s).";
        if ($result['pushes'] > 0) {
            $message .= " {$result['pushes']} device push notification(s) delivered.";
        } else {
            $message .= ' No registered device accepted a push notification.';
        }

        return redirect()->route('admin.notification-campaigns.show', $campaign)
            ->with('success', $message);
    }

    public function show(NotificationCampaign $notificationCampaign)
    {
        $notificationCampaign->load('creator');

        return view('admin.notification-campaigns.show', compact('notificationCampaign'));
    }

    /**
     * Keep links in the Tourliz origin. This matches the notification bell
     * and avoids a staff broadcast being used as an external redirect.
     */
    private function normaliseInternalUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (Str::startsWith($url, '/') && !Str::startsWith($url, '//')) {
            return $url;
        }

        $parts = parse_url($url);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $isSameOrigin = is_array($parts)
            && in_array($parts['scheme'] ?? null, ['http', 'https'], true)
            && ($parts['host'] ?? null) === $appHost;

        if ($isSameOrigin) {
            return $url;
        }

        throw ValidationException::withMessages([
            'url' => 'Use a Tourliz link beginning with / (for example /admin/follow-ups).',
        ]);
    }
}
