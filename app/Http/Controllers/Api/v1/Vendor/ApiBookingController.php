<?php

namespace App\Http\Controllers\Api\v1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Vendor\BookingRequest;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiBookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    public function index(): JsonResponse
    {
        $bookings = $this->bookingService->paginateForVendor($this->currentVendorUser(), 100);

        return response()->json(['data' => $bookings]);
    }

    public function store(BookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->create($this->currentVendorUser(), $request->validated());

        return response()->json([
            'message' => 'Booking created successfully.',
            'data' => $booking,
        ], 201);
    }

    public function show(Booking $booking): JsonResponse
    {
        $this->authorizeVendorBooking($booking);

        return response()->json(['data' => $booking->load(['couple.couple', 'vendor'])]);
    }

    public function update(BookingRequest $request, Booking $booking): JsonResponse
    {
        $this->authorizeVendorBooking($booking);
        $updatedBooking = $this->bookingService->update($booking, $request->validated());

        return response()->json([
            'message' => 'Booking updated successfully.',
            'data' => $updatedBooking,
        ]);
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $this->authorizeVendorBooking($booking);
        $this->bookingService->delete($booking);

        return response()->json(['message' => 'Booking deleted successfully.']);
    }

    private function currentVendorUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized action.');
        }

        return $user;
    }

    private function authorizeVendorBooking(Booking $booking): void
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
