@extends('vendor.layout.layout-vendor')

@section('title', 'Vendor Dashboard - WebPlan')
@section('page-title', 'Welcome, ' . ($vendor->business_name ?? 'Vendor'))
@section('page-subtitle', 'Your Business Status Is ' . ucfirst($vendor->status ?? 'pending'))

@push('page-styles')
	@vite(['resources/css/vendor/dashboard.css'])
@endpush

@section('content')
	@php
		$bookingItems = collect($bookings ?? []);
		$serviceItems = collect($services ?? []);
	@endphp

	<div class="vendor-dashboard-page" data-vendor-dashboard data-booking-dates='@json($dashboardData['booking_dates'] ?? [])'>
		<section class="vendor-calendar-panel">
			<div class="vendor-calendar-head">
				<h3>Booking Date</h3>
				<div class="vendor-calendar-controls">
					<button type="button" data-vendor-calendar-prev aria-label="Previous month">&#9664;</button>
					<p data-vendor-calendar-label>Month Year</p>
					<button type="button" data-vendor-calendar-next aria-label="Next month">&#9654;</button>
				</div>
			</div>

			<div class="vendor-calendar-grid" data-vendor-calendar-grid aria-label="Booking calendar"></div>
		</section>

		<section class="vendor-dashboard-grid">
			<article class="vendor-card">
				<header class="vendor-card-head">
					<h4>Summary Booking</h4>
					@if(Route::has('vendor.booking.index'))
						<a href="{{ route('vendor.booking.index') }}">See More</a>
					@endif
				</header>
				<div class="vendor-card-list">
					@forelse($bookingItems->take(4) as $booking)
						@php
							$coupleProfile = $booking->couple?->couple;
							$coupleNameA = $coupleProfile?->partner_1_name ?? 'Couple';
							$coupleNameB = $coupleProfile?->partner_2_name ?? 'Guest';
						@endphp
						<div class="vendor-list-row">
							<span>{{ $coupleNameA }} & {{ $coupleNameB }}</span>
							<strong>{{ $booking->booking_date ? $booking->booking_date->format('d M Y') : 'No date' }}</strong>
						</div>
					@empty
						<p class="vendor-empty-copy">No bookings available yet.</p>
					@endforelse
				</div>
			</article>

			<article class="vendor-card">
				<header class="vendor-card-head">
					<h4>Service List</h4>
					@if(Route::has('vendor.service.index'))
						<a href="{{ route('vendor.service.index') }}">See More</a>
					@endif
				</header>
				<div class="vendor-card-list">
					@forelse($serviceItems->take(4) as $service)
						<div class="vendor-list-row">
							<span>{{ $service->service_name }}</span>
							<strong>RM {{ number_format((float) $service->price_estimate, 2) }}</strong>
						</div>
					@empty
						<p class="vendor-empty-copy">No services available yet.</p>
					@endforelse
				</div>
			</article>
		</section>
	</div>
@endsection

@push('page-scripts')
	@vite(['resources/js/vendor/dashboard.js'])
@endpush
