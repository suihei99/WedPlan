<?php

namespace App\Http\Controllers\Api\v1\Couple;

use App\Http\Controllers\Controller;
use App\Services\CoupleDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiDashboardController extends Controller
{
    public function __construct(private readonly CoupleDashboardService $dashboardService) {}

    public function index(): JsonResponse
    {
        $couple = Auth::user()?->couple;
        abort_if(! $couple, 403, 'Couple profile not found.');

        $summary = $this->dashboardService->getSummary($couple);

        return response()->json(['data' => $summary]);
    }
}
