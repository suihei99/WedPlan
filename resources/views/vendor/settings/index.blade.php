@extends('vendor.layout.layout-vendor')

@section('title', 'Settings - WebPlan')
@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your vendor account and business settings.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css'])
@endpush

@section('content')
    <section class="vendor-card">
        <h3>Vendor Settings</h3>
        <p>This feature is ready for your next integration step.</p>
    </section>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js'])
@endpush
