<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Couple\TaskRequest;
use App\Models\Couple;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    public function showTasks()
    {
        $couple = $this->currentCouple();
        $tasks = $this->taskService->getAll($couple);

        return view('couple.tasks.index', compact('tasks', 'couple'));
    }

    public function displayAddTaskForm()
    {
        $couple = $this->currentCouple();
        $priorities = [Task::PRIORITY_LOW, Task::PRIORITY_MEDIUM, Task::PRIORITY_HIGH];

        return view('couple.tasks.create', compact('priorities', 'couple'));
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $couple = $this->currentCouple();
        $this->taskService->create($couple, $request->validated());

        return redirect()->route('couple.tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $this->authorizeTask($task);
        $couple = $this->currentCouple();
        $priorities = [Task::PRIORITY_LOW, Task::PRIORITY_MEDIUM, Task::PRIORITY_HIGH];

        return view('couple.tasks.show', compact('task', 'couple', 'priorities'));
    }

    public function markComplete(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);
        $this->taskService->markDone($task);

        return redirect()->route('couple.tasks.index')->with('success', 'Task marked as completed.');
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);
        $this->taskService->update($task, $request->validated());

        return redirect()->route('couple.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);
        $this->taskService->delete($task);

        return redirect()->route('couple.tasks.index')->with('success', 'Task deleted successfully.');
    }

    private function authorizeTask(Task $task): void
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function currentCouple(): Couple
    {
        $couple = Auth::user()?->couple;

        if (! $couple) {
            abort(403, 'Couple profile not found.');
        }

        return $couple;
    }
}
