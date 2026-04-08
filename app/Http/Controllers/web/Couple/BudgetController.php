<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use App\Models\BudgetCategory;
use App\Services\BudgetService;
use App\Http\Requests\Requests\Couple\BudgetCategoryRequest;
use App\Models\Couple;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class BudgetController extends Controller
{
    public function __construct(private readonly BudgetService $budgetService){}

    public function index()
    {
        $couple = Auth::user()->couple;
        $summary = $this->budgetService->getSummary($couple);

        return view('couple.budget.index', compact('summary'));
    }

    public function displayAddCategoryForm()
    {
        return view('couple.budget.add-category');
    }


    // Show details of a specific budget category
    public function show(BudgetCategory $budgetCategory)
    {
        $this->authorizeCouple($budgetCategory);
        return view('couple.budget.show', compact('budgetCategory'));
    }

    public function store(BudgetCategoryRequest $request)
    {
        $couple = Auth::user()->couple;

        $this->budgetService->createCategory($couple, $request->validated());

        return redirect()->route('couple.budget.index')->with('success', 'Budget category created successfully.');
    }

    public function update(BudgetCategoryRequest $request, BudgetCategory $budgetCategory)
    {
      $this->authorizeCouple($budgetCategory);
      $this->budgetService->updateCategory($budgetCategory, $request->validated());

      return redirect()->route('couple.budget.index')->with('success', 'Budget category updated successfully.');
    }

    public function destroy(BudgetCategory $budgetCategory)
    {
        $this->authorizeCouple($budgetCategory);
        $this->budgetService->deleteCategory($budgetCategory);

        return redirect()->route('couple.budget.index')->with('success', 'Budget category deleted successfully.');
    }

    private function authorizeCouple(BudgetCategory $budgetCategory) : void
    {
        $couple = Auth::user()->couple;

        if ($budgetCategory->user_id !== $couple->user_id) {
            abort(403, 'Unauthorized action.');
        }
    }

}
