@extends('couple.layout.layout-couple')

@section('title', 'Expenses - WebPlan')
@section('page-title', $budgetCategory->category_name . ' Expenses')
@section('page-subtitle', 'Track every payment recorded for this category.')

@push('page-styles')
	@vite(['resources/css/couple/budget.css'])
@endpush

@section('content')
	@php
		$expenses = collect($expenses ?? []);
		$spentAmount = (float) $budgetCategory->total_spent;
		$allocatedAmount = (float) $budgetCategory->allocated_amount;
		$remainingAmount = (float) $budgetCategory->remaining_budget;
		$usage = $allocatedAmount > 0 ? min(100, ($spentAmount / $allocatedAmount) * 100) : 0;
	@endphp

	<div class="budget-page">
		<section class="budget-hero">
			<div class="budget-hero-top">
				<div>
					<span class="budget-kicker">Expense Log</span>
					<h1 class="budget-title">{{ $budgetCategory->category_name }}</h1>
					<p class="budget-subtitle">Manage payments, mark items complete, and keep this budget category synchronized with real wedding costs.</p>
				</div>
				<div class="budget-actions">
					@if(Route::has('couple.budget.expenses.create'))
						<a href="{{ route('couple.budget.expenses.create', $budgetCategory) }}" class="budget-action">Add Expense</a>
					@endif
					@if(Route::has('couple.budget.show'))
						<a href="{{ route('couple.budget.show', $budgetCategory) }}" class="budget-action-secondary">Category Details</a>
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
				<p class="budget-stat-label">Records</p>
				<p class="budget-stat-value">{{ $expenses->count() }}</p>
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
				@if($budgetCategory->is_overspent)
					<span>Overspent by RM {{ number_format($budgetCategory->overspent_amount, 2) }}</span>
				@else
					<span>RM {{ number_format($remainingAmount, 2) }} left</span>
				@endif
			</div>
		</section>

		@if($expenses->isNotEmpty())
			<section class="budget-grid">
				@foreach($expenses as $expense)
					@php
						$expenseName = is_array($expense) ? ($expense['expense_name'] ?? 'Expense') : ($expense->expense_name ?? 'Expense');
						$expenseAmount = is_array($expense) ? (float) ($expense['amount'] ?? 0) : (float) $expense->amount;
						$expenseDate = is_array($expense) ? ($expense['date_paid'] ?? null) : $expense->date_paid;
						$expenseDescription = is_array($expense) ? ($expense['description'] ?? null) : $expense->description;
						$paymentMethod = is_array($expense) ? ($expense['payment_method'] ?? null) : $expense->payment_method;
						$receiptPath = is_array($expense) ? ($expense['receipt_url'] ?? null) : $expense->receipt_url;
						$receiptUrl = $receiptPath ? asset('storage/' . $receiptPath) : null;
						$receiptType = $receiptPath && \Illuminate\Support\Str::endsWith(strtolower($receiptPath), ['.png', '.jpg', '.jpeg']) ? 'image' : 'document';
					@endphp
					<article class="budget-card">
						<div class="budget-card-head">
							<div class="budget-card-title-wrap">
								<div class="budget-card-icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" fill="none">
										<path d="M4 7H20V17H4V7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
										<path d="M8 11H12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
									</svg>
								</div>
								<div class="budget-card-title">
									<h3>{{ $expenseName }}</h3>
									<p>RM {{ number_format($expenseAmount, 2) }}</p>
								</div>
							</div>
							<span class="budget-status is-balanced">{{ $paymentMethod ?? 'Recorded' }}</span>
						</div>

						<div class="budget-card-progress">
							<div class="budget-card-progress-meta">
								<span>{{ $expenseDate ? \Illuminate\Support\Carbon::parse($expenseDate)->format('d M Y') : 'No date recorded' }}</span>
								<span>{{ $expenseDescription ?? 'No description' }}</span>
							</div>
							@if($receiptUrl)
								<div class="budget-card-progress-meta">
									<span>Receipt attached</span>
									<a
										href="{{ $receiptUrl }}"
										class="budget-receipt-link"
										data-receipt-preview
										data-receipt-url="{{ $receiptUrl }}"
										data-receipt-label="{{ $expenseName }} receipt"
										data-receipt-type="{{ $receiptType }}"
									>Open receipt</a>
								</div>
							@endif
						</div>

						<div class="budget-card-foot">
							<div class="budget-card-links">
								@if(Route::has('couple.budget.expenses.show'))
									<a class="budget-card-link" href="{{ route('couple.budget.expenses.show', [$budgetCategory, $expense]) }}">Details</a>
								@endif
								@if(Route::has('couple.budget.expenses.complete'))
									<form method="POST" action="{{ route('couple.budget.expenses.complete', [$budgetCategory, $expense]) }}">
										@csrf
										@method('PUT')
										<button type="submit" class="budget-card-link">Mark Paid</button>
									</form>
								@endif
							</div>
						</div>
					</article>
				@endforeach
			</section>
		@else
			<section class="budget-empty">
				<h3>No expenses yet</h3>
				<p>Add the first expense entry to begin tracking this category.</p>
				@if(Route::has('couple.budget.expenses.create'))
					<a href="{{ route('couple.budget.expenses.create', $budgetCategory) }}" class="budget-action">Add Expense</a>
				@endif
			</section>
		@endif
	</div>

	<div class="budget-receipt-modal" data-receipt-modal aria-hidden="true">
		<div class="budget-receipt-modal-backdrop" data-receipt-close></div>
		<div class="budget-receipt-modal-panel" role="dialog" aria-modal="true" aria-label="Receipt preview">
			<div class="budget-receipt-modal-header">
				<h2 class="budget-receipt-modal-title" data-receipt-title>Receipt preview</h2>
				<div class="budget-receipt-modal-actions">
					<a href="#" target="_blank" rel="noopener" class="budget-receipt-link" data-receipt-download>Open in new tab</a>
					<button type="button" class="budget-receipt-modal-close" data-receipt-close aria-label="Close receipt preview">×</button>
				</div>
			</div>
			<div class="budget-receipt-modal-body">
				<iframe class="budget-receipt-modal-frame" data-receipt-frame hidden></iframe>
				<img class="budget-receipt-modal-image" data-receipt-image hidden alt="Receipt preview">
			</div>
		</div>
	</div>
@endsection

@push('page-scripts')
	@vite(['resources/js/couple/budget.js'])
@endpush

