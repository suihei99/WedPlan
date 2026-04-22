@extends('couple.layout.layout-couple')

@section('title', 'Budget Category - WebPlan')
@section('page-title', $budgetCategory->category_name)
@section('page-subtitle', 'View category details, spending, and available balance.')

@push('page-styles')
	@vite(['resources/css/couple/budget.css'])
@endpush

@section('content')
	@php
		$allocatedAmount = (float) $budgetCategory->allocated_amount;
		$spentAmount = (float) $budgetCategory->total_spent;
		$remainingAmount = (float) $budgetCategory->remaining_budget;
		$usage = $allocatedAmount > 0 ? min(100, ($spentAmount / $allocatedAmount) * 100) : 0;
		$expenseCount = $budgetCategory->expenses()->count();
	@endphp

	<div class="budget-page">
		@if(session('success'))
			<section class="budget-flash budget-flash-success" role="status">
				<strong>Success</strong>
				<span>{{ session('success') }}</span>
			</section>
		@endif

		@if($errors->any())
			<section class="budget-flash budget-flash-error" role="alert">
				<strong>Please review the form</strong>
				<span>{{ $errors->first() }}</span>
			</section>
		@endif

		<section class="budget-hero">
			<div class="budget-hero-top">
				<div>
					<span class="budget-kicker">Budget Category</span>
					<h1 class="budget-title">{{ $budgetCategory->category_name }}</h1>
					<p class="budget-subtitle">A focused view of this wedding budget bucket, with live totals and expense tracking.</p>
				</div>
				<div class="budget-actions">
					@if(Route::has('couple.budget.index'))
						<a href="{{ route('couple.budget.index') }}" class="budget-action-secondary">Back to Budget</a>
					@endif
				</div>
			</div>
		</section>

		<section class="budget-stats">
			<article class="budget-stat-card">
				<p class="budget-stat-label">Allocated</p>
				<p class="budget-stat-value">RM {{ number_format($allocatedAmount, 2) }}</p>
			</article>
			<article class="budget-stat-card">
				<p class="budget-stat-label">Spent</p>
				<p class="budget-stat-value">RM {{ number_format($spentAmount, 2) }}</p>
			</article>
			<article class="budget-stat-card">
				<p class="budget-stat-label">Remaining</p>
				<p class="budget-stat-value">RM {{ number_format($remainingAmount, 2) }}</p>
			</article>
			<article class="budget-stat-card">
				<p class="budget-stat-label">Expenses</p>
				<p class="budget-stat-value">{{ $expenseCount }}</p>
			</article>
		</section>

		<section class="budget-progress-panel">
			<div class="budget-progress-head">
				<h2 class="budget-progress-title">Category Usage</h2>
				<span class="budget-pill">{{ round($usage) }}% used</span>
			</div>
			<div class="budget-progress-track">
				<span class="budget-progress-fill" data-budget-progress="{{ $usage }}"></span>
			</div>
			<div class="budget-progress-foot">
				<span>{{ $budgetCategory->is_overspent ? 'This category is over budget' : 'This category is on track' }}</span>
				<span>{{ $budgetCategory->is_overspent ? 'Overspent by RM ' . number_format(abs($remainingAmount), 2) : 'RM ' . number_format($remainingAmount, 2) . ' left' }}</span>
			</div>
		</section>

		<section class="budget-layout-split">
			<article class="budget-side-card">
				<h4>Category Details</h4>
				<div class="budget-side-list">
					<div class="budget-side-item">
						<div>
							<strong>Category name</strong>
							<small>{{ $budgetCategory->category_name }}</small>
						</div>
					</div>
					<div class="budget-side-item">
						<div>
							<strong>Allocated amount</strong>
							<small>RM {{ number_format($allocatedAmount, 2) }}</small>
						</div>
					</div>
					<div class="budget-side-item">
						<div>
							<strong>Current spend</strong>
							<small>RM {{ number_format($spentAmount, 2) }}</small>
						</div>
					</div>
				</div>
			</article>

			<article class="budget-side-card">
				<h4>Manage Category</h4>
				<p>Update details, open expense records, or remove this category when no longer needed.</p>

				<form class="budget-form" method="POST" action="{{ route('couple.budget.update', $budgetCategory) }}">
					@csrf
					@method('PUT')
					<div>
						<label for="category_name">Category Name</label>
						<input id="category_name" name="category_name" type="text" value="{{ old('category_name', $budgetCategory->category_name) }}" required>
						@error('category_name')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label for="allocated_amount">Allocated Amount</label>
						<input id="allocated_amount" name="allocated_amount" type="number" min="0" step="0.01" value="{{ old('allocated_amount', $budgetCategory->allocated_amount) }}" required>
						@error('allocated_amount')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>
					<button type="submit" class="budget-action">Update Category</button>
				</form>

				<div class="budget-side-list">
					@if(Route::has('couple.budget.expenses'))
						<a class="budget-card-link" href="{{ route('couple.budget.expenses', $budgetCategory) }}">View expenses</a>
					@endif
					@if(Route::has('couple.budget.expenses.create'))
						<a class="budget-card-link" href="{{ route('couple.budget.expenses.create', $budgetCategory) }}">Add expense</a>
					@endif
					@if(Route::has('couple.budget.destroy'))
						<form method="POST" action="{{ route('couple.budget.destroy', $budgetCategory) }}" onsubmit="return confirm('Delete this category and all related expenses?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="budget-card-link budget-card-link-danger">Delete category</button>
						</form>
					@endif
				</div>
			</article>
		</section>
	</div>
@endsection

@push('page-scripts')
	@vite(['resources/js/couple/budget.js'])
@endpush
