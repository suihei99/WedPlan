<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'Vendor Dashboard - WebPlan')</title>
	<link rel="icon" type="image/webp" href="{{ asset('assets/icons/WebPlan_logo.webp') }}">
	@vite(['resources/css/app.css', 'resources/css/vendor/layout-vendor.css'])
	@stack('page-styles')
</head>
<body class="vendor-app">
	@php
			$vendorUser = auth()->user();
			$vendorProfile = $vendor ?? $vendorUser?->vendor;
			$vendorProfileImage = $vendorUser?->profile_photo_url ?? $vendorProfile?->user?->profile_photo_url;
			$vendorInitial = strtoupper(substr($vendorProfile?->business_name ?? $vendorUser?->email ?? 'V', 0, 1));
	@endphp

	<div class="vendor-app-shell">
		<aside class="vendor-sidebar" data-vendor-sidebar>
			<div class="vendor-sidebar-logo">
				<a href="{{ route('vendor.dashboard') }}" class="vendor-logo-link">
					<img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WebPlan logo" class="vendor-logo-image">
					<div>
						<h1>WedPlan</h1>
						<p>Wedding Planner</p>
					</div>
				</a>
			</div>

			<section class="vendor-profile-card" aria-label="Vendor profile">
				<div class="vendor-profile-avatar">
					@if($vendorProfileImage)
						<img src="{{ $vendorProfileImage }}" alt="{{ $vendorProfile?->business_name ?? ($vendorUser?->email ?? 'Vendor') }} profile photo" class="vendor-profile-avatar-image">
					@else
						{{ $vendorInitial }}
					@endif
				</div>
				<div>
					<h2>{{ $vendorProfile?->business_name ?? ($vendorUser?->email ?? 'Vendor') }}</h2>
					<p>Vendor</p>
				</div>
			</section>

			<nav class="vendor-nav" aria-label="Vendor navigation">
				@php
					$vendorMenu = [
						['icon' => 'home', 'label' => 'Dashboard', 'route' => 'vendor.dashboard', 'current' => 'vendor.dashboard'],
						['icon' => 'store', 'label' => 'Service', 'route' => 'vendor.service.index', 'current' => 'vendor.service.*'],
						['icon' => 'checklist', 'label' => 'Booking', 'route' => 'vendor.booking.index', 'current' => 'vendor.booking.*'],
						['icon' => 'bell', 'label' => 'Notification', 'route' => 'vendor.notification.index', 'current' => 'vendor.notification.*'],
						['icon' => 'settings', 'label' => 'Settings', 'route' => 'vendor.settings.index', 'current' => 'vendor.settings.*'],
					];
				@endphp

				@foreach($vendorMenu as $item)
					@php
						$hasRoute = Route::has($item['route']);
						$isActive = request()->routeIs($item['current']);
					@endphp

					@if($hasRoute)
						<a href="{{ route($item['route']) }}" class="vendor-nav-item{{ $isActive ? ' active' : '' }}">
							@include('vendor.layout.partials.nav-icon', ['icon' => $item['icon']])
							<span>{{ $item['label'] }}</span>
						</a>
					@else
						<span class="vendor-nav-item disabled" aria-disabled="true">
							@include('vendor.layout.partials.nav-icon', ['icon' => $item['icon']])
							<span>{{ $item['label'] }}</span>
						</span>
					@endif
				@endforeach
			</nav>

			<div class="vendor-sidebar-logout">
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					<button type="submit" class="vendor-logout-button">Logout</button>
				</form>
			</div>
		</aside>

		<main class="vendor-main">
			<header class="vendor-topbar">
				<button type="button" class="vendor-mobile-menu-button" data-vendor-mobile-menu-toggle aria-label="Toggle vendor navigation">
					<span></span>
					<span></span>
					<span></span>
				</button>

				<div class="vendor-topbar-headings">
					<h2>@yield('page-title', 'Dashboard')</h2>
					<p>@yield('page-subtitle', '')</p>
				</div>

				<button type="button" class="vendor-topbar-bell" aria-label="Notifications">
					<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
						<path d="M15 17H9C7.343 17 6 15.657 6 14V10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14C18 15.657 16.657 17 15 17Z" stroke="currentColor" stroke-width="1.8"/>
						<path d="M4 17H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M10 20C10.355 20.622 11.078 21 12 21C12.922 21 13.645 20.622 14 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
					</svg>
				</button>
			</header>

			<section class="vendor-page-content">
				@yield('content')
			</section>
		</main>
	</div>

	@vite(['resources/js/vendor/layout-vendor.js'])
	@stack('page-scripts')
</body>
</html>
