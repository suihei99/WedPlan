<?php

namespace App\Http\Controllers\web\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Vendor\BookingRequest;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingVendorController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    public function index(): View
    {
        $vendorUser = $this->currentVendorUser();

        return view('vendor.booking.index', [
            'vendor' => $vendorUser->vendor,
            'bookings' => $this->bookingService->paginateForVendor($vendorUser),
            'summary' => $this->bookingService->summaryForVendor($vendorUser),
            'couples' => $this->couplesForVendor(),
            'serviceOptions' => $this->serviceOptionsForVendor($vendorUser),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function create(): View
    {
        $vendorUser = $this->currentVendorUser();

        return view('vendor.booking.create', [
            'vendor' => $vendorUser->vendor,
            'booking' => null,
            'couples' => $this->couplesForVendor(),
            'serviceOptions' => $this->serviceOptionsForVendor($vendorUser),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $vendorUser = $this->currentVendorUser();
        $this->bookingService->create($vendorUser, $request->validated());

        return redirect()->route('vendor.booking.index')->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking): View
    {
        $this->authorizeBooking($booking);

        return view('vendor.booking.show', [
            'vendor' => $this->currentVendorUser()->vendor,
            'booking' => $booking->load(['couple.couple', 'vendor']),
        ]);
    }

    public function edit(Booking $booking): View
    {
        $this->authorizeBooking($booking);
        $vendorUser = $this->currentVendorUser();

        return view('vendor.booking.edit', [
            'vendor' => $vendorUser->vendor,
            'booking' => $booking->load(['couple.couple']),
            'couples' => $this->couplesForVendor(),
            'serviceOptions' => $this->serviceOptionsForVendor($vendorUser),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(BookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);
        $this->bookingService->update($booking, $request->validated());

        return redirect()->route('vendor.booking.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);
        $this->bookingService->delete($booking);

        return redirect()->route('vendor.booking.index')->with('success', 'Booking deleted successfully.');
    }

    private function authorizeBooking(Booking $booking): void
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function currentVendorUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized action.');
        }

        return $user;
    }

    private function couplesForVendor(): Collection
    {
        return User::query()
            ->where('role', User::ROLE_COUPLE)
            ->with('couple')
            ->orderBy('email')
            ->get();
    }

    private function serviceOptionsForVendor(User $vendorUser): array
    {
        $serviceNames = Service::query()
            ->where('user_id', $vendorUser->id)
            ->orderBy('service_name')
            ->pluck('service_name')
            ->all();

        return $serviceNames !== [] ? $serviceNames : Service::SERVICE_TYPES;
    }

    private function statusOptions(): array
    {
        return [
            1 => 'Confirmed',
            0 => 'Pending',
        ];
    }
}
