<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BookingService
{
    public function __construct(private readonly UserNotificationService $userNotificationService) {}

    public function paginateForVendor(User $vendorUser, int $perPage = 8): LengthAwarePaginator
    {
        return Booking::query()
            ->where('user_id', $vendorUser->id)
            ->with(['couple.couple'])
            ->latest('booking_date')
            ->latest('id')
            ->paginate($perPage);
    }

    public function summaryForVendor(User $vendorUser): array
    {
        $bookings = Booking::query()
            ->where('user_id', $vendorUser->id)
            ->with(['couple.couple'])
            ->latest('booking_date')
            ->latest('id')
            ->get();

        return [
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $bookings->where('status', true)->count(),
            'pending_bookings' => $bookings->where('status', false)->count(),
            'upcoming_bookings' => $bookings
                ->filter(fn (Booking $booking): bool => $booking->booking_date !== null)
                ->filter(fn (Booking $booking): bool => Carbon::parse((string) $booking->booking_date)->greaterThanOrEqualTo(now()))
                ->count(),
            'latest_bookings' => $bookings->take(4)->map(fn (Booking $booking): array => [
                'id' => $booking->id,
                'type_service' => $booking->type_service,
                'booking_date' => $booking->booking_date ? Carbon::parse((string) $booking->booking_date)->format('d M Y') : null,
                'status' => $booking->status,
            ])->values(),
            'booking_dates' => $this->formatBookingDates($bookings),
        ];
    }

    public function create(User $vendorUser, array $data): Booking
    {
        $booking = Booking::query()->create($this->bookingPayload($vendorUser, $data));

        $this->notifyCouple($booking, 'created');

        return $booking->load(['couple.couple']);
    }

    public function update(Booking $booking, array $data): Booking
    {
        $booking->update($this->bookingPayload($booking->vendor, $data, $booking));

        $booking = $booking->refresh()->load(['couple.couple']);
        $this->notifyCouple($booking, 'updated');

        return $booking;
    }

    public function delete(Booking $booking): void
    {
        $booking->loadMissing(['couple.couple']);
        $this->notifyCouple($booking, 'deleted');
        $booking->delete();
    }

    private function bookingPayload(User $vendorUser, array $data, ?Booking $booking = null): array
    {
        return [
            'user_id' => $vendorUser->id,
            'couple_id' => $data['couple_id'],
            'type_service' => $data['type_service'],
            'booking_date' => $data['booking_date'],
            'status' => array_key_exists('status', $data) ? (bool) $data['status'] : ($booking?->status ?? true),
            'notes' => $data['notes'] ?? null,
        ];
    }

    private function notifyCouple(Booking $booking, string $action): void
    {
        $coupleUser = $booking->couple;

        if (! $coupleUser instanceof User) {
            return;
        }

        match ($action) {
            'created' => $this->userNotificationService->notifyCoupleBookingCreated($coupleUser, $booking),
            'updated' => $this->userNotificationService->notifyCoupleBookingUpdate($coupleUser, $booking, $action),
            'deleted' => $this->userNotificationService->notifyCoupleBookingDeleted($coupleUser, $booking),
            default => $this->userNotificationService->notifyCoupleBookingUpdate($coupleUser, $booking, $action),
        };
    }

    private function formatBookingDates(Collection $bookings): array
    {
        return $bookings
            ->filter(fn (Booking $booking): bool => $booking->booking_date !== null)
            ->map(fn (Booking $booking): string => Carbon::parse((string) $booking->booking_date)->format('Y-m-d'))
            ->values()
            ->all();
    }
}
