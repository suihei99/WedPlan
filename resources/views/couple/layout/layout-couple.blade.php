<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WedPlan - Wedding Planning')</title>
    @vite(['resources/css/app.css', 'resources/css/couple/layout-couple.css'])
    @stack('page-styles')
</head>
<body class="couple-app">
    <div class="couple-app-shell">
        <aside class="couple-sidebar" data-couple-sidebar>
            <div class="sidebar-logo-card">
                <a href="{{ route('couple.dashboard') }}" class="sidebar-logo-link">
                    <img src="{{ asset('assets/icons/WebPlan_logo.webp') }}" alt="WebPlan logo" class="sidebar-logo-image">
                    <div class="sidebar-logo-copy">
                        <h1>WedPlan</h1>
                        <p>Wedding Planner</p>
                    </div>
                </a>
            </div>

            <section class="sidebar-couple-card" aria-label="Couple profile">
                <div class="sidebar-couple-avatar">
                    @php
                        $fallbackName = auth()->user()->email ?? 'B';
                        $primaryName = isset($couple) ? ($couple->partner_1_name ?? null) : null;
                        $partnerName = isset($couple) ? ($couple->partner_2_name ?? null) : null;
                        $initialA = strtoupper(substr($primaryName ?? $fallbackName, 0, 1));
                        $initialB = strtoupper(substr($partnerName ?? 'J', 0, 1));
                    @endphp
                    <span>{{ $initialA }}&{{ $initialB }}</span>
                </div>
                <div>
                    <h2>{{ $primaryName ?? (auth()->user()->name ?? 'Partner') }}{{ $partnerName ? ' & ' . $partnerName : '' }}</h2>
                    <p>Couple</p>
                </div>
            </section>

            <nav class="sidebar-nav" aria-label="Couple navigation">
                @php
                    $menuItems = [
                        ['icon' => 'home', 'label' => 'Dashboard', 'route' => 'couple.dashboard', 'current' => 'couple.dashboard'],
                        ['icon' => 'wallet', 'label' => 'Budget', 'route' => 'couple.budget.index', 'current' => 'couple.budget.*'],
                        ['icon' => 'store', 'label' => 'Vendors', 'route' => 'couple.vendorlist.index', 'current' => 'couple.vendorlist.*'],
                        ['icon' => 'users', 'label' => 'Guest', 'route' => 'couple.guests.index', 'current' => 'couple.guests.*'],
                        ['icon' => 'checklist', 'label' => 'Tasklist', 'route' => 'couple.tasks.index', 'current' => 'couple.tasks.*'],
                        ['icon' => 'sparkles', 'label' => 'AI Estimate Budget', 'route' => 'couple.ai.budget-estimation', 'current' => 'couple.ai.*'],
                        ['icon' => 'settings', 'label' => 'Settings', 'route' => 'couple.settings.index', 'current' => 'couple.settings.*'],
                    ];
                @endphp

                @foreach($menuItems as $item)
                    @php
                        $hasRoute = $item['route'] && Route::has($item['route']);
                        $isActive = $item['current'] ? request()->routeIs($item['current']) : false;
                    @endphp

                    @if($hasRoute)
                        <a href="{{ route($item['route']) }}" class="sidebar-nav-item{{ $isActive ? ' active' : '' }}">
                            @include('couple.layout.partials.nav-icon', ['icon' => $item['icon']])
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @else
                        <span class="sidebar-nav-item disabled" aria-disabled="true">
                            @include('couple.layout.partials.nav-icon', ['icon' => $item['icon']])
                            <span>{{ $item['label'] }}</span>
                        </span>
                    @endif
                @endforeach
            </nav>

            <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout-button">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9 7C9 5.343 10.343 4 12 4H17C18.657 4 20 5.343 20 7V17C20 18.657 18.657 20 17 20H12C10.343 20 9 18.657 9 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 12H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 9L4 12L7 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="couple-main">
            <header class="couple-topbar">
                <button type="button" class="mobile-menu-button" data-mobile-menu-toggle aria-label="Toggle navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="topbar-headings">
                    <h2>@yield('page-title', 'Dashboard')</h2>
                    <p>@yield('page-subtitle', '')</p>
                </div>

                <div class="topbar-actions">
                    <button type="button" class="topbar-bell" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 17H9C7.343 17 6 15.657 6 14V10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14C18 15.657 16.657 17 15 17Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M4 17H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M10 20C10.355 20.622 11.078 21 12 21C12.922 21 13.645 20.622 14 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </header>

            <section class="couple-page-content">
                @yield('content')
            </section>
        </main>
    </div>

    @vite(['resources/js/couple/layout-couple.js'])
    @stack('page-scripts')
</body>
</html>
