<?php

namespace App\Http\Controllers\Api\v1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Vendor\BookingRequest;
use App\Models\Booking;
use App\Models\User;
use App\Services\UserNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiBookingController extends Controller
{
    public function __construct(private readonly UserNotificationService $userNotificationService) {}

    public function index(): JsonResponse
    {
        $bookings = Booking::query()
            ->where('user_id', Auth::id())
            ->latest('booking_date')
            ->get();

        return response()->json(['data' => $bookings]);
    }

    public function store(BookingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $booking = Booking::query()->create([
            'user_id' => Auth::id(),
            'couple_id' => $validated['couple_id'],
            'type_service' => $validated['type_service'],
            'booking_date' => $validated['booking_date'],
            'status' => $validated['status'] ?? true,
            'notes' => $validated['notes'] ?? null,
        ]);

        $coupleUser = User::query()->find($booking->couple_id);
        if ($coupleUser) {
            $this->userNotificationService->notifyCoupleBookingUpdate($coupleUser, $booking, 'created');
        }

        return response()->json([
            'message' => 'Booking created successfully.',
            'data' => $booking,
        ], 201);
    }

    public function update(BookingRequest $request, Booking $booking): JsonResponse
    {
        $this->authorizeVendorBooking($booking);

        $validated = $request->validated();
        $booking->update([
            'couple_id' => $validated['couple_id'],
            'type_service' => $validated['type_service'],
            'booking_date' => $validated['booking_date'],
            'status' => $validated['status'] ?? $booking->status,
            'notes' => $validated['notes'] ?? null,
        ]);

        $coupleUser = User::query()->find($booking->couple_id);
        if ($coupleUser) {
            $this->userNotificationService->notifyCoupleBookingUpdate($coupleUser, $booking->fresh(), 'updated');
        }

        return response()->json([
            'message' => 'Booking updated successfully.',
            'data' => $booking->fresh(),
        ]);
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $this->authorizeVendorBooking($booking);
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully.']);
    }

    private function authorizeVendorBooking(Booking $booking): void
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
