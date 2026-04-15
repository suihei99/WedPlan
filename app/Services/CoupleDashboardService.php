<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Couple;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CoupleDashboardService
{
    public function __construct(
        private BudgetService $budgetService,
        private GuestService $guestService,
        private TaskService $taskService
    ) {}

    public function getSummary(Couple $couple): array
    {
        $budgetSummary = $this->budgetService->getSummary($couple);
        $guestSummary = $this->guestService->getSummary($couple);

        $tasksTotal = $couple->tasks()->count();
        $tasksDone = $couple->tasks()->where('is_completed', true)->count();
        $taskProgress = $tasksTotal > 0 ? ($tasksDone / $tasksTotal) * 100 : 0;

        $guestTotal = (int) ($guestSummary['total_guests'] ?? 0);
        $guestConfirmed = (int) ($guestSummary['confirmed_guests'] ?? 0);
        $guestProgress = $guestTotal > 0 ? ($guestConfirmed / $guestTotal) * 100 : 0;

        $vendorsBooked = Booking::query()
            ->where('couple_id', $couple->user_id)
            ->where('status', true)
            ->count();

        $vendorsPending = Booking::query()
            ->where('couple_id', $couple->user_id)
            ->where('status', false)
            ->count();

        $vendorProgress = ($vendorsBooked + $vendorsPending) > 0
            ? ($vendorsBooked / ($vendorsBooked + $vendorsPending)) * 100
            : 0;

        $progressPercentage = (int) round(($taskProgress + $guestProgress + $vendorProgress) / 3);

        $totalBudget = (float) ($budgetSummary['total_budget_limit'] ?? 0);
        $totalAllocated = (float) ($budgetSummary['total_allocated'] ?? 0);
        $totalSpent = (float) ($budgetSummary['total_spent'] ?? 0);

        if ($totalBudget <= 0) {
            $totalBudget = $totalAllocated;
        }

        $upcomingTasks = $this->formatUpcomingTasks($couple);
        $budgetCategories = $this->formatBudgetCategories($budgetSummary['categories'] ?? collect(), $totalBudget);

        $weddingDate = $couple->wedding_date ? Carbon::parse($couple->wedding_date) : null;

        return [
            'wedding_date' => $weddingDate ? $weddingDate->format('F j, Y') : null,
            'days_until_wedding' => $weddingDate ? max(0, now()->diffInDays($weddingDate, false)) : null,
            'progress_percentage' => max(0, min(100, $progressPercentage)),

            'tasks_done' => $tasksDone,
            'tasks_total' => $tasksTotal,
            'upcoming_tasks' => $upcomingTasks,

            'guests_total' => $guestTotal,
            'guests_confirmed' => $guestConfirmed,

            'total_budget' => $totalBudget,
            'budget_spent' => $totalSpent,
            'budget_remaining' => max(0, $totalBudget - $totalSpent),
            'budget_categories' => $budgetCategories,

            'vendors_booked' => $vendorsBooked,
            'vendors_pending' => $vendorsPending,

            // Keep nested keys for compatibility with any existing callers.
            'budget' => $budgetSummary,
            'guests_summary' => $guestSummary,
        ];
    }

    private function formatUpcomingTasks(Couple $couple): array
    {
        return $this->taskService->getUpcomingTasks($couple)
            ->map(function (Task $task): array {
                return [
                    'title' => $task->task_name,
                    'due_date' => $task->deadline ? Carbon::parse($task->deadline)->format('d M Y') : 'No deadline',
                ];
            })
            ->values()
            ->all();
    }

    private function formatBudgetCategories(Collection $categories, float $totalBudget): array
    {
        return $categories
            ->map(function (array $category) use ($totalBudget): array {
                $allocatedAmount = (float) ($category['allocated_amount'] ?? 0);
                $percentage = $totalBudget > 0
                    ? (int) round(($allocatedAmount / $totalBudget) * 100)
                    : 0;

                return [
                    'name' => $category['category_name'] ?? 'Category',
                    'amount' => $allocatedAmount,
                    'percentage' => max(0, min(100, $percentage)),
                ];
            })
            ->values()
            ->all();
    }
}
