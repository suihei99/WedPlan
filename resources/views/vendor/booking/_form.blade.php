@php
    $bookingValue = $booking ?? null;
    $selectedCoupleId = old('couple_id', $bookingValue ? $bookingValue->couple_id : null);
    $selectedService = old('type_service', $bookingValue ? $bookingValue->type_service : null);
    $selectedStatus = old('status', $bookingValue ? ($bookingValue->status ? '1' : '0') : '0');
    $bookingDateValue = old('booking_date', $bookingValue && $bookingValue->booking_date ? \Illuminate\Support\Carbon::parse((string) $bookingValue->booking_date)->format('Y-m-d') : null);
    $bookingNotes = old('notes', $bookingValue ? $bookingValue->notes : null);
@endphp

<div class="booking-form-grid">
    <div class="booking-field booking-field-full">
        <label for="couple_id">Couple</label>
        <div class="booking-input-wrap {{ $errors->has('couple_id') ? 'is-error' : '' }}">
            <span class="booking-input-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <select id="couple_id" name="couple_id" class="booking-field-input booking-select" required>
                <option value="" disabled {{ $selectedCoupleId ? '' : 'selected' }}>Choose a couple</option>
                @foreach($couples as $coupleUser)
                    @php
                        $coupleProfile = $coupleUser->couple;
                        $coupleLabel = trim(($coupleProfile?->partner_1_name ?? 'Couple').' & '.($coupleProfile?->partner_2_name ?? 'Guest'));
                    @endphp
                    <option value="{{ $coupleUser->id }}" {{ (string) $selectedCoupleId === (string) $coupleUser->id ? 'selected' : '' }}>{{ $coupleLabel }} - {{ $coupleUser->email }}</option>
                @endforeach
            </select>
        </div>
        @error('couple_id')
            <p class="booking-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="booking-field">
        <label for="type_service">Service</label>
        <div class="booking-input-wrap {{ $errors->has('type_service') ? 'is-error' : '' }}">
            <span class="booking-input-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/></svg>
            </span>
            <select id="type_service" name="type_service" class="booking-field-input booking-select" required>
                <option value="" disabled {{ $selectedService ? '' : 'selected' }}>Select a service</option>
                @foreach($serviceOptions as $serviceOption)
                    <option value="{{ $serviceOption }}" {{ $selectedService === $serviceOption ? 'selected' : '' }}>{{ $serviceOption }}</option>
                @endforeach
            </select>
        </div>
        @error('type_service')
            <p class="booking-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="booking-field">
        <label for="booking_date">Booking Date</label>
        <input id="booking_date" type="date" name="booking_date" class="booking-field-input {{ $errors->has('booking_date') ? 'is-invalid' : '' }}" value="{{ $bookingDateValue }}" required>
        @error('booking_date')
            <p class="booking-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="booking-field">
        <label for="status">Status</label>
        <div class="booking-input-wrap {{ $errors->has('status') ? 'is-error' : '' }}">
            <span class="booking-input-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
            </span>
            <select id="status" name="status" class="booking-field-input booking-select" required>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ (string) $selectedStatus === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @error('status')
            <p class="booking-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="booking-field booking-field-full">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="4" class="booking-field-input booking-textarea {{ $errors->has('notes') ? 'is-invalid' : '' }}" placeholder="Add request details, reminders, or special requirements">{{ $bookingNotes }}</textarea>
        @error('notes')
            <p class="booking-field-error">{{ $message }}</p>
        @enderror
    </div>
</div>