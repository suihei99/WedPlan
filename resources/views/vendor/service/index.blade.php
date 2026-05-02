@extends('vendor.layout.layout-vendor')

@php
	use Illuminate\Support\Str;
@endphp

@section('title', 'Services - WebPlan')
@section('page-title', 'Services')
@section('page-subtitle', 'Manage wedding-ready services, pricing, and package details.')

@push('page-styles')
	@vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/service.css'])
@endpush

@section('content')
	<div class="vendor-service-page">
		<section class="vendor-service-hero">
			<div>
				<span class="vendor-service-kicker">Wedding Service Manager</span>
				<h1 class="vendor-service-title">Build a service catalog couples can trust.</h1>
				<p class="vendor-service-subtitle">Organize your venue, catering, styling, and planning packages in a layout that feels polished, modern, and easy to scan on mobile or web.</p>

				<div class="vendor-service-cta-row">
					<a href="{{ route('vendor.service.create') }}" class="vendor-service-button">Add Service</a>
					<a href="#serviceList" class="vendor-service-button-secondary">Browse Services</a>
				</div>
			</div>

			<div class="vendor-service-metrics">
				<div class="vendor-service-metric">
					<span>Total Services</span>
					<strong>{{ $summary['total_services'] }}</strong>
					<p>All active listings linked to your vendor account.</p>
				</div>
				<div class="vendor-service-metric">
					<span>Average Price</span>
					<strong>RM {{ number_format((float) $summary['average_price'], 2) }}</strong>
					<p>Helpful for keeping packages aligned with market expectations.</p>
				</div>
				<div class="vendor-service-metric">
					<span>Top Categories</span>
					<p>
						@forelse($summary['top_categories'] as $category => $count)
							{{ $category }} · {{ $count }}@if(! $loop->last)<br>@endif
						@empty
							No categories yet.
						@endforelse
					</p>
				</div>
			</div>
		</section>

		<section class="vendor-service-toolbar">
			<input type="search" class="vendor-service-search" placeholder="Search service name or category..." data-service-search>

			<select class="vendor-service-select" data-service-type-filter>
				<option value="">All service types</option>
				@foreach ($serviceTypes as $type)
					<option value="{{ Str::lower($type) }}">{{ $type }}</option>
				@endforeach
			</select>

			<a href="{{ route('vendor.service.create') }}" class="vendor-service-button">Add Service</a>
		</section>

		<section class="vendor-service-list-area" id="serviceList">
			<div class="vendor-service-list">
				@forelse ($services as $service)
					@php
						$imageUrl = $service->image_url
							? ((str_starts_with($service->image_url, 'http://') || str_starts_with($service->image_url, 'https://')) ? $service->image_url : asset('storage/' . ltrim($service->image_url, '/')))
							: asset('assets/icons/WebPlan_logo.webp');
					@endphp

					<article class="vendor-service-card" data-service-card data-service-name="{{ Str::lower($service->service_name) }}" data-service-type="{{ Str::lower($service->type_service) }}">
						<div class="vendor-service-card-media">
							<img src="{{ $imageUrl }}" alt="{{ $service->service_name }}">
							<span class="vendor-service-card-badge">{{ $service->type_service }}</span>
						</div>

						<div class="vendor-service-card-body">
							<div class="vendor-service-card-head">
								<div>
									<h3>{{ $service->service_name }}</h3>
									<div class="vendor-service-card-meta" style="margin-top: 0.45rem;">
										<span class="vendor-service-chip">Wedding Ready</span>
										<span class="vendor-service-chip">{{ $service->type_service }}</span>
									</div>
								</div>

								<div class="vendor-service-price">RM {{ number_format((float) $service->price_estimate, 2) }}</div>
							</div>

							<p class="vendor-service-card-copy">{{ $service->description ?: 'Use a short description to explain what couples get, what is included, and why this package stands out.' }}</p>

							<div class="vendor-service-actions">
								<a href="{{ route('vendor.service.show', $service) }}" class="vendor-service-action-secondary">View</a>
								<a href="{{ route('vendor.service.edit', $service) }}" class="vendor-service-action">Edit</a>
								<form method="POST" action="{{ route('vendor.service.destroy', $service) }}" data-service-delete>
									@csrf
									@method('DELETE')
									<button type="submit" class="vendor-service-action-danger">Delete</button>
								</form>
							</div>
						</div>
					</article>
				@empty
					<div class="vendor-service-empty" data-service-empty-state>
						<strong>No services added yet</strong>
						<p>Create your first wedding package so couples can start discovering your offerings.</p>
						<div class="vendor-service-cta-row" style="justify-content: center;">
							<a href="{{ route('vendor.service.create') }}" class="vendor-service-button">Add Your First Service</a>
						</div>
					</div>
				@endforelse

				<div class="vendor-service-empty" data-service-empty-state hidden>
					<strong>No matching services</strong>
					<p>Try a different search term or clear the filter to show all listings again.</p>
				</div>

				@if ($services->hasPages())
					<div class="vendor-service-pagination">
						<a href="{{ $services->previousPageUrl() ?? '#' }}" class="{{ $services->onFirstPage() ? 'is-disabled' : '' }}" @if($services->onFirstPage()) aria-disabled="true" tabindex="-1" @endif>Previous</a>
						<span>Page {{ $services->currentPage() }} of {{ $services->lastPage() }}</span>
						<a href="{{ $services->nextPageUrl() ?? '#' }}" class="{{ $services->hasMorePages() ? '' : 'is-disabled' }}" @if(! $services->hasMorePages()) aria-disabled="true" tabindex="-1" @endif>Next</a>
					</div>
				@endif
			</div>

			<aside class="vendor-service-side">
				<div class="vendor-service-guide">
					<h3>Wedding Planning Focus</h3>
					<p>Structure your services around what couples are actually comparing when they plan a wedding.</p>
					<ul>
						<li>Use package names that mention the real deliverable</li>
						<li>Keep the cover image bright and simple</li>
						<li>Show an estimated price even if the final quote varies</li>
					</ul>
				</div>

				<div class="vendor-service-guide">
					<h3>Featured Services</h3>
					<div class="vendor-service-quick-list">
						@forelse ($summary['featured'] as $featuredService)
							<div class="vendor-service-quick-item">
								<div>
									<strong>{{ $featuredService['service_name'] }}</strong>
									<small>{{ $featuredService['type_service'] }}</small>
								</div>
								<span>RM {{ number_format((float) $featuredService['price_estimate'], 2) }}</span>
							</div>
						@empty
							<div class="vendor-service-empty" style="padding: 0.9rem; text-align: left;">
								<strong style="font-size: 1rem;">No featured services yet</strong>
								<p>Add at least one service to surface it here.</p>
							</div>
						@endforelse
					</div>
				</div>
			</aside>
		</section>
	</div>
@endsection

@push('page-scripts')
	@vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/service.js'])
@endpush
