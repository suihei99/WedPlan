<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Couple;
use App\Models\Budget;
use App\Services\CoupleService;
use Illuminate\Support\Facades\Auth;

class DashboardCoupleController extends Controller
{
    protected $coupleService;

    public function __construct(CoupleService $coupleService)
    {
        $this->coupleService = $coupleService;
    }

    // Display couple dashboard with wedding date countdown, budget summary, and upcoming tasks
    public function showCoupleDashboard()
    {
        $user = Auth::user();
        $dashboardData = $this->coupleService->getDashboardData($user);

        return view('couple.dashboard', compact('dashboardData'));
    }
}
