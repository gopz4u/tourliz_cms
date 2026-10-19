<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        if (!config('services.webpush.public_key')) {
            return response()->json(['message' => 'Push notifications are not configured yet.'], 422);
        }

        $data = $request->validate([
            'endpoint' => 'required|string|max:2000',
            'keys.p256dh' => 'required|string|max:255',
            'keys.auth' => 'required|string|max:255',
            'contentEncoding' => 'nullable|in:aesgcm,aes128gcm',
        ]);

        PushSubscription::updateOrCreate([
            'endpoint_hash' => hash('sha256', $data['endpoint']),
        ], [
            'admin_id' => $request->user()->id,
            'endpoint' => $data['endpoint'],
            'public_key' => $data['keys']['p256dh'],
            'auth_token' => $data['keys']['auth'],
            'content_encoding' => $data['contentEncoding'] ?? 'aes128gcm',
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string|max:2000',
        ]);

        PushSubscription::where('admin_id', $request->user()->id)
            ->where('endpoint_hash', hash('sha256', $data['endpoint']))
            ->delete();

        return response()->json(['success' => true]);
    }
}
