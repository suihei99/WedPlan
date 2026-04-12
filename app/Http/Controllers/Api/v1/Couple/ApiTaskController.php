<?php

namespace App\Http\Controllers\Api\v1\Couple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Couple\TaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiTaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    public function index(): JsonResponse
    {
        $couple = Auth::user()?->couple;
        abort_if(! $couple, 403, 'Couple profile not found.');

        $tasks = $this->taskService->getAll($couple);

        return response()->json(['data' => $tasks]);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $couple = Auth::user()?->couple;
        abort_if(! $couple, 403, 'Couple profile not found.');

        $task = $this->taskService->create($couple, $request->validated());

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        return response()->json(['data' => $task]);
    }

    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $this->authorizeTask($task);
        $updated = $this->taskService->update($task, $request->validated());

        return response()->json([
            'message' => 'Task updated successfully.',
            'data' => $updated,
        ]);
    }

    public function markComplete(Task $task): JsonResponse
    {
        $this->authorizeTask($task);
        $updated = $this->taskService->markDone($task);

        return response()->json([
            'message' => 'Task marked as completed.',
            'data' => $updated,
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorizeTask($task);
        $this->taskService->delete($task);

        return response()->json(['message' => 'Task deleted successfully.']);
    }

    private function authorizeTask(Task $task): void
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
