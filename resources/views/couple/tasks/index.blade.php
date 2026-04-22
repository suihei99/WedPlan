@extends('couple.layout.layout-couple')

@section('title', 'Task List - WebPlan')
@section('page-title', 'Task List')
@section('page-subtitle', 'Keep your wedding checklist focused, visible, and on schedule.')

@push('page-styles')
	@vite(['resources/css/couple/tasks.css'])
@endpush

@section('content')
	@php
		$taskCollection = collect($tasks ?? []);
		$totalTasks = $taskCollection->count();
		$completedTasks = $taskCollection->where('is_completed', true)->count();
		$pendingTasks = max(0, $totalTasks - $completedTasks);
		$completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
	@endphp

	<div class="tasks-page" data-tasks-page>
		@if(session('success'))
			<section class="tasks-flash tasks-flash-success" role="status">
				<strong>Success</strong>
				<span>{{ session('success') }}</span>
			</section>
		@endif

		@if($errors->any())
			<section class="tasks-flash tasks-flash-error" role="alert">
				<strong>Please review the form</strong>
				<span>{{ $errors->first() }}</span>
			</section>
		@endif

		<section class="tasks-hero">
			<div>
				<span class="tasks-kicker">Wedding Planner Workflow</span>
				<h1 class="tasks-title">Tasks List</h1>
				<p class="tasks-subtitle">Track every checklist item from guest coordination to vendor confirmations with one focused board.</p>
			</div>

			<div class="tasks-hero-stats">
				<article>
					<span>Total</span>
					<strong>{{ $totalTasks }}</strong>
				</article>
				<article>
					<span>Completed</span>
					<strong>{{ $completedTasks }}</strong>
				</article>
				<article>
					<span>Progress</span>
					<strong>{{ $completionRate }}%</strong>
				</article>
			</div>
		</section>

		<section class="tasks-toolbar">
			<div class="tasks-toolbar-search">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
					<circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
					<path d="M16 16L20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
				</svg>
				<input type="search" placeholder="Search task name..." data-task-search>
			</div>

			<select class="tasks-filter-select" data-task-status-filter aria-label="Task status">
				<option value="all">Status</option>
				<option value="pending">Pending</option>
				<option value="completed">Completed</option>
				<option value="overdue">Overdue</option>
			</select>

			@if(Route::has('couple.tasks.create'))
				<a href="{{ route('couple.tasks.create') }}" class="tasks-add-btn">
					<span>+</span>
					Add Task
				</a>
			@endif
		</section>

		<section class="tasks-table-wrap">
			<header class="tasks-table-head">
				<h2>Tasks List</h2>
			</header>

			<div class="tasks-table" role="table" aria-label="Wedding tasks">
				<div class="tasks-row tasks-row-head" role="row">
					<span role="columnheader">Status</span>
					<span role="columnheader">Task Name</span>
					<span role="columnheader">Due Date</span>
					<span role="columnheader">Priority</span>
				</div>

				<div class="tasks-table-body" data-task-table-body>
					@forelse($taskCollection as $task)
						@php
							$isCompleted = (bool) $task->is_completed;
							$isOverdue = ! $isCompleted && $task->deadline && $task->deadline->isPast();
							$statusKey = $isCompleted ? 'completed' : ($isOverdue ? 'overdue' : 'pending');
							$priorityMap = [
								\App\Models\Task::PRIORITY_HIGH => ['label' => 'High', 'class' => 'is-high'],
								\App\Models\Task::PRIORITY_MEDIUM => ['label' => 'Medium', 'class' => 'is-medium'],
								\App\Models\Task::PRIORITY_LOW => ['label' => 'Low', 'class' => 'is-low'],
							];
							$priority = $priorityMap[$task->priority] ?? $priorityMap[\App\Models\Task::PRIORITY_LOW];
							$searchText = strtolower($task->task_name);
						@endphp
						<article
							class="tasks-row"
							role="row"
							data-task-row
							data-task-status="{{ $statusKey }}"
							data-task-name="{{ $searchText }}"
						>
							<div class="tasks-status" role="cell">
								@if($isCompleted)
									<span class="tasks-status-dot is-complete" aria-label="Completed">&#10003;</span>
								@elseif($isOverdue)
									<span class="tasks-status-dot is-overdue" aria-label="Overdue">!</span>
								@else
									<span class="tasks-status-dot is-pending" aria-label="Pending"></span>
								@endif
							</div>

							<div class="tasks-name" role="cell">
								@if(Route::has('couple.tasks.show'))
									<a href="{{ route('couple.tasks.show', $task) }}">{{ $task->task_name }}</a>
								@else
									<span>{{ $task->task_name }}</span>
								@endif
								@if($task->description)
									<small>{{ \Illuminate\Support\Str::limit($task->description, 72) }}</small>
								@endif
							</div>

							<div class="tasks-date" role="cell">
								{{ $task->deadline ? $task->deadline->format('j F Y') : 'No due date' }}
							</div>

							<div class="tasks-priority" role="cell">
								<span class="tasks-priority-pill {{ $priority['class'] }}">{{ $priority['label'] }}</span>
							</div>
						</article>
					@empty
						<div class="tasks-empty" data-task-empty>
							<h3>No tasks yet</h3>
							<p>Create your first wedding task to start tracking progress.</p>
						</div>
					@endforelse
				</div>
			</div>

			@if($taskCollection->isNotEmpty())
				<footer class="tasks-pagination" data-task-pagination>
					<button type="button" data-task-page-prev aria-label="Previous page">&#8249;</button>
					<span data-task-page-current>1</span>
					<button type="button" data-task-page-next aria-label="Next page">&#8250;</button>
				</footer>
			@endif
		</section>
	</div>
@endsection

@push('page-scripts')
	@vite(['resources/js/couple/tasks.js'])
@endpush

