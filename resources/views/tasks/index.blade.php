@extends('layouts.app')

@section('content')
<style>
    /* Force page background */
    body {
        background-color: var(--bg-color) !important;
    }
    
    [data-theme="dark"] body,
    [data-theme="dark"] html,
    [data-theme="dark"] main,
    [data-theme="dark"] .container {
        background-color: #0f172a !important;
    }
    
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
    
    .page-header-simple {
        margin-bottom: 2rem;
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }
    
    .page-subtitle {
        color: var(--muted-text);
        margin: 0;
    }
    
    .filter-section {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 2px solid rgba(167, 139, 250, 0.2);
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    }
    
    [data-theme="dark"] .filter-section {
        border-color: rgba(167, 139, 250, 0.3);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
    }
    
    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-color);
        color: var(--text-color);
    }
    
    .filter-select {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-color);
        color: var(--text-color);
        min-width: 150px;
    }
    
    .btn-create {
        background: var(--purple-gradient);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .tasks-table {
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        border: 2px solid rgba(167, 139, 250, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    [data-theme="dark"] .tasks-table {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border-color: rgba(167, 139, 250, 0.3);
    }
    
    .tasks-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tasks-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-bottom: none;
    }
    
    .tasks-table th {
        padding: 1.25rem 1rem;
        text-align: left;
        font-weight: 700;
        color: white;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .tasks-table td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(167, 139, 250, 0.1);
        color: var(--text-color);
    }
    
    .tasks-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .tasks-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .tasks-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    }
    
    [data-theme="dark"] .tasks-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(139, 156, 245, 0.12) 0%, rgba(157, 110, 201, 0.12) 100%);
    }
    
    .btn-view {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-view:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }
    
    [data-theme="dark"] .btn-view {
        background: linear-gradient(135deg, #8b9cf5 0%, #9d6ec9 100%);
    }
    
    [data-theme="dark"] .btn-view:hover {
        background: linear-gradient(135deg, #9d6ec9 0%, #8b9cf5 100%);
        box-shadow: 0 6px 16px rgba(139, 156, 245, 0.4);
    }
    
    .task-title-cell {
        max-width: 300px;
    }
    
    .task-title {
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: #667eea;
        font-size: 1rem;
    }
    
    [data-theme="dark"] .task-title {
        color: #a78bfa;
    }
    
    .task-description {
        font-size: 0.85rem;
        color: var(--muted-text);
        margin: 0;
        line-height: 1.5;
    }
    
    .category-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.75rem;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .category-badge.frontend {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
    }
    
    .category-badge.backend {
        background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        color: white;
    }
    
    .category-badge.server {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: white;
    }
    
    [data-theme="dark"] .category-badge.frontend {
        background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
    }
    
    [data-theme="dark"] .category-badge.backend {
        background: linear-gradient(135deg, #f472b6 0%, #ec4899 100%);
    }
    
    [data-theme="dark"] .category-badge.server {
        background: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 100%);
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.75rem;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .status-badge.pending {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }
    
    .status-badge.in-progress {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    
    .status-badge.in-review {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
    }
    
    .status-badge.completed {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    [data-theme="dark"] .status-badge.pending {
        background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
    }
    
    [data-theme="dark"] .status-badge.in-progress {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
    }
    
    [data-theme="dark"] .status-badge.in-review {
        background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
    }
    
    [data-theme="dark"] .status-badge.completed {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
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
    
    [data-theme="dark"] .tasks-table {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .filter-section {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .search-input,
    [data-theme="dark"] .filter-select {
        background-color: var(--bg-color);
        border-color: var(--border-color);
        color: var(--text-color);
    }
</style>

<div class="container py-4">
    <!-- Page Header -->
    <div class="page-header-simple">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="page-title">
                    @if(auth()->user()->isAdmin())
                        All Tasks (Admin View)
                    @elseif(auth()->user()->isCustomer())
                        My Tasks
                    @else
                        My Assigned Tasks
                    @endif
                </h1>
                <p class="page-subtitle">
                    @if(auth()->user()->isAdmin())
                        View all tasks in the system
                    @elseif(auth()->user()->isCustomer())
                        Manage and track all your created tasks
                    @else
                        {{ auth()->user()->getRoleLabel() }} - Complete your tasks
                    @endif
                </p>
            </div>
            @if(auth()->user()->isCustomer())
                <a href="{{ route('tasks.create') }}" class="btn-create">
                    <i class="bi bi-plus-circle"></i> Create Task
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <i class="bi bi-search" style="color: var(--muted-text);"></i>
        <input type="text" class="search-input" placeholder="Search tasks..." id="searchInput">
        <select class="filter-select" id="statusFilter">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="in_review">In Review</option>
            <option value="done">Completed</option>
        </select>
        @if(!auth()->user()->isDeveloper())
            <select class="filter-select" id="categoryFilter">
                <option value="">All Categories</option>
                <option value="frontend">Frontend</option>
                <option value="backend">Backend</option>
                <option value="server">Server</option>
            </select>
        @endif
    </div>

    <!-- Tasks Table -->
    @php
        if(auth()->user()->isAdmin()) {
            $displayTasks = $tasks;
        } elseif(auth()->user()->isCustomer()) {
            $displayTasks = $tasks;
        } else {
            $displayTasks = $assignedTasks;
        }
    @endphp

    @if($displayTasks->count() > 0)
        <div class="tasks-table">
            <table>
                <thead>
                    <tr>
                        <th>Task Title</th>
                        <th style="text-align: center;">Project</th>
                        @if(!auth()->user()->isDeveloper())
                            <th style="text-align: center;">Category</th>
                        @endif
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Created Date</th>
                        <th style="text-align: center;">Updated Date</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tasksTableBody">
                    @foreach($displayTasks as $task)
                        <tr class="task-row" 
                            data-status="{{ $task->status }}" 
                            data-category="{{ $task->category }}"
                            data-title="{{ strtolower($task->title) }}"
                            data-description="{{ strtolower($task->description ?? '') }}">
                            <td class="task-title-cell">
                                <div class="task-title">{{ $task->title }}</div>
                            </td>
                            <td style="text-align: center;">
                                <span style="color: var(--muted-text);">{{ $task->project->name ?? 'N/A' }}</span>
                            </td>
                            @if(!auth()->user()->isDeveloper())
                                <td style="text-align: center;">
                                    <span class="category-badge {{ $task->category }}">
                                        {{ $task->getCategoryLabel() }}
                                    </span>
                                </td>
                            @endif
                            <td style="text-align: center;">
                                @if($task->status === 'pending')
                                    <span class="status-badge pending">Pending</span>
                                @elseif($task->status === 'in_progress')
                                    <span class="status-badge in-progress">In Progress</span>
                                @elseif($task->status === 'in_review')
                                    <span class="status-badge in-review">In Review</span>
                                @elseif($task->status === 'done')
                                    <span class="status-badge completed">Completed</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span style="color: var(--muted-text);">{{ $task->created_at->format('M d, Y') }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span style="color: var(--muted-text);">{{ $task->updated_at->format('M d, Y') }}</span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('tasks.show', $task) }}" class="btn-view">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>No Tasks Yet</h4>
            <p class="text-muted mb-4">
                @if(auth()->user()->isCustomer())
                    Create your first task to get started
                @else
                    You don't have any tasks assigned yet
                @endif
            </p>
            @if(auth()->user()->isCustomer())
                <a href="{{ route('tasks.create') }}" class="btn-create">
                    <i class="bi bi-plus-circle"></i> Create First Task
                </a>
            @endif
        </div>
    @endif
</div>

<script>
    // Search and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const categoryFilter = document.getElementById('categoryFilter');
        const taskRows = document.querySelectorAll('.task-row');

        function filterTasks() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const categoryValue = categoryFilter.value;

            taskRows.forEach(row => {
                const title = row.getAttribute('data-title');
                const description = row.getAttribute('data-description');
                const status = row.getAttribute('data-status');
                const category = row.getAttribute('data-category');

                const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                const matchesStatus = !statusValue || status === statusValue;
                const matchesCategory = !categoryValue || category === categoryValue;

                if (matchesSearch && matchesStatus && matchesCategory) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterTasks);
        statusFilter.addEventListener('change', filterTasks);
        categoryFilter.addEventListener('change', filterTasks);
    });
</script>
@endsection
