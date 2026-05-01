<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class VendorDashboardService
{
    public function getSummary(User $user): array
    {
        $vendor = $user->vendor;

        $bookings = Booking::query()
            ->where('user_id', $user->id)
            ->with(['couple.couple'])
            ->orderBy('booking_date')
            ->limit(8)
            ->get();

        $services = Service::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $bookingDates = $this->formatBookingDates($bookings);

        return [
            'vendor' => $vendor,
            'bookings' => $bookings,
            'services' => $services,
            'dashboardData' => [
                'bookings_total' => $bookings->count(),
                'bookings_confirmed' => $bookings->where('status', true)->count(),
                'bookings_pending' => $bookings->where('status', false)->count(),
                'services_total' => $services->count(),
                'booking_dates' => $bookingDates,
            ],
        ];
    }

    private function formatBookingDates(Collection $bookings): array
    {
        return $bookings
            ->filter(fn (Booking $booking) => $booking->booking_date !== null)
            ->map(fn (Booking $booking): string => Carbon::parse($booking->booking_date)->format('Y-m-d'))
            ->values()
            ->all();
    }
}
