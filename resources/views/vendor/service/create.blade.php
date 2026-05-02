@extends('vendor.layout.layout-vendor')

@section('title', 'Create Service - WebPlan')
@section('page-title', 'Create Service')
@section('page-subtitle', 'Add a wedding-ready package for couples to discover and book.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/service.css'])
@endpush

@section('content')
    <div class="vendor-service-page">
        <section class="vendor-service-hero">
            <div>
                <span class="vendor-service-kicker">Service Studio</span>
                <h1 class="vendor-service-title">Design a service that feels built for wedding planning.</h1>
                <p class="vendor-service-subtitle">Present clear pricing, service categories, and a polished image so couples can quickly understand what you offer.</p>
                <div class="vendor-service-cta-row">
                    <a href="{{ route('vendor.service.index') }}" class="vendor-service-button-secondary">Back to Service List</a>
                </div>
            </div>

            <div class="vendor-service-metrics">
                <div class="vendor-service-metric">
                    <span>Primary Focus</span>
                    <strong>Wedding Ready</strong>
                    <p>Keep your package focused on couples searching for trusted vendors.</p>
                </div>
                <div class="vendor-service-metric">
                    <span>Best Use</span>
                    <strong>Packages</strong>
                    <p>Great for service bundles, styled shoots, and planning add-ons.</p>
                </div>
            </div>
        </section>

        <section class="vendor-service-form-layout">
            <article class="vendor-service-form-panel">
                <h2 class="vendor-service-form-title">Add New Service</h2>
                <p class="vendor-service-helper">Use wedding-friendly wording that tells couples exactly what is included.</p>

                <form method="POST" action="{{ route('vendor.service.store') }}" enctype="multipart/form-data" class="vendor-service-form-footer">
                    @csrf
                    @include('vendor.service._form', ['service' => $service ?? null])

                    <div class="vendor-service-form-actions">
                        <button type="submit" class="vendor-service-action">Save Service</button>
                        <a href="{{ route('vendor.service.index') }}" class="vendor-service-action-secondary">Cancel</a>
                    </div>
                </form>
            </article>

            <aside class="vendor-service-preview">
                <div class="vendor-service-preview-card">
                    <div class="vendor-service-preview-media">
                        <img id="servicePreviewImage" src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="Service preview" class="vendor-service-preview-image">
                        <div class="vendor-service-card-badge">Preview Image</div>
                    </div>
                    <div class="vendor-service-preview-body">
                        <span class="vendor-service-chip">Live Preview</span>
                        <h3>Couple-facing card</h3>
                        <p>This preview mirrors how the service can feel in your public listing. Keep the image bright, the title direct, and the price easy to scan.</p>
                    </div>
                </div>

                <div class="vendor-service-guide">
                    <h3>Wedding Service Ideas</h3>
                    <p>These categories fit wedding planning businesses and keep the service catalog easy to browse.</p>
                    <ul>
                        <li>Venue and reception packages</li>
                        <li>Catering menus and tasting packages</li>
                        <li>Photography and video coverage</li>
                        <li>Bridal styling and makeup packages</li>
                    </ul>
                </div>
            </aside>
        </section>
    </div>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/service.js'])
@endpush
