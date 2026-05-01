@extends('vendor.layout.layout-vendor')

@section('title', 'Service - WebPlan')
@section('page-title', 'Service')
@section('page-subtitle', 'Manage your vendor services and wedding packages.')

@push('page-styles')
	@vite(['resources/css/vendor/dashboard.css'])
@endpush

@section('content')
	<section class="vendor-card">
		<h3>Service List</h3>
		<p>This section is ready for service management integration.</p>
	</section>
@endsection

@push('page-scripts')
	@vite(['resources/js/vendor/dashboard.js'])
@endpush
