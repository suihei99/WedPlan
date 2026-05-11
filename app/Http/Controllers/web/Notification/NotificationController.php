<?php

namespace App\Http\Controllers\web\Notification;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $notifications = $user->notifications()
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $user->notifications()->count(),
            'unread' => $user->notifications()->where('is_read', false)->count(),
        ];

        // Return couple or vendor view based on role
        if ($user->isCouple()) {
            return view('couple.notification.index', [
                'notifications' => $notifications,
                'stats' => $stats,
            ]);
        }

        return view('vendor.notification.index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'vendor' => $user->vendor,
        ]);
    }

    public function show(UserNotification $notification): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($notification->user_id === $user->id, 403);

        $notification->update(['is_read' => true]);

        return view('vendor.notification.show', [
            'notification' => $notification,
            'vendor' => $user->vendor,
        ]);
    }

    public function markAsRead(UserNotification $notification): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($notification->user_id === $user->id, 403);

        $notification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAsReadAjax(UserNotification $notification)
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($notification->user_id === $user->id, 403);

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
    }

    public function destroy(UserNotification $notification): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($notification->user_id === $user->id, 403);

        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }
}
