<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unread(Request $request)
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data ?: [];

                return [
                    'id' => $notification->id,
                    'kind' => $data['kind'] ?? 'general',
                    'title' => $data['title'] ?? 'Tourliz notification',
                    'message' => $data['message'] ?? '',
                    'url' => $data['url'] ?? route('admin.dashboard'),
                    'created_at' => optional($notification->created_at)->diffForHumans(),
                ];
            })
            ->values();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->count(),
        ]);
    }

    public function markAsRead(Request $request, $notification)
    {
        $item = $request->user()->unreadNotifications()->findOrFail($notification);
        $item->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
