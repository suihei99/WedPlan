<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Couple\BudgetCategoryRequest;
use App\Http\Requests\Requests\Couple\ExpenseRequest;
use App\Models\BudgetCategory;
use App\Models\Couple;
use App\Models\Expense;
use App\Services\BudgetService;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function __construct(
        private readonly BudgetService $budgetService,
        private readonly ExpenseService $expenseService
    ) {}

    public function index()
    {
        $couple = $this->currentCouple();
        $summary = $this->budgetService->getSummary($couple);

        return view('couple.budget.index', compact('summary', 'couple'));
    }

    public function printReport()
    {
        $couple = $this->currentCouple();
        $summary = $this->budgetService->getSummary($couple);
        $report = $this->budgetService->getPrintableReport($couple);

        return view('couple.budget.print', compact('couple', 'summary', 'report'));
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
        $couple = $this->currentCouple();

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

    public function showExpenses(BudgetCategory $budgetCategory)
    {
        $this->authorizeCouple($budgetCategory);
        $expenses = $budgetCategory->expenses()->latest('date_paid')->get();

        return view('couple.budget.expenses.index', compact('budgetCategory', 'expenses'));
    }

    public function completedExpense(BudgetCategory $budgetCategory, Expense $expense): RedirectResponse
    {
        $this->authorizeCouple($budgetCategory);
        $this->authorizeExpense($budgetCategory, $expense);

        $this->expenseService->update($expense, [
            'expense_name' => $expense->expense_name,
            'amount' => $expense->amount,
            'date_paid' => now()->toDateString(),
            'description' => $expense->description,
            'payment_method' => $expense->payment_method,
        ]);

        return redirect()->route('couple.budget.expenses', $budgetCategory)->with('success', 'Expense marked as paid.');
    }

    public function displayAddExpenseForm(BudgetCategory $budgetCategory)
    {
        $this->authorizeCouple($budgetCategory);

        return view('couple.budget.expenses.create', compact('budgetCategory'));
    }

    public function addExpense(ExpenseRequest $request, BudgetCategory $budgetCategory): RedirectResponse
    {
        $this->authorizeCouple($budgetCategory);
        $this->expenseService->create($budgetCategory, $request->validated());

        return redirect()->route('couple.budget.expenses', $budgetCategory)->with('success', 'Expense added successfully.');
    }

    public function showExpense(BudgetCategory $budgetCategory, Expense $expense)
    {
        $this->authorizeCouple($budgetCategory);
        $this->authorizeExpense($budgetCategory, $expense);

        return view('couple.budget.expenses.show', compact('budgetCategory', 'expense'));
    }

    public function updateLimit(Request $request): RedirectResponse
    {
        $couple = $this->currentCouple();
        $validated = $request->validate([
            'total_budget_limit' => ['required', 'numeric', 'min:0'],
        ]);

        $couple->update(['total_budget_limit' => $validated['total_budget_limit']]);

        return redirect()->route('couple.budget.index')->with('success', 'Total budget limit updated successfully.');
    }

    public function updateExpense(ExpenseRequest $request, BudgetCategory $budgetCategory, Expense $expense): RedirectResponse
    {
        $this->authorizeCouple($budgetCategory);
        $this->authorizeExpense($budgetCategory, $expense);
        $this->expenseService->update($expense, $request->validated());

        return redirect()->route('couple.budget.expenses', $budgetCategory)->with('success', 'Expense updated successfully.');
    }

    public function dueDateExpense(Request $request, BudgetCategory $budgetCategory, Expense $expense): RedirectResponse
    {
        $this->authorizeCouple($budgetCategory);
        $this->authorizeExpense($budgetCategory, $expense);

        $validated = $request->validate([
            'date_paid' => ['required', 'date'],
        ]);

        $this->expenseService->update($expense, [
            'expense_name' => $expense->expense_name,
            'amount' => $expense->amount,
            'date_paid' => $validated['date_paid'],
            'description' => $expense->description,
            'payment_method' => $expense->payment_method,
        ]);

        return redirect()->route('couple.budget.expenses', $budgetCategory)->with('success', 'Expense payment date updated.');
    }

    public function destroyExpense(BudgetCategory $budgetCategory, Expense $expense): RedirectResponse
    {
        $this->authorizeCouple($budgetCategory);
        $this->authorizeExpense($budgetCategory, $expense);
        $this->expenseService->delete($expense);

        return redirect()->route('couple.budget.expenses', $budgetCategory)->with('success', 'Expense deleted successfully.');
    }

    private function authorizeCouple(BudgetCategory $budgetCategory): void
    {
        $couple = $this->currentCouple();

        if ($budgetCategory->user_id !== $couple->user_id) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function authorizeExpense(BudgetCategory $budgetCategory, Expense $expense): void
    {
        if ($expense->budget_category_id !== $budgetCategory->id) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function currentCouple(): Couple
    {
        $couple = Auth::user()?->couple;

        if (! $couple) {
            abort(403, 'Couple profile not found.');
        }

        return $couple;
    }
}
