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
            })->with(['user.vendor']);

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

        return response()->json(['data' => $services]);
    }

    public function show(Service $service)
    {
        $vendor = $service->user->vendor ?? null;

        if (! $vendor || $vendor->status !== 'approved') {
            abort(404);
        }

        $bookingDates = $vendor->bookings()
            ->where('type_service', $service->type_service)
            ->whereNotNull('booking_date')
            ->orderBy('booking_date')
            ->pluck('booking_date')
            ->map(fn ($bookingDate): string => Carbon::parse((string) $bookingDate)->format('Y-m-d'))
            ->values()
            ->all();

        return response()->json([
            'data' => [
                'service' => $service->load('user.vendor'),
                'vendor' => $vendor,
                'booking_dates' => $bookingDates,
            ],
        ]);
    }
}
