@extends('couple.layout.layout-couple')

@section('title', $service->service_name . ' - WebPlan')
@section('page-title', 'Vendor Details')
@section('page-subtitle', 'Learn more about this vendor and their services.')

@push('page-styles')
    @vite(['resources/css/couple/vendorlist.css'])
@endpush

@section('content')
    @php
        $normalizedContact = preg_replace('/\D+/', '', (string) ($vendor->contact_number ?? ''));

        if (str_starts_with($normalizedContact, '0')) {
            $normalizedContact = '60' . substr($normalizedContact, 1);
        }

        $isMalaysiaWhatsapp = preg_match('/^60[1-9]\d{7,9}$/', $normalizedContact) === 1;
        $whatsAppMessage = rawurlencode("Hi {$vendor->business_name}, I'm interested in your {$service->service_name} service.");
        $whatsAppLink = $isMalaysiaWhatsapp ? "https://wa.me/{$normalizedContact}?text={$whatsAppMessage}" : null;
    @endphp

    <div class="vendorlist-detail-page">
        <!-- Back Link -->
        <a href="{{ route('couple.vendorlist.index') }}" class="vendorlist-back-link">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M15.41 7.41L14 6 8 12l6 6 1.41-1.41L9.83 12z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
            </svg>
            Back to Vendors
        </a>

        <!-- Service Details -->
        <section class="vendorlist-detail">
            <div class="vendorlist-detail-image">
                @if($service->image_url_resolved)
                    <img src="{{ $service->image_url_resolved }}" alt="{{ $service->service_name }}" onerror="this.parentElement.classList.add('image-failed')">
                @else
                    <div class="vendorlist-detail-image-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="9" cy="9" r="1.5" fill="currentColor"/>
                            <path d="M3 15l6-6 4 4 8-8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="vendorlist-detail-content">
                <div class="vendorlist-detail-header">
                    <div>
                        <span class="vendorlist-detail-badge">{{ $service->type_service }}</span>
                        <h1 class="vendorlist-detail-title">{{ $service->service_name }}</h1>
                        <p class="vendorlist-detail-vendor">{{ $vendor->business_name }}</p>
                    </div>

                    @if($service->price_estimate)
                        <div class="vendorlist-detail-price">
                            <span class="vendorlist-detail-price-label">Starting from</span>
                            <span class="vendorlist-detail-price-value">RM {{ number_format($service->price_estimate, 0) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Vendor Info -->
                <section class="vendorlist-detail-section">
                    <h2>Vendor Information</h2>
                    <div class="vendorlist-detail-info-grid">
                        <div class="vendorlist-detail-info-item">
                            <span class="vendorlist-detail-info-label">Business Type</span>
                            <span class="vendorlist-detail-info-value">{{ $vendor->business_type }}</span>
                        </div>
                        <div class="vendorlist-detail-info-item">
                            <span class="vendorlist-detail-info-label">Location</span>
                            <span class="vendorlist-detail-info-value">{{ $vendor->address }}</span>
                        </div>
                        <div class="vendorlist-detail-info-item">
                            <span class="vendorlist-detail-info-label">Contact Number</span>
                            <span class="vendorlist-detail-info-value">{{ $vendor->contact_number }}</span>
                        </div>
                    </div>
                </section>

                <!-- Service Description -->
                @if($service->description)
                    <section class="vendorlist-detail-section">
                        <h2>Service Description</h2>
                        <p class="vendorlist-detail-description">{{ $service->description }}</p>
                    </section>
                @endif

                <!-- Contact Section -->
                <section class="vendorlist-detail-section vendorlist-detail-contact">
                    @if($whatsAppLink)
                        <button type="button" class="vendorlist-detail-contact-btn" onclick="window.open('{{ $whatsAppLink }}', '_blank', 'noopener')">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M16.75 13.96c.25.13 1.47.72 1.7.8.23.09.39.13.56-.13.16-.25.64-.8.78-.96.14-.17.29-.19.54-.07.25.13 1.06.39 2.01 1.24.74.66 1.24 1.48 1.38 1.73.15.25.02.39-.11.52-.11.11-.25.29-.38.43-.13.15-.17.25-.26.42-.09.17-.05.32.01.45.07.13.57 1.38.78 1.89.21.5.42.43.56.44.15.01.32.01.48.01.17 0 .44-.06.67-.32.23-.25.87-.87.87-2.13 0-1.25-.91-2.47-1.04-2.63-.13-.17-1.79-2.73-4.34-3.83-.61-.26-1.08-.42-1.45-.53-.61-.2-1.16-.17-1.6-.1-.49.07-1.5.62-1.71 1.21-.21.6-.21 1.1-.15 1.21.07.1.23.17.48.3M12.04 2C6.5 2 2 6.49 2 12.03c0 1.77.46 3.5 1.34 5.03L2 22l5.07-1.33a9.93 9.93 0 0 0 4.97 1.34h.01c5.54 0 10.04-4.5 10.04-10.03A10.01 10.01 0 0 0 12.04 2z"/>
                            </svg>
                            Contact on WhatsApp
                        </button>
                    @else
                        <button type="button" class="vendorlist-detail-contact-btn vendorlist-detail-contact-btn-disabled" disabled title="Vendor contact is not a valid Malaysia WhatsApp number">
                            WhatsApp Unavailable
                        </button>
                    @endif
                </section>
            </div>
        </section>
    </div>

    @vite(['resources/js/couple/vendorlist.js'])
@endsection
