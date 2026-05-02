@extends('vendor.layout.layout-vendor')

@section('title', 'View Booking - WebPlan')
@section('page-title', 'Booking Details')
@section('page-subtitle', 'Inspect the booking information and manage next steps.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/booking.css'])
@endpush

@section('content')
    @php
        $coupleProfile = $booking->couple?->couple;
        $bookingStatusLabel = $booking->status ? 'Confirmed' : 'Pending';
        $bookingDate = $booking->booking_date ? $booking->booking_date->format('d M Y') : 'No date';
    @endphp

    <section class="booking-detail-hero">
        <div>
            <span class="booking-kicker">{{ $booking->type_service }}</span>
            <h1>{{ trim(($coupleProfile?->partner_1_name ?? 'Couple').' & '.($coupleProfile?->partner_2_name ?? 'Guest')) }}</h1>
            <p>{{ $bookingDate }} · {{ $bookingStatusLabel }}</p>
        </div>
        <span class="booking-status-badge {{ $booking->status ? 'is-confirmed' : 'is-pending' }}">{{ $bookingStatusLabel }}</span>
    </section>

    <section class="booking-detail-grid">
        <article class="booking-detail-card">
            <h3>Booking Overview</h3>
            <dl class="booking-detail-list">
                <div>
                    <dt>Couple</dt>
                    <dd>{{ $coupleProfile?->partner_1_name ?? 'Unknown' }} &amp; {{ $coupleProfile?->partner_2_name ?? 'Guest' }}</dd>
                </div>
                <div>
                    <dt>Email</dt>
                    <dd>{{ $booking->couple?->email ?? 'Not set' }}</dd>
                </div>
                <div>
                    <dt>Service</dt>
                    <dd>{{ $booking->type_service }}</dd>
                </div>
                <div>
                    <dt>Booking Date</dt>
                    <dd>{{ $bookingDate }}</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd>{{ $bookingStatusLabel }}</dd>
                </div>
                <div>
                    <dt>Notes</dt>
                    <dd>{{ $booking->notes ?: 'No notes added yet.' }}</dd>
                </div>
            </dl>
        </article>

        <aside class="booking-detail-side">
            <article class="booking-detail-card">
                <h3>Actions</h3>
                <div class="booking-detail-actions">
                    <a href="{{ route('vendor.booking.edit', $booking) }}" class="booking-button">Edit Booking</a>
                    <form method="POST" action="{{ route('vendor.booking.destroy', $booking) }}" data-booking-delete>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="booking-button-secondary is-danger">Delete Booking</button>
                    </form>
                    <a href="{{ route('vendor.booking.index') }}" class="booking-button-secondary">Back To List</a>
                </div>
            </article>

            <article class="booking-detail-card">
                <h3>Timeline</h3>
                <dl class="booking-detail-list">
                    <div>
                        <dt>Created</dt>
                        <dd>{{ $booking->created_at?->format('d M Y, h:i A') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt>Updated</dt>
                        <dd>{{ $booking->updated_at?->format('d M Y, h:i A') ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </article>
        </aside>
    </section>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/booking.js'])
@endpush