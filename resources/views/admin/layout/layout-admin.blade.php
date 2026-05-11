<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'Admin Dashboard - WebPlan')</title>
	<link rel="icon" type="image/webp" href="{{ asset('assets/icons/WebPlan_logo.webp') }}">
	@vite(['resources/css/app.css', 'resources/css/admin/admin.css'])
	@stack('page-styles')
</head>
<body class="admin-app">
	@php
		$adminUser = auth()->user();
		$adminMenu = [
			['icon' => 'home', 'label' => 'Dashboard', 'route' => 'admin.dashboard', 'current' => 'admin.dashboard'],
			['icon' => 'shield', 'label' => 'Vendors', 'route' => 'admin.vendors.index', 'current' => 'admin.vendors.*'],
			['icon' => 'users', 'label' => 'Users', 'route' => 'admin.users.index', 'current' => 'admin.users.*'],
			['icon' => 'settings', 'label' => 'Settings', 'route' => 'admin.settings.index', 'current' => 'admin.settings.*'],
		];
	@endphp

	<div class="admin-app-shell">
		<aside class="admin-sidebar" data-admin-sidebar>
			<div class="admin-sidebar-logo">
				<a href="{{ route('admin.dashboard') }}" class="admin-logo-link">
					<img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WebPlan logo" class="admin-logo-image">
					<div>
						<h1>WedPlan</h1>
						<p>Admin Control</p>
					</div>
				</a>
			</div>

			<section class="admin-profile-card" aria-label="Admin profile">
				<div class="admin-profile-avatar">{{ strtoupper(substr($adminUser?->email ?? 'A', 0, 1)) }}</div>
				<div>
					<h2>{{ $adminUser?->email ?? 'Administrator' }}</h2>
					<p>Administrator</p>
				</div>
			</section>

			<nav class="admin-nav" aria-label="Admin navigation">
				@foreach($adminMenu as $item)
					@php
						$hasRoute = Route::has($item['route']);
						$isActive = request()->routeIs($item['current']);
					@endphp

					@if($hasRoute)
						<a href="{{ route($item['route']) }}" class="admin-nav-item{{ $isActive ? ' active' : '' }}">
							@include('admin.layout.partials.nav-icon', ['icon' => $item['icon']])
							<span>{{ $item['label'] }}</span>
						</a>
					@else
						<span class="admin-nav-item disabled" aria-disabled="true">
							@include('admin.layout.partials.nav-icon', ['icon' => $item['icon']])
							<span>{{ $item['label'] }}</span>
						</span>
					@endif
				@endforeach
			</nav>

			<div class="admin-sidebar-logout">
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					<button type="submit" class="admin-logout-button">Logout</button>
				</form>
			</div>
		</aside>

		<main class="admin-main">
			<header class="admin-topbar">
				<button type="button" class="admin-mobile-menu-button" data-admin-mobile-menu-toggle aria-label="Toggle admin navigation">
					<span></span>
					<span></span>
					<span></span>
				</button>

				<div class="admin-topbar-headings">
					<h2>@yield('page-title', 'Dashboard')</h2>
					<p>@yield('page-subtitle', '')</p>
				</div>

				<div class="admin-topbar-meta">
					<span class="admin-topbar-pill">Control Center</span>
				</div>
			</header>

			<section class="admin-page-content">
				@yield('content')
			</section>
		</main>
	</div>

	@vite(['resources/js/admin/admin.js'])
	@stack('page-scripts')
</body>
</html>
