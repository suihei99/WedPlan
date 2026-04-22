@extends('couple.layout.layout-couple')

@section('title', 'Settings - WebPlan')
@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your profile and secure your account details.')

@push('page-styles')
    @vite(['resources/css/couple/settings.css'])
@endpush

@section('content')
    @php
        $coupleProfile = $couple ?? $user->couple;
        $partnerOne = $coupleProfile?->partner_1_name ?? 'Not set';
        $partnerTwo = $coupleProfile?->partner_2_name ?? 'Not set';
        $weddingDate = $coupleProfile?->wedding_date ? $coupleProfile->wedding_date->format('d / m / Y') : 'Not set';
        $weddingTime = $coupleProfile?->wedding_time ? \Illuminate\Support\Carbon::parse($coupleProfile->wedding_time)->format('g:i A') : 'Not set';
        $weddingVenue = $coupleProfile?->wedding_venue ?? 'Not set';
        $totalBudgetLimit = $coupleProfile?->total_budget_limit !== null ? number_format((float) $coupleProfile->total_budget_limit, 2) : 'Not set';
        $profileErrors = $errors->getBag('profileUpdate');
        $passwordErrors = $errors->getBag('passwordUpdate');
        $hasProfileErrors = $profileErrors->any();
    @endphp

    <div class="settings-page" data-settings-page data-profile-errors="{{ $hasProfileErrors ? '1' : '0' }}">
        <section class="settings-intro-card">
            <div>
                <span class="settings-kicker">Account</span>
                <h1>Couple Settings</h1>
                <p>Keep your wedding profile updated and protect your account with strong password settings.</p>
            </div>
            <button type="button" class="settings-edit-trigger" data-profile-toggle aria-expanded="{{ $hasProfileErrors ? 'true' : 'false' }}" aria-controls="profile-edit-panel">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 20H8L18.5 9.5C19.3 8.7 19.3 7.4 18.5 6.6L17.4 5.5C16.6 4.7 15.3 4.7 14.5 5.5L4 16V20Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M13.5 6.5L17.5 10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Edit Profile
            </button>
        </section>

        @if(session('success'))
            <section class="settings-flash settings-flash-success" role="status">
                <strong>Success</strong>
                <span>{{ session('success') }}</span>
            </section>
        @endif

        @if($hasProfileErrors)
            <section class="settings-flash settings-flash-error" role="alert">
                <strong>Profile update failed</strong>
                <span>{{ $profileErrors->first() }}</span>
            </section>
        @elseif($passwordErrors->any())
            <section class="settings-flash settings-flash-error" role="alert">
                <strong>Please check the form</strong>
                <span>{{ $passwordErrors->first() }}</span>
            </section>
        @endif

        <section id="profile-edit-panel" class="settings-profile-edit" data-profile-panel @if(! $hasProfileErrors) hidden @endif>
            <h2>Profile Details</h2>
            <p>Update your core wedding profile details for planning and budget tracking.</p>

            <form method="POST" action="{{ route('couple.settings.profile.update') }}" class="settings-form-grid">
                @csrf
                @method('PUT')

                <div class="settings-field">
                    <label for="partner_1_name">1 - Person Couple Name</label>
                    <input id="partner_1_name" type="text" name="partner_1_name" value="{{ old('partner_1_name', $coupleProfile?->partner_1_name) }}" required class="{{ $profileErrors->has('partner_1_name') ? 'is-invalid' : '' }}" aria-invalid="{{ $profileErrors->has('partner_1_name') ? 'true' : 'false' }}">
                    @error('partner_1_name', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-field">
                    <label for="partner_2_name">2 - Person Couple Name</label>
                    <input id="partner_2_name" type="text" name="partner_2_name" value="{{ old('partner_2_name', $coupleProfile?->partner_2_name) }}" required class="{{ $profileErrors->has('partner_2_name') ? 'is-invalid' : '' }}" aria-invalid="{{ $profileErrors->has('partner_2_name') ? 'true' : 'false' }}">
                    @error('partner_2_name', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-field">
                    <label for="wedding_date">Wedding Date</label>
                    <input id="wedding_date" type="date" name="wedding_date" value="{{ old('wedding_date', optional($coupleProfile?->wedding_date)->format('Y-m-d')) }}" class="{{ $profileErrors->has('wedding_date') ? 'is-invalid' : '' }}" aria-invalid="{{ $profileErrors->has('wedding_date') ? 'true' : 'false' }}">
                    @error('wedding_date', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-field">
                    <label for="wedding_time">Wedding Time</label>
                    <input id="wedding_time" type="time" name="wedding_time" value="{{ old('wedding_time', $coupleProfile?->wedding_time) }}" class="{{ $profileErrors->has('wedding_time') ? 'is-invalid' : '' }}" aria-invalid="{{ $profileErrors->has('wedding_time') ? 'true' : 'false' }}">
                    @error('wedding_time', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-field settings-field-full">
                    <label for="wedding_venue">Wedding Venue</label>
                    <input id="wedding_venue" type="text" name="wedding_venue" value="{{ old('wedding_venue', $coupleProfile?->wedding_venue) }}" placeholder="Enter wedding venue" class="{{ $profileErrors->has('wedding_venue') ? 'is-invalid' : '' }}" aria-invalid="{{ $profileErrors->has('wedding_venue') ? 'true' : 'false' }}">
                    @error('wedding_venue', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-field settings-field-full">
                    <label for="total_budget_limit">Total Budget Limit</label>
                    <input id="total_budget_limit" type="number" name="total_budget_limit" min="0" step="0.01" value="{{ old('total_budget_limit', $coupleProfile?->total_budget_limit) }}" placeholder="0.00" class="{{ $profileErrors->has('total_budget_limit') ? 'is-invalid' : '' }}" aria-invalid="{{ $profileErrors->has('total_budget_limit') ? 'true' : 'false' }}">
                    @error('total_budget_limit', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-actions settings-field-full">
                    <button type="submit" class="settings-primary-btn">Save Profile</button>
                </div>
            </form>
        </section>

        <section class="settings-content-grid">
            <article class="settings-info-card">
                <h2>Wedding Information</h2>
                <div class="settings-info-list">
                    <div class="settings-info-item">
                        <span>Email</span>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>1 - Person Couple Name</span>
                        <strong>{{ $partnerOne }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>2 - Person Couple Name</span>
                        <strong>{{ $partnerTwo }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Wedding Date</span>
                        <strong>{{ $weddingDate }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Wedding Time</span>
                        <strong>{{ $weddingTime }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Wedding Venue</span>
                        <strong>{{ $weddingVenue }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Total Budget Limit</span>
                        <strong>{{ $totalBudgetLimit === 'Not set' ? 'Not set' : 'RM ' . $totalBudgetLimit }}</strong>
                    </div>
                </div>
            </article>

            <article class="settings-password-card">
                <h2>Password Security</h2>
                <p>Use at least 8 characters and include both letters and numbers for stronger protection.</p>

                <form method="POST" action="{{ route('couple.settings.password.update') }}" class="settings-password-form" data-password-form>
                    @csrf
                    @method('PUT')

                    <div class="settings-field">
                        <label for="current_password">Current Password</label>
                        <input id="current_password" type="password" name="current_password" autocomplete="current-password" required>
                        @error('current_password', 'passwordUpdate')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="settings-password-row">
                        <div class="settings-field">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" required data-password-input>
                            @error('password', 'passwordUpdate')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="settings-field">
                            <label for="password_confirmation">Confirmation Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required data-password-confirm>
                        </div>
                    </div>

                    <p class="settings-password-status" data-password-status aria-live="polite"></p>

                    <div class="settings-actions">
                        <button type="submit" class="settings-primary-btn">Change Password</button>
                    </div>
                </form>
            </article>
        </section>
    </div>
@endsection

@push('page-scripts')
    @vite(['resources/js/couple/settings.js'])
@endpush
