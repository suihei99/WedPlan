<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reset Password — {{ config('app.name', 'WedPlan') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|playfair-display:400,500,600,700i" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/css/auth/login.css'])
    </head>
    <body class="login-body reset-page-body">
        <div class="petals-container" aria-hidden="true">
            <span class="petal petal-1">🌸</span>
            <span class="petal petal-4">🌸</span>
            <span class="petal petal-6">🌹</span>
        </div>

        <main class="reset-page-shell">
            <section class="reset-card">
                <a href="{{ url('/') }}" class="logo-link reset-logo-link" aria-label="Back to welcome page">
                    <img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan" class="reset-logo">
                </a>

                <span class="reset-kicker">Secure Update</span>
                <h1 class="reset-title">Set a new password</h1>
                <p class="reset-copy">
                    Enter a new password for your WedPlan account. The reset link expires automatically for your security.
                </p>

                @if ($errors->any())
                    <div class="alert-error" role="alert">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Please review the password details and try again.
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="w-full" novalidate>
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request('email') }}">

                    <div class="field-group">
                        <label for="password" class="field-label">New Password</label>
                        <div class="input-wrap @error('password') input-wrap-error @enderror" id="password-wrap">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="field-input"
                                placeholder="Create a new password"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-pw" id="togglePw" aria-label="Toggle password visibility">
                                <svg id="eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg id="eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
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
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                class="field-input"
                                placeholder="Confirm your new password"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-pw" id="toggleConfirmPw" aria-label="Toggle confirm password visibility">
                                <svg id="confirm-eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg id="confirm-eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="reset-actions">
                        <button type="submit" class="submit-btn submit-btn-primary" aria-label="Reset password">
                            <span class="submit-btn-text">Reset Password</span>
                            <svg class="submit-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>

                        <a href="{{ route('login') }}" class="submit-btn submit-btn-secondary reset-back-btn" aria-label="Back to login">
                            <svg class="submit-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            <span class="submit-btn-text">Back to Login</span>
                        </a>
                    </div>
                </form>
            </section>
        </main>

        <script>
            const togglePassword = document.getElementById('togglePw');
            const passwordInput = document.getElementById('password');
            const eyeShow = document.getElementById('eye-show');
            const eyeHide = document.getElementById('eye-hide');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    eyeShow.style.display = isHidden ? 'none' : '';
                    eyeHide.style.display = isHidden ? '' : 'none';
                });
            }

            const toggleConfirmPassword = document.getElementById('toggleConfirmPw');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const confirmEyeShow = document.getElementById('confirm-eye-show');
            const confirmEyeHide = document.getElementById('confirm-eye-hide');

            if (toggleConfirmPassword && confirmPasswordInput) {
                toggleConfirmPassword.addEventListener('click', function () {
                    const isHidden = confirmPasswordInput.type === 'password';
                    confirmPasswordInput.type = isHidden ? 'text' : 'password';
                    confirmEyeShow.style.display = isHidden ? 'none' : '';
                    confirmEyeHide.style.display = isHidden ? '' : 'none';
                });
            }
        </script>
    </body>
</html>