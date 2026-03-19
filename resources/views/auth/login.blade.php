<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login — {{ config('app.name', 'WedPlan') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|playfair-display:400,500,600,700i" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/auth/login.css'])
    </head>
    <body class="login-body">

        <!-- Floating decorative petals -->
        <div class="petals-container" aria-hidden="true">
            <span class="petal petal-1">🌸</span>
            <span class="petal petal-2">🌹</span>
            <span class="petal petal-3">💮</span>
            <span class="petal petal-4">🌸</span>
            <span class="petal petal-5">🌺</span>
            <span class="petal petal-6">🌹</span>
            <span class="petal petal-7">💮</span>
            <span class="petal petal-8">🌸</span>
        </div>

        <!-- Main layout -->
        <div class="login-wrapper">

            <!-- Left branding panel -->
            <div class="brand-panel">
                <!-- Decorative rings -->
                <div class="ring-deco ring-deco-1" aria-hidden="true"></div>
                <div class="ring-deco ring-deco-2" aria-hidden="true"></div>

                <div class="brand-content">
                    <!-- Logo -->
                    <div class="logo-wrap">
                        <a href="{{ url('/') }}" class="logo-link" aria-label="Back to welcome page">
                            <img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan Logo" class="brand-logo">
                        </a>
                    </div>

                    <!-- Badge -->
                    <div class="brand-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402C1 3.338 4.08 1 7.5 1c1.93 0 3.775.82 5.003 2.21C13.725 1.82 15.57 1 17.5 1 20.92 1 24 3.338 24 7.191c0 4.105-5.37 8.863-11 14.402z"/></svg>
                        Sandakan &amp; Sabah
                    </div>

                    <h1 class="brand-title">Welcome To<br><span>WedPlan</span></h1>
                    <p class="brand-subtitle">Your Complete Wedding Planning<br>Companion for <strong>Sandakan &amp; Sabah</strong></p>

                    <!-- Feature highlights -->
                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="feature-icon">💍</span>
                            <span>Manage your wedding budget</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">📅</span>
                            <span>Track tasks &amp; timelines</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">🥂</span>
                            <span>Connect with local vendors</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right form panel -->
            <div class="form-panel">
                <div class="form-card">

                    <!-- Card header -->
                    <div class="form-card-header">
                        <div class="form-logo-small">
                            <a href="{{ url('/') }}" class="logo-link" aria-label="Back to welcome page">
                                <img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan" class="w-10 h-10 object-contain">
                            </a>
                        </div>
                        <h2 class="form-title">Welcome Back</h2>
                        <p class="form-subtitle">Sign in to continue planning your dream wedding</p>
                    </div>

                    <!-- Session errors -->
                    @if (session('error'))
                        <div class="alert-error" role="alert">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ session('error') }}
                        </div>
                    @endif
                    @php
                        // Clear session error after displaying
                        session()->forget('error');
                    @endphp
                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <!-- Email -->
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

                        <!-- Password -->
                        <div class="field-group">
                            <label for="password" class="field-label">Password</label>
                            <div class="input-wrap @error('password') input-wrap-error @enderror" id="password-wrap">
                                <span class="input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="field-input"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                >
                                <button type="button" class="toggle-pw" id="togglePw" aria-label="Toggle password visibility">
                                    <svg id="eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg id="eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="field-error">{{ $message }}</p>
                            @enderror

                            @if (Route::has('password.request'))
                                <div class="forgot-row">
                                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                                </div>
                            @endif
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="submit-btn">
                            <span class="submit-btn-text">Enter</span>
                            <svg class="submit-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </form>

                    <!-- Register links -->
                    <div class="register-links">
                        <div class="register-divider">
                            <span>Don't have an account?</span>
                        </div>
                        <div class="register-btns">
                            <a href="{{ route('register.couple') }}" class="register-btn register-btn-couple">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                Register as Couple
                            </a>
                            <a href="{{ route('register.vendor') }}" class="register-btn register-btn-vendor">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                Register as Vendor
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="login-footer">
            <div class="footer-inner">
                <a href="{{ url('/') }}" class="logo-link footer-logo-link" aria-label="Back to welcome page">
                    <img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan" class="footer-logo">
                </a>
                <div class="footer-info">
                    <span class="footer-brand">WedPlan</span>
                    <span class="footer-desc">A Web &amp; Mobile-Based Wedding Budgeting And Management System</span>
                    <span class="footer-copy">Copyright &copy; {{ date('Y') }} WedPlan. All Rights Reserved.</span>
                </div>
            </div>
        </footer>
        

        
        <script>
            // Password toggle
            const togglePw = document.getElementById('togglePw');
            const pwInput  = document.getElementById('password');
            const eyeShow  = document.getElementById('eye-show');
            const eyeHide  = document.getElementById('eye-hide');
            if (togglePw) {
                togglePw.addEventListener('click', function () {
                    const isHidden = pwInput.type === 'password';
                    pwInput.type   = isHidden ? 'text' : 'password';
                    eyeShow.style.display = isHidden ? 'none'  : '';
                    eyeHide.style.display = isHidden ? ''      : 'none';
                });
            }

            // Input focus ring animation
            document.querySelectorAll('.field-input').forEach(function (input) {
                input.addEventListener('focus', function () {
                    this.closest('.input-wrap').classList.add('input-wrap-focus');
                });
                input.addEventListener('blur', function () {
                    this.closest('.input-wrap').classList.remove('input-wrap-focus');
                });
            });
        </script>
    </body>
</html>
