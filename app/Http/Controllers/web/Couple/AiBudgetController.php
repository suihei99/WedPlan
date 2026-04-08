<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use App\Services\AiBudgetService;
use Illuminate\Http\Request;
use App\Http\Requests\Requests\Couple\AiBudgetRequest;

class AiBudgetController extends Controller
{
    public function __construct(private readonly AiBudgetService $aiService)
    {}

    public function index()
    {
        return view('couple.ai_budget.index');
    }

    // public function generate(AiBudgetRequest $request)
    // {
    //     $validated = $request->validated();

    //     try {
    //         $budgetPlan = $this->aiService->generateBudgetPlan($validated);
    //         return response()->json(['success' => true, 'data' => $budgetPlan]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Failed to generate budget plan. Please try again.']);
    //     }
    // }

    public function estimate(Request $request)
    {
        $couple = auth()->couple;
        $suggestion = $this->aiService->estimateBudget($couple);

        return view('couple.ai_budget.result', compact('suggestion'));
    }
}
