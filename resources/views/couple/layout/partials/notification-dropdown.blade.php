<div class="notification-dropdown" data-notification-dropdown>
    <div class="notification-header">
        <h3>Notifications</h3>
        @if($unreadCount > 0)
            <button type="button" class="mark-all-read" data-mark-all-read aria-label="Mark all as read">
                Mark all as read
            </button>
        @endif
    </div>

    <div class="notification-list">
        @forelse($notifications as $notification)
            <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}" data-notification-id="{{ $notification->id }}" data-notification-unread="{{ !$notification->is_read ? 'true' : 'false' }}">
                <div class="notification-content">
                    <div class="notification-badge">
                        @if(str_contains(strtolower($notification->title), 'booking'))
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        @else
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 2.2"/>
                            </svg>
                        @endif
                    </div>
                    <div class="notification-text">
                        <p class="notification-title">{{ $notification->title }}</p>
                        <p class="notification-message">{{ $notification->message }}</p>
                        <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @if(!$notification->is_read)
                    <button type="button" class="notification-close" data-close-notification data-notification-id="{{ $notification->id }}" aria-label="Mark as read">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                    </button>
                @endif
            </div>
        @empty
            <div class="notification-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path d="M15 17H9C7.343 17 6 15.657 6 14V10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14C18 15.657 16.657 17 15 17Z"/>
                    <path d="M4 17H20"/>
                    <path d="M10 20C10.355 20.622 11.078 21 12 21C12.922 21 13.645 20.622 14 20"/>
                </svg>
                <p>No new notifications</p>
            </div>
        @endforelse
    </div>

    @if($notifications->count() > 0)
        <div class="notification-footer">
            <a href="{{ route('couple.notifications.index') }}" class="view-all-link">View all notifications →</a>
        </div>
    @endif
</div>
