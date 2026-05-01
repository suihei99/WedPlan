<?php

namespace App\Http\Controllers\web\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VendorDashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardVendorController extends Controller
{
    public function __construct(private readonly VendorDashboardService $vendorDashboardService) {}

    public function showDashboard(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $summary = $this->vendorDashboardService->getSummary($user);

        return view('vendor.dashboard', $summary);
    }
}
