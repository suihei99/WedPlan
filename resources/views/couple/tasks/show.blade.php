@extends('couple.layout.layout-couple')

@section('title', 'Task Detail - WebPlan')
@section('page-title', 'Task Detail')
@section('page-subtitle', 'Update or remove task details as your wedding plan evolves.')

@push('page-styles')
	@vite(['resources/css/couple/tasks.css'])
@endpush

@section('content')
	@php
		$priorityMap = [
			\App\Models\Task::PRIORITY_HIGH => ['label' => 'High', 'class' => 'is-high'],
			\App\Models\Task::PRIORITY_MEDIUM => ['label' => 'Medium', 'class' => 'is-medium'],
			\App\Models\Task::PRIORITY_LOW => ['label' => 'Low', 'class' => 'is-low'],
		];
		$priorityMeta = $priorityMap[$task->priority] ?? $priorityMap[\App\Models\Task::PRIORITY_LOW];
		$taskStatus = $task->is_completed ? 'Completed' : (($task->deadline && $task->deadline->isPast()) ? 'Overdue' : 'Pending');
	@endphp

	<div class="tasks-page tasks-page-form">
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

		<section class="tasks-hero tasks-hero-form">
			<div>
				<span class="tasks-kicker">Wedding Planner Workflow</span>
				<h1 class="tasks-title">{{ $task->task_name }}</h1>
				<p class="tasks-subtitle">Keep this checklist item accurate, update details, or remove it when no longer needed.</p>
			</div>

			<div class="tasks-actions">
				@if(Route::has('couple.tasks.index'))
					<a href="{{ route('couple.tasks.index') }}" class="tasks-secondary-btn">Back to Task List</a>
				@endif
			</div>
		</section>

		<section class="tasks-stats-grid">
			<article class="tasks-stat-card">
				<p>Status</p>
				<strong>{{ $taskStatus }}</strong>
			</article>
			<article class="tasks-stat-card">
				<p>Priority</p>
				<strong><span class="tasks-priority-pill {{ $priorityMeta['class'] }}">{{ $priorityMeta['label'] }}</span></strong>
			</article>
			<article class="tasks-stat-card">
				<p>Due Date</p>
				<strong>{{ $task->deadline ? $task->deadline->format('j F Y') : 'No due date' }}</strong>
			</article>
		</section>

		<section class="tasks-layout-split">
			<article class="tasks-form-card">
				<h2>Update Task</h2>

				<form method="POST" action="{{ route('couple.tasks.update', $task) }}" class="tasks-form-grid">
					@csrf
					@method('PUT')

					<div class="tasks-field tasks-field-full">
						<label for="task_name">Task Name</label>
						<input id="task_name" name="task_name" type="text" value="{{ old('task_name', $task->task_name) }}" required>
						@error('task_name')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div class="tasks-field tasks-field-full">
						<label for="description">Description</label>
						<textarea id="description" name="description" rows="4" placeholder="Optional details for this task...">{{ old('description', $task->description) }}</textarea>
						@error('description')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div class="tasks-field">
						<label for="deadline">Due Date</label>
						<input id="deadline" name="deadline" type="date" value="{{ old('deadline', optional($task->deadline)->format('Y-m-d')) }}">
						@error('deadline')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div class="tasks-field">
						<label for="priority">Priority</label>
						<select id="priority" name="priority">
							@foreach($priorities as $priority)
								@php
									$priorityLabel = $priority === \App\Models\Task::PRIORITY_HIGH ? 'High' : ($priority === \App\Models\Task::PRIORITY_MEDIUM ? 'Medium' : 'Low');
								@endphp
								<option value="{{ $priority }}" @selected((int) old('priority', $task->priority) === $priority)>{{ $priorityLabel }}</option>
							@endforeach
						</select>
						@error('priority')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div class="tasks-field tasks-field-full">
						<label class="tasks-checkbox">
							<input type="hidden" name="is_completed" value="0">
							<input type="checkbox" name="is_completed" value="1" @checked(old('is_completed', $task->is_completed))>
							<span>Mark as completed</span>
						</label>
					</div>

					<div class="tasks-form-actions tasks-field-full">
						<button type="submit" class="tasks-add-btn">Update Task</button>
					</div>
				</form>
			</article>

			<article class="tasks-side-card">
				<h3>Task Actions</h3>
				<p>Complete this task immediately or remove it from your checklist.</p>

				<div class="tasks-side-actions">
					@if(! $task->is_completed && Route::has('couple.tasks.complete'))
						<form method="POST" action="{{ route('couple.tasks.complete', $task) }}">
							@csrf
							@method('PUT')
							<button type="submit" class="tasks-secondary-btn tasks-full-btn">Mark Completed</button>
						</form>
					@endif

					@if(Route::has('couple.tasks.destroy'))
						<form method="POST" action="{{ route('couple.tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="tasks-danger-btn tasks-full-btn">Delete Task</button>
						</form>
					@endif
				</div>
			</article>
		</section>
	</div>
@endsection

@push('page-scripts')
	@vite(['resources/js/couple/tasks.js'])
@endpush
