@extends('vendor.layout.layout-vendor')

@section('title', 'Create Booking - WebPlan')
@section('page-title', 'Create Booking')
@section('page-subtitle', 'Add a booking and notify the couple instantly.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/booking.css'])
@endpush

@section('content')
    <section class="booking-form-hero">
        <div>
            <span class="booking-kicker">New Booking</span>
            <h1>Create A Booking Record</h1>
            <p>Keep the booking list clean, interactive, and aligned with WedPlan’s wedding-focused palette.</p>
        </div>
        <a href="{{ route('vendor.booking.index') }}" class="booking-button-secondary">Back To Booking List</a>
    </section>

    <section class="booking-form-panel">
        <form method="POST" action="{{ route('vendor.booking.store') }}" class="booking-form">
            @csrf

            @include('vendor.booking._form', ['booking' => $booking ?? null])

            <div class="booking-form-actions">
                <button type="submit" class="booking-button">Save Booking</button>
            </div>
        </form>
    </section>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/booking.js'])
@endpush