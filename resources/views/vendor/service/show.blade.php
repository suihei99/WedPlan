@php
    use Illuminate\Support\Str;

    $imageUrl = $service->image_url
        ? ((str_starts_with($service->image_url, 'http://') || str_starts_with($service->image_url, 'https://')) ? $service->image_url : asset('storage/' . ltrim($service->image_url, '/')))
        : asset('assets/icons/WebPlan_logo.webp');
@endphp

@extends('vendor.layout.layout-vendor')

@section('title', 'Service Details - WebPlan')
@section('page-title', 'Service Details')
@section('page-subtitle', 'Review and manage a single wedding service listing.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/service.css'])
@endpush

@section('content')
    <div class="vendor-service-page">
        <section class="vendor-service-hero">
            <div>
                <span class="vendor-service-kicker">Service Profile</span>
                <h1 class="vendor-service-title">{{ $service->service_name }}</h1>
                <p class="vendor-service-subtitle">{{ $service->description ?: 'This service is ready to be expanded with wedding-ready details, pricing, and a polished image.' }}</p>
                <div class="vendor-service-cta-row">
                    <a href="{{ route('vendor.service.edit', $service) }}" class="vendor-service-button">Edit Service</a>
                    <a href="{{ route('vendor.service.index') }}" class="vendor-service-button-secondary">Back to Service List</a>
                </div>
            </div>

            <div class="vendor-service-metrics">
                <div class="vendor-service-metric">
                    <span>Category</span>
                    <strong>{{ $service->type_service }}</strong>
                    <p>Use a category that matches how couples search for your service.</p>
                </div>
                <div class="vendor-service-metric">
                    <span>Estimate</span>
                    <strong>RM {{ number_format((float) $service->price_estimate, 2) }}</strong>
                    <p>Make pricing easy to scan alongside other wedding vendor options.</p>
                </div>
            </div>
        </section>

        <section class="vendor-service-list-area">
            <article class="vendor-service-detail">
                <div class="vendor-service-preview-card">
                    <div class="vendor-service-preview-media">
                        <img src="{{ $imageUrl }}" alt="{{ $service->service_name }}" class="vendor-service-preview-image">
                    </div>
                    <div class="vendor-service-preview-body">
                        <span class="vendor-service-chip">{{ $service->type_service }}</span>
                        <h2 class="vendor-service-detail-title">{{ $service->service_name }}</h2>
                        <p class="vendor-service-detail-copy">{{ $service->description ?: 'No description has been added yet.' }}</p>
                    </div>
                </div>
            </article>

            <aside class="vendor-service-side">
                <div class="vendor-service-guide">
                    <h3>Service Snapshot</h3>
                    <p>Keep this listing aligned with your actual wedding package so couples can trust the details at a glance.</p>
                    <div class="vendor-service-quick-list" style="margin-top: 0.9rem;">
                        <div class="vendor-service-quick-item">
                            <div>
                                <strong>Service Name</strong>
                                <small>{{ $service->service_name }}</small>
                            </div>
                        </div>
                        <div class="vendor-service-quick-item">
                            <div>
                                <strong>Service Type</strong>
                                <small>{{ $service->type_service }}</small>
                            </div>
                        </div>
                        <div class="vendor-service-quick-item">
                            <div>
                                <strong>Price Estimate</strong>
                                <small>RM {{ number_format((float) $service->price_estimate, 2) }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('vendor.service.destroy', $service) }}" data-service-delete>
                    @csrf
                    @method('DELETE')
                    <div class="vendor-service-form-actions" style="margin-top: 0;">
                        <button type="submit" class="vendor-service-action-danger">Delete Service</button>
                    </div>
                </form>
            </aside>
        </section>
    </div>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/service.js'])
@endpush
