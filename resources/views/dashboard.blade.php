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
        --bg-color: #f8f9fa;
        --text-color: #1f2937;
        --card-bg: #ffffff;
        --border-color: #e5e7eb;
        --muted-text: #6b7280;
    }
    
    [data-theme="dark"] {
        --primary-purple: #8b9cf5;
        --primary-purple-dark: #7c8de8;
        --primary-purple-light: #a5b4f7;
        --secondary-purple: #9d6ec9;
        --purple-gradient: linear-gradient(135deg, #8b9cf5 0%, #9d6ec9 100%);
        --purple-light-bg: #1e2a3a;
        --purple-border: #2d3e50;
        --bg-color: #0f172a;
        --text-color: #f1f5f9;
        --card-bg: #1e293b;
        --border-color: #334155;
        --muted-text: #94a3b8;
    }
    
    .dashboard-card {
        border-radius: 12px;
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2) !important;
    }
    .stat-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid var(--purple-border);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        border-color: var(--primary-purple-light);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: var(--purple-light-bg);
        color: var(--primary-purple);
    }
    
    /* Make stat cards blend in dark mode */
    [data-theme="dark"] .stat-card {
        border: 1px solid rgba(30, 41, 59, 0.5);
    }
    .percentage-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
    }
    .percentage-badge.positive {
        background-color: rgba(102, 126, 234, 0.1);
        color: var(--primary-purple);
    }
    .percentage-badge.negative {
        background-color: rgba(220, 38, 38, 0.1);
        color: #dc2626;
    }
    .action-card {
        border-radius: 12px;
        padding: 2rem;
        cursor: pointer;
        text-decoration: none;
        display: block;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(167, 139, 250, 0.3);
    }
    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.25) !important;
        border-color: rgba(167, 139, 250, 0.6);
    }
    .action-card-vibrant {
        border-radius: 12px;
        padding: 2rem;
        cursor: pointer;
        text-decoration: none;
        display: block;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
        border: none;
    }
    .action-card-vibrant:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important;
    }
    .stat-icon-vibrant {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .action-card .arrow-icon {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        opacity: 0.7;
    }
    .gradient-primary {
        background: var(--purple-gradient);
    }
    .gradient-secondary {
        background: var(--card-bg);
        border: 2px solid var(--primary-purple);
    }
    .gradient-secondary:hover {
        background: var(--purple-light-bg);
    }
    .activity-item {
        padding: 1rem;
        border-left: 3px solid transparent;
        transition: all 0.2s;
        border-radius: 8px;
        background: transparent !important;
    }
    .activity-item:hover {
        background-color: transparent !important;
        border-left-color: var(--primary-purple);
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
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
    .btn-outline-primary {
        color: var(--primary-purple);
        border-color: var(--primary-purple);
    }
    .btn-outline-primary:hover {
        background-color: var(--primary-purple);
        border-color: var(--primary-purple);
        color: white;
    }
    
    [data-theme="dark"] .status-badge.pending {
        background-color: rgba(254, 243, 199, 0.2);
        color: #fbbf24;
    }
    
    [data-theme="dark"] .status-badge.completed {
        background-color: rgba(209, 250, 229, 0.2);
        color: #34d399;
    }
    
    [data-theme="dark"] .card {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .text-muted {
        color: var(--muted-text) !important;
    }
    
    /* Additional dark mode support */
    [data-theme="dark"] .dashboard-card {
        background-color: #1e293b !important;
        color: var(--text-color);
        border: 1px solid #334155 !important;
    }
    
    [data-theme="dark"] .dashboard-card .card-body {
        background-color: #1e293b !important;
    }
    
    [data-theme="dark"] .card.dashboard-card {
        background-color: #1e293b !important;
    }
    
    [data-theme="dark"] .card.dashboard-card .card-body {
        background-color: #1e293b !important;
    }
    
    /* Make stat cards blend in dark mode */
    [data-theme="dark"] .stat-card {
        border: 1px solid rgba(30, 41, 59, 0.5);
    }
    
    [data-theme="dark"] .stat-card {
        background-color: #1e293b !important;
        color: var(--text-color);
        border-color: #334155;
    }
    
    [data-theme="dark"] .action-card {
        background-color: #1e293b;
        color: var(--text-color);
    }
    
    [data-theme="dark"] .action-card.gradient-secondary {
        background-color: #1e293b;
        border-color: var(--primary-purple);
    }
    
    [data-theme="dark"] .action-card.gradient-secondary h4 {
        color: var(--primary-purple);
    }
    
    [data-theme="dark"] .action-card.gradient-secondary p {
        color: var(--muted-text);
    }
    
    [data-theme="dark"] .activity-item {
        color: var(--text-color);
        background-color: transparent !important;
        border-bottom-color: var(--border-color) !important;
    }
    
    [data-theme="dark"] .activity-item:hover {
        background-color: transparent !important;
        border-left-color: var(--primary-purple) !important;
    }
    
    [data-theme="dark"] .activity-item * {
        background-color: transparent !important;
    }
    
    [data-theme="dark"] .activity-item p,
    [data-theme="dark"] .activity-item div,
    [data-theme="dark"] .activity-item span {
        background-color: transparent !important;
    }
    
    [data-theme="dark"] .list-group-item {
        background-color: transparent !important;
        border-color: var(--border-color) !important;
    }
    
    [data-theme="dark"] .dashboard-card .activity-item {
        background-color: transparent !important;
    }
    
    [data-theme="dark"] .dashboard-card .list-group-flush .activity-item {
        background-color: transparent !important;
        border-bottom-color: var(--border-color) !important;
    }
    
    [data-theme="dark"] .activity-item h6 {
        color: var(--text-color);
    }
    
    [data-theme="dark"] .list-group-flush {
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .border-bottom {
        border-color: var(--border-color) !important;
    }
    
    [data-theme="dark"] .deadline-item {
        background-color: var(--purple-light-bg) !important;
        color: var(--text-color);
    }
    
    [data-theme="dark"] .deadline-item .fw-semibold {
        color: var(--text-color);
    }
    
    [data-theme="dark"] .page-title,
    [data-theme="dark"] .page-subtitle {
        color: var(--text-color) !important;
    }
    
    [data-theme="dark"] h1, 
    [data-theme="dark"] h2, 
    [data-theme="dark"] h3, 
    [data-theme="dark"] h4, 
    [data-theme="dark"] h5, 
    [data-theme="dark"] h6 {
        color: var(--text-color) !important;
    }
    
    [data-theme="dark"] .display-6 {
        color: var(--text-color) !important;
    }
    
    /* Fix light mode eyesore issues */
    body {
        background-color: var(--bg-color);
    }
    
    .container {
        background-color: transparent;
    }
    
    /* Force dark mode on this page */
    [data-theme="dark"] {
        background-color: var(--bg-color) !important;
    }
    
    [data-theme="dark"] body,
    [data-theme="dark"] .container,
    [data-theme="dark"] main {
        background-color: var(--bg-color) !important;
    }
    
    /* Ensure proper contrast in light mode */
    .stat-card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    [data-theme="dark"] .stat-card {
        background-color: #1e293b !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    
    /* Fix action card visibility */
    .action-card.gradient-secondary {
        background-color: var(--card-bg);
        border: 2px solid var(--primary-purple);
    }
    
    .action-card.gradient-secondary h4,
    .action-card.gradient-secondary p {
        color: var(--text-color);
    }
    
    [data-theme="dark"] .action-card.gradient-secondary h4 {
        color: var(--primary-purple-light);
    }
    
    [data-theme="dark"] .action-card.gradient-secondary p {
        color: var(--muted-text);
    }
    
    /* Fix profile avatar in dark mode */
    .profile-avatar {
        background: var(--purple-gradient);
    }
    
    [data-theme="dark"] .profile-avatar {
        border-color: var(--primary-purple-light);
    }
    
    /* Fix deadline items in dark mode */
    [data-theme="dark"] .deadline-item {
        background-color: var(--purple-light-bg) !important;
        border-left-color: var(--primary-purple-light);
    }
    
    [data-theme="dark"] .deadline-item .fw-semibold,
    [data-theme="dark"] .deadline-item .fw-bold {
        color: var(--text-color) !important;
    }
    
    /* Fix alert colors in dark mode */
    [data-theme="dark"] .alert-danger {
        background-color: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    
    /* Make Recent Activity card more visible but still blend in dark mode */
    [data-theme="dark"] .card.dashboard-card.shadow-sm {
        background-color: rgba(30, 41, 59, 0.5) !important;
        border: 2px solid rgba(139, 156, 245, 0.4) !important;
        box-shadow: none !important;
    }
    
    [data-theme="dark"] .card.dashboard-card.shadow-sm .card-body {
        background-color: transparent !important;
    }
    
    /* Make activity items and list items blend */
    [data-theme="dark"] .list-group-flush .activity-item {
        background-color: transparent !important;
        border-bottom: 1px solid rgba(139, 156, 245, 0.7) !important;
        border-left: 3px solid rgba(139, 156, 245, 0.6) !important;
        padding: 1rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    /* Make project cards in task list blend */
    [data-theme="dark"] .card.shadow-sm {
        background-color: transparent !important;
        border: 1px solid rgba(51, 65, 85, 0.3) !important;
        box-shadow: none !important;
    }
    
    [data-theme="dark"] .card.shadow-sm .card-header {
        background-color: transparent !important;
        border-bottom-color: rgba(51, 65, 85, 0.3) !important;
    }
</style>

<div class="container py-4" style="background-color: var(--bg-color);">
    @if(auth()->user()->isAdmin())
        @php
            $user = auth()->user();
            $totalUsers = \App\Models\User::count();
            $totalTasks = \App\Models\Task::count();
            $totalProjects = \App\Models\Project::count();
            $pendingTasks = \App\Models\Task::where('status', 'pending')->count();
            $recentTasks = \App\Models\Task::with('createdBy', 'assignedTo', 'project')->latest()->take(5)->get();
            $recentUsers = \App\Models\User::latest()->take(5)->get();
        @endphp

        <!-- Welcome Header -->
        <div class="mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-avatar" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary-purple); background: var(--purple-gradient); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="bi bi-shield-check"></i>
                    @endif
                </div>
                <div>
                    <h1 class="display-6 fw-bold mb-1">Welcome, {{ $user->name }}! 🛡️</h1>
                    <p class="text-muted mb-0">System overview and quick access to admin functions</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Users</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $totalUsers }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-list-task"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Tasks</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $totalTasks }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-folder"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Projects</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $totalProjects }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card dashboard-card shadow-sm" style="background: var(--card-bg); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 16px; overflow: hidden;">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: var(--card-bg); border-bottom: 1px solid rgba(167, 139, 250, 0.2); padding: 1.25rem;">
                        <h5 class="mb-0" style="font-weight: 600;">
                            <i class="bi bi-activity me-2" style="color: var(--primary-purple);"></i>Recent Tasks Activity
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; padding: 0.4rem 1rem;">
                                <i class="bi bi-search me-1"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; padding: 0.4rem 1rem;">
                                All Status
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="border: none;">
                                <thead style="background: rgba(167, 139, 250, 0.05); border-bottom: 1px solid rgba(167, 139, 250, 0.1);">
                                    <tr>
                                        <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Task</th>
                                        <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">User</th>
                                        <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Category</th>
                                        <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Status</th>
                                        <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Priority</th>
                                        <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTasks as $task)
                                        <tr style="border-bottom: 1px solid rgba(167, 139, 250, 0.05);">
                                            <td style="padding: 1.25rem 1rem; border: none;">
                                                <div>
                                                    <h6 class="mb-1" style="font-weight: 600; font-size: 0.95rem;">{{ $task->title }}</h6>
                                                    <small class="text-muted" style="font-size: 0.8rem;">{{ $task->created_at->format('Y-m-d') }}</small>
                                                </div>
                                            </td>
                                            <td style="padding: 1.25rem 1rem; border: none;">
                                                <div class="d-flex align-items-center">
                                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; margin-right: 0.75rem;">
                                                        {{ strtoupper(substr($task->assignedTo->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <span style="font-weight: 500;">{{ $task->assignedTo->name ?? 'Unassigned' }}</span>
                                                </div>
                                            </td>
                                            <td style="padding: 1.25rem 1rem; border: none;">
                                                <span class="text-muted">{{ $task->project->name ?? 'General' }}</span>
                                            </td>
                                            <td style="padding: 1.25rem 1rem; border: none;">
                                                @if($task->status === 'done')
                                                    <span class="badge" style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 500;">Completed</span>
                                                @elseif($task->status === 'in_progress')
                                                    <span class="badge" style="background: #dbeafe; color: #1e40af; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 500;">In Progress</span>
                                                @else
                                                    <span class="badge" style="background: #fef3c7; color: #92400e; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 500;">Pending</span>
                                                @endif
                                            </td>
                                            <td style="padding: 1.25rem 1rem; border: none;">
                                                <span class="badge" style="background: #fee2e2; color: #991b1b; padding: 0.4rem 0.8rem; border-radius: 12px; font-weight: 500; font-size: 0.75rem;">high</span>
                                            </td>
                                            <td style="padding: 1.25rem 1rem; border: none;">
                                                <a href="{{ route('tasks.show', $task->id) }}" class="text-decoration-none" style="color: var(--text-color); font-weight: 500;">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5" style="border: none;">
                                                <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                                                <p class="text-muted mt-2 mb-0">No recent tasks</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card dashboard-card shadow-sm" style="background: var(--card-bg); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 16px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-bottom: none; padding: 1.25rem;">
                        <h5 class="mb-0" style="color: white; font-weight: 600;"><i class="bi bi-people me-2"></i>Recent Users</h5>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        @forelse($recentUsers as $recentUser)
                            <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid rgba(167, 139, 250, 0.1); transition: all 0.2s;" onmouseover="this.style.backgroundColor='rgba(167, 139, 250, 0.05)'; this.style.marginLeft='-1rem'; this.style.marginRight='-1rem'; this.style.paddingLeft='1rem'; this.style.paddingRight='1rem'; this.style.borderRadius='8px';" onmouseout="this.style.backgroundColor='transparent'; this.style.marginLeft='0'; this.style.marginRight='0'; this.style.paddingLeft='0'; this.style.paddingRight='0';">
                                <div class="me-3" style="position: relative;">
                                    {!! $recentUser->getProfilePictureHtml(45) !!}
                                    <span style="position: absolute; bottom: 0; right: 0; width: 12px; height: 12px; background: #10b981; border: 2px solid var(--card-bg); border-radius: 50%;"></span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0" style="font-weight: 600;">{{ $recentUser->name }}</h6>
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        <i class="bi bi-shield-check me-1"></i>{{ $recentUser->getRoleLabel() }}
                                    </small>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem; background: rgba(167, 139, 250, 0.1); padding: 0.3rem 0.6rem; border-radius: 12px;">
                                    {{ $recentUser->created_at->diffForHumans() }}
                                </small>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No recent users</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Actions -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('admin.users') }}" class="action-card-vibrant shadow-sm" style="background: var(--card-bg); color: var(--text-color); border: 2px solid #a78bfa;">
                    <i class="bi bi-arrow-right-circle arrow-icon" style="color: #a78bfa;"></i>
                    <div class="text-center">
                        <div class="stat-icon-vibrant mx-auto mb-3" style="background: rgba(167, 139, 250, 0.1); color: #a78bfa;">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5 class="mb-2" style="color: var(--text-color);">Manage Users</h5>
                        <p class="text-muted small mb-0">View, create, edit users</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="{{ route('admin.tasks') }}" class="action-card-vibrant shadow-sm" style="background: var(--card-bg); color: var(--text-color); border: 2px solid #a78bfa;">
                    <i class="bi bi-arrow-right-circle arrow-icon" style="color: #a78bfa;"></i>
                    <div class="text-center">
                        <div class="stat-icon-vibrant mx-auto mb-3" style="background: rgba(167, 139, 250, 0.1); color: #a78bfa;">
                            <i class="bi bi-list-task"></i>
                        </div>
                        <h5 class="mb-2" style="color: var(--text-color);">Manage Tasks</h5>
                        <p class="text-muted small mb-0">View and delete tasks</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="{{ route('admin.projects') }}" class="action-card-vibrant shadow-sm" style="background: var(--card-bg); color: var(--text-color); border: 2px solid #a78bfa;">
                    <i class="bi bi-arrow-right-circle arrow-icon" style="color: #a78bfa;"></i>
                    <div class="text-center">
                        <div class="stat-icon-vibrant mx-auto mb-3" style="background: rgba(167, 139, 250, 0.1); color: #a78bfa;">
                            <i class="bi bi-folder"></i>
                        </div>
                        <h5 class="mb-2" style="color: var(--text-color);">Manage Projects</h5>
                        <p class="text-muted small mb-0">View and delete projects</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="{{ route('admin.users.create') }}" class="action-card-vibrant shadow-sm" style="background: var(--card-bg); color: var(--text-color); border: 2px solid #a78bfa;">
                    <i class="bi bi-arrow-right-circle arrow-icon" style="color: #a78bfa;"></i>
                    <div class="text-center">
                        <div class="stat-icon-vibrant mx-auto mb-3" style="background: rgba(167, 139, 250, 0.1); color: #a78bfa;">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h5 class="mb-2" style="color: var(--text-color);">Create User</h5>
                        <p class="text-muted small mb-0">Add new user account</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="{{ route('admin.password-reset-codes') }}" class="action-card-vibrant shadow-sm" style="background: var(--card-bg); color: var(--text-color); border: 2px solid #a78bfa;">
                    <i class="bi bi-arrow-right-circle arrow-icon" style="color: #a78bfa;"></i>
                    <div class="text-center">
                        <div class="stat-icon-vibrant mx-auto mb-3" style="background: rgba(167, 139, 250, 0.1); color: #a78bfa;">
                            <i class="bi bi-key"></i>
                        </div>
                        <h5 class="mb-2" style="color: var(--text-color);">Password Reset Codes</h5>
                        <p class="text-muted small mb-0">Generate reset codes</p>
                    </div>
                </a>
            </div>
        </div>

    @elseif(auth()->user()->isCustomer())
        @php
            $user = auth()->user();
            $allTasks = $user->tasksCreated;
            $totalTasks = $allTasks->count();
            $inProgressTasks = $allTasks->where('status', 'in_progress')->count();
            $completedTasks = $allTasks->where('status', 'done')->count();
            $pendingTasks = $allTasks->where('status', 'pending')->count();
            
            // Calculate percentage changes (mock data for demo - you can implement real calculation)
            $totalChange = $totalTasks > 0 ? 12 : 0;
            $inProgressChange = $inProgressTasks > 0 ? 5 : 0;
            $completedChange = $completedTasks > 0 ? 8 : 0;
            $pendingChange = $pendingTasks > 0 ? -3 : 0;
            
            // Get recent tasks
            $recentTasks = $user->tasksCreated()->with('project', 'assignedTo')->latest()->take(4)->get();
        @endphp

        <!-- Welcome Header -->
        <div class="mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-avatar" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary-purple); background: var(--purple-gradient); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="bi bi-person-circle"></i>
                    @endif
                </div>
                <div>
                    <h1 class="display-6 fw-bold mb-1">Welcome back, {{ $user->name }}! 👋</h1>
                    <p class="text-muted mb-0">Here's an overview of your tasks and progress</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Tasks -->
            <div class="col-md-6 col-lg-3">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-list-check"></i>
                        </div>
                        @if($totalChange != 0)
                            <span class="percentage-badge {{ $totalChange > 0 ? 'positive' : 'negative' }}">
                                {{ $totalChange > 0 ? '+' : '' }}{{ $totalChange }}%
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Tasks</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $totalTasks }}</h2>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="col-md-6 col-lg-3">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        @if($inProgressChange != 0)
                            <span class="percentage-badge {{ $inProgressChange > 0 ? 'positive' : 'negative' }}">
                                {{ $inProgressChange > 0 ? '+' : '' }}{{ $inProgressChange }}%
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">In Progress</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $inProgressTasks }}</h2>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="col-md-6 col-lg-3">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        @if($completedChange != 0)
                            <span class="percentage-badge {{ $completedChange > 0 ? 'positive' : 'negative' }}">
                                {{ $completedChange > 0 ? '+' : '' }}{{ $completedChange }}%
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Completed</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $completedTasks }}</h2>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="col-md-6 col-lg-3">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        @if($pendingChange != 0)
                            <span class="percentage-badge {{ $pendingChange > 0 ? 'positive' : 'negative' }}">
                                {{ $pendingChange > 0 ? '+' : '' }}{{ $pendingChange }}%
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Pending</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $pendingTasks }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="row g-3 mb-4">
            <!-- Create New Task -->
            <div class="col-md-6">
                <a href="{{ route('tasks.create') }}" class="action-card gradient-primary text-white shadow">
                    <i class="bi bi-arrow-up-right arrow-icon"></i>
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-white bg-opacity-25 me-3" style="color: white;">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2">Create New Task</h4>
                    <p class="mb-0 opacity-75">Start tracking a new task and manage it efficiently</p>
                </a>
            </div>

            <!-- View All Tasks -->
            <div class="col-md-6">
                <a href="{{ route('tasks.index') }}" class="action-card gradient-secondary shadow">
                    <i class="bi bi-arrow-up-right arrow-icon" style="color: var(--primary-purple);"></i>
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon me-3">
                            <i class="bi bi-list-ul"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2" style="color: var(--primary-purple);">View All Tasks</h4>
                    <p class="mb-0 text-muted">Access and manage all your tasks in one place</p>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card dashboard-card shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Recent Activity</h5>
                        <p class="text-muted small mb-0">Your latest task updates</p>
                    </div>
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                @if($recentTasks->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                        <p class="text-muted mt-3 mb-0">No tasks yet. Create your first task to get started!</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($recentTasks as $task)
                            <div class="activity-item border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $task->title }}</h6>
                                        <div class="text-muted small mb-2" style="background: transparent !important;">{{ $task->category }}</div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted small" style="background: transparent !important;">
                                                <i class="bi bi-calendar3"></i> {{ $task->created_at->format('Y-m-d') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $statusConfig = [
                                                'pending' => ['class' => 'pending', 'text' => 'Pending'],
                                                'in_progress' => ['class' => 'in-progress', 'text' => 'In Progress'],
                                                'in_review' => ['class' => 'in-review', 'text' => 'In Review'],
                                                'done' => ['class' => 'completed', 'text' => 'Completed']
                                            ];
                                            $config = $statusConfig[$task->status] ?? $statusConfig['pending'];
                                        @endphp
                                        <span class="status-badge {{ $config['class'] }}">
                                            {{ $config['text'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    @elseif(auth()->user()->isDeveloper())
        <!-- Developer view - Show Assigned Tasks -->
        @php
            $assignedTasks = auth()->user()->tasksAssigned()->latest()->get();
            $roleName = auth()->user()->getRoleLabel();
            
            // Calculate completion rate
            $totalTasks = $assignedTasks->count();
            $completedTasks = $assignedTasks->where('status', 'done')->count();
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            
            // Get tasks with deadlines
            $upcomingDeadlines = $assignedTasks->filter(function($task) {
                return $task->deadline && $task->deadline->isFuture() && $task->status !== 'done';
            })->sortBy('deadline')->take(5);
            
            $overdueTasksCount = $assignedTasks->filter(function($task) {
                return $task->deadline && $task->deadline->isPast() && $task->status !== 'done';
            })->count();
        @endphp

        <!-- Welcome Message -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-avatar" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary-purple); background: var(--purple-gradient); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="bi bi-person-circle"></i>
                    @endif
                </div>
                <div>
                    <h1 class="page-title mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h1>
                    <p class="page-subtitle mb-0">{{ $roleName }} - Here's your task overview</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-list-check"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Tasks</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $assignedTasks->count() }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">In Progress</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $assignedTasks->where('status', 'in_progress')->count() }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Completed</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $assignedTasks->where('status', 'done')->count() }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Overdue</p>
                        <h2 class="mb-0 fw-bold" style="color: var(--primary-purple);">{{ $overdueTasksCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="row g-3 mb-4">
            <!-- Progress Chart -->
            <div class="col-lg-6">
                <div class="dashboard-card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-graph-up text-purple me-2"></i>
                            Task Progress
                        </h5>
                        
                        <!-- Completion Rate -->
                        <div class="text-center mb-4">
                            <div class="progress-circle mx-auto" style="width: 150px; height: 150px; position: relative;">
                                <svg width="150" height="150" style="transform: rotate(-90deg);">
                                    <circle cx="75" cy="75" r="65" fill="none" stroke="var(--purple-border)" stroke-width="12"/>
                                    <circle cx="75" cy="75" r="65" fill="none" stroke="var(--primary-purple)" stroke-width="12"
                                            stroke-dasharray="{{ 2 * 3.14159 * 65 }}"
                                            stroke-dashoffset="{{ 2 * 3.14159 * 65 * (1 - $completionRate / 100) }}"
                                            stroke-linecap="round"
                                            style="transition: stroke-dashoffset 1s ease;"/>
                                </svg>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary-purple);">{{ $completionRate }}%</div>
                                    <div style="font-size: 0.85rem; color: var(--muted-text);">Complete</div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Breakdown -->
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: var(--purple-light-bg);">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #fbbf24;"></div>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                    <div class="fw-bold" style="color: var(--primary-purple);">{{ $assignedTasks->where('status', 'pending')->count() }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: var(--purple-light-bg);">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary-purple);"></div>
                                        <small class="text-muted">In Progress</small>
                                    </div>
                                    <div class="fw-bold" style="color: var(--primary-purple);">{{ $assignedTasks->where('status', 'in_progress')->count() }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: var(--purple-light-bg);">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #a855f7;"></div>
                                        <small class="text-muted">In Review</small>
                                    </div>
                                    <div class="fw-bold" style="color: var(--primary-purple);">{{ $assignedTasks->where('status', 'in_review')->count() }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: var(--purple-light-bg);">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
                                        <small class="text-muted">Completed</small>
                                    </div>
                                    <div class="fw-bold" style="color: var(--primary-purple);">{{ $assignedTasks->where('status', 'done')->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines Calendar -->
            <div class="col-lg-6">
                <div class="dashboard-card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-calendar-event text-purple me-2"></i>
                            Upcoming Deadlines
                        </h5>
                        
                        @if($upcomingDeadlines->count() > 0)
                            <div class="deadline-list">
                                @foreach($upcomingDeadlines as $task)
                                    @php
                                        $daysUntil = now()->diffInDays($task->deadline, false);
                                        $isUrgent = $daysUntil <= 3;
                                    @endphp
                                    <div class="deadline-item" style="padding: 0.75rem; border-left: 3px solid {{ $isUrgent ? '#ef4444' : 'var(--primary-purple)' }}; background: var(--purple-light-bg); border-radius: 8px; margin-bottom: 0.75rem;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold" style="color: var(--text-color);">{{ Str::limit($task->title, 30) }}</div>
                                                <small class="text-muted">
                                                    <i class="bi bi-tag"></i> {{ ucfirst($task->category) }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold" style="color: {{ $isUrgent ? '#ef4444' : 'var(--primary-purple)' }}; font-size: 0.9rem;">
                                                    {{ $task->deadline->format('M d') }}
                                                </div>
                                                <small style="color: {{ $isUrgent ? '#ef4444' : 'var(--muted-text)' }};">
                                                    @if($daysUntil == 0)
                                                        Today!
                                                    @elseif($daysUntil == 1)
                                                        Tomorrow
                                                    @else
                                                        {{ $daysUntil }} days
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-check" style="font-size: 3rem; color: var(--muted-text); opacity: 0.5;"></i>
                                <p class="text-muted mt-3 mb-0">No upcoming deadlines</p>
                            </div>
                        @endif

                        @if($overdueTasksCount > 0)
                            <div class="alert alert-danger mt-3 mb-0" style="border-radius: 10px;">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong>{{ $overdueTasksCount }}</strong> task{{ $overdueTasksCount > 1 ? 's are' : ' is' }} overdue!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- View All Tasks Button -->
        <div class="text-center">
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary" style="border-radius: 10px; padding: 0.75rem 2rem; font-weight: 600;">
                <i class="bi bi-list-ul me-2"></i> View All Assigned Tasks
            </a>
        </div>
    @else
            <!-- Generic Non-developer view -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <h4>Welcome {{ auth()->user()->name }}!</h4>
                    <p class="text-muted">You are logged in as: <strong>{{ auth()->user()->getRoleLabel() }}</strong></p>
                </div>
            </div>
    @endif
</div>
@endsection
