@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-purple: #667eea;
        --primary-purple-dark: #5568d3;
        --primary-purple-light: #8b9cf5;
        --secondary-purple: #764ba2;
        --purple-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --purple-light-bg: #f5f3ff;
        --purple-border: #e0d9ff;
    }
    
    [data-theme="dark"] {
        --primary-purple: #8b9cf5;
        --primary-purple-dark: #667eea;
        --primary-purple-light: #a5b4f7;
        --secondary-purple: #9d6ec9;
        --purple-gradient: linear-gradient(135deg, #8b9cf5 0%, #9d6ec9 100%);
        --purple-light-bg: #2d2d44;
        --purple-border: #3d3d5c;
    }
    
    .task-detail-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-color);
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .back-button:hover {
        background: var(--purple-light-bg);
        color: var(--primary-purple);
    }
    
    .task-header-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .task-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 1rem;
    }
    
    .task-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        color: var(--muted-text);
        font-size: 0.9rem;
    }
    
    .task-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .task-meta-item i {
        color: var(--primary-purple);
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .status-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .status-badge.in-progress {
        background-color: rgba(102, 126, 234, 0.15);
        color: var(--primary-purple-dark);
    }
    
    .status-badge.in-review {
        background-color: rgba(118, 75, 162, 0.15);
        color: var(--secondary-purple);
    }
    
    .status-badge.completed {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .priority-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .info-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        height: 100%;
    }
    
    .info-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }
    
    .info-card-header i {
        color: var(--primary-purple);
        font-size: 1.25rem;
    }
    
    .info-card-header h5 {
        margin: 0;
        font-weight: 700;
        color: var(--text-color);
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: var(--muted-text);
        font-weight: 500;
    }
    
    .info-value {
        color: var(--text-color);
        font-weight: 600;
        text-align: right;
    }
    
    .timeline-item {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .timeline-item:last-child {
        border-bottom: none;
    }
    
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .timeline-icon.status-change {
        background: rgba(102, 126, 234, 0.15);
        color: var(--primary-purple);
    }
    
    .timeline-icon.task-updated {
        background: rgba(168, 85, 247, 0.15);
        color: #a855f7;
    }
    
    .timeline-icon.task-created {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }
    
    .timeline-content h6 {
        margin: 0 0 0.25rem 0;
        font-weight: 600;
        color: var(--text-color);
    }
    
    .timeline-content small {
        color: var(--muted-text);
    }
    
    .action-buttons {
        display: flex;
        gap: 0.75rem;
    }
    
    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-color);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .btn-icon:hover {
        border-color: var(--primary-purple);
        color: var(--primary-purple);
        transform: translateY(-2px);
    }
    
    .btn-icon.delete:hover {
        border-color: #ef4444;
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }
    
    .description-text {
        color: var(--muted-text);
        line-height: 1.6;
        margin-top: 1rem;
    }
    
    .status-update-dropdown {
        position: relative;
    }
    
    .status-update-btn {
        background: var(--purple-gradient);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-update-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }
    
    [data-theme="dark"] .status-badge.pending {
        background-color: rgba(254, 243, 199, 0.2);
        color: #fbbf24;
    }
    
    [data-theme="dark"] .status-badge.completed {
        background-color: rgba(209, 250, 229, 0.2);
        color: #34d399;
    }
</style>

