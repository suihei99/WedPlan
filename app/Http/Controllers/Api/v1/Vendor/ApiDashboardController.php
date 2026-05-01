<?php

namespace App\Http\Controllers\Api\v1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VendorDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiDashboardController extends Controller
{
    public function __construct(private readonly VendorDashboardService $vendorDashboardService) {}

    public function index(): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $summary = $this->vendorDashboardService->getSummary($user);

        return response()->json([
            'vendor' => $summary['vendor'],
            'dashboard' => $summary['dashboardData'],
            'bookings' => $summary['bookings'],
            'services' => $summary['services'],
        ]);
    }
}
