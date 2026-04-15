@extends('couple.layout.layout-couple')

@section('title', 'Add Budget Category - WebPlan')
@section('page-title', 'Add Budget Category')
@section('page-subtitle', 'Create a new wedding budget category and allocate funds.')

@push('page-styles')
	@vite(['resources/css/couple/budget.css'])
@endpush

@section('content')
	<div class="budget-page">
		<section class="budget-hero">
			<div class="budget-hero-top">
				<div>
					<span class="budget-kicker">Budget Setup</span>
					<h1 class="budget-title">Create Category</h1>
					<p class="budget-subtitle">Add a planning bucket for venue, catering, photography, attire, gifts, or any other wedding cost.</p>
				</div>

				@if(Route::has('couple.budget.index'))
					<div class="budget-actions">
						<a href="{{ route('couple.budget.index') }}" class="budget-action-secondary">Back to Budget</a>
					</div>
				@endif
			</div>
		</section>

		<section class="budget-layout-split">
			<article class="budget-side-card">
				<h4>New Category Form</h4>
				<form class="budget-form" method="POST" action="{{ route('couple.budget.store') }}">
					@csrf
					<div>
						<label for="category_name">Category Name</label>
						<input id="category_name" name="category_name" type="text" value="{{ old('category_name') }}" placeholder="Venue, Catering, Photography..." required>
						@error('category_name')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label for="allocated_amount">Allocated Amount</label>
						<input id="allocated_amount" name="allocated_amount" type="number" min="0" step="0.01" value="{{ old('allocated_amount') }}" placeholder="0.00" required>
						@error('allocated_amount')
							<p class="field-error">{{ $message }}</p>
						@enderror
					</div>
					<button type="submit" class="budget-action">Save Category</button>
				</form>
			</article>

			<article class="budget-side-card">
				<h4>Planning Tips</h4>
				<p>Use clear category names so your spending dashboard stays easy to read for both partners.</p>
				<div class="budget-side-list">
					<div class="budget-side-item">
						<div>
							<strong>Venue</strong>
							<small>Reception, hall, and ceremony expenses</small>
						</div>
					</div>
					<div class="budget-side-item">
						<div>
							<strong>Catering</strong>
							<small>Food, drinks, and dessert table</small>
						</div>
					</div>
					<div class="budget-side-item">
						<div>
							<strong>Photography</strong>
							<small>Photo, video, and editing packages</small>
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
<h1>Add Budget Category</h1>
<p>Use this page to create a new budget category.</p>
