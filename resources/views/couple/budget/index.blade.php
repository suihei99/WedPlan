@extends('couple.layout.layout-couple')

@section('title', 'Budget - WebPlan')
@section('page-title', 'Budget Overview')
@section('page-subtitle', 'Plan and track every wedding expense in one place.')

@push('page-styles')
    @vite(['resources/css/couple/budget.css'])
@endpush

@section('content')
    @php
        $categories = collect($summary['categories'] ?? []);
        $totalBudgetLimit = (float) ($summary['total_budget_limit'] ?? 0);
        $totalAllocated = (float) ($summary['total_allocated'] ?? 0);
        $totalSpent = (float) ($summary['total_spent'] ?? 0);
        $remainingBudget = (float) ($summary['remaining'] ?? max(0, $totalBudgetLimit - $totalSpent));
        $overallUsage = $totalBudgetLimit > 0 ? min(100, ($totalSpent / $totalBudgetLimit) * 100) : 0;
        $categoryCount = $categories->count();
        $overspentCount = $categories->where('is_overspent', true)->count();
        $balancedCount = max(0, $categoryCount - $overspentCount);
    @endphp

    <div class="budget-page">
        @if(session('success'))
            <section class="budget-flash budget-flash-success" role="status">
                <strong>Success</strong>
                <span>{{ session('success') }}</span>
            </section>
        @endif

        @if($errors->any())
            <section class="budget-flash budget-flash-error" role="alert">
                <strong>Please review the form</strong>
                <span>{{ $errors->first() }}</span>
            </section>
        @endif

        <section class="budget-hero">
            <div class="budget-hero-top">
                <div>
                    <span class="budget-kicker">Wedding Budget</span>
                    <h1 class="budget-title">Budget Overview</h1>
                    <p class="budget-subtitle">Track spending, compare allocations, and keep every category aligned with your wedding plan.</p>
                </div>

                <div class="budget-actions">
                    @if(Route::has('couple.budget.create'))
                        <a href="{{ route('couple.budget.create') }}" class="budget-action">Add Category</a>
                    @endif
                </div>
            </div>
        </section>

        <section class="budget-stats">
            <article class="budget-stat-card">
                <p class="budget-stat-label">Total Budget</p>
                <p class="budget-stat-value">RM {{ number_format($totalBudgetLimit, 2) }}</p>
                <p class="budget-stat-note">Overall limit set for the wedding.</p>
            </article>
            <article class="budget-stat-card">
                <p class="budget-stat-label">Allocated</p>
                <p class="budget-stat-value">RM {{ number_format($totalAllocated, 2) }}</p>
                <p class="budget-stat-note">Approved across all categories.</p>
            </article>
            <article class="budget-stat-card">
                <p class="budget-stat-label">Spent</p>
                <p class="budget-stat-value">RM {{ number_format($totalSpent, 2) }}</p>
                <p class="budget-stat-note">Recorded expenses so far.</p>
            </article>
            <article class="budget-stat-card">
                <p class="budget-stat-label">Remaining</p>
                <p class="budget-stat-value">RM {{ number_format($remainingBudget, 2) }}</p>
                <p class="budget-stat-note">Money still available to plan.</p>
            </article>
        </section>

        <section class="budget-progress-panel">
            <div class="budget-progress-head">
                <h2 class="budget-progress-title">Budget Health</h2>
                <div class="budget-progress-meta">
                    <span class="budget-pill">{{ $categoryCount }} Categories</span>
                    <span class="budget-pill">{{ $balancedCount }} Balanced</span>
                    <span class="budget-pill">{{ $overspentCount }} Over Budget</span>
                </div>
            </div>
            <div class="budget-progress-track">
                <span class="budget-progress-fill" data-budget-progress="{{ $overallUsage }}"></span>
            </div>
            <div class="budget-progress-foot">
                <span>{{ round($overallUsage) }}% of total budget used</span>
                <span>RM {{ number_format($remainingBudget, 2) }} left to allocate</span>
            </div>
        </section>

        <section class="budget-toolbar">
            <div class="budget-filter-group" aria-label="Budget filters">
                <button type="button" class="budget-filter-button is-active" data-budget-filter="all">All</button>
                <button type="button" class="budget-filter-button" data-budget-filter="healthy">Healthy</button>
                <button type="button" class="budget-filter-button" data-budget-filter="over">Over Budget</button>
            </div>

            @if(Route::has('couple.budget.limit'))
                <form class="budget-form" method="POST" action="{{ route('couple.budget.limit') }}">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="total_budget_limit">Update Budget Limit</label>
                        <input id="total_budget_limit" name="total_budget_limit" type="number" min="0" step="0.01" value="{{ old('total_budget_limit', $totalBudgetLimit > 0 ? $totalBudgetLimit : '') }}" placeholder="Enter new total budget limit">
                    </div>
                    <button type="submit" class="budget-action">Save Limit</button>
                </form>
            @endif
        </section>

        @if($categories->isNotEmpty())
            <section class="budget-grid">
                @foreach($categories as $category)
                    @php
                        $allocatedAmount = (float) ($category['allocated_amount'] ?? 0);
                        $spentAmount = (float) ($category['total_spent'] ?? 0);
                        $remainingAmount = (float) ($category['remaining_budget'] ?? max(0, $allocatedAmount - $spentAmount));
                        $categoryUsage = $allocatedAmount > 0 ? min(100, ($spentAmount / $allocatedAmount) * 100) : 0;
                        $isOver = (bool) ($category['is_overspent'] ?? false);
                    @endphp
                    <article class="budget-card" data-budget-card data-budget-card-status="{{ $isOver ? 'over' : 'healthy' }}">
                        <div class="budget-card-head">
                            <div class="budget-card-title-wrap">
                                <div class="budget-card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M4 10H20L18.5 6H5.5L4 10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M5 10V19H19V10" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M10 19V14H14V19" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="budget-card-title">
                                    <h3>{{ $category['category_name'] }}</h3>
                                    <p>RM {{ number_format($spentAmount, 2) }} spent of RM {{ number_format($allocatedAmount, 2) }}</p>
                                </div>
                            </div>
                            <span class="budget-status {{ $isOver ? 'is-over' : 'is-balanced' }}">
                                {{ $isOver ? 'Over Budget' : 'On Track' }}
                            </span>
                        </div>

                        <div class="budget-card-progress">
                            <div class="budget-card-progress-track">
                                <span class="budget-card-progress-fill" data-budget-progress="{{ $categoryUsage }}"></span>
                            </div>
                            <div class="budget-card-progress-meta">
                                <span>{{ round($categoryUsage) }}% used</span>
                                <span>{{ $isOver ? 'Overspent by RM ' . number_format(abs($remainingAmount), 2) : 'RM ' . number_format($remainingAmount, 2) . ' left' }}</span>
                            </div>
                        </div>

                        <div class="budget-card-foot">
                            <div class="budget-card-links">
                                @if(Route::has('couple.budget.show'))
                                    <a class="budget-card-link" href="{{ route('couple.budget.show', $category['id']) }}">Details</a>
                                @endif
                                @if(Route::has('couple.budget.expenses'))
                                    <a class="budget-card-link" href="{{ route('couple.budget.expenses', $category['id']) }}">Expenses</a>
                                @endif
                            </div>

                            <div class="budget-card-links">
                                @if(Route::has('couple.budget.show'))
                                    <a class="budget-card-link" href="{{ route('couple.budget.show', $category['id']) }}">Manage Category</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <section class="budget-empty">
                <h3>No budget categories yet</h3>
                <p>Create your first category to start tracking wedding spending.</p>
                @if(Route::has('couple.budget.create'))
                    <a href="{{ route('couple.budget.create') }}" class="budget-action">Create Category</a>
                @endif
            </section>
        @endif

        <section class="budget-layout-split">
            <article class="budget-side-card">
                <h4>Budget Planning Notes</h4>
                <p>Use your category cards to keep venue, catering, photography, and dress spending visible in one place.</p>
                <div class="budget-side-list">
                    <div class="budget-side-item">
                        <div>
                            <strong>Healthy categories</strong>
                            <small>{{ $balancedCount }} stay within budget</small>
                        </div>
                        <span>{{ $balancedCount }}</span>
                    </div>
                    <div class="budget-side-item">
                        <div>
                            <strong>Categories over limit</strong>
                            <small>{{ $overspentCount }} need attention</small>
                        </div>
                        <span>{{ $overspentCount }}</span>
                    </div>
                </div>
            </article>

            <article class="budget-side-card">
                <h4>Quick Links</h4>
                <p>Jump to budget workflows without leaving the wedding planning flow.</p>
                <div class="budget-side-list">
                    @if(Route::has('couple.budget.create'))
                        <a class="budget-card-link" href="{{ route('couple.budget.create') }}">Add category</a>
                    @endif
                    @if(Route::has('couple.dashboard'))
                        <a class="budget-card-link" href="{{ route('couple.dashboard') }}">Back to dashboard</a>
                    @endif
                </div>
            </article>
        </section>
    </div>
@endsection

@push('page-scripts')
    @vite(['resources/js/couple/budget.js'])
@endpush
