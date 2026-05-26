<?php

namespace App\Services;

use App\Models\Couple;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TaskService
{
    /**
     * addTask(), updateTask(), deleteTask(), alertTask(), viewTask()
     * methods to manage task records for each couple.
     */
    public function __construct(private readonly UserNotificationService $userNotificationService) {}

    public function getAll(Couple $couple): Collection
    {
        return $couple->tasks()->byPriority()->get();
    }

    public function create(Couple $couple, array $data): Task
    {
        $task = $couple->tasks()->create([
            'task_name' => $data['task_name'],
            'description' => $data['description'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'is_completed' => $data['is_completed'] ?? false,
            'priority' => $data['priority'] ?? Task::PRIORITY_LOW,
        ]);

        $this->notifyDueDateSet($task);

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $previousDeadline = $task->deadline instanceof Carbon
            ? $task->deadline->copy()
            : ($task->deadline ? Carbon::parse((string) $task->deadline) : null);

        $task->update([
            'task_name' => $data['task_name'] ?? $task->task_name,
            'description' => $data['description'] ?? $task->description,
            'deadline' => $data['deadline'] ?? $task->deadline,
            'is_completed' => $data['is_completed'] ?? $task->is_completed,
            'priority' => $data['priority'] ?? $task->priority,
        ]);

        $task = $task->fresh();

        if ($this->deadlineChanged($previousDeadline, $task->deadline)) {
            $this->notifyDueDateUpdated($task, $previousDeadline);
        }

        return $task;
    }

    public function markDone(Task $task): Task
    {
        $task->update(['is_completed' => true]);

        return $task->fresh();
    }

    public function markUndone(Task $task): Task
    {
        $task->update(['is_completed' => false]);

        return $task->fresh();
    }

    public function getUpcomingTasks(Couple $couple): Collection
    {
        return $couple->tasks()
            ->where('is_completed', false)
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('deadline')
            ->byPriority()
            ->limit(5)
            ->get();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    private function notifyDueDateSet(Task $task): void
    {
        if (! $task->deadline) {
            return;
        }

        $coupleUser = $task->user;

        if (! $coupleUser instanceof User) {
            return;
        }

        $this->userNotificationService->notifyTaskDueDateSet($coupleUser, $task);
    }

    private function notifyDueDateUpdated(Task $task, ?Carbon $previousDeadline): void
    {
        if (! $task->deadline) {
            return;
        }

        $coupleUser = $task->user;

        if (! $coupleUser instanceof User) {
            return;
        }

        $this->userNotificationService->notifyTaskDueDateUpdated($coupleUser, $task, $previousDeadline);
    }

    private function deadlineChanged(?Carbon $previousDeadline, mixed $currentDeadline): bool
    {
        if ($previousDeadline === null && $currentDeadline === null) {
            return false;
        }

        if ($previousDeadline === null || $currentDeadline === null) {
            return true;
        }

        return ! $previousDeadline->equalTo(Carbon::parse((string) $currentDeadline));
    }
}
