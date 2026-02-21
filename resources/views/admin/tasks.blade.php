@extends('layouts.app')

@section('content')
<style>
    .projects-container {
        column-count: 2;
        column-gap: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .projects-container {
            column-count: 1;
        }
    }
    
    .project-section {
        break-inside: avoid;
        margin-bottom: 1.5rem;
    }
    
    .project-header {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .project-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .project-stats {
        display: flex;
        gap: 1.5rem;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .task-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #e5e7eb;
    }
    
    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2);
        border-color: #667eea;
    }
    
    .task-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: #1f2937;
    }
    
    .task-card-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }
    
    .meta-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
    }
    
    .meta-row i {
        color: #667eea;
        width: 16px;
    }
    
    .developer-info {
        background: #f9fafb;
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .developer-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    
    .role-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-weight: 600;
        display: inline-block;
    }
    
    .role-frontend {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .role-backend {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .role-server {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    .task-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .btn-action {
        flex: 1;
        padding: 0.5rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .category-badge {
        background: #f3f4f6;
        color: #4b5563;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    [data-theme="dark"] .task-card {
        background: transparent !important;
        border-color: rgba(51, 65, 85, 0.3) !important;
        box-shadow: none !important;
    }
    
    [data-theme="dark"] .task-card:hover {
        border-color: var(--primary-purple) !important;
    }
    
    [data-theme="dark"] .task-card-title {
        color: #f1f5f9;
    }
    
    [data-theme="dark"] .developer-info {
        background: #0f172a;
    }
    
    [data-theme="dark"] .developer-name {
        color: #f1f5f9;
    }
    
    [data-theme="dark"] .meta-row {
        color: #94a3b8;
    }
    
    [data-theme="dark"] .role-frontend {
        background-color: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
    }
    
    [data-theme="dark"] .role-backend {
        background-color: rgba(34, 197, 94, 0.2);
        color: #86efac;
    }
    
    [data-theme="dark"] .role-server {
        background-color: rgba(251, 191, 36, 0.2);
        color: #fcd34d;
    }
    
    [data-theme="dark"] .category-badge {
        background: #334155;
        color: #cbd5e1;
    }
    
    [data-theme="dark"] .project-header {
        background: rgba(30, 41, 59, 0.6) !important;
        border: 1px solid rgba(139, 156, 245, 0.3) !important;
        border-left: 4px solid var(--primary-purple) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
    }
</style>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white py-4">
                        <h1 class="mb-2"><i class="bi bi-list-task me-2"></i>Task Management</h1>
                        <p class="mb-0 opacity-75">Oversee all tasks organized by project</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            // Group tasks by project
            $tasksByProject = $tasks->groupBy('project_id');
        @endphp

        @if($tasksByProject->count() > 0)
            <div class="projects-container">
            @foreach($tasksByProject as $projectId => $projectTasks)
                @php
                    $project = $projectTasks->first()->project;
                    $totalTasks = $projectTasks->count();
                    $completedTasks = $projectTasks->where('status', 'done')->count();
                    $inProgressTasks = $projectTasks->where('status', 'in_progress')->count();
                @endphp
                
                <div class="project-section">
                    <div class="project-header">
                        <div>
                            <h2 class="project-title">
                                <i class="bi bi-folder-fill"></i>
                                {{ $project->name }}
                            </h2>
                            <div class="project-stats">
                                <span><i class="bi bi-list-check"></i> {{ $totalTasks }} Tasks</span>
                                <span><i class="bi bi-arrow-repeat"></i> {{ $inProgressTasks }} In Progress</span>
                                <span><i class="bi bi-check-circle"></i> {{ $completedTasks }} Completed</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        @foreach($projectTasks as $task)
                            <div class="col-12">
                                <div class="task-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="category-badge">{{ $task->getCategoryLabel() }}</span>
                                        @if($task->status === 'pending')
                                            <span class="status-badge badge bg-warning">Pending</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="status-badge badge bg-info">In Progress</span>
                                        @elseif($task->status === 'in_review')
                                            <span class="status-badge badge bg-primary">In Review</span>
                                        @elseif($task->status === 'done')
                                            <span class="status-badge badge bg-success">Done</span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="task-card-title">{{ $task->title }}</h3>
                                    
                                    <div class="task-card-meta">
                                        <div class="meta-row">
                                            <i class="bi bi-person"></i>
                                            <span>Created by: {{ $task->createdBy->name ?? 'N/A' }}</span>
                                        </div>
                                        @if($task->deadline)
                                            <div class="meta-row">
                                                <i class="bi bi-calendar"></i>
                                                <span>Due: {{ $task->deadline->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($task->assignedTo)
                                        <div class="developer-info">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="bi bi-person-badge text-primary"></i>
                                                <span style="font-size: 0.75rem; color: #6b7280; font-weight: 600;">ASSIGNED TO</span>
                                            </div>
                                            <div class="developer-name">{{ $task->assignedTo->name }}</div>
                                            @php
                                                $roleClass = 'role-backend';
                                                if($task->assignedTo->role === 'frontend_dev') {
                                                    $roleClass = 'role-frontend';
                                                } elseif($task->assignedTo->role === 'server_admin') {
                                                    $roleClass = 'role-server';
                                                }
                                            @endphp
                                            <span class="role-badge {{ $roleClass }}">
                                                {{ $task->assignedTo->getRoleLabel() }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="task-actions">
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary btn-action">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <form action="{{ route('admin.tasks.delete', $task) }}" method="POST" class="flex-fill" onsubmit="return confirm('This action cannot be undone. Are you sure you want to delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-action w-100">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No tasks found</p>
                </div>
            </div>
        @endif
    </div>
@endsection
