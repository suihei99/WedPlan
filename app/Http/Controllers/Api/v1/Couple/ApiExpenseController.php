<?php

namespace App\Http\Controllers\Api\v1\Couple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Couple\ExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\BudgetCategory;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiExpenseController extends Controller
{
    public function __construct(private readonly ExpenseService $expenseService) {}

    public function index(): JsonResponse
    {
        $couple = Auth::user()?->couple;
        abort_if(! $couple, 403, 'Couple profile not found.');

        $expenses = $this->expenseService->getForCouple($couple);

        return response()->json(['data' => ExpenseResource::collection($expenses)]);
    }

    public function store(ExpenseRequest $request): JsonResponse
    {
        $budgetCategory = $this->resolveOwnedBudgetCategory((int) $request->validated('budget_category_id'));
        $expense = $this->expenseService->create($budgetCategory, $request->validated());

        return response()->json([
            'message' => 'Expense created successfully.',
            'data' => new ExpenseResource($expense),
        ], 201);
    }

    public function show(Expense $expense): JsonResponse
    {
        $this->authorizeExpense($expense);

        return response()->json(['data' => new ExpenseResource($expense)]);
    }

    public function update(ExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($expense);
        $budgetCategory = $this->resolveOwnedBudgetCategory((int) $request->validated('budget_category_id'));

        if ($budgetCategory->id !== $expense->budget_category_id) {
            abort(422, 'Changing expense budget category is not allowed.');
        }

        $updated = $this->expenseService->update($expense, $request->validated());

        return response()->json([
            'message' => 'Expense updated successfully.',
            'data' => new ExpenseResource($updated),
        ]);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $this->authorizeExpense($expense);
        $this->expenseService->delete($expense);

        return response()->json(['message' => 'Expense deleted successfully.']);
    }

    private function authorizeExpense(Expense $expense): void
    {
        $expense->loadMissing('budgetCategory');

        if (! $expense->budgetCategory || $expense->budgetCategory->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function resolveOwnedBudgetCategory(int $budgetCategoryId): BudgetCategory
    {
        return BudgetCategory::query()
            ->whereKey($budgetCategoryId)
            ->where('user_id', Auth::id())
            ->firstOr(function () {
                abort(403, 'Unauthorized budget category.');
            });
    }
}
