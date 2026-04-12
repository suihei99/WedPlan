<?php

namespace App\Services;

use App\Models\Couple;

class CoupleDashboardService
{
    public function __construct(
        private BudgetService $budgetService,
        private GuestService $guestService,
        private TaskService $taskService
    ) {}

    public function getSummary(Couple $couple): array
    {
        return [
            'budget' => $this->budgetService->getSummary($couple),
            'guests_summary' => $this->guestService->getSummary($couple),
            'upcoming_tasks' => $this->taskService->getUpcomingTasks($couple),
            'tasks_done' => $couple->tasks()->where('is_completed', true)->count(),
            'tasks_total' => $couple->tasks()->count(),
            'days_until_wedding' => $couple->wedding_date ? now()->diffInDays($couple->wedding_date, false) : null,
        ];
    }
}
