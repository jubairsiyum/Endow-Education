@extends('layouts.admin')

@section('title', 'Notifications')

@push('styles')
<style>
    .notifications-page {
        background: #F8FAFC;
        min-height: calc(100vh - 100px);
        padding: 1.5rem 0;
    }
    
    .notification-header {
        background: white;
        border-radius: 8px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #E2E8F0;
    }
    
    .notification-filters {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    
    .filter-btn {
        padding: 0.4rem 0.875rem;
        border-radius: 6px;
        border: 1px solid #E2E8F0;
        background: white;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }
    
    .filter-btn:hover {
        border-color: #DC143C;
        color: #DC143C;
        background: #FFF5F7;
    }
    
    .filter-btn.active {
        background: #DC143C;
        color: white;
        border-color: #DC143C;
    }
    
    .filter-badge {
        background: rgba(0,0,0,0.1);
        padding: 0.125rem 0.5rem;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .filter-btn.active .filter-badge {
        background: rgba(255,255,255,0.25);
    }
    
    .notification-card {
        background: white;
        border-radius: 8px;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #E2E8F0;
        overflow: hidden;
    }
    
    .date-header {
        background: linear-gradient(135deg, #DC143C 0%, #B8102C 100%);
        color: white;
        padding: 0.625rem 1.25rem;
        font-weight: 600;
        font-size: 0.8125rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        letter-spacing: 0.01em;
    }
    
    .notification-item {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #F1F5F9;
        display: flex;
        align-items: start;
        gap: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
    }
    
    .notification-item:last-child {
        border-bottom: none;
    }
    
    .notification-item:hover {
        background: #F8FAFC;
    }
    
    .notification-item.unread {
        background: #FFF5F7;
        border-left: 3px solid #DC143C;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .icon-primary { background: #DC143C; color: white; }
    .icon-success { background: #10B981; color: white; }
    .icon-danger { background: #DC143C; color: white; }
    .icon-warning { background: #F59E0B; color: white; }
    .icon-info { background: #3B82F6; color: white; }
    
    .notification-content {
        flex: 1;
        min-width: 0;
    }
    
    .notification-title {
        font-weight: 600;
        color: #1E293B;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }
    
    .notification-message {
        color: #64748B;
        font-size: 0.8125rem;
        margin-bottom: 0.375rem;
        line-height: 1.5;
    }
    
    .notification-time {
        color: #94A3B8;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .notification-actions {
        display: flex;
        gap: 0.375rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .notification-item:hover .notification-actions {
        opacity: 1;
    }
    
    .action-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: none;
        background: #E2E8F0;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 0.75rem;
    }
    
    .action-btn:hover {
        background: #CBD5E1;
        transform: scale(1.05);
    }
    
    .action-btn.delete:hover {
        background: #DC143C;
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #E2E8F0;
    }
    
    .empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: #DC143C;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }
    
    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1E293B;
        margin-bottom: 0.5rem;
    }
    
    .empty-message {
        color: #64748B;
        font-size: 0.875rem;
    }
    
    .action-bar {
        background: white;
        padding: 0.875rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #E2E8F0;
    }
    
    .action-bar .text-muted {
        font-size: 0.8125rem;
        color: #64748B !important;
    }
    
    .btn-gradient {
        background: #DC143C;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }
    
    .btn-gradient:hover {
        background: #B8102C;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 20, 60, 0.25);
        color: white;
    }
    
    .btn-outline-gradient {
        border: 1px solid #DC143C;
        color: #DC143C;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }
    
    .btn-outline-gradient:hover {
        background: #DC143C;
        color: white;
    }
    
    .pagination {
        justify-content: center;
        margin-top: 1.5rem;
    }
    
    .page-link {
        border-radius: 6px;
        margin: 0 0.125rem;
        border: 1px solid #E2E8F0;
        color: #475569;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    .page-link:hover {
        background: #DC143C;
        border-color: #DC143C;
        color: white;
    }
    
    .page-item.active .page-link {
        background: #DC143C;
        border-color: #DC143C;
    }
    
    .page-header-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1E293B;
        margin-bottom: 0.25rem;
    }
    
    .page-header-subtitle {
        font-size: 0.875rem;
        color: #64748B;
    }
    
    .days-badge {
        background: #DC143C;
        color: white;
        padding: 0.5rem 0.875rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
    }
    
    .alert {
        border-radius: 6px;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        border: none;
    }
    
    .alert-success {
        background: #D1FAE5;
        color: #065F46;
    }
    
    .alert-danger {
        background: #FEE2E2;
        color: #991B1B;
    }
    
    @media (max-width: 768px) {
        .notifications-page {
            padding: 1rem 0;
        }
        
        .notification-filters {
            flex-direction: column;
        }
        
        .filter-btn {
            width: 100%;
            justify-content: space-between;
        }
        
        .action-bar {
            flex-direction: column;
            gap: 0.75rem;
            align-items: stretch;
        }
        
        .action-bar .d-flex {
            width: 100%;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .notification-actions {
            opacity: 1;
        }
    }
</style>
@endpush

@section('content')
<div class="notifications-page">
    <div class="container">
        <!-- Header -->
        <div class="notification-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-header-title mb-0">
                        <i class="fas fa-bell" style="color: #DC143C;"></i> Notifications
                    </h2>
                    <p class="page-header-subtitle mb-0">Stay updated with your latest activities</p>
                </div>
                <div>
                    <div class="days-badge">
                        <i class="fas fa-calendar-day"></i> Last {{ $days }} Days
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="notification-filters">
                <a href="{{ route('notifications.index', ['filter' => 'all', 'days' => $days]) }}" 
                   class="filter-btn {{ $filter === 'all' ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    All
                    <span class="filter-badge">{{ $totalCount }}</span>
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread', 'days' => $days]) }}" 
                   class="filter-btn {{ $filter === 'unread' ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    Unread
                    <span class="filter-badge">{{ $unreadCount }}</span>
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'read', 'days' => $days]) }}" 
                   class="filter-btn {{ $filter === 'read' ? 'active' : '' }}">
                    <i class="fas fa-envelope-open"></i>
                    Read
                    <span class="filter-badge">{{ $readCount }}</span>
                </a>
            </div>
        </div>

        <!-- Action Bar -->
        @if($notifications->count() > 0)
        <div class="action-bar">
            <div class="text-muted">
                <i class="fas fa-info-circle"></i>
                Showing {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} of {{ $notifications->total() }}
            </div>
            <div class="d-flex gap-2">
                @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-check-double"></i> Mark All Read
                    </button>
                </form>
                @endif
                @if($readCount > 0)
                <form action="{{ route('notifications.clear-read') }}" method="POST" style="display: inline;" 
                      onsubmit="return confirm('Delete all read notifications?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-gradient">
                        <i class="fas fa-trash-alt"></i> Clear Read
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Notifications List -->
        @if($notifications->count() > 0)
            @php
                $groupedNotifications = $notifications->groupBy(function($notification) {
                    return $notification->created_at->format('Y-m-d');
                });
            @endphp

            @foreach($groupedNotifications as $date => $dayNotifications)
            <div class="notification-card">
                <div class="date-header">
                    <i class="fas fa-calendar"></i>
                    {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today' : (\Carbon\Carbon::parse($date)->isYesterday() ? 'Yesterday' : \Carbon\Carbon::parse($date)->format('l, F j, Y')) }}
                </div>
                
                @foreach($dayNotifications as $notification)
                <div class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}" 
                     onclick="window.location.href='{{ route('notifications.redirect', $notification->id) }}'">
                    <div class="notification-icon {{ $notification->data['color'] ?? 'icon-primary' }}">
                        <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                        <div class="notification-message">{{ $notification->data['message'] ?? '' }}</div>
                        <div class="notification-time">
                            <i class="fas fa-clock"></i>
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="notification-actions" onclick="event.stopPropagation();">
                        @if(is_null($notification->read_at))
                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="action-btn" title="Mark as read">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this notification?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $notifications->appends(['filter' => $filter, 'days' => $days])->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3 class="empty-title">No Notifications</h3>
                <p class="empty-message">
                    @if($filter === 'unread')
                        You're all caught up! No unread notifications.
                    @elseif($filter === 'read')
                        No read notifications in the last {{ $days }} days.
                    @else
                        No notifications in the last {{ $days }} days.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
