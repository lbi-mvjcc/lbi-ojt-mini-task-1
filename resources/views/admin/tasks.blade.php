@extends('layouts.app')

@section('content')
<style>
    .task-status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .task-status-badge.completed {
        background: #d1fae5;
        color: #065f46;
    }
    
    .task-status-badge.in-progress {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .task-status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .task-status-badge.in-review {
        background: #e0e7ff;
        color: #3730a3;
    }
    
    [data-theme="dark"] .task-status-badge.completed {
        background: rgba(209, 250, 229, 0.2);
        color: #34d399;
    }
    
    [data-theme="dark"] .task-status-badge.in-progress {
        background: rgba(219, 234, 254, 0.2);
        color: #93c5fd;
    }
    
    [data-theme="dark"] .task-status-badge.pending {
        background: rgba(254, 243, 199, 0.2);
        color: #fbbf24;
    }
    
    [data-theme="dark"] .task-status-badge.in-review {
        background: rgba(224, 231, 255, 0.2);
        color: #a5b4fc;
    }
    
    .priority-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    .priority-badge.high {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .priority-badge.medium {
        background: #fef3c7;
        color: #92400e;
    }
    
    .priority-badge.low {
        background: #dbeafe;
        color: #1e40af;
    }
    
    [data-theme="dark"] .priority-badge.high {
        background: rgba(254, 226, 226, 0.2);
        color: #fca5a5;
    }
    
    [data-theme="dark"] .priority-badge.medium {
        background: rgba(254, 243, 199, 0.2);
        color: #fbbf24;
    }
    
    [data-theme="dark"] .priority-badge.low {
        background: rgba(219, 234, 254, 0.2);
        color: #93c5fd;
    }
    
    [data-theme="dark"] .table tbody tr {
        background-color: transparent !important;
        color: #f1f5f9 !important;
    }
    
    [data-theme="dark"] .table tbody td {
        color: #f1f5f9 !important;
        background-color: transparent !important;
    }
    
    [data-theme="dark"] .table tbody td strong,
    [data-theme="dark"] .table tbody td .fw-bold {
        color: #f1f5f9 !important;
    }
    
    [data-theme="dark"] .table-hover tbody tr:hover {
        background-color: rgba(167, 139, 250, 0.1) !important;
        color: #f1f5f9 !important;
    }
    
    [data-theme="dark"] .table-hover tbody tr:hover td {
        color: #f1f5f9 !important;
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

        @if($tasks->count() > 0)
            <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 16px; overflow: hidden;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.1) 0%, rgba(196, 181, 253, 0.1) 100%); border-bottom: 2px solid rgba(167, 139, 250, 0.3); padding: 1.25rem;">
                    <h5 class="mb-0" style="font-weight: 600; color: var(--text-color);">
                        <i class="bi bi-list-check me-2" style="color: #a78bfa;"></i>All Tasks
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="border: none;">
                            <thead style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.1) 0%, rgba(196, 181, 253, 0.1) 100%); border-bottom: 2px solid rgba(167, 139, 250, 0.2);">
                                <tr>
                                    <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none; text-align: center;">Task</th>
                                    <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none; text-align: center;">Assigned To</th>
                                    <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none; text-align: center;">Project</th>
                                    <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none; text-align: center;">Status</th>
                                    <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none; text-align: center;">Priority</th>
                                    <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr style="border-bottom: 1px solid rgba(167, 139, 250, 0.05);">
                                        <td style="padding: 1.25rem 1rem; border: none; text-align: center;">
                                            <h6 class="mb-0" style="font-weight: 600; font-size: 0.95rem;">{{ $task->title }}</h6>
                                        </td>
                                        <td style="padding: 1.25rem 1rem; border: none; text-align: center;">
                                            @if($task->assignedTo)
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; margin-right: 0.75rem;">
                                                        {{ strtoupper(substr($task->assignedTo->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <span style="font-weight: 500;">{{ $task->assignedTo->name }}</span>
                                                        <br>
                                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $task->assignedTo->getRoleLabel() }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td style="padding: 1.25rem 1rem; border: none; text-align: center;">
                                            <span class="text-muted"><i class="bi bi-folder me-1"></i>{{ $task->project->name ?? 'General' }}</span>
                                        </td>
                                        <td style="padding: 1.25rem 1rem; border: none; text-align: center;">
                                            @if($task->status === 'done')
                                                <span class="badge task-status-badge completed">Completed</span>
                                            @elseif($task->status === 'in_progress')
                                                <span class="badge task-status-badge in-progress">In Progress</span>
                                            @elseif($task->status === 'in_review')
                                                <span class="badge task-status-badge in-review">In Review</span>
                                            @else
                                                <span class="badge task-status-badge pending">Pending</span>
                                            @endif
                                        </td>
                                        <td style="padding: 1.25rem 1rem; border: none; text-align: center;">
                                            <span class="badge priority-badge high">High</span>
                                        </td>
                                        <td style="padding: 1.25rem 1rem; border: none; text-align: center;">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none" style="color: #3b82f6; font-weight: 600; transition: all 0.2s ease;" onmouseover="this.style.color='#1d4ed8'; this.style.textDecoration='underline';" onmouseout="this.style.color='#3b82f6'; this.style.textDecoration='none';">
                                                    View
                                                </a>
                                                <form action="{{ route('admin.tasks.delete', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('This action cannot be undone. Are you sure you want to delete this?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0" style="text-decoration: none; font-weight: 500;">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
