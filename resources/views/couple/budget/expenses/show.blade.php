@extends('couple.layout.layout-couple')

@section('title', 'Expense Detail - WebPlan')
@section('page-title', $expense->expense_name)
@section('page-subtitle', 'Review and update this expense record.')

@push('page-styles')
	@vite(['resources/css/couple/budget.css'])
@endpush

@section('content')
	@php
		$expenseAmount = (float) $expense->amount;
		$paidOn = $expense->date_paid ? \Illuminate\Support\Carbon::parse($expense->date_paid)->format('d M Y') : 'No date recorded';
		$receiptUrl = $expense->receipt_url ? asset('storage/' . $expense->receipt_url) : null;
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
					<span class="budget-kicker">Expense Detail</span>
					<h1 class="budget-title">{{ $expense->expense_name }}</h1>
					<p class="budget-subtitle">Manage this item, adjust values, and keep category totals accurate.</p>
				</div>

				<div class="budget-actions">
					@if(Route::has('couple.budget.expenses'))
						<a href="{{ route('couple.budget.expenses', $budgetCategory) }}" class="budget-action-secondary">Back to Expenses</a>
					@endif
				</div>
			</div>
		</section>

		<section class="budget-stats">
			<article class="budget-stat-card">
				<p class="budget-stat-label">Amount</p>
				<p class="budget-stat-value">RM {{ number_format($expenseAmount, 2) }}</p>
			</article>
			<article class="budget-stat-card">
				<p class="budget-stat-label">Date Paid</p>
				<p class="budget-stat-value">{{ $paidOn }}</p>
			</article>
			<article class="budget-stat-card">
				<p class="budget-stat-label">Method</p>
				<p class="budget-stat-value">{{ str_replace('_', ' ', $expense->payment_method ?? 'Not set') }}</p>
			</article>
			<article class="budget-stat-card">
				<p class="budget-stat-label">Category</p>
				<p class="budget-stat-value">{{ $budgetCategory->category_name }}</p>
			</article>
		</section>

		<section class="budget-layout-split">
			<article class="budget-side-card">
				<h4>Edit Expense</h4>
					<form class="budget-form" method="POST" action="{{ route('couple.budget.expenses.update', [$budgetCategory, $expense]) }}" enctype="multipart/form-data">
					@csrf
					@method('PUT')

					<input type="hidden" name="budget_category_id" value="{{ $budgetCategory->id }}">

					<div>
						<label for="expense_name">Expense Name</label>
						<input id="expense_name" name="expense_name" type="text" value="{{ old('expense_name', $expense->expense_name) }}" required>
						@error('expense_name')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="amount">Amount</label>
						<input id="amount" name="amount" type="number" min="0" step="0.01" value="{{ old('amount', $expense->amount) }}" required>
						@error('amount')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="date_paid">Payment Date</label>
						<input id="date_paid" name="date_paid" type="date" value="{{ old('date_paid', optional($expense->date_paid)->format('Y-m-d')) }}">
						@error('date_paid')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="payment_method">Payment Method</label>
						<select id="payment_method" name="payment_method" class="budget-form-select">
							<option value="">Select method</option>
							@foreach(\App\Models\Expense::METHOD as $method)
								<option value="{{ $method }}" @selected(old('payment_method', $expense->payment_method) === $method)>{{ str_replace('_', ' ', $method) }}</option>
							@endforeach
						</select>
						@error('payment_method')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="description">Description</label>
						<textarea id="description" name="description" rows="4" class="budget-form-textarea" placeholder="Optional note about this expense...">{{ old('description', $expense->description) }}</textarea>
						@error('description')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

						<div>
							<label for="receipt">Receipt</label>
							<input id="receipt" name="receipt" type="file" accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg">
							<small class="field-hint">Leave empty to keep the current receipt.</small>
							@error('receipt')
								<p class="field-error">{{ $message }}</p>
							@enderror
						</div>

						@if($receiptUrl)
							<div>
								<label>Current Receipt</label>
								<p><a href="{{ $receiptUrl }}" target="_blank" rel="noopener">View uploaded receipt</a></p>
							</div>
						@endif

					<button type="submit" class="budget-action">Update Expense</button>
				</form>
			</article>

			<article class="budget-side-card">
				<h4>Actions</h4>
				<p>Use these actions to quickly mark this item paid or remove it from the category.</p>

				<div class="budget-side-list">
					@if(Route::has('couple.budget.expenses.complete'))
						<form method="POST" action="{{ route('couple.budget.expenses.complete', [$budgetCategory, $expense]) }}">
							@csrf
							@method('PUT')
							<button type="submit" class="budget-card-link">Mark Paid Today</button>
						</form>
					@endif

					@if(Route::has('couple.budget.expenses.delete'))
						<form method="POST" action="{{ route('couple.budget.expenses.delete', [$budgetCategory, $expense]) }}" onsubmit="return confirm('Delete this expense record?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="budget-card-link budget-card-link-danger">Delete Expense</button>
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

