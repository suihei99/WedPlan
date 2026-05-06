<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use App\Models\Couple;
use App\Models\Service;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class VendorListController extends Controller
{
    public function index(Request $request)
    {
        $couple = $this->currentCouple();

        $query = Service::query()
            ->whereHas('user', function ($q) {
                $q->whereHas('vendor', function ($subQ) {
                    $subQ->where('status', 'approved');
                });
            });

        // Search by business name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user.vendor', function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%");
            });
        }

        // Filter by service type
        if ($request->filled('type_service')) {
            $serviceType = $request->input('type_service');
            $query->where('type_service', $serviceType);
        }

        $services = $query->paginate(9)->appends($request->query());
        $serviceTypes = Service::SERVICE_TYPES;

        return view('couple.vendorlist.index', compact('services', 'couple', 'serviceTypes'));
    }

    public function show(Service $service)
    {
        $this->authorizeVendor($service);
        $couple = $this->currentCouple();

        // Get vendor details
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

        return view('couple.vendorlist.view', compact('service', 'vendor', 'couple', 'bookingDates'));
    }

    private function currentCouple(): Couple
    {
        return Auth::user()->couple ?? abort(403, 'Unauthorized');
    }

    private function authorizeVendor(Service $service): void
    {
        $vendor = $service->user->vendor ?? null;
        if (! $vendor || $vendor->status !== 'approved') {
            abort(404);
        }
    }
}
