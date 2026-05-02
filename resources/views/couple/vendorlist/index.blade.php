@extends('couple.layout.layout-couple')

@section('title', 'Vendor Directory - WebPlan')
@section('page-title', 'Vendor Directory')
@section('page-subtitle', 'Browse and connect with approved vendors for your wedding.')

@push('page-styles')
    @vite(['resources/css/couple/vendorlist.css'])
@endpush

@section('content')
    <div class="vendorlist-page" data-vendorlist-page>
        <!-- Hero Section -->
        <section class="vendorlist-hero">
            <div>
                <span class="vendorlist-kicker">Vendor Services</span>
                <h1 class="vendorlist-title">Vendor Directory</h1>
                <p class="vendorlist-subtitle">Discover and connect with trusted vendors who have been verified and approved to help make your wedding day special.</p>
            </div>
        </section>

        <!-- Toolbar -->
        <section class="vendorlist-toolbar">
            <div class="vendorlist-toolbar-search">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 16L20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <form method="GET" action="{{ route('couple.vendorlist.index') }}" class="vendorlist-search-form" data-vendorlist-search-form>
                    <input type="search" name="search" placeholder="Search Business Name..." value="{{ request('search') }}" data-vendor-search>
                </form>
            </div>

            <div class="vendorlist-toolbar-filters">
                <form method="GET" action="{{ route('couple.vendorlist.index') }}" class="vendorlist-filter-form" data-vendorlist-filter-form>
                    <select name="type_service" class="vendorlist-filter-select" aria-label="Service type filter" data-vendor-type-filter>
                        <option value="">Type of Service</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type }}" @selected(request('type_service') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </section>

        <!-- Services Grid -->
        <section class="vendorlist-grid" data-vendorlist-grid>
            @forelse($services as $service)
                @php
                    $vendor = $service->user->vendor;
                    $normalizedContact = preg_replace('/\D+/', '', (string) ($vendor->contact_number ?? ''));

                    if (str_starts_with($normalizedContact, '0')) {
                        $normalizedContact = '60' . substr($normalizedContact, 1);
                    }

                    $isMalaysiaWhatsapp = preg_match('/^60[1-9]\d{7,9}$/', $normalizedContact) === 1;
                    $whatsAppMessage = rawurlencode("Hi {$vendor->business_name}, I'm interested in your {$service->service_name} service.");
                    $whatsAppLink = $isMalaysiaWhatsapp ? "https://wa.me/{$normalizedContact}?text={$whatsAppMessage}" : null;
                @endphp
                <article class="vendorlist-card" data-vendor-card>
                    <a href="{{ route('couple.vendorlist.show', $service) }}" class="vendorlist-card-link">
                        <div class="vendorlist-card-image">
                            @if($service->image_url_resolved)
                                <img src="{{ $service->image_url_resolved }}" alt="{{ $service->service_name }}" loading="lazy" onerror="this.parentElement.classList.add('image-failed')">
                            @else
                                <div class="vendorlist-card-image-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="9" cy="9" r="1.5" fill="currentColor"/>
                                        <path d="M3 15l6-6 4 4 8-8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            @endif
                            <span class="vendorlist-card-badge">{{ $service->type_service }}</span>
                        </div>

                        <div class="vendorlist-card-content">
                            <h3 class="vendorlist-card-service-name">{{ $service->service_name }}</h3>
                            <p class="vendorlist-card-vendor-name">{{ $vendor->business_name }}</p>
                            
                            <div class="vendorlist-card-meta">
                                <span class="vendorlist-card-location">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 2C7.6 2 4 5.6 4 10c0 6 8 12 8 12s8-6 8-12c0-4.4-3.6-8-8-8zm0 11c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z" fill="currentColor"/>
                                    </svg>
                                    {{ $vendor->address ? Str::limit($vendor->address, 30) : 'Location not provided' }}
                                </span>
                            </div>

                            @if($service->price_estimate)
                                <p class="vendorlist-card-price">
                                    RM {{ number_format($service->price_estimate, 0) }}
                                    <span class="vendorlist-card-price-label">/ Package</span>
                                </p>
                            @endif
                        </div>

                        <div class="vendorlist-card-footer">
                            @if($whatsAppLink)
                                <button type="button" class="vendorlist-card-contact-btn" onclick="window.open('{{ $whatsAppLink }}', '_blank', 'noopener')">
                                    WhatsApp
                                </button>
                            @else
                                <button type="button" class="vendorlist-card-contact-btn vendorlist-card-contact-btn-disabled" disabled title="Vendor contact is not a valid Malaysia WhatsApp number">
                                    WhatsApp Unavailable
                                </button>
                            @endif
                        </div>
                    </a>
                </article>
            @empty
                <div class="vendorlist-empty" data-vendorlist-empty>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <h3>No Vendors Found</h3>
                    <p>Try adjusting your search or filter criteria.</p>
                </div>
            @endforelse
        </section>

        <!-- Pagination -->
        @if($services->hasPages())
            <section class="vendorlist-pagination" data-vendorlist-pagination>
                @if($services->onFirstPage())
                    <button class="vendorlist-pagination-prev" disabled aria-label="Previous page">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.41 7.41L14 6 8 12l6 6 1.41-1.41L9.83 12z"/>
                        </svg>
                    </button>
                @else
                    <a href="{{ $services->previousPageUrl() }}" class="vendorlist-pagination-prev" aria-label="Previous page">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.41 7.41L14 6 8 12l6 6 1.41-1.41L9.83 12z"/>
                        </svg>
                    </a>
                @endif

                <div class="vendorlist-pagination-info">
                    <span>{{ $services->currentPage() }}</span>
                    <span class="vendorlist-pagination-separator">of</span>
                    <span>{{ $services->lastPage() }}</span>
                </div>

                @if($services->hasMorePages())
                    <a href="{{ $services->nextPageUrl() }}" class="vendorlist-pagination-next" aria-label="Next page">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L14.17 12z"/>
                        </svg>
                    </a>
                @else
                    <button class="vendorlist-pagination-next" disabled aria-label="Next page">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L14.17 12z"/>
                        </svg>
                    </button>
                @endif
            </section>
        @endif
    </div>

    @vite(['resources/js/couple/vendorlist.js'])
@endsection
