<?php

namespace App\Services;

use App\Models\Couple;
use App\Models\Task;
use Illuminate\Support\Collection;

class TaskService
{
    /**
     * addTask(), updateTask(), deleteTask(), alertTask(), viewTask()
     * methods to manage task records for each couple.
     */
    public function getAll(Couple $couple): Collection
    {
        return $couple->tasks()->byPriority()->get();
    }

    public function create(Couple $couple, array $data): Task
    {
        return $couple->tasks()->create([
            'task_name' => $data['task_name'],
            'description' => $data['description'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'is_completed' => $data['is_completed'] ?? false,
            'priority' => $data['priority'] ?? Task::PRIORITY_LOW,
        ]);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update([
            'task_name' => $data['task_name'] ?? $task->task_name,
            'description' => $data['description'] ?? $task->description,
            'deadline' => $data['deadline'] ?? $task->deadline,
            'is_completed' => $data['is_completed'] ?? $task->is_completed,
            'priority' => $data['priority'] ?? $task->priority,
        ]);

        return $task->fresh();
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
}
