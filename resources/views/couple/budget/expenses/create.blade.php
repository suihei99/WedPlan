@extends('couple.layout.layout-couple')

@section('title', 'Add Expense - WebPlan')
@section('page-title', 'Add Expense')
@section('page-subtitle', 'Create a new expense item for this budget category.')

@push('page-styles')
	@vite(['resources/css/couple/budget.css'])
@endpush

@section('content')
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
					<span class="budget-kicker">Expense Setup</span>
					<h1 class="budget-title">{{ $budgetCategory->category_name }}</h1>
					<p class="budget-subtitle">Record a payment and keep this category spending up to date for your wedding planning dashboard.</p>
				</div>

				<div class="budget-actions">
					@if(Route::has('couple.budget.expenses'))
						<a href="{{ route('couple.budget.expenses', $budgetCategory) }}" class="budget-action-secondary">Back to Expenses</a>
					@endif
				</div>
			</div>
		</section>

		<section class="budget-layout-split">
			<article class="budget-side-card">
				<h4>New Expense Form</h4>
				<form class="budget-form" method="POST" action="{{ route('couple.budget.expenses.add', $budgetCategory) }}" enctype="multipart/form-data">
					@csrf

					<input type="hidden" name="budget_category_id" value="{{ $budgetCategory->id }}">

					<div>
						<label for="expense_name">Expense Name</label>
						<input id="expense_name" name="expense_name" type="text" value="{{ old('expense_name') }}" placeholder="Deposit, vendor payment, accessories..." required>
						@error('expense_name')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="amount">Amount</label>
						<input id="amount" name="amount" type="number" min="0" step="0.01" value="{{ old('amount') }}" placeholder="0.00" required>
						@error('amount')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="date_paid">Payment Date</label>
						<input id="date_paid" name="date_paid" type="date" value="{{ old('date_paid') }}">
						@error('date_paid')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="payment_method">Payment Method</label>
						<select id="payment_method" name="payment_method" class="budget-form-select">
							<option value="">Select method</option>
							@foreach(\App\Models\Expense::METHOD as $method)
								<option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ str_replace('_', ' ', $method) }}</option>
							@endforeach
						</select>
						@error('payment_method')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="description">Description</label>
						<textarea id="description" name="description" rows="4" class="budget-form-textarea" placeholder="Optional note about this expense...">{{ old('description') }}</textarea>
						@error('description')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="receipt">Receipt</label>
						<input id="receipt" name="receipt" type="file" accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg">
						<small class="field-hint">Upload a PDF, PNG, JPG, or JPEG receipt up to 5 MB.</small>
						@error('receipt')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>

					<button type="submit" class="budget-action">Save Expense</button>
				</form>
			</article>

			<article class="budget-side-card">
				<h4>Category Snapshot</h4>
				<p>Keep each entry specific so reports and spending percentages stay accurate.</p>
				<div class="budget-side-list">
					<div class="budget-side-item">
						<div>
							<strong>Category</strong>
							<small>{{ $budgetCategory->category_name }}</small>
						</div>
					</div>
					<div class="budget-side-item">
						<div>
							<strong>Allocated</strong>
							<small>RM {{ number_format((float) $budgetCategory->allocated_amount, 2) }}</small>
						</div>
					</div>
					<div class="budget-side-item">
						<div>
							<strong>Current spent</strong>
							<small>RM {{ number_format((float) $budgetCategory->total_spent, 2) }}</small>
						</div>
					</div>
				</div>
			</article>
		</section>
	</div>
@endsection

@push('page-scripts')
	@vite(['resources/js/couple/budget.js'])
@endpush
