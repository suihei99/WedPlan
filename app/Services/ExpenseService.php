<?php

namespace App\Services;

use App\Models\BudgetCategory;
use App\Models\Couple;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Support\Collection;

class ExpenseService
{
    public function getForCouple(Couple $couple): Collection
    {
        return Expense::whereHas('budgetCategory', function ($query) use ($couple) {
            $query->where('user_id', $couple->user_id);
        })->with('budgetCategory')->latest('expense_date')->get();
    }

    public function create(BudgetCategory $category, array $data): Expense
    {
        return $category->expeneses()->create([
            'expense_name' => $data['expense_name'],
            'amount' => $data['amount'],
            'expense_date' => $data['expense_date'] ?? null,
            'description' => $data['description'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
        ]);
    }

    public function update(Expense $expense, array $data): Expense
    {
        $expense->update([
            'expense_name' => $data['expense_name'],
            'amount' => $data['amount'],
            'expense_date' => $data['expense_date'] ?? null,
            'description' => $data['description'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        return $expense->refresh();
    }

    public function delete(Expense $expense): void
    {
        $expense->delete();
    }
}