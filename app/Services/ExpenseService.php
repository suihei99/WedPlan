<?php

namespace App\Services;

use App\Models\BudgetCategory;
use App\Models\Couple;
use App\Models\Expense;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
            'receipt_url' => $this->storeReceipt($data['receipt'] ?? null),
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        $this->notifyIfCategoryOverLimit($category);
        $this->notifyIfCoupleBudgetOverLimit((int) $category->user_id);

        return $expense;
    }

    public function update(Expense $expense, array $data): Expense
    {
        $receiptUrl = $this->storeReceipt($data['receipt'] ?? null, $expense->receipt_url);

        $expense->update([
            'expense_name' => $data['expense_name'],
            'amount' => $data['amount'],
            'date_paid' => $data['date_paid'] ?? null,
            'description' => $data['description'] ?? null,
            'receipt_url' => $receiptUrl,
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        $this->notifyIfCategoryOverLimit($expense->budgetCategory);
        $this->notifyIfCoupleBudgetOverLimit((int) $expense->budgetCategory->user_id);

        return $expense->refresh();
    }

    public function delete(Expense $expense): void
    {
        $this->deleteStoredReceipt($expense->receipt_url);
        $expense->delete();
    }

    private function storeReceipt(mixed $receipt, ?string $existingReceipt = null): ?string
    {
        if ($receipt instanceof UploadedFile) {
            $this->deleteStoredReceipt($existingReceipt);

            return $receipt->store('expense-receipts', 'public');
        }

        if (is_string($receipt) && $receipt !== '') {
            return $receipt;
        }

        return $existingReceipt;
    }

    private function deleteStoredReceipt(?string $receiptPath): void
    {
        if (! $receiptPath) {
            return;
        }

        Storage::disk('public')->delete($receiptPath);
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

    private function notifyIfCategoryOverLimit(BudgetCategory $category): void
    {
        $category->loadMissing('user');

        if (! $category->user) {
            return;
        }

        $spent = (float) $category->total_spent;
        $limit = (float) $category->allocated_amount;

        if ($limit <= 0 || $spent <= $limit) {
            return;
        }

        $cacheKey = 'alerts:category-overlimit:'.$category->user_id.':'.$category->id.':'.now()->format('Y-m-d');
        if (! Cache::add($cacheKey, true, now()->endOfDay())) {
            return;
        }

        $this->userNotificationService->notifyBudgetCategoryOverLimit($category->user, $category, $spent, $limit);
    }
}
