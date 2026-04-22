@extends('couple.layout.layout-couple')

@section('title', 'Add Guest - WebPlan')
@section('page-title', 'Add New Guest')
@section('page-subtitle', 'Add guests to your wedding and manage their information.')

@push('page-styles')
    @vite(['resources/css/couple/guests.css'])
@endpush

@section('content')
    <div class="guests-page">
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
                <p class="guests-subtitle">Enter guest details to add them to your wedding guest list.</p>
            </div>
        </section>

        <div style="max-width: 700px;">
            <form method="POST" action="{{ route('couple.guests.store') }}" class="guests-form" novalidate>
                @csrf

                <!-- Guest Name -->
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

                <!-- Contact Number -->
                <div class="guests-form-group">
                    <label for="contact_number" class="guests-form-label">WhatsApp Contact Number *</label>
                    <input
                        id="contact_number"
                        type="tel"
                        name="contact_number"
                        class="guests-form-input @error('contact_number') is-invalid @enderror"
                        value="{{ old('contact_number') }}"
                        placeholder="e.g., +60123456789"
                        required
                    >
                    <small style="color: #715b64; margin-top: 0.2rem; display: block;">Include country code (e.g., +60 for Malaysia)</small>
                    @error('contact_number')
                        <small style="color: #9f2943;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Email (Optional) -->
                <div class="guests-form-group">
                    <label for="email" class="guests-form-label">Email Address (Optional)</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="guests-form-input @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="john@example.com"
                    >
                    @error('email')
                        <small style="color: #9f2943;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Plus Ones -->
                <div class="guests-form-group">
                    <label for="plus_one_count" class="guests-form-label">Number of Plus Ones (Optional)</label>
                    <input
                        id="plus_one_count"
                        type="number"
                        name="plus_one_count"
                        class="guests-form-input"
                        value="{{ old('plus_one_count', 0) }}"
                        min="0"
                        max="5"
                        placeholder="0"
                    >
                    <small style="color: #715b64; margin-top: 0.2rem; display: block;">Number of additional guests they can bring</small>
                </div>

                <!-- Dietary Preferences -->
                <div class="guests-form-group">
                    <label for="dietary_preference" class="guests-form-label">Dietary Preference (Optional)</label>
                    <select id="dietary_preference" name="dietary_preference" class="guests-form-select">
                        <option value="">Select preference...</option>
                        <option value="vegetarian" {{ old('dietary_preference') === 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                        <option value="vegan" {{ old('dietary_preference') === 'vegan' ? 'selected' : '' }}>Vegan</option>
                        <option value="halal" {{ old('dietary_preference') === 'halal' ? 'selected' : '' }}>Halal</option>
                        <option value="gluten-free" {{ old('dietary_preference') === 'gluten-free' ? 'selected' : '' }}>Gluten-Free</option>
                        <option value="no-restriction" {{ old('dietary_preference') === 'no-restriction' ? 'selected' : '' }}>No Restriction</option>
                        <option value="other" {{ old('dietary_preference') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="guests-form-group">
                    <label for="notes" class="guests-form-label">Additional Notes (Optional)</label>
                    <textarea
                        id="notes"
                        name="notes"
                        class="guests-form-textarea"
                        placeholder="Add any special notes about this guest..."
                    >{{ old('notes') }}</textarea>
                    <small style="color: #715b64; margin-top: 0.2rem; display: block;">Max 500 characters</small>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 0.8rem; margin-top: 1.5rem;">
                    <button type="submit" class="guests-form-submit">
                        <span>✓</span>
                        Add Guest to List
                    </button>
                    @if(Route::has('couple.guests.index'))
                        <a href="{{ route('couple.guests.index') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.7rem 1.2rem; background: #fff; border: 1px solid #efd7df; border-radius: 0.7rem; color: #d54c6d; text-decoration: none; font-weight: 600; transition: all 0.2s ease;">
                            ← Cancel
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tips Section -->
        <section style="max-width: 700px; margin-top: 2rem; background: #fef7fa; border: 1px solid #efd7df; border-radius: 0.85rem; padding: 1.5rem;">
            <h3 style="margin: 0 0 0.8rem; font-size: 1rem; color: #201419;">💡 Pro Tips</h3>
            <ul style="margin: 0; padding: 0 0 0 1.2rem; color: #715b64; font-size: 0.9rem; line-height: 1.6;">
                <li>Use the WhatsApp number format with country code for easy invitation sharing</li>
                <li>Add notes for special requests or dietary restrictions</li>
                <li>You can invite guests with their personalized QR code after adding them</li>
                <li>Guests who confirm their RSVP will be marked as confirmed</li>
            </ul>
        </section>
    </div>
@endsection

