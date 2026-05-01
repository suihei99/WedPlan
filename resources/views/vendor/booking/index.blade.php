@extends('vendor.layout.layout-vendor')

@section('title', 'Booking - WebPlan')
@section('page-title', 'Booking')
@section('page-subtitle', 'Track all booking requests and confirmations.')

@push('page-styles')
	@vite(['resources/css/vendor/dashboard.css'])
@endpush

@section('content')
	<section class="vendor-card">
		<h3>Booking List</h3>
		<p>This section is ready for booking management integration.</p>
	</section>
@endsection

@push('page-scripts')
	@vite(['resources/js/vendor/dashboard.js'])
@endpush
