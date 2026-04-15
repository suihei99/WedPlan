<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use App\Services\CoupleDashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardCoupleController extends Controller
{
    public function __construct(private readonly CoupleDashboardService $coupleService) {}

    // Display couple dashboard with wedding date countdown, budget summary, and upcoming tasks
    public function showCoupleDashboard(): \Illuminate\Contracts\View\View
    {
        $couple = Auth::user()?->couple;

        if (! $couple) {
            abort(403, 'Couple profile not found.');
        }

        $dashboardData = $this->coupleService->getSummary($couple);

        return view('couple.dashboard-couple', compact('dashboardData', 'couple'));
    }
}
