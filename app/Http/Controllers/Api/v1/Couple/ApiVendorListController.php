<?php

namespace App\Http\Controllers\Api\v1\Couple;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ApiVendorListController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query()
            ->whereHas('user', function ($q) {
                $q->whereHas('vendor', function ($subQ) {
                    $subQ->where('status', 'approved');
                });
            })->with(['user.vendor.bookings']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user.vendor', function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type_service')) {
            $query->where('type_service', $request->input('type_service'));
        }

        $perPage = (int) $request->input('per_page', 9);

        $services = $query->latest()->paginate($perPage)->appends($request->query());

        $services->getCollection()->transform(function (Service $service): Service {
            $service->setAttribute('booking_dates', $this->bookingDatesForService($service));

            return $service;
        });

        return response()->json(['data' => $services]);
    }

    public function show(Service $service)
    {
        $vendor = $service->user->vendor ?? null;

        if (! $vendor || $vendor->status !== 'approved') {
            abort(404);
        }

        $bookingDates = $this->bookingDatesForService($service);

        $service->setAttribute('booking_dates', $bookingDates);

        return response()->json([
            'data' => [
                'service' => $service->load('user.vendor.bookings'),
                'vendor' => $vendor,
                'booking_dates' => $bookingDates,
            ],
        ]);
    }

    private function bookingDatesForService(Service $service): array
    {
        $vendor = $service->user->vendor ?? null;

        if (! $vendor) {
            return [];
        }

        $bookingQuery = $vendor->relationLoaded('bookings')
            ? $vendor->bookings->where('type_service', $service->type_service)
            : $vendor->bookings()->where('type_service', $service->type_service)->get();

        return $bookingQuery
            ->whereNotNull('booking_date')
            ->sortBy('booking_date')
            ->pluck('booking_date')
            ->map(fn ($bookingDate): string => Carbon::parse((string) $bookingDate)->format('Y-m-d'))
            ->values()
            ->all();
    }
}
