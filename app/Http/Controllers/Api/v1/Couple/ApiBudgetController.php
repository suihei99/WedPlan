<?php

namespace App\Http\Controllers\Api\v1\Couple;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Requests\Couple\BudgetCategoryRequest;
use App\Models\BudgetCategory;
use App\Services\BudgetService;
use App\Models\Couple;
use Illuminate\Http\JsonResponse;

/**
 * Version 1 API Controller for managing couple's budget categories and summary 
 * API Controller for managing couple's budget categories and summary
 * 
 */

class ApiBudgetController extends Controller
{
    // Inject the BudgetService to handle business logic related to budgets
    public function __construct(private readonly BudgetService $budgetService){}

    /**
     * Display a summary of the couple's budget categories and total budget limit.
     *
     * @return JsonResponse
     */

    public function index() : JsonResponse
    {
        $couple = auth()->couple;
        $summary = $this->budgetService->getSummary($couple);

        return response()->json(['data'=>$summary]);
    }

    public function store(BudgetCategoryRequest $request) : JsonResponse
    {
        $couple = auth()->couple;
        $category = $this->budgetService->createCategory($couple, $request->validated());

        return response()->json(
            ['message' => 'Budget category created successfully.', 
            'data' => $category
        ], 201);
    }

    // Show details of a specific budget category
      public function show(BudgetCategory $budgetCategory) : JsonResponse
    {
        abort_if($budgetCategory->user_id !== auth()->couple->user_id, 403);
        return response()->json(['data'=>$budgetCategory]);
    }

    public function update(BudgetCategoryRequest $request, BudgetCategory $budgetCategory) : JsonResponse
    {
        abort_if($budgetCategory->user_id !== auth()->couple->user_id, 403);
        $updated = $this->budgetService->updateCategory($budgetCategory, $request->validated());

        return response()->json([
            'message' => 'Budget category updated successfully.',
            'data' => $updated
        ], 200);
    }

    public function destroy(BudgetCategory $budgetCategory) : JsonResponse
    {
        abort_if($budgetCategory->user_id !== auth()->couple->user_id, 403);
        $this->budgetService->deleteCategory($budgetCategory);

        return response()->json(['message' => 'Budget category deleted successfully.'], 200);
    }

}
