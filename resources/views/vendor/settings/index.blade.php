@extends('vendor.layout.layout-vendor')

@section('title', 'Settings - WebPlan')
@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your business profile, photo, and account security.')

@push('page-styles')
    @vite(['resources/css/vendor/dashboard.css', 'resources/css/vendor/settings.css'])
@endpush

@section('content')
    @php
        $vendorProfile = $vendor ?? $user->vendor;
        $profilePhoto = $user->profile_photo_path ? asset('storage/' . ltrim($user->profile_photo_path, '/')) : asset('assets/icons/WebPlan_logo.webp');
        $businessDocumentUrl = $vendorProfile?->business_document_url;
        $businessDocumentName = $vendorProfile?->business_documents ? basename($vendorProfile->business_documents) : 'No document uploaded';
        $isVerified = $vendorProfile?->status === \App\Models\Vendor::STATUS_APPROVED;
        $profileErrors = $errors->getBag('profileUpdate');
        $passwordErrors = $errors->getBag('passwordUpdate');
        $hasProfileErrors = $profileErrors->any();
    @endphp

    <div class="settings-page vendor-settings-page" data-settings-page data-profile-errors="{{ $hasProfileErrors ? '1' : '0' }}">
        <section class="settings-intro-card vendor-settings-intro-card">
            <div>
                <span class="settings-kicker">Account</span>
                <h1>Vendor Settings</h1>
                <p>Keep your wedding business profile polished, current, and easy for couples to trust.</p>
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
            <h2>Business Details</h2>
            <p>Update the information couples see when they browse your vendor profile.</p>

            <form method="POST" action="{{ route('vendor.settings.profile.update') }}" enctype="multipart/form-data" class="settings-form-grid vendor-settings-form-grid">
                @csrf
                @method('PUT')

                <div class="settings-field settings-field-full vendor-photo-upload">
                    <label for="profile_photo">Profile Photo</label>
                    <div class="vendor-photo-row">
                        <div class="vendor-photo-preview">
                            <img src="{{ $profilePhoto }}" alt="Vendor profile photo" data-profile-photo-preview>
                        </div>
                        <div class="vendor-photo-upload-copy">
                            <input id="profile_photo" type="file" name="profile_photo" accept=".png,.webp,.jpeg,.jpg,.gif" data-profile-photo-input>
                            <p class="settings-helper-copy">Upload a clear PNG, WEBP, JPEG, or GIF photo for your business profile.</p>
                            @error('profile_photo', 'profileUpdate')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="settings-field settings-field-full">
                    <label for="business_name">Business Name</label>
                    <input id="business_name" type="text" value="{{ $vendorProfile?->business_name ?? 'Not set' }}" readonly class="settings-readonly-field" aria-readonly="true">
                    <p class="settings-helper-copy">Business name is locked after registration and can only be changed by admin support.</p>
                </div>

                <div class="settings-field">
                    <label for="business_type">Business Type</label>
                    <div class="input-wrap {{ $profileErrors->has('business_type') ? 'input-wrap-error' : '' }}">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16"/><path d="M4 12h10"/><path d="M4 17h16"/></svg>
                        </span>
                        <select id="business_type" name="business_type" class="field-input field-select" required aria-invalid="{{ $profileErrors->has('business_type') ? 'true' : 'false' }}">
                            <option value="" disabled {{ old('business_type', $vendorProfile?->business_type) ? '' : 'selected' }}>Select your service</option>
                            @foreach (['Venue', 'Catering', 'Photography', 'Makeup Artist', 'Wedding Planner', 'Bridal Wear', 'Decor & Styling', 'Entertainment', 'Transportation', 'Other'] as $type)
                                <option value="{{ $type }}" {{ old('business_type', $vendorProfile?->business_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="field-helper" id="businessTypeHelper">Choose the primary service category couples will see first.</p>
                    @error('business_type', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-field">
                    <label for="contact_number">Contact Number</label>
                    <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number', $vendorProfile?->contact_number) }}" placeholder="e.g. +60123456789" required class="{{ $profileErrors->has('contact_number') ? 'is-invalid' : '' }}" aria-invalid="{{ $profileErrors->has('contact_number') ? 'true' : 'false' }}">
                    @error('contact_number', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-field settings-field-full">
                    <label for="address">Business Address</label>
                    <input id="address" type="text" name="address" value="{{ old('address', $vendorProfile?->address) }}" placeholder="Enter business address" required class="{{ $profileErrors->has('address') ? 'is-invalid' : '' }}" aria-invalid="{{ $profileErrors->has('address') ? 'true' : 'false' }}">
                    @error('address', 'profileUpdate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-field settings-field-full vendor-document-upload">
                    <label for="business_documents">Business Documentation</label>
                    <div class="vendor-document-row">
                        <div class="vendor-document-meta">
                            <div class="vendor-document-state">
                                @if($isVerified)
                                    <span class="vendor-document-badge is-verified">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 7L10 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="vendor-document-badge is-pending">Pending Review</span>
                                @endif
                            </div>
                            <p class="settings-helper-copy">Upload your SSM certificate, license, or portfolio document. Admin will be notified when you update it.</p>
                        </div>
                        <div class="vendor-document-file">
                            @if($businessDocumentUrl)
                                <a href="{{ $businessDocumentUrl }}" target="_blank" rel="noopener" class="vendor-document-link" data-open-pdf>{{ $businessDocumentName }}</a>
                            @else
                                <span class="vendor-document-empty">No document uploaded</span>
                            @endif
                            <input id="business_documents" type="file" name="business_documents" accept=".pdf,.png,.webp,.jpeg,.jpg,.gif" data-business-document-input>
                            @error('business_documents', 'profileUpdate')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="settings-actions settings-field-full">
                    <button type="submit" class="settings-primary-btn">Save Profile</button>
                </div>
            </form>
        </section>

        <section class="settings-content-grid vendor-settings-content-grid">
            <article class="settings-info-card">
                <h2>Business Information</h2>
                <div class="settings-info-list">
                    <div class="settings-info-item">
                        <span>Email</span>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Business Name</span>
                        <strong>{{ $vendorProfile?->business_name ?? 'Not set' }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Business Documentation</span>
                        <strong>
                            @if($businessDocumentUrl)
                                <a href="{{ $businessDocumentUrl }}" target="_blank" rel="noopener" class="vendor-document-link-inline" data-open-pdf>
                                    {{ $businessDocumentName }}
                                    @if($isVerified)
                                        <span class="vendor-inline-verified" aria-label="Verified">
                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 7L10 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </a>
                            @else
                                Not set
                            @endif
                        </strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Business Type</span>
                        <strong>{{ $vendorProfile?->business_type ?? 'Not set' }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Contact Number</span>
                        <strong>{{ $vendorProfile?->contact_number ?? 'Not set' }}</strong>
                    </div>
                    <div class="settings-info-item">
                        <span>Business Address</span>
                        <strong>{{ $vendorProfile?->address ?? 'Not set' }}</strong>
                    </div>
                </div>
            </article>

            <article class="settings-password-card">
                <h2>Password Security</h2>
                <p>Use at least 8 characters and include both letters and numbers for stronger protection.</p>

                <form method="POST" action="{{ route('vendor.settings.password.update') }}" class="settings-password-form" data-password-form>
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
    @vite(['resources/js/vendor/dashboard.js', 'resources/js/vendor/settings.js'])
@endpush

    <div id="pdf-modal" class="pdf-modal" aria-hidden="true">
        <div class="pdf-modal-backdrop" data-pdf-close></div>
        <div class="pdf-modal-body" role="dialog" aria-modal="true">
            <button type="button" class="pdf-modal-close" data-pdf-close aria-label="Close">×</button>
            <div class="pdf-modal-toolbar">
                <a href="#" target="_blank" rel="noopener" class="pdf-download-link" data-pdf-download>Open in new tab</a>
            </div>
            <iframe src="" frameborder="0" class="pdf-modal-iframe" data-pdf-iframe></iframe>
        </div>
    </div>
