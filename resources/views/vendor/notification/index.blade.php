@extends('vendor.layout.layout-vendor')

@section('title', 'Notifications - WebPlan')
@section('page-title', 'Notifications')
@section('page-subtitle', 'Vendor notifications and booking alerts will appear here.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css'])
@endpush

@section('content')
    <section class="vendor-card">
        <h3>Notifications</h3>
        <p>This feature is ready for your next integration step.</p>
    </section>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js'])
@endpush
