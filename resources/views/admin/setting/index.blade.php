@extends('admin.layout.layout-admin')

@section('title', 'Admin Settings - WebPlan')
@section('page-title', 'Security Settings')
@section('page-subtitle', 'Update the admin password without changing the account profile.')

@section('content')
	<div class="admin-settings-page">
		<section class="admin-settings-hero">
			<span class="admin-kicker">Account security</span>
			<h1>Protect the control center with a strong password.</h1>
			<p>This admin area only manages password changes. Profile information stays fixed so access remains simple and secure.</p>
		</section>

		@if(session('success'))
			<section class="admin-flash admin-flash-success" role="status">{{ session('success') }}</section>
		@endif

		@if($errors->getBag('passwordUpdate')->any())
			<section class="admin-flash admin-flash-error" role="alert">{{ $errors->getBag('passwordUpdate')->first() }}</section>
		@endif

		<section class="admin-settings-card">
			<div class="admin-card-head">
				<div>
					<h2>Update password</h2>
					<p>Use a password with at least 8 characters.</p>
				</div>
			</div>

			<form method="POST" action="{{ route('admin.settings.password.update') }}" class="admin-form-grid" novalidate>
				@csrf
				@method('PUT')

				<div class="admin-field-group">
					<label for="current_password">Current password</label>
					<input id="current_password" type="password" name="current_password" class="admin-input" autocomplete="current-password" required>
					@error('current_password', 'passwordUpdate')
						<span class="field-error">{{ $message }}</span>
					@enderror
				</div>

				<div class="admin-password-grid">
					<div class="admin-field-group">
						<label for="password">New password</label>
						<input id="password" type="password" name="password" class="admin-input" autocomplete="new-password" required>
						@error('password', 'passwordUpdate')
							<span class="field-error">{{ $message }}</span>
						@enderror
					</div>

					<div class="admin-field-group">
						<label for="password_confirmation">Confirm password</label>
						<input id="password_confirmation" type="password" name="password_confirmation" class="admin-input" autocomplete="new-password" required>
					</div>
				</div>

				<div>
					<button type="submit" class="admin-primary-btn">Save password</button>
				</div>
			</form>
		</section>
	</div>
@endsection
