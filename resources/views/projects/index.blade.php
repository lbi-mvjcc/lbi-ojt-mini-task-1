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
    
    .project-card {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .project-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.15);
        border-color: var(--primary-purple-light);
    }
    
    .project-header {
        background: var(--purple-light-bg);
        padding: 1.5rem;
        border-bottom: 1px solid var(--purple-border);
    }
    
    .project-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-color);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .project-title i {
        color: var(--primary-purple);
    }
    
    .project-body {
        padding: 1.5rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-item {
        text-align: center;
        padding: 1rem;
        background: var(--purple-light-bg);
        border-radius: 10px;
        border: 1px solid var(--purple-border);
    }
    
    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--primary-purple);
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: var(--muted-text);
        margin-top: 0.25rem;
    }
    
    .task-list {
        max-height: 250px;
        overflow-y: auto;
    }
    
    .task-item {
        padding: 0.75rem;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }
    
    .task-item:last-child {
        border-bottom: none;
    }
    
    .task-item:hover {
        background: var(--purple-light-bg);
    }
    
    .task-item-title {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.25rem;
    }
    
    .task-item-meta {
        font-size: 0.85rem;
        color: var(--muted-text);
    }
    
    .status-badge {
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
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
    
    .project-footer {
        padding: 1rem 1.5rem;
        background: var(--purple-light-bg);
        border-top: 1px solid var(--purple-border);
        display: flex;
        gap: 0.75rem;
    }
    
    .btn-project {
        flex: 1;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-color);
    }
    
    .btn-project:hover {
        border-color: var(--primary-purple);
        color: var(--primary-purple);
        transform: translateY(-2px);
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
    
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-title i {
        color: var(--primary-purple);
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
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="page-title">My Projects</h1>
                <p class="page-subtitle">View and manage all your active projects</p>
            </div>
            <a href="{{ route('tasks.create') }}" class="btn-purple">
                <i class="bi bi-plus-circle"></i> Create New Task
            </a>
        </div>
    </div>

    @if($projects->count() > 0)
        <div class="row g-4">
            @foreach($projects as $project)
                <div class="col-lg-6">
                    <div class="project-card">
                        <!-- Project Header -->
                        <div class="project-header">
                            <h3 class="project-title">
                                <i class="bi bi-folder-fill"></i>
                                {{ $project->name }}
                            </h3>
                        </div>

                        <!-- Project Body -->
                        <div class="project-body">
                            <!-- Stats Grid -->
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $project->tasks()->select('title', 'project_id', 'category')->distinct()->count() }}</div>
                                    <div class="stat-label">Unique Tasks</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $project->tasks()->where('status', 'in_progress')->count() }}</div>
                                    <div class="stat-label">In Progress</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $project->tasks()->where('status', 'in_review')->count() }}</div>
                                    <div class="stat-label">In Review</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $project->tasks()->where('status', 'done')->count() }}</div>
                                    <div class="stat-label">Completed</div>
                                </div>
                            </div>

                            <!-- Recent Tasks -->
                            @if($project->tasks()->count() > 0)
                                <div class="section-title">
                                    <i class="bi bi-list-task"></i>
                                    Recent Tasks
                                </div>
                                <div class="task-list">
                                    @php
                                        // Get all tasks and manually group them
                                        $allTasks = $project->tasks()->latest()->get();
                                        $uniqueTasks = collect();
                                        $seenTasks = [];
                                        
                                        foreach($allTasks as $task) {
                                            $key = $task->title . '|' . $task->category . '|' . $task->project_id;
                                            if (!isset($seenTasks[$key])) {
                                                $seenTasks[$key] = true;
                                                $uniqueTasks->push($task);
                                                if ($uniqueTasks->count() >= 5) break;
                                            }
                                        }
                                    @endphp
                                    @foreach($uniqueTasks as $task)
                                        @php
                                            // Count developers assigned to this specific task
                                            $taskDeveloperCount = App\Models\Task::where('title', $task->title)
                                                ->where('project_id', $task->project_id)
                                                ->where('category', $task->category)
                                                ->count();
                                        @endphp
                                        <div class="task-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="task-item-title">{{ Str::limit($task->title, 35) }}</div>
                                                    <div class="task-item-meta">
                                                        <i class="bi bi-people"></i> {{ $taskDeveloperCount }} dev{{ $taskDeveloperCount > 1 ? 's' : '' }} • 
                                                        <i class="bi bi-tag"></i> {{ ucfirst($task->category) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    @if($task->status === 'pending')
                                                        <span class="status-badge pending">Pending</span>
                                                    @elseif($task->status === 'in_progress')
                                                        <span class="status-badge in-progress">In Progress</span>
                                                    @elseif($task->status === 'in_review')
                                                        <span class="status-badge in-review">In Review</span>
                                                    @else
                                                        <span class="status-badge completed">Done</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem; color: var(--muted-text);"></i>
                                    <p class="text-muted mt-2 mb-0">No tasks in this project yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Project Footer -->
                        <div class="project-footer">
                            <a href="{{ route('tasks.index') }}" class="btn-project">
                                <i class="bi bi-eye"></i> View Tasks
                            </a>
                            <a href="{{ route('tasks.create') }}" class="btn-project">
                                <i class="bi bi-plus"></i> Add Task
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-folder-x"></i>
            <h4>No Projects Yet</h4>
            <p class="text-muted mb-4">You haven't created any projects or tasks yet</p>
            <a href="{{ route('tasks.create') }}" class="btn btn-purple" style="background: var(--purple-gradient); color: white;">
                <i class="bi bi-plus-circle"></i> Create Your First Task
            </a>
        </div>
    @endif
</div>
@endsection
