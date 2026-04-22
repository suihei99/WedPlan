@extends('couple.layout.layout-couple')

@section('title', 'Create Task - WebPlan')
@section('page-title', 'Create Task')
@section('page-subtitle', 'Add a new wedding checklist item and keep your planning on track.')

@push('page-styles')
	@vite(['resources/css/couple/tasks.css'])
@endpush

@section('content')
	<div class="tasks-page tasks-page-form">
		@if($errors->any())
			<section class="tasks-flash tasks-flash-error" role="alert">
				<strong>Please review the form</strong>
				<span>{{ $errors->first() }}</span>
			</section>
		@endif

		<section class="tasks-hero tasks-hero-form">
			<div>
				<span class="tasks-kicker">Wedding Planner Workflow</span>
				<h1 class="tasks-title">Create a Task</h1>
				<p class="tasks-subtitle">Capture deadlines, priorities, and notes so both partners stay aligned throughout planning.</p>
			</div>

			<div class="tasks-actions">
				@if(Route::has('couple.tasks.index'))
					<a href="{{ route('couple.tasks.index') }}" class="tasks-secondary-btn">Back to Task List</a>
				@endif
			</div>
		</section>

		<section class="tasks-form-card">
			<h2>Task Details</h2>

			<form method="POST" action="{{ route('couple.tasks.store') }}" class="tasks-form-grid">
				@csrf

				<div class="tasks-field tasks-field-full">
					<label for="task_name">Task Name</label>
					<input id="task_name" name="task_name" type="text" value="{{ old('task_name') }}" placeholder="Finalize guest list" required>
					@error('task_name')
						<p class="field-error">{{ $message }}</p>
					@enderror
				</div>

				<div class="tasks-field tasks-field-full">
					<label for="description">Description</label>
					<textarea id="description" name="description" rows="4" placeholder="Optional details for this task...">{{ old('description') }}</textarea>
					@error('description')
						<p class="field-error">{{ $message }}</p>
					@enderror
				</div>

				<div class="tasks-field">
					<label for="deadline">Due Date</label>
					<input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}">
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
							<option value="{{ $priority }}" @selected((int) old('priority', \App\Models\Task::PRIORITY_LOW) === $priority)>{{ $priorityLabel }}</option>
						@endforeach
					</select>
					@error('priority')
						<p class="field-error">{{ $message }}</p>
					@enderror
				</div>

				<div class="tasks-field tasks-field-full">
					<label class="tasks-checkbox">
						<input type="hidden" name="is_completed" value="0">
						<input type="checkbox" name="is_completed" value="1" @checked(old('is_completed'))>
						<span>Mark as completed</span>
					</label>
				</div>

				<div class="tasks-form-actions tasks-field-full">
					<button type="submit" class="tasks-add-btn">Save Task</button>
				</div>
			</form>
		</section>
	</div>
@endsection

@push('page-scripts')
	@vite(['resources/js/couple/tasks.js'])
@endpush

