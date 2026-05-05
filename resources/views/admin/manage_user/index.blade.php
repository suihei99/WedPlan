@extends('admin.layout.layout-admin')

@section('title', 'Manage Users - WebPlan')
@section('page-title', 'User Management')
@section('page-subtitle', 'Keep accounts active, review roles, and open each wedding profile quickly.')

@section('content')
	<div class="admin-index-page">
		<section class="admin-hero-card">
			<div>
				<span class="admin-kicker">User moderation</span>
				<h1>Manage every account from one dashboard.</h1>
				<p>Search by email, inspect the linked couple or vendor profile, and turn access on or off without leaving the admin area.</p>

				<div class="admin-hero-actions">
					<a href="#userGrid" class="admin-primary-btn">Browse users</a>
					<a href="{{ route('admin.dashboard') }}" class="admin-secondary-btn">Back to dashboard</a>
				</div>
			</div>

			<div class="admin-hero-summary">
				<div class="admin-summary-tile">
					<span>Total users</span>
					<strong>{{ $summary['total'] }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Active accounts</span>
					<strong>{{ $summary['active'] }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Inactive accounts</span>
					<strong>{{ $summary['inactive'] }}</strong>
				</div>
			</div>
		</section>

		@if(session('success'))
			<section class="admin-flash admin-flash-success" role="status">{{ session('success') }}</section>
		@endif

		<section class="admin-table-card">
			<div class="admin-table-head">
				<div>
					<h2>User directory</h2>
					<p>Filter the list live while you review roles and account status.</p>
				</div>
				<input type="search" class="admin-search" placeholder="Search email, role, or profile..." data-admin-search-input data-admin-search-scope="#userGrid">
			</div>

			<div class="admin-table-wrap" id="userGrid">
				<table class="admin-table">
					<thead>
						<tr>
							<th>Email</th>
							<th>Role</th>
							<th>Status</th>
							<th>Profile</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($users as $user)
							@php
								$roleLabel = match ($user->role) {
									\App\Models\User::ROLE_ADMIN => 'Admin',
									\App\Models\User::ROLE_VENDOR => 'Vendor',
									default => 'Couple',
								};

								$profileName = $user->isVendor() ? ($user->vendor?->business_name ?? 'Vendor profile not set') : ($user->isCouple() ? trim(($user->couple?->partner_1_name ?? 'Couple') . ($user->couple?->partner_2_name ? ' & ' . $user->couple?->partner_2_name : '')) : 'Administrator');
								$statusClass = $user->is_active ? 'is-approved' : 'is-inactive';
								$searchText = strtolower($user->email . ' ' . $roleLabel . ' ' . $profileName);
							@endphp

							<tr class="admin-table-row" data-admin-searchable data-admin-search-text="{{ $searchText }}">
								<td>{{ $user->email }}</td>
								<td><span class="admin-inline-badge">{{ $roleLabel }}</span></td>
								<td><span class="admin-status-badge {{ $statusClass }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
								<td>{{ $profileName }}</td>
								<td>
									<div class="admin-action-group">
										<a href="{{ route('admin.users.show', $user) }}" class="admin-table-link">View</a>
										@if(! $user->isAdmin())
											<form method="POST" action="{{ route('admin.users.status', $user) }}">
												@csrf
												@method('PUT')
												<button type="submit" class="admin-action-button-secondary">{{ $user->is_active ? 'Deactivate' : 'Activate' }}</button>
											</form>
										@endif
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			@if($users->hasPages())
				<div class="admin-toolbar" style="margin-top: 1rem; justify-content: space-between;">
					{{ $users->links() }}
				</div>
			@endif
		</section>
	</div>
@endsection