<div class="container py-4">
    <div class="task-detail-container">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('tasks.index') }}" class="back-button">
                <i class="bi bi-arrow-left"></i>
                Back to Tasks
            </a>
        </div>

        <!-- Task Header Card -->
        <div class="task-header-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    @if($task->status === 'pending')
                        <span class="status-badge pending">Pending</span>
                    @elseif($task->status === 'in_progress')
                        <span class="status-badge in-progress">In Progress</span>
                    @elseif($task->status === 'in_review')
                        <span class="status-badge in-review">In Review</span>
                    @else
                        <span class="status-badge completed">Completed</span>
                    @endif
                    
                    @if($task->deadline && $task->deadline->isPast() && $task->status !== 'done')
                        <span class="priority-badge">High Priority</span>
                    @endif
                </div>
                
                <div class="action-buttons">
                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('tasks.edit', $task) }}" class="btn-icon" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon delete" onclick="return confirm('This action cannot be undone. Are you sure to you want to delete this task')" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            
            <h1 class="task-title">{{ $task->title }}</h1>
            
            <div class="task-meta">
                <div class="task-meta-item">
                    <i class="bi bi-tag"></i>
                    <span>{{ $task->getCategoryLabel() }}</span>
                </div>
                <div class="task-meta-item">
                    <i class="bi bi-calendar3"></i>
                    <span>Created {{ $task->created_at->format('Y-m-d') }}</span>
                </div>
                @if($task->deadline)
                    <div class="task-meta-item">
                        <i class="bi bi-clock"></i>
                        <span>Updated {{ $task->updated_at->format('Y-m-d') }}</span>
                    </div>
                @endif
            </div>
            
            @if($task->description)
                <div class="description-text">
                    <strong>Description</strong><br>
                    {{ $task->description }}
                </div>
            @endif
        </div>

        <!-- Main Content Grid -->
        <div class="row g-3">
            <!-- Left Column - Task Information -->
            <div class="col-lg-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-activity"></i>
                        <h5>Task Information</h5>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            @if($task->status === 'pending')
                                <span class="status-badge pending">Pending</span>
                            @elseif($task->status === 'in_progress')
                                <span class="status-badge in-progress">In Progress</span>
                            @elseif($task->status === 'in_review')
                                <span class="status-badge in-review">In Review</span>
                            @else
                                <span class="status-badge completed">Completed</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Category</span>
                        <span class="info-value">{{ $task->getCategoryLabel() }}</span>
                    </div>
                    
                    @if($task->deadline && $task->deadline->isPast() && $task->status !== 'done')
                        <div class="info-row">
                            <span class="info-label">Priority</span>
                            <span class="info-value">
                                <span class="priority-badge">High</span>
                            </span>
                        </div>
                    @endif
                    
                    <div class="info-row">
                        <span class="info-label">Created Date</span>
                        <span class="info-value">{{ $task->created_at->format('Y-m-d') }}</span>
                    </div>
                    
                    @if($task->deadline)
                        <div class="info-row">
                            <span class="info-label">Deadline</span>
                            <span class="info-value">{{ $task->deadline->format('Y-m-d') }}</span>
                        </div>
                    @endif
                    
                    <div class="info-row">
                        <span class="info-label">Project</span>
                        <span class="info-value">{{ $task->project->name }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Created By</span>
                        <span class="info-value">{{ $task->createdBy->name }}</span>
                    </div>
                    
                    @if(auth()->user()->isAdmin())
                        <div class="info-row">
                            <span class="info-label">Assigned To</span>
                            <span class="info-value">
                                <div class="d-flex align-items-center gap-2">
                                    {!! $task->assignedTo->getProfilePictureHtml(24) !!}
                                    <span>{{ $task->assignedTo->name }}</span>
                                    <span class="badge bg-info">{{ $task->assignedTo->getRoleLabel() }}</span>
                                </div>
                            </span>
                        </div>
                    @endif
                    
                    @if(auth()->user()->isDeveloper())
                        <div class="mt-4">
                            <div class="dropdown w-100">
                                <button class="status-update-btn w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-arrow-repeat"></i>
                                    Update Status
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li>
                                        <form method="POST" action="{{ route('tasks.updateStatus', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-clock"></i> Pending
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('tasks.updateStatus', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-arrow-repeat"></i> In Progress
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('tasks.updateStatus', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="in_review">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-search"></i> In Review
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('tasks.updateStatus', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="done">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-check-circle"></i> Completed
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Activity Timeline -->
            <div class="col-lg-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-clock-history"></i>
                        <h5>Activity Timeline</h5>
                    </div>
                    
                    @if($task->status !== 'pending')
                        <div class="timeline-item">
                            <div class="timeline-icon status-change">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Status changed to {{ ucfirst(str_replace('_', ' ', $task->status)) }}</h6>
                                <small>{{ $task->updated_at->format('Y-m-d') }} at {{ $task->updated_at->format('h:i A') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($task->updated_at != $task->created_at)
                        <div class="timeline-item">
                            <div class="timeline-icon task-updated">
                                <i class="bi bi-pencil"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Task updated</h6>
                                <small>{{ $task->updated_at->format('Y-m-d') }} at {{ $task->updated_at->format('h:i A') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    <div class="timeline-item">
                        <div class="timeline-icon task-created">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Task created</h6>
                            <small>{{ $task->created_at->format('Y-m-d') }} at {{ $task->created_at->format('h:i A') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submissions Section -->
        @if($task->hasSubmissionRequirements())
            <div class="info-card mt-3">
                <div class="info-card-header">
                    <i class="bi bi-cloud-upload"></i>
                    <h5>Submissions</h5>
                </div>
                
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if($task->requires_file_submission)
                        <span class="status-badge in-progress">
                            <i class="bi bi-file-earmark"></i> Files Accepted
                        </span>
                    @endif
                    @if($task->requires_image_submission)
                        <span class="status-badge completed">
                            <i class="bi bi-image"></i> Images Accepted
                        </span>
                    @endif
                    @if($task->requires_link_submission)
                        <span class="status-badge in-review">
                            <i class="bi bi-link-45deg"></i> Links Accepted
                        </span>
                    @endif
                </div>

                @if($task->submission_instructions)
                    <div class="mb-3">
                        <strong>Instructions:</strong>
                        <p class="text-muted mb-0">{{ $task->submission_instructions }}</p>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">
                        {{ $task->submissions()->count() }} submission(s) uploaded
                    </span>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('tasks.submissions.index', $task) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye"></i> View All
                        </a>
                        @can('upload', $task)
                            <a href="{{ route('tasks.submissions.create', $task) }}" class="status-update-btn">
                                <i class="bi bi-cloud-upload"></i> Upload
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
