<?php
namespace App\Services;

use App\Models\Couple;
use App\Models\Task;
use App\Models\Guest;
use App\Models\BudgetCategory;
use Illuminate\Support\Collection;


class CoupleDashboardService
{
    // Inject necessary services for handling business logic related to budgets, guests, and tasks
    public function __construct(
        private BudgetService $budgetService, 
        private GuestService $guestService, 
        private TaskService $taskService) {}

    public function getSummary(Couple $couple) : array
    {
        return [
            'tasks' => $this->taskService->getSummary($couple),
            'guests' => $this->guestService->getSummary($couple),
            'budget' => $this->budgetService->getSummary($couple),
        ];
    }
}