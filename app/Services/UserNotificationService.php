<?php

namespace App\Services;

use App\Mail\UserAlertMail;
use App\Models\Booking;
use App\Models\BudgetCategory;
use App\Models\Guest;
use App\Models\Task;
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

    public function notifyAdminsVendorPendingApproval(User $vendorUser, Vendor $vendor): void
    {
        $admins = User::query()->admins()->get();

        foreach ($admins as $admin) {
            $this->send(
                $admin,
                'New Vendor Registration',
                'Vendor '.$vendor->business_name.' ('.$vendorUser->email.') has registered and is waiting for approval.'
            );
        }
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

    public function notifyTaskDueDateSet(User $coupleUser, Task $task): void
    {
        $deadline = $this->formatDeadline($task->deadline);

        $this->send(
            $coupleUser,
            'Task Due Date Set: '.$task->task_name,
            'The due date for '.$task->task_name.' has been set to '.$deadline.'.'
        );
    }

    public function notifyTaskDueDateUpdated(User $coupleUser, Task $task, ?Carbon $previousDeadline): void
    {
        $currentDeadline = $this->formatDeadline($task->deadline);

        if ($previousDeadline instanceof Carbon) {
            $previousDeadline = $previousDeadline->copy();
        }

        $previousText = $this->formatDeadline($previousDeadline);

        $this->send(
            $coupleUser,
            'Task Due Date Updated: '.$task->task_name,
            'The due date for '.$task->task_name.' has been updated from '.$previousText.' to '.$currentDeadline.'.'
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

    public function notifyBudgetCategoryOverLimit(User $coupleUser, BudgetCategory $category, float $spent, float $limit): void
    {
        $this->send(
            $coupleUser,
            'Budget Category Over Limit: '.$category->category_name,
            'Your '.$category->category_name.' category has exceeded its budget. Spent: RM '.number_format($spent, 2).' / Limit: RM '.number_format($limit, 2).'.'
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

    public function notifyGuestRsvp(User $coupleUser, Guest $guest, string $status): void
    {
        $statusText = match ($status) {
            Guest::RSVP_CONFIRMED => 'confirmed their attendance',
            Guest::RSVP_DECLINED => 'declined the invitation',
            default => 'updated their RSVP status',
        };

        $this->send(
            $coupleUser,
            'Guest RSVP Update: '.($guest->name ?? 'Guest'),
            ($guest->name ?? 'A guest').' has '.$statusText.'.'
        );
    }

    public function notifyGuestCheckedIn(User $coupleUser, Guest $guest): void
    {
        $this->send(
            $coupleUser,
            'Guest Checked In: '.($guest->name ?? 'Guest'),
            ($guest->name ?? 'A guest').' has checked in successfully.'
        );
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

    private function formatDeadline(Carbon|string|null $deadline): string
    {
        if ($deadline instanceof Carbon) {
            return $deadline->format('d M Y');
        }

        if (is_string($deadline) && trim($deadline) !== '') {
            return Carbon::parse($deadline)->format('d M Y');
        }

        return 'N/A';
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
