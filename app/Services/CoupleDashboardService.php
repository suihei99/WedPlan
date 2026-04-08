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


    
    // Get a summary of the couple's dashboard, including tasks, guests, and budget information    
    public function getSummary(Couple $couple) : array
    {
        return [
            'budget' => $this->budgetService->getSummary($couple),
            'guests_summary' => $this->guestService->getSummary($couple),
            'upcoming_tasks' => $this->taskService->getUpcomingTasks($couple),
            'tasks_done' => $couple->tasks()->where('is_completed', true)->count(),
            'tasks_total' => $couple->tasks()->count(),
            'days_until_wedding' => $couple->wedding_date ? now()->diffInDays($couple->wedding_date, false) : null,
            'couples' => $couple->couples()->get(),
        ];
    }
}