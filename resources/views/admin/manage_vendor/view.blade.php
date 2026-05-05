@extends('admin.layout.layout-admin')

@section('title', 'Vendor Details - WebPlan')
@section('page-title', 'Vendor Details')
@section('page-subtitle', 'Inspect the vendor profile, documents, and approval status.')

@section('content')
	@php
		$statusClass = match ($vendor->status) {
			\App\Models\Vendor::STATUS_APPROVED => 'is-approved',
			\App\Models\Vendor::STATUS_REJECTED => 'is-rejected',
			default => 'is-pending',
		};

		$statusLabel = ucfirst($vendor->status ?? 'pending');
		$documentName = $vendor->business_documents ? basename($vendor->business_documents) : 'No document uploaded';
	@endphp

	<div class="admin-settings-page">
		<section class="admin-hero-card">
			<div>
				<span class="admin-kicker">Vendor profile</span>
				<h1>{{ $vendor->business_name ?? 'Unnamed vendor' }}</h1>
				<p>{{ $vendor->business_type ?? 'Business type not set' }} · {{ $vendor->user?->email ?? 'No linked account' }}</p>

				<div class="admin-hero-actions">
					<form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}">
						@csrf
						@method('PUT')
						<button type="submit" class="admin-primary-btn">Approve vendor</button>
					</form>
					<form method="POST" action="{{ route('admin.vendors.reject', $vendor) }}">
						@csrf
						@method('PUT')
						<button type="submit" class="admin-secondary-btn">Reject vendor</button>
					</form>
					<a href="{{ route('admin.vendors.index') }}" class="admin-tertiary-btn">Back to list</a>
				</div>
			</div>

			<div class="admin-hero-summary">
				<div class="admin-summary-tile">
					<span>Status</span>
					<strong class="admin-inline-badge {{ $statusClass }}">{{ $statusLabel }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Services</span>
					<strong>{{ $vendor->services->count() }}</strong>
				</div>
				<div class="admin-summary-tile">
					<span>Document</span>
					<strong>{{ $vendor->business_documents ? 'Uploaded' : 'Missing' }}</strong>
				</div>
			</div>
		</section>

		<section class="admin-panel-grid">
			<article class="admin-panel">
				<div class="admin-panel-head">
					<div>
						<h2>Business information</h2>
						<p>Key profile details from the vendor registration form.</p>
					</div>
				</div>

				<div class="admin-status-list">
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Contact</strong>
							<span>{{ $vendor->contact_number ?? 'Not set' }}</span>
						</div>
					</div>
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Address</strong>
							<span>{{ $vendor->address ?? 'Not set' }}</span>
						</div>
					</div>
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Business document</strong>
							<span>
								@if($vendor->business_document_url)
									<a href="{{ $vendor->business_document_url }}" target="_blank" rel="noopener" class="admin-document-link">{{ $documentName }}</a>
								@else
									Not uploaded
								@endif
							</span>
						</div>
					</div>
				</div>
			</article>

			<article class="admin-panel">
				<div class="admin-panel-head">
					<div>
						<h2>Linked account</h2>
						<p>Account state used when the vendor signs in.</p>
					</div>
				</div>

				<div class="admin-status-list">
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Email</strong>
							<span>{{ $vendor->user?->email ?? 'Not set' }}</span>
						</div>
					</div>
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Account status</strong>
							<span class="admin-status-badge {{ $vendor->user?->is_active ? 'is-approved' : 'is-inactive' }}">{{ $vendor->user?->is_active ? 'Active' : 'Inactive' }}</span>
						</div>
					</div>
					<div class="admin-status-item">
						<div class="admin-row-title">
							<strong>Created at</strong>
							<span>{{ $vendor->created_at?->format('M d, Y') ?? 'Unknown' }}</span>
						</div>
					</div>
				</div>
			</article>
		</section>
	</div>
@endsection
