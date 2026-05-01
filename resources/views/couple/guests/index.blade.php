@extends('couple.layout.layout-couple')

@section('title', 'Guest List - WebPlan')
@section('page-title', 'Guest Management')
@section('page-subtitle', 'Manage your guest list and send personalized invitations with QR codes.')

@push('page-styles')
    @vite(['resources/css/couple/guests.css'])
@endpush

@section('content')
    @php
        $guestCollection = collect($guests ?? []);
        $totalGuests = $guestCollection->count();
        $confirmedGuests = $guestCollection->where('rsvp_status', 'confirmed')->count();
        $pendingGuests = $guestCollection->where('rsvp_status', 'pending')->count();
        $declinedGuests = $guestCollection->where('rsvp_status', 'declined')->count();
    @endphp

    <div class="guests-page" data-guests-page>
        @if(session('success'))
            <section class="guests-flash guests-flash-success" role="status">
                <strong>Success</strong>
                <span>{{ session('success') }}</span>
            </section>
        @endif

        @if($errors->any())
            <section class="guests-flash guests-flash-error" role="alert">
                <strong>Please review the form</strong>
                <span>{{ $errors->first() }}</span>
            </section>
        @endif

        <!-- Hero Section -->
        <section class="guests-hero">
            <div>
                <span class="guests-kicker">Wedding Guest Coordination</span>
                <h1 class="guests-title">Guest Management</h1>
                <p class="guests-subtitle">Keep track of your guest list, send personalized invitations with QR codes, and manage RSVPs all in one place.</p>
            </div>

            <div class="guests-hero-stats">
                <article>
                    <span>Total Guests</span>
                    <strong>{{ $totalGuests }}</strong>
                </article>
                <article>
                    <span>Confirmed</span>
                    <strong>{{ $confirmedGuests }}</strong>
                </article>
                <article>
                    <span>Pending</span>
                    <strong>{{ $pendingGuests }}</strong>
                </article>
                <article>
                    <span>Declined</span>
                    <strong>{{ $declinedGuests }}</strong>
                </article>
            </div>
        </section>

        <!-- Toolbar -->
        <section class="guests-toolbar">
            <div class="guests-toolbar-search">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 16L20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <input type="search" placeholder="Search guest name or contact..." data-guest-search>
            </div>

            <div class="guests-toolbar-filters">
                <select class="guests-filter-select" data-guest-status-filter aria-label="Guest status">
                    <option value="all">All Status</option>
                    <option value="invited">Invited</option>
                    <option value="pending">Pending Response</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="declined">Declined</option>
                </select>

                @if(Route::has('couple.guests.create'))
                    <a href="{{ route('couple.guests.create') }}" class="guests-add-btn">
                        <span>+</span>
                        Add Guest
                    </a>
                @endif
            </div>
        </section>

        <!-- Guests Container -->
        <section class="guests-container" data-guests-container>
            @forelse($guestCollection as $guest)
                @php
                    $rsvpStatus = strtolower($guest->rsvp_status ?? 'pending');
                    $inviteStatus = $guest->invite_code ? 'invited' : 'pending';
                    $statusLabel = match($rsvpStatus) {
                        'confirmed' => 'Confirmed',
                        'declined' => 'Declined',
                        'pending' => 'Pending Response',
                        default => 'Not Invited',
                    };
                    $searchText = strtolower(($guest->name ?? '') . ' ' . ($guest->phone ?? ''));
                @endphp
                <article
                    class="guests-card"
                    data-guest-card
                    data-guest-status="{{ $rsvpStatus }}"
                    data-search-text="{{ $searchText }}"
                    data-invite-status="{{ $inviteStatus }}"
                >
                    <div class="guests-card-head">
                        <div class="guests-card-info">
                            <h3 class="guests-card-name">{{ $guest->name }}</h3>
                            <p class="guests-card-contact">
                                📱 {{ $guest->phone ?? 'No contact' }}
                            </p>
                        </div>
                        <span class="guests-card-status is-{{ $rsvpStatus }}">
                            <span>●</span>
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="guests-card-meta">
                        <div class="guests-card-meta-item">
                            <span class="guests-card-meta-label">Pax Count</span>
                            <span class="guests-card-meta-value">{{ $guest->pax_count ?? 1 }}</span>
                        </div>
                        <div class="guests-card-meta-item">
                            <span class="guests-card-meta-label">Invite Code</span>
                            <span class="guests-card-meta-value">{{ $guest->invite_code ?? 'N/A' }}</span>
                        </div>
                        <div class="guests-card-meta-item">
                            <span class="guests-card-meta-label">QR Ready</span>
                            <span class="guests-card-meta-value">{{ $guest->qr_code_string ? '✓' : '—' }}</span>
                        </div>
                    </div>

                    <div class="guests-card-actions">
                        @if(Route::has('couple.guests.show'))
                            <a href="{{ route('couple.guests.show', $guest) }}" class="guests-card-link">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19H4v-3L16.5 3.5z"/>
                                </svg>
                                Details
                            </a>
                        @endif
                        @if(Route::has('couple.guests.checkin'))
                            <form method="POST" action="{{ route('couple.guests.checkin', $guest) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="guests-card-link" onclick="return confirm('Mark RSVP as confirmed?')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    Check-In
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <section class="guests-empty">
                    <h3>🎭 No guests added yet</h3>
                    <p>Start building your guest list to manage invitations and RSVPs.</p>
                    @if(Route::has('couple.guests.create'))
                        <a href="{{ route('couple.guests.create') }}" class="guests-add-btn">
                            <span>+</span>
                            Add Your First Guest
                        </a>
                    @endif
                </section>
            @endforelse
        </section>

        @if($guestCollection->count() > 0)
            <footer class="guests-pagination" data-guests-pagination>
                <button type="button" data-guest-page-prev aria-label="Previous page">←</button>
                <span data-guest-page-current>1</span>
                <button type="button" data-guest-page-next aria-label="Next page">→</button>
            </footer>
        @endif
    </div>
@endsection

@push('page-scripts')
    @vite(['resources/js/couple/guests.js'])
@endpush

