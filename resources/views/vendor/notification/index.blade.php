@extends('vendor.layout.layout-vendor')

@section('title', 'Notifications - WebPlan')
@section('page-title', 'Notifications')
@section('page-subtitle', 'Manage your vendor notifications and booking alerts.')

@push('page-styles')
    @vite(['resources/css/vendor/notification.css'])
@endpush

@section('content')
    <!-- Hero Section with Stats -->
    <section class="notification-hero">
        <div class="notification-hero__content">
            <div>
                <h1>Your Notifications</h1>
                <p>Stay updated with vendor approvals, bookings, and wedding updates.</p>
            </div>
        </div>
        <div class="notification-hero__metrics">
            <div class="metric-card">
                <div class="metric-value">{{ $stats['total'] }}</div>
                <div class="metric-label">Total</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $stats['unread'] }}</div>
                <div class="metric-label">Unread</div>
            </div>
        </div>
    </section>

    <!-- Toolbar -->
    <section class="notification-toolbar">
        <input 
            type="text" 
            id="notificationSearch" 
            class="notification-search" 
            placeholder="Search notifications..."
        >
        <div class="notification-filters">
            <select id="statusFilter" class="notification-filter">
                <option value="">All Status</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>
    </section>

    <!-- Notifications List -->
    <section class="notification-list">
        @if($notifications->count() > 0)
            @foreach($notifications as $notification)
                <div class="notification-card" data-notification-id="{{ $notification->id }}" data-read="{{ $notification->is_read ? 'read' : 'unread' }}">
                    <div class="notification-card__header">
                        <div class="notification-card__title-group">
                            <a href="{{ route('vendor.notification.show', $notification) }}" class="notification-card__title">
                                {{ $notification->title }}
                            </a>
                            @if(!$notification->is_read)
                                <span class="notification-badge">New</span>
                            @endif
                        </div>
                        <div class="notification-card__date">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    <p class="notification-card__message">
                        {{ Str::limit($notification->message, 100) }}
                    </p>

                    <div class="notification-card__footer">
                        <div class="notification-card__actions">
                            <a href="{{ route('vendor.notification.show', $notification) }}" class="action-link">
                                View
                            </a>
                            @if(!$notification->is_read)
                                <form action="{{ route('vendor.notification.mark-read', $notification) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="action-link action-link--mark-read">
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('vendor.notification.destroy', $notification) }}" method="POST" style="display: inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link action-link--delete">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="notification-empty">
                <div class="notification-empty__icon">📭</div>
                <h3>No Notifications Yet</h3>
                <p>You're all caught up! New notifications will appear here.</p>
            </div>
        @endif
    </section>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="notification-pagination">
            {{ $notifications->links() }}
        </div>
    @endif
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/notification.js'])
@endpush

