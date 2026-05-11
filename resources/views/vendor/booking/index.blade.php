@extends('vendor.layout.layout-vendor')

@section('title', 'Booking - WebPlan')
@section('page-title', 'Booking')
@section('page-subtitle', 'Manage booking requests, confirmations, and updates in one place.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/service.css', 'resources/css/vendor/booking.css'])
@endpush

@section('content')
    @php
        $bookingItems = $bookings ?? collect();
        $summaryData = $summary ?? [];
        $bookingStatusOptions = $statusOptions ?? [];
    @endphp

    <div class="vendor-service-page booking-page" data-booking-page>
        <section class="vendor-service-hero booking-hero">
            <div>
                <span class="vendor-service-kicker">Booking Manager</span>
                <h1 class="vendor-service-title">Track every booking with a clear, elegant workflow.</h1>
                <p class="vendor-service-subtitle">Review couple requests, update booking status, and keep your calendar, messages, and notifications aligned.</p>

                <div class="vendor-service-cta-row">
                    <a href="{{ route('vendor.booking.create') }}" class="vendor-service-button">Add Booking</a>
                    <a href="{{ route('vendor.dashboard') }}" class="vendor-service-button-secondary">Back To Dashboard</a>
                </div>
            </div>

            <div class="vendor-service-metrics booking-metrics">
                <article class="vendor-service-metric booking-metric">
                    <span>Total</span>
                    <strong>{{ $summaryData['total_bookings'] ?? 0 }}</strong>
                    <p>Booking records managed by your vendor account.</p>
                </article>
                <article class="vendor-service-metric booking-metric">
                    <span>Confirmed</span>
                    <strong>{{ $summaryData['confirmed_bookings'] ?? 0 }}</strong>
                    <p>Bookings that are marked ready and confirmed.</p>
                </article>
                <article class="vendor-service-metric booking-metric">
                    <span>Pending</span>
                    <strong>{{ $summaryData['pending_bookings'] ?? 0 }}</strong>
                    <p>Requests waiting for a follow-up or approval.</p>
                </article>
                <article class="vendor-service-metric booking-metric">
                    <span>Upcoming</span>
                    <strong>{{ $summaryData['upcoming_bookings'] ?? 0 }}</strong>
                    <p>Scheduled bookings coming on or after today.</p>
                </article>
            </div>
        </section>

        <section class="booking-toolbar">
            <div class="booking-toolbar-field booking-toolbar-search">
                <label for="booking-search">Search</label>
                <input id="booking-search" type="search" placeholder="Search couple or service" data-booking-search>
            </div>

            <div class="booking-toolbar-field">
                <label for="booking-status-filter">Status</label>
                <select id="booking-status-filter" data-booking-status-filter>
                    <option value="">All Status</option>
                    @foreach($bookingStatusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <a href="{{ route('vendor.booking.create') }}" class="booking-toolbar-action">New Booking</a>
        </section>

        <section class="booking-list-shell">
            <div class="booking-list">
                @forelse($bookingItems as $booking)
                    @php
                        $coupleProfile = $booking->couple?->couple;
                        $coupleLabel = trim(($coupleProfile?->partner_1_name ?? 'Couple').' & '.($coupleProfile?->partner_2_name ?? 'Guest'));
                        $bookingDate = $booking->booking_date ? $booking->booking_date->format('d M Y') : 'No date';
                        $bookingStatusLabel = $booking->status ? 'Confirmed' : 'Pending';
                        $bookingStatusValue = $booking->status ? '1' : '0';
                    @endphp

                    <article class="booking-card" data-booking-card data-booking-name="{{ $coupleLabel }} {{ $booking->type_service }}" data-booking-status="{{ $bookingStatusValue }}">
                        <div class="booking-card-header">
                            <div>
                                <span class="booking-card-kicker">{{ $booking->type_service }}</span>
                                <h3>{{ $coupleLabel }}</h3>
                                <p>{{ $coupleProfile?->user?->email ?? 'No couple account linked' }}</p>
                            </div>
                            <span class="booking-status-badge {{ $booking->status ? 'is-confirmed' : 'is-pending' }}">{{ $bookingStatusLabel }}</span>
                        </div>

                        <div class="booking-card-grid">
                            <div class="booking-card-field">
                                <span>Date</span>
                                <strong>{{ $bookingDate }}</strong>
                            </div>
                            <div class="booking-card-field">
                                <span>Status</span>
                                <strong>{{ $bookingStatusLabel }}</strong>
                            </div>
                            <div class="booking-card-field booking-card-field-full">
                                <span>Notes</span>
                                <strong>{{ $booking->notes ?: 'No notes added yet.' }}</strong>
                            </div>
                        </div>

                        <div class="booking-card-actions">
                            <a href="{{ route('vendor.booking.show', $booking) }}" class="booking-card-link">View</a>
                            <a href="{{ route('vendor.booking.edit', $booking) }}" class="booking-card-link">Edit</a>
                            <form method="POST" action="{{ route('vendor.booking.destroy', $booking) }}" data-booking-delete>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="booking-card-link is-danger">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="booking-empty-state" data-booking-empty-state>
                        <h3>No bookings yet</h3>
                        <p>Create the first booking to start tracking confirmations and notifications.</p>
                        <a href="{{ route('vendor.booking.create') }}" class="booking-button">Add Booking</a>
                    </div>
                @endforelse
            </div>

            <aside class="booking-side-panel">
                <article class="booking-guide-card">
                    <h3>Workflow</h3>
                    <ol>
                        <li>Create or update the booking for the couple.</li>
                        <li>Set the booking status and review notes.</li>
                        <li>Couples receive email and push notifications automatically.</li>
                    </ol>
                </article>

                <article class="booking-guide-card">
                    <h3>Status Legend</h3>
                    <div class="booking-legend-list">
                        <span><i class="dot is-confirmed"></i> Confirmed</span>
                        <span><i class="dot is-pending"></i> Pending</span>
                    </div>
                </article>
            </aside>
        </section>

        @if(method_exists($bookingItems, 'links'))
            <div class="booking-pagination">
                {{ $bookingItems->links() }}
            </div>
        @endif
    </div>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/booking.js'])
@endpush
