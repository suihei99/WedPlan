<?php

namespace App\Services;

use App\Models\BudgetCategory;
use App\Models\Couple;
use App\Models\Expense;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ExpenseService
{
    public function __construct(
        private readonly BudgetService $budgetService,
        private readonly UserNotificationService $userNotificationService
    ) {}

    public function getForCouple(Couple $couple): Collection
    {
        return Expense::whereHas('budgetCategory', function ($query) use ($couple) {
            $query->where('user_id', $couple->user_id);
        })->with('budgetCategory')->latest('date_paid')->get();
    }

    public function create(BudgetCategory $category, array $data): Expense
    {
        $expense = $category->expenses()->create([
            'expense_name' => $data['expense_name'],
            'amount' => $data['amount'],
            'date_paid' => $data['date_paid'] ?? null,
            'description' => $data['description'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        $this->notifyIfCoupleBudgetOverLimit((int) $category->user_id);

        return $expense;
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

        $this->notifyIfCoupleBudgetOverLimit((int) $expense->budgetCategory->user_id);

        return $expense->refresh();
    }

    public function delete(Expense $expense): void
    {
        $expense->delete();
    }

    private function notifyIfCoupleBudgetOverLimit(int $userId): void
    {
        $couple = Couple::query()->where('user_id', $userId)->with('user')->first();

        if (! $couple || ! $couple->user) {
            return;
        }

        $summary = $this->budgetService->getSummary($couple);
        $spent = (float) ($summary['total_spent'] ?? 0);
        $limit = (float) ($summary['effective_budget_limit'] ?? $summary['total_budget_limit'] ?? 0);

        if ($limit <= 0 || $spent <= $limit) {
            return;
        }

        $cacheKey = 'alerts:budget-overlimit:'.$userId.':'.now()->format('Y-m-d');
        if (! Cache::add($cacheKey, true, now()->endOfDay())) {
            return;
        }

        $this->userNotificationService->notifyBudgetOverLimit($couple->user, $spent, $limit);
    }
}
