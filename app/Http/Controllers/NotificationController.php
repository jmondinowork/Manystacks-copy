<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Fetch all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'notifications' => $user->notifications,
            'unread_count' => $user->unreadNotifications->count()
        ]);
    }

    /**
     * Mark a specific notification or all notifications as read.
     */
    public function markAsRead(Request $request, $id = null)
    {
        $user = Auth::user();

        if ($id) {
            $notification = $user->notifications()->findOrFail($id);
            $notification->markAsRead();
        } else {
            $user->unreadNotifications->markAsRead();
        }

        $user->refresh();

        return response()->json([
            'success' => true,
            'notifications' => $user->notifications,
            'unread_count' => $user->unreadNotifications->count()
        ]);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        $user->refresh();

        return response()->json([
            'success' => true,
            'notifications' => $user->notifications,
            'unread_count' => $user->unreadNotifications->count()
        ]);
    }
}
