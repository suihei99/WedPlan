<?php

namespace App\Services;

use App\Models\BudgetCategory;
use App\Models\Couple;
use App\Models\Expense;
use Illuminate\Support\Collection;

class ExpenseService
{
    public function getForCouple(Couple $couple): Collection
    {
        return Expense::whereHas('budgetCategory', function ($query) use ($couple) {
            $query->where('user_id', $couple->user_id);
        })->with('budgetCategory')->latest('date_paid')->get();
    }

    public function create(BudgetCategory $category, array $data): Expense
    {
        return $category->expenses()->create([
            'expense_name' => $data['expense_name'],
            'amount' => $data['amount'],
            'date_paid' => $data['date_paid'] ?? null,
            'description' => $data['description'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
        ]);
    }

    public function update(Expense $expense, array $data): Expense
    {
        $expense->update([
            'expense_name' => $data['expense_name'],
            'amount' => $data['amount'],
            'date_paid' => $data['date_paid'] ?? null,
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
