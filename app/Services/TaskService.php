<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Couple;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TaskService
{
    /**
     * addTask(), updateTask(), deleteTask(), alertTask(), viewTask()
     * methods to manage task records for each couple.
     */

    public function getAll(Couple $couple) : Collection
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
          'priority' => $data['priority'] ??  Task::PRIORITY_LOW,
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
            ->where('is_completed', '!=', true)
            ->whereBetween('deadline', [now(), now()->addDays(3)])
            ->byPriority()
            ->get();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}