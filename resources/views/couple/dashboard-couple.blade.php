@extends('couple.layout.layout-couple')

@section('title', 'Dashboard - WebPlan')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome Back! Here\'s your wedding planning overview.')

@push('page-styles')
    @vite(['resources/css/couple/dashboard.css'])
@endpush

@section('content')
    @php
        $couple = auth()->user()?->couple;
        $tasksDone = (int) ($dashboardData['tasks_done'] ?? 0);
        $tasksTotal = (int) ($dashboardData['tasks_total'] ?? 0);
        $tasksProgress = $tasksTotal > 0 ? min(100, ($tasksDone / $tasksTotal) * 100) : 0;
        $weddingDaysLeft = $dashboardData['days_until_wedding'] ?? 0;
    @endphp

    <div class="dashboard-page">
        <!-- Welcome Hero -->
        <section class="dashboard-welcome" style="margin-bottom: 1.5rem;">
            <div>
                <h1 style="margin: 0; font-size: 2rem; font-weight: 800; color: #201419;">
                    Welcome, {{ $couple?->partner_1_name ?? 'Partner 1' }} & {{ $couple?->partner_2_name ?? 'Partner 2' }} 👋
                </h1>
                <p style="margin: 0.3rem 0 0; color: #715b64; font-size: 0.95rem;">
                    Your wedding journey is <span style="font-weight: 700; color: #d54c6d;">{{ round($tasksProgress) }}% complete</span>
                </p>
            </div>
        </section>

        <!-- Countdown Section -->
        <section class="countdown-panel" style="margin-bottom: 1.5rem;">
            <h3>Countdown To Your Special Day</h3>
            <p class="countdown-date">
                {{ $couple?->wedding_date ? \Carbon\Carbon::parse($couple->wedding_date)->format('F j, Y') : 'Date not set' }}
            </p>
        
                    <div class="countdown-grid">
                        <div class="countdown-unit">
                            <p class="countdown-value" id="days">{{ $weddingDaysLeft }}</p>
                            <p class="countdown-label">Days</p>
                        </div>
                        <div class="countdown-unit">
                            <p class="countdown-value" id="hours">00</p>
                            <p class="countdown-label">Hours</p>
                        </div>
                        <div class="countdown-unit">
                            <p class="countdown-value" id="minutes">00</p>
                            <p class="countdown-label">Minutes</p>
                        </div>
                        <div class="countdown-unit">
                            <p class="countdown-value" id="seconds">00</p>
                            <p class="countdown-label">Seconds</p>
                        </div>
                    </div>
                </section>

		<!-- Metrics Section -->
		<section class="dashboard-metrics" style="margin-bottom: 1.5rem;">
			<!-- Budget Card (Primary) -->
			<article class="metric-card metric-card-primary">
				<div class="metric-head">
					<div style="flex: 1;">
						<p style="margin: 0; font-size: 0.85rem; opacity: 0.9;">Total Budget</p>
					</div>
					<span class="metric-icon">💰</span>
				</div>
				<p class="metric-value">RM {{ number_format($dashboardData['budget']['total_budget_limit'] ?? 0, 0) }}</p>
				<p class="metric-note">
					RM {{ number_format($dashboardData['budget']['remaining'] ?? 0, 0) }} remaining
				</p>
			</article>

            <!-- Guests Card -->
            <article class="metric-card">
                <div class="metric-head">
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 0.85rem; color: #876f79;">Guests Invited</p>
                    </div>
                    <span class="metric-icon">👥</span>
                </div>
                <p class="metric-value">{{ $dashboardData['guests_summary']['total'] ?? 0 }}</p>
                <p class="metric-note">
                    {{ $dashboardData['guests_summary']['confirmed'] ?? 0 }} confirmed
                </p>
            </article>

            <!-- Tasks Card -->
            <article class="metric-card">
                <div class="metric-head">
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 0.85rem; color: #876f79;">Tasks Progress</p>
                    </div>
                    <span class="metric-icon">✓</span>
                </div>
                <p class="metric-value">{{ $tasksDone }}/{{ $tasksTotal }}</p>
                <div class="metric-meter">
                    <span style="width: {{ $tasksProgress }}%"></span>
                </div>
            </article>

			<!-- Vendors Card -->
			<article class="metric-card">
				<div class="metric-head">
					<div style="flex: 1;">
						<p style="margin: 0; font-size: 0.85rem; color: #876f79;">Vendors Booked</p>
					</div>
					<span class="metric-icon">🏬</span>
				</div>
				<p class="metric-value">{{ $dashboardData['vendors_booked'] ?? 0 }}</p>
				<p class="metric-note">
					{{ $dashboardData['vendors_pending'] ?? 0 }} pending confirmation
				</p>
			</article>
		</section>

		<!-- Secondary Grid -->
		<section class="dashboard-secondary-grid" style="margin-bottom: 1.5rem;">
			<!-- Upcoming Tasks Panel -->
			<article class="panel-card">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
					<h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #201419;">Upcoming Tasks</h3>
					@if(Route::has('couple.tasks.index'))
						<a href="{{ route('couple.tasks.index') }}" style="color: #d54c6d; text-decoration: none; font-size: 0.85rem; font-weight: 600;">View all →</a>
					@endif
				</div>

				@if(isset($dashboardData['upcoming_tasks']) && count($dashboardData['upcoming_tasks']) > 0)
					<div style="display: grid; gap: 0.6rem;">
						@foreach($dashboardData['upcoming_tasks'] as $task)
							<div style="padding: 0.6rem; background: #fef7fa; border-radius: 0.6rem; border-left: 3px solid #d54c6d;">
								<p style="margin: 0; font-size: 0.9rem; font-weight: 600; color: #201419;">{{ $task['task_name'] ?? 'Task' }}</p>
								<p style="margin: 0.2rem 0 0; font-size: 0.8rem; color: #876f79;">📅 {{ $task['deadline'] ?? 'No date' }}</p>
							</div>
						@endforeach
					</div>
				@else
					<div style="text-align: center; padding: 1.2rem 0;">
						<p style="margin: 0; color: #715b64; font-size: 0.9rem;">No upcoming tasks</p>
						@if(Route::has('couple.tasks.create'))
							<a href="{{ route('couple.tasks.create') }}" style="display: inline-block; margin-top: 0.6rem; color: #d54c6d; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
								Create a task →
							</a>
						@endif
					</div>
				@endif
			</article>

			<!-- Budget Breakdown Panel -->
			<article class="panel-card">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
					<h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #201419;">Budget Breakdown</h3>
					@if(Route::has('couple.budget.index'))
						<a href="{{ route('couple.budget.index') }}" style="color: #d54c6d; text-decoration: none; font-size: 0.85rem; font-weight: 600;">Manage →</a>
					@endif
				</div>

                @if(isset($dashboardData['budget']['categories']) && count($dashboardData['budget']['categories']) > 0)
                    <div style="display: grid; gap: 0.8rem;">
                        @foreach($dashboardData['budget']['categories'] as $category)
                            @php
                                $allocated = (float) ($category['allocated_amount'] ?? 0);
                                $spent = (float) ($category['total_spent'] ?? 0);
                                $percentage = $allocated > 0 ? min(100, ($spent / $allocated) * 100) : 0;
                            @endphp
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                                    <p style="margin: 0; font-size: 0.9rem; font-weight: 600; color: #201419;">{{ $category['category_name'] }}</p>
                                    <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: #d54c6d;">RM {{ number_format($spent, 2) }}</p>
                                </div>
                                <div style="width: 100%; height: 8px; border-radius: 4px; background: #f3d8df; overflow: hidden;">
                                    <div style="height: 100%; width: {{ $percentage }}%; background: linear-gradient(90deg, #ef7f9c 0%, #e15576 100%); border-radius: 4px; transition: width 0.3s ease;"></div>
                                </div>
                                <p style="margin: 0.2rem 0 0; font-size: 0.75rem; color: #876f79;">{{ round($percentage) }}% of RM {{ number_format($allocated, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 1.2rem 0;">
                        <p style="margin: 0; color: #715b64; font-size: 0.9rem;">No budget categories yet</p>
                        @if(Route::has('couple.budget.create'))
                            <a href="{{ route('couple.budget.create') }}" style="display: inline-block; margin-top: 0.6rem; color: #d54c6d; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                Set up budget →
                            </a>
                        @endif
                    </div>
                @endif
            </article>
        </section>

        <!-- Quick Actions -->
        <section style="background: linear-gradient(145deg, #fff7fa 0%, #ffe8ee 100%); border: 1px solid #efd7df; border-radius: 0.85rem; padding: 1.2rem;">
            <h3 style="margin: 0 0 1rem; font-size: 1rem; font-weight: 700; color: #201419;">Quick Actions</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.8rem;">
                @if(Route::has('couple.budget.index'))
                    <a href="{{ route('couple.budget.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem; background: #fff; border: 1px solid #efd7df; border-radius: 0.7rem; text-decoration: none; transition: all 0.2s ease; color: #201419;">
                        <span style="font-size: 1.8rem;">💰</span>
                        <span style="font-size: 0.85rem; font-weight: 600; text-align: center;">Manage Budget</span>
                    </a>
                @endif

                @if(Route::has('couple.guests.index'))
                    <a href="{{ route('couple.guests.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem; background: #fff; border: 1px solid #efd7df; border-radius: 0.7rem; text-decoration: none; transition: all 0.2s ease; color: #201419;">
                        <span style="font-size: 1.8rem;">👥</span>
                        <span style="font-size: 0.85rem; font-weight: 600; text-align: center;">Manage Guests</span>
                    </a>
                @endif

                @if(Route::has('couple.tasks.index'))
                    <a href="{{ route('couple.tasks.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem; background: #fff; border: 1px solid #efd7df; border-radius: 0.7rem; text-decoration: none; transition: all 0.2s ease; color: #201419;">
                        <span style="font-size: 1.8rem;">✓</span>
                        <span style="font-size: 0.85rem; font-weight: 600; text-align: center;">View Tasks</span>
                    </a>
                @endif

                @if(Route::has('couple.settings.index'))
                    <a href="{{ route('couple.settings.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem; background: #fff; border: 1px solid #efd7df; border-radius: 0.7rem; text-decoration: none; transition: all 0.2s ease; color: #201419;">
                        <span style="font-size: 1.8rem;">⚙️</span>
                        <span style="font-size: 0.85rem; font-weight: 600; text-align: center;">Settings</span>
                    </a>
                @endif
            </div>
        </section>
    </div>

    @push('page-scripts')
        <script>
            // Countdown Timer
            function updateCountdown() {
                @if($couple && $couple->wedding_date)
                    const weddingDate = new Date('{{ $couple->wedding_date }}').getTime();
                    const now = new Date().getTime();
                    const distance = weddingDate - now;

                    if (distance > 0) {
                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        document.getElementById('days').textContent = days;
                        document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                        document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                        document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
                    }
                @endif
            }

            // Update countdown every second
            setInterval(updateCountdown, 1000);
            updateCountdown();
        </script>
    @endpush
@endsection
