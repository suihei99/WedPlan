<?php

namespace App\Console\Commands;

use App\Models\Couple;
use App\Models\Task;
use App\Services\BudgetService;
use App\Services\UserNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendCoupleAlertsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:dispatch-couple-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send overdue task and overbudget alerts to couples';

    public function __construct(
        private readonly BudgetService $budgetService,
        private readonly UserNotificationService $userNotificationService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $couples = Couple::query()->with('user')->get();

        foreach ($couples as $couple) {
            if (! $couple instanceof Couple) {
                continue;
            }

            if (! $couple->user) {
                continue;
            }

            $this->notifyOverdueTasks($couple);
            $this->notifyOverBudget($couple);
        }

        $this->info('Couple alerts dispatched.');

        return self::SUCCESS;
    }

    private function notifyOverdueTasks(Couple $couple): void
    {
        $overdueCount = Task::query()
            ->where('user_id', $couple->user_id)
            ->overdue()
            ->count();

        if ($overdueCount <= 0) {
            return;
        }

        $cacheKey = 'alerts:overdue:'.$couple->user_id.':'.now()->format('Y-m-d');
        if (! Cache::add($cacheKey, true, now()->endOfDay())) {
            return;
        }

        $this->userNotificationService->notifyTaskOverdue($couple->user, $overdueCount);
    }

    private function notifyOverBudget(Couple $couple): void
    {
        $summary = $this->budgetService->getSummary($couple);
        $spent = (float) ($summary['total_spent'] ?? 0);
        $limit = (float) ($summary['effective_budget_limit'] ?? $summary['total_budget_limit'] ?? 0);

        if ($limit <= 0 || $spent <= $limit) {
            return;
        }

        $cacheKey = 'alerts:budget-overlimit:'.$couple->user_id.':'.now()->format('Y-m-d');
        if (! Cache::add($cacheKey, true, now()->endOfDay())) {
            return;
        }

        $this->userNotificationService->notifyBudgetOverLimit($couple->user, $spent, $limit);
    }
}
