<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Couple;
use Illuminate\Support\Facades\Auth;
use App\Models\BudgetCategory;
use App\Services\ExpenseService;
use App\Http\Requests\Requests\Couple\ExpenseRequest;

class ExpenseController extends Controller
{
    /**
     * Expense functionilty : view , Summary, Add, Update, UpdateStatus, expense total, Delete
     */

   public function __construct(private readonly ExpenseService $expenseService) {}

   public function index()
   {
        $couple = Auth::user()->couple;
        $expenses = $this->expenseService->getForCouple($couple);

        return view('couple.expenses.index', compact('expenses'));
    }


    public function create()
    {
        $couple = Auth::user()->couple;
        $categories = BudgetCategory::where('couple_id', $couple->user_id)->get();

        return view('couple.expenses.create', compact('categories'));
    }

    public function addExpense(ExpenseRequest $request, BudgetCategory $budget)
    {
        $couple = Auth::user()->couple;

        $this->expenseService->create($couple, $request->validated());

        return redirect()->route('couple.expenses.index')->with('success', 'Expense added successfully.');
    }

    public function viewExpensesDetail(Expense $expense, BudgetCategory $budget)
    {
            $this->authorizeCouple($expense);
            return view('couple.expenses.show', compact('expense', 'budget'));
    }

    public function updateExpense(ExpenseRequest $request, Expense $expense)
    {
          $this->authorizeCouple($expense);
          $this->expenseService->update($expense, $request->validated());
    
          return redirect()->route('couple.expenses.index')->with('success', 'Expense updated successfully.');
    }


    public function deleteExpense(Expense $expense)
    {
        $this->authorizeCouple($expense);
        $this->expenseService->delete($expense);

        return redirect()->route('couple.expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function expenseSummeryTotal(Expense $expense, BudgetCategory $budgetCategory)
    {
        $this->authorizeCouple($expense);
        $total = $budgetCategory->expenses()->sum('amount');

        return view('couple.expenses.index', compact('total', 'budgetCategory'));
    }

    public function expenseOverBudget(Expense $expense, BudgetCategory $budgetCategory)
    {
        $this->authorizeCouple($expense);
        $total = $budgetCategory->expenses()->sum('amount');
        $overBudget = $total > $budgetCategory->limit;

        return view('couple.expenses.index', compact('overBudget', 'total', 'budgetCategory'));
    }

    public function authorizeCouple(Expense $expense)
    {
        $couple = Auth::user()->couple;

        if ($expense->budgetCategory->couple_id !== $couple->user_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
