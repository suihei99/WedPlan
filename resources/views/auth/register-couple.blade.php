<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Register As Couple - {{ config('app.name', 'WedPlan') }}</title>

		<link rel="preconnect" href="https://fonts.bunny.net">
		<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|playfair-display:400,500,600,700i" rel="stylesheet" />

		@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth/register-couple.js', 'resources/css/auth/register-couple.css'])
	</head>
	<body class="login-body register-couple-body">
		<div class="register-layer" aria-hidden="true">
			<span class="register-orb register-orb-1"></span>
			<span class="register-orb register-orb-2"></span>
		</div>

		<div class="login-wrapper register-shell">
			<aside class="brand-panel register-brand-panel">
				<div class="ring-deco ring-deco-1" aria-hidden="true"></div>
				<div class="ring-deco ring-deco-2" aria-hidden="true"></div>

				<div class="brand-content">
					<div class="logo-wrap">
						<a href="{{ url('/') }}" class="logo-link" aria-label="Back to home">
							<img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan Logo" class="brand-logo">
						</a>
					</div>

					<div class="brand-badge">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402C1 3.338 4.08 1 7.5 1c1.93 0 3.775.82 5.003 2.21C13.725 1.82 15.57 1 17.5 1 20.92 1 24 3.338 24 7.191c0 4.105-5.37 8.863-11 14.402z"/></svg>
						Couple Onboarding
					</div>

					<h1 class="brand-title register-brand-title">Plan Your Wedding Journey Together</h1>
					<p class="brand-subtitle">Create your shared couple account and keep budget, timeline, and key details in one place.</p>

					<div class="register-highlight">
						<div class="highlight-card">
							<p class="highlight-title">Shared Workspace</p>
							<p class="highlight-copy">Both partners can track tasks, spending, and event priorities from a single dashboard.</p>
						</div>
						<div class="highlight-card">
							<p class="highlight-title">Smart Planning</p>
							<p class="highlight-copy">Set your wedding date, location, and budget to personalize your planning timeline instantly.</p>
						</div>
					</div>
				</div>
			</aside>

			<section class="form-panel register-form-panel">
				<div class="form-card register-form-card">
					<div class="form-card-header">
						<span class="register-kicker">Create Couple Account</span>
						<div class="form-logo-small">
							<a href="{{ url('/') }}" class="logo-link" aria-label="Back to home">
								<img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan" class="w-10 h-10 object-contain">
							</a>
						</div>
						<h2 class="form-title">Register As Couple</h2>
						<p class="form-subtitle">Start with account details, then add your wedding plan basics.</p>
					</div>

					@if ($errors->any())
						<div class="alert-error" role="alert">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
							Please review your registration details and try again.
						</div>
					@endif

					<form method="POST" action="{{ url('/register/couple') }}" novalidate>
						@csrf

						<div class="register-form-grid">
							<div class="field-group field-full">
								<label for="email" class="field-label">Email Address</label>
								<div class="input-wrap @error('email') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
									</span>
									<input id="email" type="email" name="email" class="field-input" value="{{ old('email') }}" placeholder="couple@email.com" required autocomplete="email" autofocus>
								</div>
								@error('email')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group field-full">
								<label for="password" class="field-label">Password</label>
								<div class="input-wrap @error('password') input-wrap-error @enderror" id="password-wrap">
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

							<div class="field-group field-full">
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
								<label for="partner_1_name" class="field-label">Partner 1 Name</label>
								<div class="input-wrap @error('partner_1_name') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
									</span>
									<input id="partner_1_name" type="text" name="partner_1_name" class="field-input" value="{{ old('partner_1_name') }}" placeholder="First partner name" required>
								</div>
								@error('partner_1_name')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group">
								<label for="partner_2_name" class="field-label">Partner 2 Name</label>
								<div class="input-wrap @error('partner_2_name') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
									</span>
									<input id="partner_2_name" type="text" name="partner_2_name" class="field-input" value="{{ old('partner_2_name') }}" placeholder="Second partner name" required>
								</div>
								@error('partner_2_name')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group field-full">
								<label for="wedding_venue" class="field-label">Wedding Location</label>
								<div class="input-wrap @error('wedding_venue') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
									</span>
									<input id="wedding_venue" type="text" name="wedding_venue" class="field-input" value="{{ old('wedding_venue') }}" placeholder="Venue, city, district" required>
								</div>
								@error('wedding_venue')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group">
								<label for="wedding_date" class="field-label">Wedding Date (Optional)</label>
								<div class="input-wrap @error('wedding_date') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
									</span>
									<input id="wedding_date" type="date" name="wedding_date" class="field-input" value="{{ old('wedding_date') }}">
								</div>
								@error('wedding_date')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group">
								<label for="wedding_time" class="field-label">Wedding Time (Optional)</label>
								<div class="input-wrap @error('wedding_time') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
									</span>
									<input id="wedding_time" type="time" name="wedding_time" class="field-input" value="{{ old('wedding_time') }}">
								</div>
								@error('wedding_time')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group field-full">
								<label for="total_budget_limit" class="field-label">Estimated Budget Limit (Optional)</label>
								<div class="input-wrap @error('total_budget_limit') input-wrap-error @enderror">
									<span class="input-icon">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"/></svg>
									</span>
									<input id="total_budget_limit" type="number" name="total_budget_limit" min="0" step="0.01" class="field-input" value="{{ old('total_budget_limit') }}" placeholder="50000">
									<span class="input-suffix">MYR</span>
								</div>
								@error('total_budget_limit')
									<p class="field-error">{{ $message }}</p>
								@enderror
							</div>

							<div class="field-group field-full register-consent">
								<label class="checkbox-wrap" for="confirmation_acknowledged">
									<input id="confirmation_acknowledged" type="checkbox" class="checkbox-input" required>
									<span class="checkbox-label">I confirm that all registration information is accurate.</span>
								</label>
							</div>
						</div>

						<button type="submit" class="submit-btn register-submit">
							<span class="submit-btn-text">Create Couple Account</span>
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
