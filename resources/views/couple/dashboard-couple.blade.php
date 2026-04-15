@extends('couple.layout.layout-couple')

@section('title', 'Dashboard - WedPlan')
@section('page-title', 'Welcome, ' . ($couple?->partner_1_name ?? (auth()->user()->name ?? 'Partner')) . (($couple?->partner_2_name ?? null) ? ' & ' . $couple->partner_2_name : ''))
@section('page-subtitle', 'Your Wedding Journey Is ' . ($dashboardData['progress_percentage'] ?? 0) . '% Complete')

@push('page-styles')
    @vite(['resources/css/couple/dashboard.css'])
@endpush

@section('content')
    @php
        $tasksDone = (int) ($dashboardData['tasks_done'] ?? 0);
        $tasksTotal = (int) ($dashboardData['tasks_total'] ?? 0);
        $tasksProgress = $tasksTotal > 0 ? min(100, ($tasksDone / $tasksTotal) * 100) : 0;
        $guestTotal = (int) ($dashboardData['guests_total'] ?? 0);
        $guestConfirmed = (int) ($dashboardData['guests_confirmed'] ?? 0);
        $guestProgress = $guestTotal > 0 ? min(100, ($guestConfirmed / $guestTotal) * 100) : 0;
        $weddingDate = $dashboardData['wedding_date'] ?? 'Not set';
    @endphp

    <section class="countdown-panel" data-wedding-date="{{ $weddingDate }}">
        <h3>Countdown To Your Special Day</h3>
        <p class="countdown-date">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 3V6M17 3V6M4 9H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <rect x="4" y="5" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/>
            </svg>
            <span>{{ $weddingDate }}</span>
        </p>

        <div class="countdown-grid">
            <article class="countdown-unit">
                <p class="countdown-value" id="days">{{ $dashboardData['days_until_wedding'] ?? 0 }}</p>
                <p class="countdown-label">Days</p>
            </article>
            <article class="countdown-unit">
                <p class="countdown-value" id="hours">{{ $dashboardData['hours_until_wedding'] ?? 0 }}</p>
                <p class="countdown-label">Hours</p>
            </article>
            <article class="countdown-unit">
                <p class="countdown-value" id="minutes">{{ $dashboardData['minutes_until_wedding'] ?? 0 }}</p>
                <p class="countdown-label">Minutes</p>
            </article>
            <article class="countdown-unit">
                <p class="countdown-value" id="seconds">{{ $dashboardData['seconds_until_wedding'] ?? 0 }}</p>
                <p class="countdown-label">Seconds</p>
            </article>
        </div>
    </section>

    <section class="dashboard-metrics">
        <article class="metric-card metric-card-primary">
            <div class="metric-head">
                <span class="metric-icon">Total Budget</span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="3" y="6" width="18" height="13" rx="2.4" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M15 12H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="16.5" cy="12" r="1" fill="currentColor"/>
                </svg>
            </div>
            <p class="metric-value">RM {{ number_format((float) ($dashboardData['total_budget'] ?? 0), 2) }}</p>
            <p class="metric-note">RM {{ number_format((float) ($dashboardData['budget_spent'] ?? 0), 2) }} Spent</p>
        </article>

        <article class="metric-card">
            <div class="metric-head">
                <span class="metric-icon">Guest</span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="9" cy="9" r="3" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M3.5 19C4.3 15.8 6.2 14 9 14C11.8 14 13.7 15.8 14.5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="16.5" cy="8.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </div>
            <p class="metric-value">{{ $guestTotal }}</p>
            <div class="metric-meter"><span data-meter-value="{{ $guestProgress }}"></span></div>
            <p class="metric-note">{{ $guestConfirmed }} Confirmed</p>
        </article>

        <article class="metric-card">
            <div class="metric-head">
                <span class="metric-icon">Tasklist</span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M10 6H20M10 12H20M10 18H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M4 6.2L5.2 7.4L7.4 5.2M4 12.2L5.2 13.4L7.4 11.2M4 18.2L5.2 19.4L7.4 17.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="metric-value">{{ $tasksDone }}/{{ $tasksTotal }}</p>
            <div class="metric-meter"><span data-meter-value="{{ $tasksProgress }}"></span></div>
            <p class="metric-note">{{ round($tasksProgress) }} % Completed</p>
        </article>

        <article class="metric-card">
            <div class="metric-head">
                <span class="metric-icon">Vendor</span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 10H20L18.5 6H5.5L4 10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M5 10V19H19V10" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M10 19V14H14V19" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="metric-value">{{ $dashboardData['vendors_booked'] ?? 0 }}</p>
            <p class="metric-note">{{ $dashboardData['vendors_pending'] ?? 0 }} Pending</p>
        </article>
    </section>

    <section class="dashboard-secondary-grid">
        <article class="panel-card">
            <header class="panel-card-head">
                <h4>Upcoming Tasks</h4>
                @if(Route::has('couple.tasks.index'))
                    <a href="{{ route('couple.tasks.index') }}">View all</a>
                @endif
            </header>

            <div class="panel-card-list">
                @if(isset($dashboardData['upcoming_tasks']) && count($dashboardData['upcoming_tasks']) > 0)
                    @foreach($dashboardData['upcoming_tasks'] as $task)
                        <div class="panel-row">
                            <div>
                                <p>{{ $task['title'] }}</p>
                                <small>{{ $task['due_date'] }}</small>
                            </div>
                            <span class="panel-row-tag">Pending</span>
                        </div>
                    @endforeach
                @else
                    <div class="panel-empty">
                        <p>No upcoming tasks</p>
                        @if(Route::has('couple.tasks.create'))
                            <a href="{{ route('couple.tasks.create') }}">Create a task</a>
                        @endif
                    </div>
                @endif
            </div>
        </article>

        <article class="panel-card">
            <header class="panel-card-head">
                <h4>Budget Breakdown</h4>
                @if(Route::has('couple.budget.index'))
                    <a href="{{ route('couple.budget.index') }}">Manage</a>
                @endif
            </header>

            <div class="panel-card-list">
                @if(isset($dashboardData['budget_categories']) && count($dashboardData['budget_categories']) > 0)
                    @foreach($dashboardData['budget_categories'] as $category)
                        <div class="panel-row panel-row-budget">
                            <div class="panel-budget-copy">
                                <p>{{ $category['name'] }}</p>
                                <div class="metric-meter"><span data-meter-value="{{ (int) ($category['percentage'] ?? 0) }}"></span></div>
                            </div>
                            <strong>RM {{ number_format((float) ($category['amount'] ?? 0), 2) }}</strong>
                        </div>
                    @endforeach
                @else
                    <div class="panel-empty">
                        <p>No budget categories yet</p>
                        @if(Route::has('couple.budget.create'))
                            <a href="{{ route('couple.budget.create') }}">Set up budget</a>
                        @endif
                    </div>
                @endif
            </div>
        </article>
    </section>

    <section class="quick-actions-panel">
        <h4>Quick Actions</h4>
        <div class="quick-actions-grid">
            @if(Route::has('couple.budget.index'))
                <a href="{{ route('couple.budget.index') }}" class="quick-action-btn">
                    <span>Manage Budget</span>
                </a>
            @endif
            @if(Route::has('couple.guests.index'))
                <a href="{{ route('couple.guests.index') }}" class="quick-action-btn">
                    <span>Manage Guests</span>
                </a>
            @endif
            @if(Route::has('couple.tasks.index'))
                <a href="{{ route('couple.tasks.index') }}" class="quick-action-btn">
                    <span>View Tasks</span>
                </a>
            @endif
            @if(Route::has('couple.settings.index'))
                <a href="{{ route('couple.settings.index') }}" class="quick-action-btn">
                    <span>Settings</span>
                </a>
            @endif
        </div>
    </section>
@endsection

@push('page-scripts')
    @vite(['resources/js/couple/dashboard.js'])
@endpush
