@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start mb-4 gap-3">
                <div class="flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('notifications.index') }}" class="text-decoration-none">
                                    <i class="bi bi-bell"></i> Notifications
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Notification Details</li>
                        </ol>
                    </nav>
                    <h1 class="display-6 fw-bold text-dark">{{ $notification->title }}</h1>
                </div>
                <div class="btn-group flex-shrink-0">
                    @unless($notification->isRead())
                        <button class="btn btn-success" onclick="markAsRead('{{ $notification->id }}')">
                            <i class="bi bi-check"></i> 
                            <span class="d-none d-sm-inline">Mark as Read</span>
                        </button>
                    @endunless
                    <button class="btn btn-outline-danger" onclick="deleteNotification('{{ $notification->id }}')">
                        <i class="bi bi-trash"></i> 
                        <span class="d-none d-sm-inline">Delete</span>
                    </button>
                </div>
            </div>



            <!-- Notification Details -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm {{ $notification->isRead() ? '' : 'unread-notification' }}">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if($notification->type === 'task_assigned')
                                        <div class="notification-icon bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-plus-circle" style="font-size: 1.5rem;"></i>
                                        </div>
                                    @elseif($notification->type === 'status_updated')
                                        <div class="notification-icon bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-arrow-repeat" style="font-size: 1.5rem;"></i>
                                        </div>
                                    @elseif($notification->type === 'task_updated')
                                        <div class="notification-icon bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-pencil-square" style="font-size: 1.5rem;"></i>
                                        </div>
                                    @elseif($notification->type === 'task_deleted')
                                        <div class="notification-icon bg-danger text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                        </div>
                                    @elseif($notification->type === 'task_due_soon')
                                        <div class="notification-icon bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-clock-history" style="font-size: 1.5rem;"></i>
                                        </div>
                                    @elseif($notification->type === 'task_overdue')
                                        <div class="notification-icon bg-danger text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-exclamation-triangle" style="font-size: 1.5rem;"></i>
                                        </div>
                                    @elseif($notification->type === 'submission_uploaded')
                                        <div class="notification-icon bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-file-earmark-arrow-up" style="font-size: 1.5rem;"></i>
                                        </div>
                                    @else
                                        <div class="notification-icon bg-secondary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-bell" style="font-size: 1.5rem;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="mb-0">{{ $notification->title }}</h5>
                                        <small class="text-muted">
                                            {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                            @unless($notification->isRead())
                                                <span class="badge bg-danger ms-2">Unread</span>
                                            @endunless
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="lead">{{ $notification->message }}</p>
                            
                            <!-- Notification Details -->
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">From</h6>
                                    <p class="mb-3">
                                        <i class="bi bi-person-circle me-2"></i>
                                        {{ $notification->fromUser ? $notification->fromUser->getRoleLabel() : 'System' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Date & Time</h6>
                                    <p class="mb-3">
                                        <i class="bi bi-clock me-2"></i>
                                        {{ $notification->created_at->format('M d, Y \a\t g:i A') }}
                                        <small class="text-muted">({{ $notification->created_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>

                            @if($notification->data)
                                <h6 class="text-muted mb-2">Additional Information</h6>
                                <div class="bg-light p-3 rounded">
                                    @if(isset($notification->data['project_name']))
                                        <p class="mb-2">
                                            <strong>Project:</strong> {{ $notification->data['project_name'] }}
                                        </p>
                                    @endif
                                    @if(isset($notification->data['task_title']))
                                        <p class="mb-2">
                                            <strong>Task:</strong> {{ $notification->data['task_title'] }}
                                        </p>
                                    @endif
                                    @if(isset($notification->data['task_category']))
                                        <p class="mb-2">
                                            <strong>Category:</strong> 
                                            <span class="badge bg-info">{{ ucfirst($notification->data['task_category']) }}</span>
                                        </p>
                                    @endif
                                    @if(isset($notification->data['old_status']) && isset($notification->data['new_status']))
                                        <p class="mb-2">
                                            <strong>Status Change:</strong> 
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $notification->data['old_status'])) }}</span>
                                            <i class="bi bi-arrow-right mx-2"></i>
                                            <span class="badge bg-success">{{ ucfirst(str_replace('_', ' ', $notification->data['new_status'])) }}</span>
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if($notification->task_id && $notification->task)
                                <hr>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('tasks.show', $notification->task_id) }}" class="btn btn-primary">
                                        <i class="bi bi-eye"></i> View Related Task
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar with Related Actions -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            @unless($notification->isRead())
                                <button class="btn btn-success w-100 mb-2" onclick="markAsRead('{{ $notification->id }}')">
                                    <i class="bi bi-check"></i> Mark as Read
                                </button>
                            @endunless
                            
                            @if($notification->task_id && $notification->task)
                                <a href="{{ route('tasks.show', $notification->task_id) }}" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-eye"></i> View Task
                                </a>
                                @if(auth()->user()->isDeveloper() && $notification->task->assigned_to === auth()->id())
                                    <a href="{{ route('tasks.edit', $notification->task_id) }}" class="btn btn-warning w-100 mb-2">
                                        <i class="bi bi-pencil"></i> Update Task Status
                                    </a>
                                @endif
                            @endif
                            
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="bi bi-arrow-left"></i> Back to Notifications
                            </a>
                            
                            <button class="btn btn-outline-danger w-100" onclick="deleteNotification('{{ $notification->id }}')">
                                <i class="bi bi-trash"></i> Delete Notification
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<style>
.unread-notification {
    border-left: 4px solid #007bff !important;
    background-color: #f8f9ff;
}

.notification-icon {
    font-size: 1.1rem;
}

/* Mobile responsive improvements */
@media (max-width: 576px) {
    .btn-group .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    
    .display-6 {
        font-size: 1.75rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Ensure proper spacing */
.container-fluid {
    min-height: calc(100vh - 200px);
}
</style>

<!-- JavaScript functions are centralized in layouts/app.blade.php -->

@endsection