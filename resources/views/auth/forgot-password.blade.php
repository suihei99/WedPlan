<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Forgot Password — {{ config('app.name', 'WedPlan') }}</title>

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

                <span class="reset-kicker">Account Recovery</span>
                <h1 class="reset-title">Forgot your password?</h1>

                @if (session('status'))
                    <div class="reset-help-box" role="status">
                        <strong>Reset link sent</strong>
                        <span>{{ session('status') }}</span>
                    </div>
                @else
                    <p class="reset-copy">
                        Enter the email address tied to your WedPlan account and we’ll send a password reset link.
                    </p>
                @endif

                @if ($errors->any())
                    <div class="alert-error" role="alert">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Please check the email address and try again.
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="w-full" novalidate>
                    @csrf

                    <div class="field-group">
                        <label for="email" class="field-label">Email Address</label>
                        <div class="input-wrap @error('email') input-wrap-error @enderror">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </span>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                class="field-input"
                                value="{{ old('email') }}"
                                placeholder="your@email.com"
                                required
                                autocomplete="email"
                                autofocus
                            >
                        </div>
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="reset-actions">
                        <button type="submit" class="submit-btn">
                            <span class="submit-btn-text">Send Reset Link</span>
                            <svg class="submit-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>

                        <a href="{{ route('login') }}" class="submit-btn reset-back-btn">
                            <span class="submit-btn-text">Back to Login</span>
                        </a>
                    </div>
                </form>
            </section>
        </main>
    </body>
</html>