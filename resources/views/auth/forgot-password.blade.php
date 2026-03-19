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
                <p class="reset-copy">
                    Password reset by email has not been enabled yet for WedPlan. Please contact the administrator or support team
                    to recover access to your account.
                </p>

                <div class="reset-help-box">
                    <strong>Recommended next step</strong>
                    <span>Contact the WedPlan administrator and provide the email address used for your account.</span>
                </div>

                <div class="reset-actions">
                    <a href="{{ route('login') }}" class="submit-btn reset-back-btn">
                        <span class="submit-btn-text">Back to Login</span>
                    </a>
                </div>
            </section>
        </main>
    </body>
</html>