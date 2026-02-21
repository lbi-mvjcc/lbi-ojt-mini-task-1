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
                    <button class="btn btn-outline-primary" onclick="markAllAsRead()" title="Mark all as read">
                        <i class="bi bi-check-all"></i> 
                        <span class="d-none d-sm-inline">Mark All Read</span>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteAllNotifications()" title="Delete all notifications">
                        <i class="bi bi-trash"></i> 
                        <span class="d-none d-sm-inline">Clear All</span>
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            @if($notifications->count() > 0)
                <div class="row">
                    @foreach($notifications as $notification)
                        <div class="col-12 mb-3">
                            <div class="card border-0 shadow-sm notification-card {{ $notification->isRead() ? '' : 'unread-notification' }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="flex-shrink-0">
                                            @if($notification->type === 'task_assigned')
                                                    <div class="notification-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-plus-circle"></i>
                                                    </div>
                                                @elseif($notification->type === 'status_updated')
                                                    <div class="notification-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </div>
                                                @elseif($notification->type === 'task_updated')
                                                    <div class="notification-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </div>
                                                @elseif($notification->type === 'task_deleted')
                                                    <div class="notification-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-trash"></i>
                                                    </div>
                                                @elseif($notification->type === 'task_due_soon')
                                                    <div class="notification-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-clock-history"></i>
                                                    </div>
                                                @elseif($notification->type === 'task_overdue')
                                                    <div class="notification-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                    </div>
                                                @elseif($notification->type === 'submission_uploaded')
                                                    <div class="notification-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-file-earmark-arrow-up"></i>
                                                    </div>
                                                @else
                                                    <div class="notification-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-bell"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start gap-3">
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
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div class="btn-group" role="group">
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
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="card border-0 shadow-sm">
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
    border-left: 4px solid #007bff !important;
    background-color: #f8f9ff;
}

.notification-card {
    transition: all 0.2s ease;
}

.notification-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.notification-icon {
    font-size: 1.1rem;
}

/* Dark mode support */
[data-theme="dark"] .notification-card {
    background-color: var(--card-bg);
    border-color: var(--border-color);
}

[data-theme="dark"] .unread-notification {
    background-color: rgba(102, 126, 234, 0.1);
    border-left-color: var(--primary-purple) !important;
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
</style>

<!-- JavaScript functions are centralized in layouts/app.blade.php -->

@endsection