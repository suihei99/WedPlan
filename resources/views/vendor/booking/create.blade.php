@extends('vendor.layout.layout-vendor')

@section('title', 'Create Booking - WebPlan')
@section('page-title', 'Create Booking')
@section('page-subtitle', 'Add a booking and notify the couple instantly.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/booking.css'])
@endpush

@section('content')
    <div class="booking-page">
        <section class="booking-form-hero">
            <div class="booking-form-hero-copy">
                <span class="booking-kicker">New Booking</span>
                <h1>Create a booking that stays easy to scan and update.</h1>
                <p>Add the couple, service, date, status, and notes in a layout made for quick vendor entry without sacrificing clarity.</p>

                <div class="booking-hero-actions">
                    <a href="{{ route('vendor.booking.index') }}" class="booking-button-secondary booking-back-link">Back To Booking List</a>
                </div>
            </div>

            <article class="booking-guide-card booking-form-hero-panel">
                <h3>Quick Booking Flow</h3>
                <ol>
                    <li>Select the couple linked to your vendor account.</li>
                    <li>Choose the service, date, and status that match the request.</li>
                    <li>Save short notes so follow-up stays simple later.</li>
                </ol>
            </article>
        </section>

        <section class="booking-form-shell">
            <article class="booking-form-panel">
                <h2 class="booking-form-title">Add New Booking</h2>
                <p class="booking-form-helper">Fill in the booking details below so the vendor timeline stays accurate and easy to manage.</p>

                <form method="POST" action="{{ route('vendor.booking.store') }}" class="booking-form">
                    @csrf

                    @include('vendor.booking._form', ['booking' => $booking ?? null])

                    <div class="booking-form-actions">
                        <button type="submit" class="booking-button">Save Booking</button>
                    </div>
                </form>
            </article>

            <aside class="booking-side-panel">
                <div class="booking-guide-card">
                    <h3>Booking Tips</h3>
                    <ul>
                        <li>Keep notes short and specific so you can review them quickly.</li>
                        <li>Use the service name couples will recognize when they check the booking.</li>
                        <li>Confirm the date before saving so notifications stay accurate.</li>
                    </ul>
                </div>

                <div class="booking-guide-card">
                    <h3>Status Guide</h3>
                    <div class="booking-legend-list">
                        <span><i class="dot is-confirmed"></i> Confirmed and scheduled</span>
                        <span><i class="dot is-pending"></i> Pending follow-up</span>
                    </div>
                </div>
            </aside>
        </section>
    </div>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/booking.js'])
@endpush