<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Register As Vendor - {{ config('app.name', 'WedPlan') }}</title>

		<link rel="preconnect" href="https://fonts.bunny.net">
		<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|playfair-display:400,500,600,700i" rel="stylesheet" />

		@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth/register-vendor.js', 'resources/css/auth/register-vendor.css'])
	</head>
	<body class="login-body register-vendor-body">
		<div class="vendor-layer" aria-hidden="true">
			<span class="vendor-orb vendor-orb-1"></span>
			<span class="vendor-orb vendor-orb-2"></span>
			<span class="vendor-orb vendor-orb-3"></span>
		</div>

		<div class="login-wrapper register-shell vendor-shell">
			<aside class="brand-panel register-brand-panel vendor-brand-panel">
				<div class="ring-deco ring-deco-1" aria-hidden="true"></div>
				<div class="ring-deco ring-deco-2" aria-hidden="true"></div>

				<div class="brand-content">
					<div class="logo-wrap">
						<a href="{{ url('/') }}" class="logo-link" aria-label="Back to home">
							<img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan Logo" class="brand-logo">
						</a>
					</div>

					<div class="brand-badge">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
						Vendor Onboarding
					</div>

					<h1 class="brand-title register-brand-title vendor-brand-title">Grow Your Wedding Business With WedPlan</h1>
					<p class="brand-subtitle">Join trusted vendors in Sandakan and Sabah to receive bookings, manage requests, and showcase your services.</p>

					<div class="register-highlight vendor-highlight">
						<div class="highlight-card vendor-highlight-card">
							<p class="highlight-title">Verified Presence</p>
							<p class="highlight-copy">Submit your business details once and build trust with couples looking for reliable wedding services.</p>
						</div>
						<div class="highlight-card vendor-highlight-card">
							<p class="highlight-title">Booking Ready</p>
							<p class="highlight-copy">Receive inquiries faster and keep your service profile aligned with wedding timelines and budget expectations.</p>
						</div>
					</div>
				</div>
			</aside>

			<section class="form-panel register-form-panel vendor-form-panel">
				<div class="form-card register-form-card vendor-form-card">
					<div class="form-card-header">
						<span class="register-kicker vendor-kicker">Create Vendor Account</span>
						<div class="form-logo-small">
							<a href="{{ url('/') }}" class="logo-link" aria-label="Back to home">
								<img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan" class="w-10 h-10 object-contain">
							</a>
						</div>
						<h2 class="form-title">Register As Vendor</h2>
						<p class="form-subtitle">Complete your business profile to start connecting with couples.</p>
					</div>

					@if ($errors->any())
						<div class="alert-error" role="alert">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
							Please review your vendor registration details and try again.
						</div>
					@endif

					<form method="POST" action="{{ url('/register/vendor') }}" enctype="multipart/form-data" novalidate>
						@csrf

						<div class="register-form-grid vendor-form-grid">
							<div class="field-group">
								<label for="email" class="field-label">Email Address</label>
								<div class="input-wrap @error('email') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
									</span>
									<input id="email" type="email" name="email" class="field-input" value="{{ old('email') }}" placeholder="vendor@email.com" required autocomplete="email" autofocus>
								</div>
								@error('email')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group">
								<label for="business_name" class="field-label">Business Name</label>
								<div class="input-wrap @error('business_name') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
									</span>
									<input id="business_name" type="text" name="business_name" class="field-input" value="{{ old('business_name') }}" placeholder="Your wedding business name" required>
								</div>
								@error('business_name')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group">
								<label for="password" class="field-label">Password</label>
								<div class="input-wrap @error('password') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
									</span>
									<input id="password" type="password" name="password" class="field-input" placeholder="Create password" required autocomplete="new-password">
									<button type="button" class="toggle-pw" data-toggle-password data-target="password" aria-label="Toggle password visibility">
										<svg data-eye-show width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
										<svg data-eye-hide width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
									</button>
								</div>
								<div class="password-meter">
									<div class="password-meter-track">
										<span class="password-meter-fill" id="passwordMeterFill"></span>
									</div>
									<p class="password-meter-label" id="passwordMeterLabel">Use at least 8 characters with letters and numbers.</p>
								</div>
								@error('password')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group">
								<label for="password_confirmation" class="field-label">Confirm Password</label>
								<div class="input-wrap">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
									</span>
									<input id="password_confirmation" type="password" name="password_confirmation" class="field-input" placeholder="Retype password" required autocomplete="new-password">
									<button type="button" class="toggle-pw" data-toggle-password data-target="password_confirmation" aria-label="Toggle confirm password visibility">
										<svg data-eye-show width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
										<svg data-eye-hide width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
									</button>
								</div>
							</div>

							<div class="field-group">
								<label for="business_type" class="field-label">Type Of Business</label>
								<div class="input-wrap @error('business_type') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16"/><path d="M4 12h10"/><path d="M4 17h16"/></svg>
									</span>
									<select id="business_type" name="business_type" class="field-input field-select" required>
										<option value="" disabled {{ old('business_type') ? '' : 'selected' }}>Select your service</option>
										@foreach (['Venue', 'Catering', 'Photography', 'Makeup Artist', 'Wedding Planner', 'Bridal Wear', 'Decor & Styling', 'Entertainment', 'Transportation', 'Other'] as $type)
											<option value="{{ $type }}" {{ old('business_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
										@endforeach
									</select>
								</div>
								<p class="field-helper" id="businessTypeHelper">Choose the primary service category couples will see first.</p>
								@error('business_type')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group">
								<label for="contact_number" class="field-label">Contact Business (WhatsApp)</label>
								<div class="input-wrap @error('contact_number') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92V19a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 3.18 2 2 0 0 1 4.11 1h2.09a2 2 0 0 1 2 1.72c.12.88.34 1.74.64 2.57a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 6 6l1.51-1.3a2 2 0 0 1 2.11-.45c.83.3 1.69.52 2.57.64A2 2 0 0 1 22 16.92z"/></svg>
									</span>
									<input id="contact_number" type="text" name="contact_number" class="field-input" value="{{ old('contact_number') }}" placeholder="e.g. +60123456789" required>
								</div>
								@error('contact_number')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group field-full">
								<label for="address" class="field-label">Business Address</label>
								<div class="input-wrap input-wrap-textarea @error('address') input-wrap-error @enderror">
									<span class="input-icon textarea-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
									</span>
									<textarea id="address" name="address" rows="3" class="field-input field-textarea" placeholder="Street, district, city" required>{{ old('address') }}</textarea>
								</div>
								@error('address')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group field-full">
								<label for="business_documents" class="field-label">Business Documents (PDF, JPG, PNG)</label>
								<label for="business_documents" class="upload-wrap @error('business_documents') upload-wrap-error @enderror">
									<span class="upload-button">Upload File</span>
									<span class="upload-meta" id="businessDocumentLabel">No file chosen</span>
									<input id="business_documents" type="file" name="business_documents" class="upload-input" accept=".pdf,.jpg,.jpeg,.png" required>
								</label>
								<ul class="upload-notes">
									<li>Upload valid business proof such as SSM certificate, service portfolio, or operating license.</li>
									<li>Maximum file size is 2MB and only PDF, JPG, PNG are supported.</li>
								</ul>
								@error('business_documents')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group field-full register-consent vendor-consent">
								<label class="checkbox-wrap" for="confirmation_acknowledged">
									<input id="confirmation_acknowledged" type="checkbox" class="checkbox-input" required>
									<span class="checkbox-label">I confirm that all information and uploaded documents are correct.</span>
								</label>
							</div>
						</div>

						<button type="submit" class="submit-btn register-submit vendor-submit">
							<span class="submit-btn-text">Register Now</span>
							<svg class="submit-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
						</button>
					</form>

					<p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in now</a></p>
				</div>
			</section>
		</div>

		<footer class="login-footer">
			<div class="footer-inner">
				<a href="{{ url('/') }}" class="logo-link footer-logo-link" aria-label="Back to home">
					<img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan" class="footer-logo">
				</a>
				<div class="footer-info">
					<span class="footer-brand">WedPlan</span>
					<span class="footer-desc">A Web &amp; Mobile-Based Wedding Budgeting And Management System</span>
					<span class="footer-copy">Copyright &copy; {{ date('Y') }} WedPlan. All Rights Reserved.</span>
				</div>
			</div>
		</footer>
	</body>
</html>
