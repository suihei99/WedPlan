@extends('vendor.layout.layout-vendor')

@section('title', 'Edit Booking - WebPlan')
@section('page-title', 'Edit Booking')
@section('page-subtitle', 'Update booking details and keep the couple informed.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/booking.css'])
@endpush

@section('content')
    <section class="booking-form-hero">
        <div>
            <span class="booking-kicker">Booking Editor</span>
            <h1>Update Booking Details</h1>
            <p>Adjust the couple, service, schedule, or status without breaking the booking workflow.</p>
        </div>
        <a href="{{ route('vendor.booking.show', $booking) }}" class="booking-button-secondary">View Booking</a>
    </section>

    <section class="booking-form-panel">
        <form method="POST" action="{{ route('vendor.booking.update', $booking) }}" class="booking-form">
            @csrf
            @method('PUT')

            @include('vendor.booking._form', ['booking' => $booking])

            <div class="booking-form-actions">
                <button type="submit" class="booking-button">Update Booking</button>
            </div>
        </form>
    </section>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/booking.js'])
@endpush