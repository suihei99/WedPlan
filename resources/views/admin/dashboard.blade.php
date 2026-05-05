@extends('admin.layout.layout-admin')

@section('title', 'Admin Dashboard - WebPlan')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Track couples, vendors, bookings, and approvals from one control center.')

@section('content')
	@php
		$statCards = [
			['label' => 'Couples', 'value' => $stats['couples'], 'note' => 'Wedding planners currently using the app.'],
			['label' => 'Vendors', 'value' => $stats['vendors'], 'note' => 'Registered service businesses in the marketplace.'],
			['label' => 'Pending Vendors', 'value' => $stats['pending_vendors'], 'note' => 'Waiting for review before going live.'],
			['label' => 'Approved Vendors', 'value' => $stats['approved_vendors'], 'note' => 'Verified vendors visible to couples.'],
			['label' => 'Active Users', 'value' => $stats['active_users'], 'note' => 'Accounts currently allowed to sign in.'],
			['label' => 'Inactive Users', 'value' => $stats['inactive_users'], 'note' => 'Accounts blocked until reactivated.'],
			['label' => 'Bookings', 'value' => $stats['bookings'], 'note' => 'Vendor booking requests across the platform.'],
			['label' => 'Wedding Tasks', 'value' => $stats['tasks'], 'note' => 'Planning tasks created by couples.'],
		];
	@endphp

	<div class="admin-dashboard-page">
		<section class="admin-hero-card">
			<div>
				<span class="admin-kicker">Wedding Planner Control Center</span>
				<h1>Keep couples, vendors, and bookings moving.</h1>
				<p>Monitor approvals, user activity, and wedding progress with a clean workspace built for fast decisions.</p>

				<div class="admin-hero-actions">
					<a href="{{ route('admin.vendors.index') }}" class="admin-primary-btn">Review Vendors</a>
					<a href="{{ route('admin.users.index') }}" class="admin-secondary-btn">Manage Users</a>
					<a href="{{ route('admin.settings.index') }}" class="admin-tertiary-btn">Security Settings</a>
				</div>
			</div>

			<div class="admin-hero-summary">
				<div class="admin-summary-tile">
					<span>Upcoming weddings</span>
					<strong>{{ $stats['upcoming_weddings'] }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Rejected vendors</span>
					<strong>{{ $stats['rejected_vendors'] }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Admin accounts</span>
					<strong>{{ $stats['admins'] }}</strong>
				</div>
			</div>
		</section>

		<section class="admin-stat-grid">
			@foreach($statCards as $card)
				<article class="admin-stat-card">
					<span class="admin-stat-label">{{ $card['label'] }}</span>
					<strong>{{ $card['value'] }}</strong>
					<p>{{ $card['note'] }}</p>
				</article>
			@endforeach
		</section>

		<section class="admin-panel-grid">
			<article class="admin-panel">
				<div class="admin-panel-head">
					<div>
						<h2>Pending vendor approvals</h2>
						<p>Review new businesses before they appear to couples.</p>
					</div>
					<a href="{{ route('admin.vendors.index') }}" class="admin-inline-link">View all</a>
				</div>

				<div class="admin-panel-list">
					@forelse($pendingVendors as $vendor)
						<div class="admin-list-item" data-admin-searchable data-admin-search-text="{{ strtolower($vendor->business_name . ' ' . ($vendor->business_type ?? '') . ' ' . ($vendor->user?->email ?? '')) }}">
							<div class="admin-list-copy">
								<strong>{{ $vendor->business_name ?? 'Unnamed vendor' }}</strong>
								<span>{{ $vendor->business_type ?? 'Business type not set' }} · {{ $vendor->user?->email ?? 'No account email' }}</span>
							</div>
							<span class="admin-status-badge is-pending">Pending</span>
						</div>
					@empty
						<div class="admin-empty-state">No vendors are waiting for review right now.</div>
					@endforelse
				</div>
			</article>

			<article class="admin-panel">
				<div class="admin-panel-head">
					<div>
						<h2>Upcoming weddings</h2>
						<p>See the next ceremonies the platform is helping coordinate.</p>
					</div>
				</div>

				<div class="admin-panel-list">
					@forelse($upcomingWeddings as $couple)
						<div class="admin-list-item" data-admin-searchable data-admin-search-text="{{ strtolower(($couple->partner_1_name ?? '') . ' ' . ($couple->partner_2_name ?? '') . ' ' . ($couple->wedding_venue ?? '')) }}">
							<div class="admin-list-copy">
								<strong>{{ trim(($couple->partner_1_name ?? 'Couple') . ($couple->partner_2_name ? ' & ' . $couple->partner_2_name : '')) }}</strong>
								<span>{{ $couple->wedding_date?->format('M d, Y') ?? 'Date not set' }} · {{ $couple->wedding_venue ?? 'Venue not set' }}</span>
							</div>
							<span class="admin-chip is-approved">Planned</span>
						</div>
					@empty
						<div class="admin-empty-state">No weddings have been scheduled yet.</div>
					@endforelse
				</div>
			</article>
		</section>

		<section class="admin-table-card">
			<div class="admin-table-head">
				<div>
					<h2>Recent users</h2>
					<p>Quickly spot who signed up last and whether their account is active.</p>
				</div>
				<a href="{{ route('admin.users.index') }}" class="admin-inline-link">Manage users</a>
			</div>

			<div class="admin-table-wrap">
				<table class="admin-table">
					<thead>
						<tr>
							<th>User</th>
							<th>Role</th>
							<th>Status</th>
							<th>Profile</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($recentUsers as $user)
							@php
								$roleLabel = match ($user->role) {
									\App\Models\User::ROLE_ADMIN => 'Admin',
									\App\Models\User::ROLE_VENDOR => 'Vendor',
									default => 'Couple',
								};

								$statusClass = $user->is_active ? 'is-approved' : 'is-inactive';
								$statusLabel = $user->is_active ? 'Active' : 'Inactive';

								$profileName = $user->isVendor() ? ($user->vendor?->business_name ?? 'Vendor profile not set') : ($user->isCouple() ? trim(($user->couple?->partner_1_name ?? 'Couple') . ($user->couple?->partner_2_name ? ' & ' . $user->couple?->partner_2_name : '')) : 'Administrator');
							@endphp
							<tr class="admin-table-row" data-admin-searchable data-admin-search-text="{{ strtolower($user->email . ' ' . $roleLabel . ' ' . $profileName) }}">
								<td>{{ $user->email }}</td>
								<td><span class="admin-inline-badge">{{ $roleLabel }}</span></td>
								<td><span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
								<td>{{ $profileName }}</td>
								<td><a href="{{ route('admin.users.show', $user) }}" class="admin-table-link">View</a></td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</section>
	</div>
@endsection
