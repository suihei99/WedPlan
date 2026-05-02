@extends('vendor.layout.layout-vendor')

@section('title', 'Edit Service - WebPlan')
@section('page-title', 'Edit Service')
@section('page-subtitle', 'Refine the package details, price, or image shown to couples.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/service.css'])
@endpush

@section('content')
    <div class="vendor-service-page">
        <section class="vendor-service-hero">
            <div>
                <span class="vendor-service-kicker">Update Listing</span>
                <h1 class="vendor-service-title">Keep your service page accurate and wedding ready.</h1>
                <p class="vendor-service-subtitle">Update package names, pricing, or the image preview so couples see the most current offer.</p>
                <div class="vendor-service-cta-row">
                    <a href="{{ route('vendor.service.index') }}" class="vendor-service-button-secondary">Back to Service List</a>
                </div>
            </div>

            <div class="vendor-service-metrics">
                <div class="vendor-service-metric">
                    <span>Current Type</span>
                    <strong>{{ $service->type_service }}</strong>
                    <p>Maintain a category that couples can quickly filter and compare.</p>
                </div>
                <div class="vendor-service-metric">
                    <span>Current Price</span>
                    <strong>RM {{ number_format((float) $service->price_estimate, 2) }}</strong>
                    <p>Keep pricing easy to understand and aligned with your packages.</p>
                </div>
            </div>
        </section>

        <section class="vendor-service-form-layout">
            <article class="vendor-service-form-panel">
                <h2 class="vendor-service-form-title">Edit Service</h2>
                <p class="vendor-service-helper">Tighten the copy if the service has changed or if you want a cleaner wedding-planning presentation.</p>

                <form method="POST" action="{{ route('vendor.service.update', $service) }}" enctype="multipart/form-data" class="vendor-service-form-footer">
                    @csrf
                    @method('PUT')
                    @include('vendor.service._form', ['service' => $service])

                    <div class="vendor-service-form-actions">
                        <button type="submit" class="vendor-service-action">Update Service</button>
                        <a href="{{ route('vendor.service.show', $service) }}" class="vendor-service-action-secondary">View Service</a>
                        <a href="{{ route('vendor.service.index') }}" class="vendor-service-action-secondary">Cancel</a>
                    </div>
                </form>
            </article>

            <aside class="vendor-service-preview">
                <div class="vendor-service-preview-card">
                    <div class="vendor-service-preview-media">
                        @php
                            $previewImage = $service->image_url
                                ? ((str_starts_with($service->image_url, 'http://') || str_starts_with($service->image_url, 'https://')) ? $service->image_url : asset('storage/' . ltrim($service->image_url, '/')))
                                : asset('assets/icons/WebPlan_logo.webp');
                        @endphp
                        <img id="servicePreviewImage" src="{{ $previewImage }}" alt="Service preview" class="vendor-service-preview-image">
                        <div class="vendor-service-card-badge">Current Image</div>
                    </div>
                    <div class="vendor-service-preview-body">
                        <span class="vendor-service-chip">Live Preview</span>
                        <h3>{{ $service->service_name }}</h3>
                        <p>{{ $service->description ?: 'No description has been added yet. Use a short note about what couples get from this service.' }}</p>
                    </div>
                </div>

                <div class="vendor-service-guide">
                    <h3>Editing Tips</h3>
                    <p>Make the title specific, keep the description short, and use a clean image that matches the wedding style you want to attract.</p>
                    <ul>
                        <li>Use a package name that sounds couple-friendly</li>
                        <li>Keep price estimates clear and honest</li>
                        <li>Use a hero image that feels polished and bright</li>
                    </ul>
                </div>
            </aside>
        </section>
    </div>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/service.js'])
@endpush
