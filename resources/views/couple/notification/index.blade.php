@extends('couple.layout.layout-couple')

@section('title', 'Notifications - WedPlan')
@section('page-title', 'Notifications')
@section('page-subtitle', 'Manage your booking and planning notifications.')

@push('page-styles')
    @vite(['resources/css/couple/notification.css'])
@endpush

@section('content')
    <!-- Hero Section with Stats -->
    <section class="notification-hero">
        <div class="notification-hero__content">
            <div>
                <h1>Your Notifications</h1>
                <p>Stay updated with vendor bookings, planning updates, and wedding reminders.</p>
            </div>
        </div>
        <div class="notification-hero__metrics">
            <div class="metric-card">
                <div class="metric-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="metric-label">Total</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $stats['unread'] ?? 0 }}</div>
                <div class="metric-label">Unread</div>
            </div>
        </div>
    </section>

    <!-- Notifications List -->
    <section class="notification-list">
        @if($notifications->count() > 0)
            @foreach($notifications as $notification)
                <div class="notification-card" data-notification-id="{{ $notification->id }}" data-read="{{ $notification->is_read ? 'read' : 'unread' }}">
                    <div class="notification-card__icon">
                        @if(str_contains(strtolower($notification->title), 'booking'))
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        @else
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 2.2"/>
                            </svg>
                        @endif
                    </div>

                    <div class="notification-card__content">
                        <div class="notification-card__title-group">
                            <h3 class="notification-card__title">{{ $notification->title }}</h3>
                            @if(!$notification->is_read)
                                <span class="notification-badge">New</span>
                            @endif
                        </div>
                        
                        <p class="notification-card__message">{{ $notification->message }}</p>

                        <div class="notification-card__meta">
                            <span class="notification-card__date">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    @if(!$notification->is_read)
                        <button 
                            type="button" 
                            class="notification-card__action"
                            data-mark-read 
                            data-notification-id="{{ $notification->id }}"
                            aria-label="Mark as read"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="10"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endforeach

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="pagination-wrapper">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            <div class="notification-empty">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                    <path d="M15 17H9C7.343 17 6 15.657 6 14V10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14C18 15.657 16.657 17 15 17Z"/>
                    <path d="M4 17H20"/>
                    <path d="M10 20C10.355 20.622 11.078 21 12 21C12.922 21 13.645 20.622 14 20"/>
                </svg>
                <h2>No Notifications</h2>
                <p>You're all caught up! Check back soon for updates on your wedding planning.</p>
            </div>
        @endif
    </section>

    @push('page-scripts')
        <script>
            document.querySelectorAll('[data-mark-read]').forEach((button) => {
                button.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const notificationId = button.getAttribute('data-notification-id');
                    const card = button.closest('[data-notification-id]');

                    try {
                        const response = await fetch(`/couple/notifications/${notificationId}/read`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                        });

                        if (response.ok) {
                            card.setAttribute('data-read', 'read');
                            card.classList.remove('unread');
                            button.remove();
                        }
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                });
            });
        </script>
    @endpush
@endsection
