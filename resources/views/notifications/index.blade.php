@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <div>
                    <h1 class="display-6 fw-bold text-dark mb-2">Notifications</h1>
                    <p class="lead text-muted mb-0">Stay updated with your tasks and projects</p>
                </div>
                <div class="btn-group flex-shrink-0">
                    <button class="btn btn-outline-primary me-2" onclick="markAllAsRead()" title="Mark all as read" {{ $notifications->count() === 0 ? 'disabled' : '' }}>
                        <i class="bi bi-check-all"></i> 
                        <span class="d-none d-sm-inline">Mark All Read</span>
                    </button>
                    <form method="POST" action="{{ route('notifications.destroyAll') }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete ALL notifications? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" title="Delete all notifications" {{ $notifications->count() === 0 ? 'disabled' : '' }}>
                            <i class="bi bi-trash"></i> 
                            <span class="d-none d-sm-inline">Clear All</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Notifications List -->
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <div class="mb-2">
                        <div class="card border-0 notification-card {{ $notification->isRead() ? '' : 'unread-notification' }}">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-shrink-0">
                                        @if($notification->type === 'task_assigned')
                                            <div class="notification-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-plus-circle"></i>
                                            </div>
                                        @elseif($notification->type === 'status_updated')
                                            <div class="notification-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </div>
                                        @elseif($notification->type === 'task_updated')
                                            <div class="notification-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-pencil-square"></i>
                                            </div>
                                        @elseif($notification->type === 'task_deleted')
                                            <div class="notification-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-trash"></i>
                                            </div>
                                        @elseif($notification->type === 'task_due_soon')
                                            <div class="notification-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-clock-history"></i>
                                            </div>
                                        @elseif($notification->type === 'task_overdue')
                                            <div class="notification-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </div>
                                        @elseif($notification->type === 'submission_uploaded')
                                            <div class="notification-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-file-earmark-arrow-up"></i>
                                            </div>
                                        @else
                                            <div class="notification-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-bell"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">
                                            {{ $notification->title }}
                                            @unless($notification->isRead())
                                                <span class="badge bg-danger ms-2">New</span>
                                            @endunless
                                        </h6>
                                        <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                        <div class="d-flex flex-wrap align-items-center text-muted small">
                                            <span class="me-3">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $notification->fromUser ? $notification->fromUser->getRoleLabel() : 'System' }}
                                            </span>
                                            <span class="me-3">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if($notification->data && isset($notification->data['project_name']))
                                                <span>
                                                    <i class="bi bi-folder me-1"></i>
                                                    {{ $notification->data['project_name'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="d-flex gap-2" role="group">
                                            @if($notification->task_id && $notification->task)
                                                <a href="{{ route('notifications.show', $notification->id) }}" class="btn btn-outline-primary btn-sm" title="View Task">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif
                                            
                                            @unless($notification->isRead())
                                                <button class="btn btn-outline-success btn-sm" onclick="markAsRead('{{ $notification->id }}')" title="Mark as read">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            @endunless
                                            
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteNotification('{{ $notification->id }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="card border-0">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-bell-slash text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">No Notifications</h4>
                        <p class="text-muted mb-0">You're all caught up! New notifications will appear here.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.unread-notification {
    border-left: 3px solid #007bff !important;
    background-color: rgba(0, 123, 255, 0.05);
}

[data-theme="dark"] .unread-notification {
    background-color: transparent !important;
    border-left: 3px solid var(--primary-purple) !important;
}

.notification-card {
    transition: all 0.2s ease;
    background-color: transparent;
    border: 1px solid rgba(139, 156, 245, 0.2);
    border-left: 3px solid transparent;
}

.notification-card:hover {
    background-color: transparent !important;
    border-left-color: var(--primary-purple) !important;
    transform: translateY(0) !important;
    box-shadow: none !important;
}

.notification-icon {
    font-size: 0.9rem;
    width: 32px !important;
    height: 32px !important;
    min-width: 32px;
    min-height: 32px;
}

.notification-card h6 {
    font-size: 1rem;
    margin-bottom: 0.25rem !important;
}

.notification-card p {
    font-size: 0.9rem;
    margin-bottom: 0.25rem !important;
}

.notification-card .small {
    font-size: 0.8rem !important;
}

.notification-card .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.flex-shrink-0 {
    flex-shrink: 0 !important;
}

/* Dark mode support */
[data-theme="dark"] .notification-card {
    background-color: transparent !important;
    border: 1px solid rgba(139, 156, 245, 0.3) !important;
    border-left: 3px solid transparent !important;
    box-shadow: none !important;
}

[data-theme="dark"] .notification-card:hover {
    background-color: transparent !important;
    border-color: rgba(139, 156, 245, 0.3) !important;
    border-left-color: var(--primary-purple) !important;
    box-shadow: none !important;
    transform: translateY(0) !important;
}

[data-theme="dark"] .notification-card .card-body {
    background-color: transparent !important;
}

[data-theme="dark"] .unread-notification {
    background-color: rgba(102, 126, 234, 0.05) !important;
    border-left: 3px solid var(--primary-purple) !important;
}

[data-theme="dark"] .text-dark {
    color: var(--text-color) !important;
}

[data-theme="dark"] .text-muted {
    color: var(--muted-text) !important;
}

[data-theme="dark"] .lead {
    color: var(--muted-text) !important;
}

[data-theme="dark"] .notification-card .card-body {
    padding: 0.75rem 1rem !important;
}

.notification-card .card-body {
    padding: 0.75rem 1rem !important;
}

/* Mobile responsive improvements */
@media (max-width: 576px) {
    .btn-group .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    
    .notification-card .card-body {
        padding: 1rem;
    }
    
    .display-6 {
        font-size: 1.75rem;
    }
}

/* Ensure proper spacing */
.container-fluid {
    min-height: calc(100vh - 200px);
}

/* Empty state styling */
[data-theme="dark"] .card.border-0 {
    background-color: transparent !important;
    border: 1px solid rgba(139, 156, 245, 0.3) !important;
}

[data-theme="dark"] .card.border-0 .card-body {
    background-color: transparent !important;
}
</style>

<!-- JavaScript functions are centralized in layouts/app.blade.php -->

@endsection