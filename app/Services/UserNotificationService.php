<?php

namespace App\Services;

use App\Mail\UserAlertMail;
use App\Models\Booking;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Vendor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserNotificationService
{
    public function __construct(private readonly PushNotificationService $pushNotificationService) {}

    public function notifyRegistrationSuccess(User $user): void
    {
        $this->send(
            $user,
            'Welcome to WebPlan',
            'Your account has been created successfully. You can now start planning your wedding journey.'
        );
    }

    public function notifyVendorPendingApproval(User $vendorUser): void
    {
        $this->send(
            $vendorUser,
            'Vendor Registration Received',
            'Your vendor account is waiting for admin approval. We will notify you as soon as it is approved.'
        );
    }

    public function notifyVendorApproved(User $vendorUser): void
    {
        $this->send(
            $vendorUser,
            'Vendor Account Approved',
            'Great news. Your vendor account has been approved by admin and is now active.'
        );
    }

    public function notifyAdminsVendorDocumentationUpdated(User $vendorUser, Vendor $vendor): void
    {
        $admins = User::query()->admins()->get();

        foreach ($admins as $admin) {
            $this->send(
                $admin,
                'Vendor Documentation Updated',
                'Vendor '.$vendor->business_name.' has updated business documentation and needs review.'
            );
        }
    }

    public function notifyTaskOverdue(User $coupleUser, int $overdueTaskCount): void
    {
        $this->send(
            $coupleUser,
            'Overdue Task Reminder',
            'You have '.$overdueTaskCount.' overdue task(s). Please review your task list to stay on track.'
        );
    }

    public function notifyBudgetOverLimit(User $coupleUser, float $spent, float $limit): void
    {
        $this->send(
            $coupleUser,
            'Budget Limit Exceeded',
            'Your total spending has exceeded your budget limit. Spent: RM '.number_format($spent, 2).' / Limit: RM '.number_format($limit, 2).'.'
        );
    }

    public function notifyCoupleBookingUpdate(User $coupleUser, Booking $booking, string $action): void
    {
        $this->sendBookingNotification($coupleUser, $booking, 'Booking Update', $action);
    }

    public function notifyCoupleBookingCreated(User $coupleUser, Booking $booking): void
    {
        $this->sendBookingNotification($coupleUser, $booking, 'Booking Created', 'created');
    }

    public function notifyCoupleBookingDeleted(User $coupleUser, Booking $booking): void
    {
        $this->sendBookingNotification($coupleUser, $booking, 'Booking Deleted', 'deleted');
    }

    private function sendBookingNotification(User $coupleUser, Booking $booking, string $title, string $action): void
    {
        $bookingDate = $booking->booking_date ? Carbon::parse((string) $booking->booking_date)->format('d M Y') : 'N/A';

        $this->send(
            $coupleUser,
            $title,
            'A vendor has '.$action.' your booking for '.$booking->type_service.' on '.$bookingDate.'.'
        );
    }

    private function send(object $user, string $title, string $message): void
    {
        UserNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);

        try {
            Mail::to($user->email)->send(new UserAlertMail($title, $message));
        } catch (\Throwable $exception) {
            Log::warning('Email notification send failed.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if (! empty($user->device_token)) {
            $this->pushNotificationService->send((string) $user->device_token, $title, $message, [
                'user_id' => (string) $user->id,
                'type' => 'general',
            ]);
        }
    }
}
