@extends('vendor.layout.layout-vendor')

@section('title', 'Notification - WebPlan')
@section('page-title', $notification->title)
@section('page-subtitle', 'Notification Details')

@push('page-styles')
    @vite(['resources/css/vendor/notification.css'])
@endpush

@section('content')
    <!-- Back Button -->
    <a href="{{ route('vendor.notification.index') }}" class="notification-back-button">
        ← Back to Notifications
    </a>

    <!-- Notification Detail Card -->
    <section class="notification-detail">
        <div class="notification-detail__header">
            <div>
                <h1>{{ $notification->title }}</h1>
                <div class="notification-detail__meta">
                    <span class="notification-detail__date">
                        {{ $notification->created_at->format('d M Y') }} at {{ $notification->created_at->format('H:i') }}
                    </span>
                    @if($notification->is_read)
                        <span class="notification-detail__status">Read</span>
                    @else
                        <span class="notification-detail__status notification-detail__status--unread">Unread</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="notification-detail__body">
            <p>{{ $notification->message }}</p>
        </div>

        <div class="notification-detail__footer">
            <div class="notification-detail__actions">
                @if(!$notification->is_read)
                    <form action="{{ route('vendor.notification.mark-read', $notification) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn--primary">
                            Mark as Read
                        </button>
                    </form>
                @endif
                <form action="{{ route('vendor.notification.destroy', $notification) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn--danger">
                        Delete Notification
                    </button>
                </form>
                <a href="{{ route('vendor.notification.index') }}" class="btn btn--secondary">
                    Back to List
                </a>
            </div>
        </div>
    </section>
@endsection

@push('page-scripts')
    @vite(['resources/js/vendor/notification.js'])
@endpush
