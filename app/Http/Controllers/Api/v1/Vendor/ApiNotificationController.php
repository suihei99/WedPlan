<?php

namespace App\Http\Controllers\Api\v1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserNotificationResource;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ApiNotificationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $notifications = $user->notifications()
            ->latest()
            ->paginate(15);

        return UserNotificationResource::collection($notifications);
    }

    public function show(UserNotification $notification): UserNotificationResource|JsonResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($notification->user_id === $user->id, 403);

        $notification->update(['is_read' => true]);

        return new UserNotificationResource($notification);
    }

    public function markAsRead(UserNotification $notification): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($notification->user_id === $user->id, 403);

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    public function destroy(UserNotification $notification): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($notification->user_id === $user->id, 403);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.',
        ]);
    }
}
