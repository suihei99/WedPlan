@extends('couple.layout.layout-couple')

@section('title', 'Add Guest - WebPlan')
@section('page-title', 'Add New Guest')
@section('page-subtitle', 'Add guests to your wedding and manage their information.')

@push('page-styles')
    @vite(['resources/css/couple/guests.css'])
@endpush

@section('content')
    <div class="guests-page guests-page-form">
        @if($errors->any())
            <section class="guests-flash guests-flash-error" role="alert">
                <strong>Please review the form</strong>
                <span>{{ $errors->first() }}</span>
            </section>
        @endif

        <section class="guests-hero">
            <div>
                <span class="guests-kicker">Guest Management</span>
                <h1 class="guests-title">Add New Guest</h1>
                <p class="guests-subtitle">Enter core guest details that match your current data model and invitation workflow.</p>
            </div>
        </section>

        <section class="guests-layout-split">
            <article class="guests-form-card">
                <h2>Add Guest</h2>

                <form method="POST" action="{{ route('couple.guests.store') }}" class="guests-form" novalidate>
                @csrf

                <div class="guests-form-group">
                    <label for="name" class="guests-form-label">Guest Name *</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="guests-form-input @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="e.g., John Doe"
                        required
                    >
                    @error('name')
                        <small style="color: #9f2943;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="guests-form-group">
                    <label for="phone" class="guests-form-label">Malaysia Mobile Number</label>
                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        class="guests-form-input @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}"
                        placeholder="e.g., +60123456789"
                        inputmode="tel"
                    >
                    <small style="color: #715b64; margin-top: 0.2rem; display: block;">Include country code (e.g., +60 for Malaysia)</small>
                    @error('phone')
                        <small style="color: #9f2943;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="guests-form-group">
                    <label for="pax_count" class="guests-form-label">Pax Count</label>
                    <input
                        type="number"
                        id="pax_count"
                        name="pax_count"
                        class="guests-form-input @error('pax_count') is-invalid @enderror"
                        value="{{ old('pax_count', 1) }}"
                        min="1"
                        placeholder="1"
                    >
                    <small style="color: #715b64; margin-top: 0.2rem; display: block;">Total seats reserved for this guest booking.</small>
                    @error('pax_count')
                        <small style="color: #9f2943;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="guests-form-group">
                    <label for="rsvp_status" class="guests-form-label">Initial RSVP Status</label>
                    <select id="rsvp_status" name="rsvp_status" class="guests-form-select">
                        @foreach($rsvpStatuses as $status)
                            <option value="{{ $status }}" @selected(old('rsvp_status', \App\Models\Guest::RSVP_PENDING) === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('rsvp_status')
                        <small style="color: #9f2943;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="guests-form-actions">
                    <button type="submit" class="guests-form-submit">
                        <span>+</span>
                        Add Guest to List
                    </button>
                    @if(Route::has('couple.guests.index'))
                        <a href="{{ route('couple.guests.index') }}" class="guests-secondary-btn">
                            ← Cancel
                        </a>
                    @endif
                </div>
            </form>

            <aside class="guests-detail-card">
                <h3>Model Fields Guide</h3>
                <div class="guests-meta-list">
                    <div class="guests-meta-item">
                        <span>Name</span>
                        <strong>Guest full name</strong>
                    </div>
                    <div class="guests-meta-item">
                        <span>Phone</span>
                        <strong>Malaysia mobile only</strong>
                    </div>
                    <div class="guests-meta-item">
                        <span>Pax Count</span>
                        <strong>Minimum 1 seat</strong>
                    </div>
                    <div class="guests-meta-item">
                        <span>RSVP</span>
                        <strong>Pending / Confirmed / Declined</strong>
                    </div>
                </div>
            </aside>
        </section>
    </div>
@endsection

