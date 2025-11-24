<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications (JSON for AJAX).
     */
    public function index(): JsonResponse
    {
        $admin = auth()->guard('admin')->user();

        $notifications = $admin->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = $admin->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Display the notifications page.
     */
    public function page(): View
    {
        $admin = auth()->guard('admin')->user();

        $notifications = $admin->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $admin = auth()->guard('admin')->user();

        $notification = $admin->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $admin = auth()->guard('admin')->user();

        $admin->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
        ]);
    }
}
