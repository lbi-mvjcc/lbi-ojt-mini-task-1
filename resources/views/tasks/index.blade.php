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
    
    .page-header {
        background: var(--purple-gradient);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        opacity: 0.9;
        margin: 0;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .btn-purple {
        background: white;
        color: var(--primary-purple);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-purple:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(255, 255, 255, 0.3);
        color: var(--primary-purple);
    }
    
    .btn-outline-white {
        background: transparent;
        color: white;
        border: 2px solid white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-outline-white:hover {
        background: white;
        color: var(--primary-purple);
        transform: translateY(-2px);
    }
    
    .task-card {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.15);
        border-color: var(--primary-purple-light);
    }
    
    .task-header {
        display: flex;
        justify-content: between;
        align-items: start;
        margin-bottom: 1rem;
    }
    
    .task-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }
    
    .task-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        color: var(--muted-text);
        font-size: 0.9rem;
        margin-bottom: 1rem;
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
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
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
    
    .category-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        background: var(--purple-light-bg);
        color: var(--primary-purple);
        border: 1px solid var(--purple-border);
    }
    
    .task-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
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
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--card-bg);
        border-radius: 16px;
        border: 2px dashed var(--border-color);
    }
    
    .empty-state i {
        font-size: 4rem;
        color: var(--muted-text);
        margin-bottom: 1rem;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        text-align: center;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-purple);
    }
    
    .stat-label {
        color: var(--muted-text);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    
    [data-theme="dark"] .status-badge.pending {
        background-color: rgba(254, 243, 199, 0.2);
        color: #fbbf24;
    }
    
    [data-theme="dark"] .status-badge.completed {
        background-color: rgba(209, 250, 229, 0.2);
        color: #34d399;
    }
    
    /* Comprehensive dark mode support */
    [data-theme="dark"] .task-card {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }
    
    [data-theme="dark"] .task-card h5,
    [data-theme="dark"] .task-card h6 {
        color: var(--text-color);
    }
    
    [data-theme="dark"] .task-meta {
        color: var(--muted-text);
    }
    
    [data-theme="dark"] .empty-state {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }
    
    [data-theme="dark"] .stat-card {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .stat-number {
        color: var(--primary-purple);
    }
    
    [data-theme="dark"] .stat-label {
        color: var(--muted-text);
    }
</style>

<div class="container py-4">
    @if(auth()->user()->isCustomer())
        <!-- CUSTOMER VIEW -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="page-title">My Tasks</h1>
                    <p class="page-subtitle">Manage and track all your created tasks</p>
                </div>
                <div class="action-buttons">
                    <form method="POST" action="{{ route('tasks.check-deadlines') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-outline-white">
                            <i class="bi bi-clock-history"></i> Check Deadlines
                        </button>
                    </form>
                    <a href="{{ route('tasks.developer-workload') }}" class="btn-outline-white">
                        <i class="bi bi-bar-chart"></i> Workload
                    </a>
                    <a href="{{ route('tasks.create') }}" class="btn-purple">
                        <i class="bi bi-plus-circle"></i> Create Task
                    </a>
                </div>
            </div>
        </div>

        @if($tasks->count() > 0)
            @foreach($tasks as $task)
                <div class="task-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h3 class="task-title">{{ $task->title }}</h3>
                            <div class="task-meta">
                                <div class="task-meta-item">
                                    <i class="bi bi-folder"></i>
                                    <span>{{ $task->project->name }}</span>
                                </div>
                                <div class="task-meta-item">
                                    <i class="bi bi-tag"></i>
                                    <span class="category-badge">{{ ucfirst($task->category) }}</span>
                                </div>
                                <div class="task-meta-item">
                                    <i class="bi bi-people"></i>
                                    <span>{{ $task->developer_count }} developer(s)</span>
                                </div>
                                <div class="task-meta-item">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $task->created_at->format('M d, Y') }}</span>
                                </div>
                                @if($task->all_tasks->first()->deadline)
                                    <div class="task-meta-item">
                                        <i class="bi bi-clock"></i>
                                        <span>Due: {{ $task->all_tasks->first()->deadline->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                            @if($task->description)
                                <p class="text-muted mb-3">{{ Str::limit($task->description, 150) }}</p>
                            @endif
                            <div class="d-flex gap-2 flex-wrap">
                                @if($task->primary_status === 'pending')
                                    <span class="status-badge pending">Pending</span>
                                @elseif($task->primary_status === 'in_progress')
                                    <span class="status-badge in-progress">In Progress</span>
                                @elseif($task->primary_status === 'in_review')
                                    <span class="status-badge in-review">In Review</span>
                                @else
                                    <span class="status-badge completed">Completed</span>
                                @endif
                                
                                @if($task->developer_count > 1)
                                    @if($task->status_counts->get('pending', 0) > 0)
                                        <small><span class="status-badge pending">{{ $task->status_counts->get('pending') }}P</span></small>
                                    @endif
                                    @if($task->status_counts->get('in_progress', 0) > 0)
                                        <small><span class="status-badge in-progress">{{ $task->status_counts->get('in_progress') }}IP</span></small>
                                    @endif
                                    @if($task->status_counts->get('in_review', 0) > 0)
                                        <small><span class="status-badge in-review">{{ $task->status_counts->get('in_review') }}IR</span></small>
                                    @endif
                                    @if($task->status_counts->get('done', 0) > 0)
                                        <small><span class="status-badge completed">{{ $task->status_counts->get('done') }}D</span></small>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="task-actions">
                            <a href="{{ route('tasks.show', $task->id) }}" class="btn-icon" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn-icon" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon delete" onclick="return confirm('Are you sure?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>No Tasks Yet</h4>
                <p class="text-muted mb-4">Create your first task to get started</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-purple" style="background: var(--purple-gradient); color: white;">
                    <i class="bi bi-plus-circle"></i> Create First Task
                </a>
            </div>
        @endif

    @elseif(auth()->user()->isDeveloper())
        <!-- DEVELOPER VIEW -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="page-title">My Assigned Tasks</h1>
                    <p class="page-subtitle">{{ auth()->user()->getRoleLabel() }} - Complete your tasks</p>
                </div>
            </div>
        </div>

        @if($assignedTasks->count() > 0)
            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-number">{{ $assignedTasks->count() }}</div>
                    <div class="stat-label">Total Tasks</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $assignedTasks->where('status', 'pending')->count() }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $assignedTasks->where('status', 'in_progress')->count() }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $assignedTasks->where('status', 'in_review')->count() }}</div>
                    <div class="stat-label">In Review</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $assignedTasks->where('status', 'done')->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>

            <!-- Task Cards -->
            @foreach($assignedTasks as $task)
                <div class="task-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h3 class="task-title">{{ $task->title }}</h3>
                            <div class="task-meta">
                                <div class="task-meta-item">
                                    <i class="bi bi-folder"></i>
                                    <span>{{ $task->project->name }}</span>
                                </div>
                                <div class="task-meta-item">
                                    <i class="bi bi-tag"></i>
                                    <span class="category-badge">{{ ucfirst($task->category) }}</span>
                                </div>
                                <div class="task-meta-item">
                                    <i class="bi bi-person"></i>
                                    <span>{{ $task->createdBy->name }}</span>
                                </div>
                                <div class="task-meta-item">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $task->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            @if($task->description)
                                <p class="text-muted mb-3">{{ Str::limit($task->description, 150) }}</p>
                            @endif
                            <div class="d-flex gap-2 align-items-center">
                                @if($task->status === 'pending')
                                    <span class="status-badge pending">Pending</span>
                                @elseif($task->status === 'in_progress')
                                    <span class="status-badge in-progress">In Progress</span>
                                @elseif($task->status === 'in_review')
                                    <span class="status-badge in-review">In Review</span>
                                @else
                                    <span class="status-badge completed">Completed</span>
                                @endif
                            </div>
                        </div>
                        <div class="task-actions">
                            <a href="{{ route('tasks.show', $task) }}" class="btn-icon" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>No Assigned Tasks</h4>
                <p class="text-muted">You don't have any tasks assigned yet</p>
            </div>
        @endif
    @endif
</div>
@endsection
