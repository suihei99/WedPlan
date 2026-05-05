@extends('admin.layout.layout-admin')

@section('title', 'Manage Vendors - WebPlan')
@section('page-title', 'Vendor Verification')
@section('page-subtitle', 'Approve, reject, and inspect wedding vendors before they reach couples.')

@section('content')
	<div class="admin-index-page">
		<section class="admin-hero-card">
			<div>
				<span class="admin-kicker">Vendor moderation</span>
				<h1>Review every vendor with one clear workflow.</h1>
				<p>Search through business profiles, verify documents, and keep the marketplace trusted for couples planning their wedding.</p>

				<div class="admin-hero-actions">
					<a href="#vendorGrid" class="admin-primary-btn">Browse vendors</a>
					<a href="{{ route('admin.dashboard') }}" class="admin-secondary-btn">Back to dashboard</a>
				</div>
			</div>

			<div class="admin-hero-summary">
				<div class="admin-summary-tile">
					<span>Total vendors</span>
					<strong>{{ $summary['total'] }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Pending review</span>
					<strong>{{ $summary['pending'] }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Approved live</span>
					<strong>{{ $summary['approved'] }}</strong>
				</div>
			</div>
		</section>

		@if(session('success'))
			<section class="admin-flash admin-flash-success" role="status">{{ session('success') }}</section>
		@endif

		<section class="admin-table-card">
			<div class="admin-table-head">
				<div>
					<h2>Vendor directory</h2>
					<p>Use search to narrow down the vendor list quickly.</p>
				</div>
				<input type="search" class="admin-search" placeholder="Search business name, type, or email..." data-admin-search-input data-admin-search-scope="#vendorGrid">
			</div>

			<div class="admin-data-list" id="vendorGrid">
				@forelse($vendors as $vendor)
					@php
						$statusClass = match ($vendor->status) {
							\App\Models\Vendor::STATUS_APPROVED => 'is-approved',
							\App\Models\Vendor::STATUS_REJECTED => 'is-rejected',
							default => 'is-pending',
						};

						$statusLabel = ucfirst($vendor->status ?? 'pending');
						$searchText = strtolower(trim(($vendor->business_name ?? '') . ' ' . ($vendor->business_type ?? '') . ' ' . ($vendor->user?->email ?? '')));
					@endphp

					<article class="admin-vendor-row" data-admin-searchable data-admin-search-text="{{ $searchText }}">
						<div class="admin-row-copy">
							<strong>{{ $vendor->business_name ?? 'Unnamed vendor' }}</strong>
							<span>{{ $vendor->business_type ?? 'Business type not set' }} · {{ $vendor->user?->email ?? 'No email provided' }}</span>
						</div>
						<div class="admin-action-group">
							<span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
							<a href="{{ route('admin.vendors.show', $vendor) }}" class="admin-table-link">View</a>
							<form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}">
								@csrf
								@method('PUT')
								<button type="submit" class="admin-action-button">Approve</button>
							</form>
							<form method="POST" action="{{ route('admin.vendors.reject', $vendor) }}">
								@csrf
								@method('PUT')
								<button type="submit" class="admin-action-button-secondary">Reject</button>
							</form>
						</div>
					</article>
				@empty
					<div class="admin-empty-state">No vendors have registered yet.</div>
				@endforelse
			</div>

			@if($vendors->hasPages())
				<div class="admin-toolbar" style="margin-top: 1rem; justify-content: space-between;">
					{{ $vendors->links() }}
				</div>
			@endif
		</section>
	</div>
@endsection
