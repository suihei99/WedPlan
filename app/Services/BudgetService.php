<?php

namespace App\Services;

use App\Models\BudgetCategory;
use App\Models\Couple;

class BudgetService
{
    // Method to get budget summary for a couple in dashboard
    public function getSummary(Couple $couple): array
    {
        $categories = $couple->budgetCategories()->with('expenses')->get();
        $totalAllocated = (float) $categories->sum('allocated_amount');
        $totalSpent = (float) $categories->flatMap->expenses->sum('amount');
        $configuredBudgetLimit = (float) ($couple->total_budget_limit ?? 0);
        $effectiveBudgetLimit = $configuredBudgetLimit > 0 ? $configuredBudgetLimit : $totalAllocated;

        return [
            'total_budget_limit' => $configuredBudgetLimit,
            'effective_budget_limit' => $effectiveBudgetLimit,
            'total_allocated' => $totalAllocated,
            'total_spent' => $totalSpent,
            'remaining' => max(0, $effectiveBudgetLimit - $totalSpent),
            'categories' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'category_name' => $category->category_name,
                    'allocated_amount' => $category->allocated_amount,
                    'total_spent' => $category->total_spent, // accessor to calculate total spent in this category
                    'remaining_budget' => $category->remaining_budget, // accessor to calculate remaining budget
                    'is_overspent' => $category->is_overspent,
                ];
            }),
        ];
    }

    public function getPrintableReport(Couple $couple): array
    {
        $categories = $couple->budgetCategories()
            ->with(['expenses' => function ($query) {
                $query->orderByDesc('date_paid')->orderByDesc('id');
            }])
            ->get();

        $rows = $categories->map(function (BudgetCategory $category) {
            $expenses = $category->expenses;
            $categorySpent = (float) $expenses->sum('amount');
            $allocatedAmount = (float) $category->allocated_amount;

            return [
                'id' => $category->id,
                'category_name' => $category->category_name,
                'allocated_amount' => $allocatedAmount,
                'spent_amount' => $categorySpent,
                'remaining_amount' => max(0, $allocatedAmount - $categorySpent),
                'is_overspent' => $categorySpent > $allocatedAmount,
                'expenses' => $expenses->map(function ($expense) {
                    return [
                        'expense_name' => $expense->expense_name,
                        'amount' => (float) $expense->amount,
                        'date_paid' => optional($expense->date_paid)->format('Y-m-d'),
                        'payment_method' => $expense->payment_method,
                        'description' => $expense->description,
                    ];
                })->values()->all(),
            ];
        })->values();

        return [
            'generated_at' => now()->format('Y-m-d H:i'),
            'categories' => $rows->all(),
        ];
    }

    // Method to create a new budget category for a couple
    public function createCategory(Couple $couple, array $data): BudgetCategory
    {
        return $couple->budgetCategories()->create([
            'category_name' => $data['category_name'],
            'allocated_amount' => $data['allocated_amount'],
        ]);
    }

    // Method to update an existing budget category
    public function updateCategory(BudgetCategory $category, array $data): BudgetCategory
    {
        $category->update([
            'category_name' => $data['category_name'],
            'allocated_amount' => $data['allocated_amount'],
        ]);

        return $category->refresh();
    }

    // Method to delete a budget category
    public function deleteCategory(BudgetCategory $category): void
    {
        // will automatically delete related expenses due to cascade delete in migration
        $category->delete();
    }
}
