<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'WedPlan') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|playfair-display:400,500,600,700,700i" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen overflow-x-hidden bg-[linear-gradient(135deg,#fff5f7_0%,#ffe8ee_40%,#fde0e8_70%,#f9cdd8_100%)] text-slate-900">
        @php
            $dashboardUrl = null;

            if (auth()->check()) {
                $user = auth()->user();

                if ($user->isAdmin()) {
                    $dashboardUrl = url('/admin/dashboard');
                } elseif ($user->isVendor()) {
                    $dashboardUrl = url('/vendor/dashboard');
                } else {
                    $dashboardUrl = url('/couple/dashboard');
                }
            }
        @endphp

        <div class="relative isolate">
            <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-144 bg-[radial-gradient(circle_at_14%_18%,rgba(244,112,138,0.24),transparent_40%),radial-gradient(circle_at_88%_20%,rgba(255,199,212,0.56),transparent_38%),linear-gradient(180deg,rgba(255,255,255,0.55),rgba(255,255,255,0))]"></div>
            <div class="pointer-events-none absolute left-[8%] top-36 -z-10 h-24 w-24 rounded-full bg-[rgba(244,112,138,0.22)] blur-2xl"></div>
            <div class="pointer-events-none absolute right-[10%] top-44 -z-10 h-32 w-32 rounded-full bg-[rgba(244,112,138,0.18)] blur-3xl"></div>

            <header class="mx-auto flex w-full max-w-6xl items-center justify-end gap-3 px-5 py-6 sm:px-8 lg:px-10">
                @auth
                    <a href="{{ $dashboardUrl }}" class="inline-flex items-center rounded-2xl bg-[linear-gradient(135deg,#f47b96_0%,#e04f6d_50%,#d63061_100%)] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_28px_rgba(224,79,109,0.36)] transition duration-300 hover:-translate-y-0.5 hover:bg-[linear-gradient(135deg,#ee708c_0%,#d54564_50%,#c52858_100%)]">
                        <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 19l7-7-7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Continue Planning
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-2xl bg-[#1f2f45] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(31,47,69,0.22)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#172438]">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-2xl bg-[linear-gradient(135deg,#f47b96_0%,#e04f6d_50%,#d63061_100%)] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_28px_rgba(224,79,109,0.36)] transition duration-300 hover:-translate-y-0.5 hover:bg-[linear-gradient(135deg,#ee708c_0%,#d54564_50%,#c52858_100%)]">
                        <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <path d="M10 17l5-5-5-5" />
                            <path d="M15 12H3" />
                        </svg>
                        Login
                    </a>
                    {{-- <a href="#join" class="inline-flex items-center rounded-2xl border border-white/70 bg-white/70 px-5 py-2.5 text-sm font-semibold text-slate-800 backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:bg-white/85">
                        <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="8.5" cy="7" r="4" />
                            <path d="M20 8v6" />
                            <path d="M23 11h-6" />
                        </svg>
                        Register
                    </a> --}}
                @endauth
            </header>

            <main class="mx-auto flex w-full max-w-6xl flex-col items-center px-5 pb-12 pt-6 text-center sm:px-8 lg:px-10 lg:pt-8">
                <img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan logo" class="h-44 w-44 object-contain sm:h-48 sm:w-48">

                <h1 class="mt-5 text-5xl font-display font-semibold italic tracking-tight text-[#2d1b21] sm:text-6xl">
                    Welcome To WedPlan
                </h1>
                <p class="mt-3 max-w-3xl text-xl italic text-[#6b3d4d] sm:text-2xl">
                    Your sweet all-in-one wedding buddy for <span class="font-semibold">Sandakan &amp; Sabah</span>
                </p>
                <p class="mt-5 max-w-3xl text-sm italic leading-7 text-[#7a5561] sm:text-base">
                    Keep your budget in check, find nearby vendors, organize your guest list, and run QR check-in without the stress.
                    One cozy space for your whole wedding journey.
                </p>

                <div class="mt-9 grid w-full max-w-5xl gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="inline-flex items-center justify-center rounded-xl border border-white/80 bg-white/72 px-4 py-3 text-sm font-medium text-[#4a2030] shadow-[0_8px_20px_rgba(224,79,109,0.08)] backdrop-blur">
                        <svg class="mr-2 h-4 w-4 text-[#e04f6d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <path d="M3 10h18" />
                            <path d="M7 15h2" />
                        </svg>
                        Budget Tracking
                    </div>
                    <div class="inline-flex items-center justify-center rounded-xl border border-white/80 bg-white/72 px-4 py-3 text-sm font-medium text-[#4a2030] shadow-[0_8px_20px_rgba(224,79,109,0.08)] backdrop-blur">
                        <svg class="mr-2 h-4 w-4 text-[#e04f6d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="9" cy="8" r="4" />
                            <path d="M17 11a3 3 0 1 0 0-6" />
                            <path d="M2 20a7 7 0 0 1 14 0" />
                            <path d="M14 20a5 5 0 0 1 8 0" />
                        </svg>
                        Guest Management
                    </div>
                    <div class="inline-flex items-center justify-center rounded-xl border border-white/80 bg-white/72 px-4 py-3 text-sm font-medium text-[#4a2030] shadow-[0_8px_20px_rgba(224,79,109,0.08)] backdrop-blur">
                        <svg class="mr-2 h-4 w-4 text-[#e04f6d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M3 3h6v6H3z" />
                            <path d="M15 3h6v6h-6z" />
                            <path d="M3 15h6v6H3z" />
                            <path d="M14 14h2v2h-2z" />
                            <path d="M18 14h3v3h-3z" />
                            <path d="M14 18h3v3h-3z" />
                            <path d="M18 19h3" />
                        </svg>
                        QR Code Check-In
                    </div>
                    <div class="inline-flex items-center justify-center rounded-xl border border-white/80 bg-white/72 px-4 py-3 text-sm font-medium text-[#4a2030] shadow-[0_8px_20px_rgba(224,79,109,0.08)] backdrop-blur">
                        <svg class="mr-2 h-4 w-4 text-[#e04f6d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 3l1.8 3.6L18 8.4l-3 2.9.7 4.2L12 13.8 8.3 15.5l.7-4.2-3-2.9 4.2-.8z" />
                        </svg>
                        AI Budget Estimate
                    </div>
                </div>

                <div id="join" class="mt-10 flex w-full max-w-3xl flex-col items-center justify-center gap-3 sm:flex-row">
                    @guest
                        <a href="{{ route('register.couple') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#f47b96_0%,#e04f6d_50%,#d63061_100%)] px-7 py-3.5 text-base font-semibold text-white shadow-[0_12px_30px_rgba(224,79,109,0.36)] transition duration-300 hover:-translate-y-1 hover:bg-[linear-gradient(135deg,#ee708c_0%,#d54564_50%,#c52858_100%)] sm:w-auto sm:min-w-64">
                            <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21.2l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z" />
                            </svg>
                            Register As Couple
                        </a>
                        <a href="{{ route('register.vendor') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-[#1f2f45] px-7 py-3.5 text-base font-semibold text-white shadow-[0_12px_26px_rgba(31,47,69,0.26)] transition duration-300 hover:-translate-y-1 hover:bg-[#172438] sm:w-auto sm:min-w-64">
                            <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 21h18" />
                                <path d="M5 21V9l7-4 7 4v12" />
                                <path d="M9 21v-6h6v6" />
                            </svg>
                            Register As Vendor
                        </a>
                    @else
                        <a href="{{ $dashboardUrl }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#f47b96_0%,#e04f6d_50%,#d63061_100%)] px-7 py-3.5 text-base font-semibold text-white shadow-[0_12px_30px_rgba(224,79,109,0.36)] transition duration-300 hover:-translate-y-1 hover:bg-[linear-gradient(135deg,#ee708c_0%,#d54564_50%,#c52858_100%)] sm:w-auto sm:min-w-64">
                            <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M12 19l7-7-7-7" />
                                <path d="M19 12H5" />
                            </svg>
                            Continue Planning
                        </a>
                    @endguest
                </div>

                <p class="mt-8 text-sm text-[#9d7480]">
                    Love first, stress less.
                </p>
            </main>

            <footer class="border-t border-rose-100/70 bg-white/55 backdrop-blur">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-3 px-5 py-4 sm:px-8 sm:py-5 lg:px-10">
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" aria-label="Back to welcome page" class="inline-flex items-center justify-center">
                            <img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WedPlan" class="h-10 w-10 object-contain">
                        </a>
                        <div class="flex flex-col gap-0.5 text-left">
                            <span class="text-sm font-semibold text-slate-800">WedPlan</span>
                            <span class="text-xs text-slate-600">A Web &amp; Mobile-Based Wedding Budgeting And Management System</span>
                            <span class="text-xs font-medium text-slate-700">Copyright &copy; {{ date('Y') }} WedPlan. All Rights Reserved.</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>