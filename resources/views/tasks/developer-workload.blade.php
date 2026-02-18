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
    
    .btn-white {
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
    
    .btn-white:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(255, 255, 255, 0.3);
        color: var(--primary-purple);
    }
    
    .role-section {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .role-header {
        background: var(--purple-light-bg);
        padding: 1.5rem;
        border-bottom: 1px solid var(--purple-border);
    }
    
    .role-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-color);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .role-title i {
        color: var(--primary-purple);
    }
    
    .role-badge {
        background: var(--card-bg);
        color: var(--primary-purple);
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        border: 1px solid var(--purple-border);
    }
    
    .developer-card {
        background: var(--card-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .developer-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.15);
        border-color: var(--primary-purple-light);
    }
    
    .developer-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--purple-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2.5rem;
        color: white;
    }
    
    .developer-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 0.25rem;
    }
    
    .developer-role {
        color: var(--muted-text);
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }
    
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .stat-row:last-child {
        border-bottom: none;
    }
    
    .stat-label {
        color: var(--muted-text);
        font-size: 0.9rem;
    }
    
    .stat-value {
        font-weight: 700;
        color: var(--text-color);
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
    
    .workload-badge {
        width: 100%;
        padding: 0.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        margin-top: 1rem;
    }
    
    .workload-badge.available {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .workload-badge.light {
        background-color: rgba(102, 126, 234, 0.15);
        color: var(--primary-purple-dark);
    }
    
    .workload-badge.moderate {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .workload-badge.heavy {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .summary-card {
        background: var(--purple-gradient);
        border-radius: 16px;
        padding: 2rem;
        color: white;
    }
    
    .summary-stat {
        text-align: center;
        padding: 1rem;
    }
    
    .summary-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .summary-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .distribution-item {
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    
    .distribution-number {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .distribution-label {
        font-size: 0.85rem;
        opacity: 0.9;
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
                <h1 class="page-title">Developer Workload</h1>
                <p class="page-subtitle">Monitor task distribution across all developers</p>
            </div>
            <a href="{{ route('tasks.index') }}" class="btn-white">
                <i class="bi bi-arrow-left"></i> Back to Tasks
            </a>
        </div>
    </div>

    @if($developers->count() > 0)
        <!-- Developer Sections by Role -->
        @foreach($developers as $role => $roleDevs)
            <div class="role-section">
                <div class="role-header">
                    <h3 class="role-title">
                        <i class="bi bi-people-fill"></i>
                        {{ ucfirst(str_replace('_', ' ', $role)) }}s
                        <span class="role-badge">{{ $roleDevs->count() }} developer{{ $roleDevs->count() > 1 ? 's' : '' }}</span>
                    </h3>
                </div>
                <div class="p-4">
                    <div class="row g-4">
                        @foreach($roleDevs as $developer)
                            <div class="col-md-6 col-lg-4">
                                <div class="developer-card">
                                    <!-- Avatar -->
                                    <div class="developer-avatar">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    
                                    <!-- Developer Info -->
                                    @php
                                        $roleDisplayNames = [
                                            'frontend_dev' => 'Frontend Developer',
                                            'backend_dev' => 'Backend Developer', 
                                            'server_admin' => 'Server Administrator'
                                        ];
                                        $displayName = $roleDisplayNames[$role] ?? ucfirst(str_replace('_', ' ', $role));
                                    @endphp
                                    <div class="developer-name text-center">{{ $displayName }} {{ $loop->iteration }}</div>
                                    <div class="developer-role text-center">Member since {{ $developer->created_at->format('M Y') }}</div>
                                    
                                    <!-- Task Statistics -->
                                    <div class="mb-3">
                                        <div class="stat-row">
                                            <span class="stat-label">Total Tasks</span>
                                            <span class="stat-value">{{ $developer->tasks_assigned_count }}</span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-label">Pending</span>
                                            <span class="status-badge pending">{{ $developer->pending_tasks_count }}</span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-label">In Progress</span>
                                            <span class="status-badge in-progress">{{ $developer->in_progress_tasks_count }}</span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-label">In Review</span>
                                            <span class="status-badge in-review">{{ $developer->in_review_tasks_count }}</span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-label">Completed</span>
                                            <span class="status-badge completed">{{ $developer->completed_tasks_count }}</span>
                                        </div>
                                    </div>

                                    <!-- Workload Indicator -->
                                    @php
                                        $activeTasksCount = $developer->pending_tasks_count + $developer->in_progress_tasks_count + $developer->in_review_tasks_count;
                                    @endphp
                                    
                                    @if($activeTasksCount == 0)
                                        <div class="workload-badge available">
                                            <i class="bi bi-check-circle"></i> Available
                                        </div>
                                    @elseif($activeTasksCount <= 2)
                                        <div class="workload-badge light">
                                            <i class="bi bi-dash-circle"></i> Light Load
                                        </div>
                                    @elseif($activeTasksCount <= 5)
                                        <div class="workload-badge moderate">
                                            <i class="bi bi-exclamation-circle"></i> Moderate Load
                                        </div>
                                    @else
                                        <div class="workload-badge heavy">
                                            <i class="bi bi-x-circle"></i> Heavy Load
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Summary Statistics -->
        <div class="summary-card">
            <h3 class="mb-4 text-center">
                <i class="bi bi-bar-chart-fill me-2"></i>
                Distribution Summary
            </h3>
            
            <div class="row g-3 mb-4">
                @php
                    $totalDevs = $developers->flatten()->count();
                    $totalTasks = $developers->flatten()->sum('tasks_assigned_count');
                    $avgTasksPerDev = $totalDevs > 0 ? round($totalTasks / $totalDevs, 1) : 0;
                    $totalPending = $developers->flatten()->sum('pending_tasks_count');
                    $totalInProgress = $developers->flatten()->sum('in_progress_tasks_count');
                    $totalCompleted = $developers->flatten()->sum('completed_tasks_count');
                @endphp
                
                <div class="col-md-3">
                    <div class="summary-stat">
                        <div class="summary-number">{{ $totalDevs }}</div>
                        <div class="summary-label">Total Developers</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-stat">
                        <div class="summary-number">{{ $totalTasks }}</div>
                        <div class="summary-label">Total Tasks Assigned</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-stat">
                        <div class="summary-number">{{ $avgTasksPerDev }}</div>
                        <div class="summary-label">Avg Tasks per Dev</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-stat">
                        <div class="summary-number">{{ $totalPending + $totalInProgress }}</div>
                        <div class="summary-label">Active Tasks</div>
                    </div>
                </div>
            </div>
            
            <hr style="border-color: rgba(255, 255, 255, 0.2);">
            
            <h5 class="mb-3 text-center">Task Status Distribution</h5>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="distribution-item">
                        <div class="distribution-number">{{ $totalPending }}</div>
                        <div class="distribution-label">Pending</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="distribution-item">
                        <div class="distribution-number">{{ $totalInProgress }}</div>
                        <div class="distribution-label">In Progress</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="distribution-item">
                        <div class="distribution-number">{{ $totalInReview }}</div>
                        <div class="distribution-label">In Review</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="distribution-item">
                        <div class="distribution-number">{{ $totalCompleted }}</div>
                        <div class="distribution-label">Completed</div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="empty-state">
            <i class="bi bi-person-x"></i>
            <h4>No Developers Registered</h4>
            <p class="text-muted">No developers have registered yet. Tasks cannot be assigned until developers join the system.</p>
        </div>
    @endif
</div>
@endsection
