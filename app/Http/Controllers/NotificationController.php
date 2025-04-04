<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function unreadCount() {
        $user = Auth::user();
        $unreadCount = $user->unreadNotifications()->count();

        return $this->success([
            'unreadCount' => $unreadCount,
        ], "Unread notifications count retrieved successfully.");
    }

    public function recentNotifications() {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'message' => $notif->data['message'],
                    'sender_id' => $notif->data['sender_id'] ?? null,
                    'type' => $notif->data['type'] ?? 'unknown',
                    'timestamp' => $notif->created_at->toDateTimeString(),
                    'read_at' => $notif->read_at ? $notif->read_at->toDateTimeString() : null,
                ];
            });

        return $this->success($notifications, 'Recent notifications retrieved');
    }

    public function markAsRead(Request $request){
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return $this->success(null, 'Notifications marked as read');
    }

    public function deleteNotification($notificationId) {
        $user = Auth::user();
        $user->unreadNotifications()->where('id', $notificationId)->delete();
        return $this->success(null,'Notification deleted successfully');
    }
}
