@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white py-4">
                        <h1 class="mb-2"><i class="bi bi-list-task me-2"></i>Task Management</h1>
                        <p class="mb-0 opacity-75">Oversee all tasks in the system</p>
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

        <!-- Tasks Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Project</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td>
                                        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                            <strong>{{ $task->title }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $task->project->name ?? 'N/A' }}</td>
                                    <td>{{ $task->createdBy->name ?? 'N/A' }}</td>
                                    <td>{{ $task->assignedTo->name ?? 'Unassigned' }}</td>
                                    <td>
                                        @if($task->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="badge bg-info">In Progress</span>
                                        @elseif($task->status === 'in_review')
                                            <span class="badge bg-primary">In Review</span>
                                        @elseif($task->status === 'done')
                                            <span class="badge bg-success">Done</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->priority === 'low')
                                            <span class="badge bg-secondary">Low</span>
                                        @elseif($task->priority === 'medium')
                                            <span class="badge bg-info">Medium</span>
                                        @elseif($task->priority === 'high')
                                            <span class="badge bg-warning">High</span>
                                        @elseif($task->priority === 'urgent')
                                            <span class="badge bg-danger">Urgent</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->deadline ? $task->deadline->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.tasks.delete', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('This action cannot be undone. Are you sure you want to delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No tasks found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
