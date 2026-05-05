@extends('admin.layout.layout-admin')

@section('title', 'User Details - WebPlan')
@section('page-title', 'User Details')
@section('page-subtitle', 'Inspect a user account and change access if required.')

@section('content')
	@php
		$roleLabel = match ($user->role) {
			\App\Models\User::ROLE_ADMIN => 'Admin',
			\App\Models\User::ROLE_VENDOR => 'Vendor',
			default => 'Couple',
		};

		$profileName = $user->isVendor() ? ($user->vendor?->business_name ?? 'Vendor profile not set') : ($user->isCouple() ? trim(($user->couple?->partner_1_name ?? 'Couple') . ($user->couple?->partner_2_name ? ' & ' . $user->couple?->partner_2_name : '')) : 'Administrator');
		$statusClass = $user->is_active ? 'is-approved' : 'is-inactive';
	@endphp

	<div class="admin-settings-page">
		<section class="admin-hero-card">
			<div>
				<span class="admin-kicker">User account</span>
				<h1>{{ $user->email }}</h1>
				<p>{{ $roleLabel }} · {{ $profileName }}</p>

				<div class="admin-hero-actions">
					@if(! $user->isAdmin())
						<form method="POST" action="{{ route('admin.users.status', $user) }}">
							@csrf
							@method('PUT')
							<button type="submit" class="admin-primary-btn">{{ $user->is_active ? 'Deactivate user' : 'Activate user' }}</button>
						</form>
					@endif
					<a href="{{ route('admin.users.index') }}" class="admin-secondary-btn">Back to users</a>
				</div>
			</div>

			<div class="admin-hero-summary">
				<div class="admin-summary-tile">
					<span>Role</span>
					<strong>{{ $roleLabel }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Status</span>
					<strong class="admin-inline-badge {{ $statusClass }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Created</span>
					<strong>{{ $user->created_at?->format('M d, Y') ?? 'Unknown' }}</strong>
				</div>
			</div>
		</section>

		<section class="admin-panel-grid">
			<article class="admin-panel">
				<div class="admin-panel-head">
					<div>
						<h2>Profile summary</h2>
						<p>Account data linked to the current role.</p>
					</div>
				</div>

				<div class="admin-status-list">
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Email</strong>
							<span>{{ $user->email }}</span>
						</div>
					</div>
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Role</strong>
							<span>{{ $roleLabel }}</span>
						</div>
					</div>
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Linked profile</strong>
							<span>{{ $profileName }}</span>
						</div>
					</div>
				</div>
			</article>

			<article class="admin-panel">
				<div class="admin-panel-head">
					<div>
						<h2>Access control</h2>
						<p>Only non-admin accounts can be toggled here.</p>
					</div>
				</div>

				<div class="admin-status-list">
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Account status</strong>
							<span class="admin-status-badge {{ $statusClass }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
						</div>
					</div>
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Verification note</strong>
							<span>{{ $user->isAdmin() ? 'Admins always remain active.' : 'Use the toggle action to change access.' }}</span>
						</div>
					</div>
				</div>
			</article>
		</section>
	</div>
@endsection
