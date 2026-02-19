<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white py-4">
                        <h1 class="mb-2"><i class="bi bi-shield-check me-2"></i>Admin Dashboard</h1>
                        <p class="mb-0 opacity-75">Oversee and manage all system activities</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Users</p>
                                <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-people fs-3 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Tasks</p>
                                <h3 class="mb-0">{{ $stats['total_tasks'] }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-list-task fs-3 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Projects</p>
                                <h3 class="mb-0">{{ $stats['total_projects'] }}</h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-folder fs-3 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Notifications</p>
                                <h3 class="mb-0">{{ $stats['unread_notifications'] }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-bell fs-3 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Pending Tasks</h6>
                        <h4 class="mb-0 text-warning">{{ $stats['pending_tasks'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">In Progress</h6>
                        <h4 class="mb-0 text-info">{{ $stats['in_progress_tasks'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Completed</h6>
                        <h4 class="mb-0 text-success">{{ $stats['completed_tasks'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <a href="{{ route('admin.users') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-people fs-1 text-primary mb-3"></i>
                            <h5>Manage Users</h5>
                            <p class="text-muted mb-0">{{ $stats['total_users'] }} users</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.tasks') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-list-task fs-1 text-success mb-3"></i>
                            <h5>Manage Tasks</h5>
                            <p class="text-muted mb-0">{{ $stats['total_tasks'] }} tasks</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.projects') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-folder fs-1 text-info mb-3"></i>
                            <h5>Manage Projects</h5>
                            <p class="text-muted mb-0">{{ $stats['total_projects'] }} projects</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.users.create') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-person-plus fs-1 text-purple mb-3"></i>
                            <h5>Create User</h5>
                            <p class="text-muted mb-0">Add new user</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Users</h5>
                    </div>
                    <div class="card-body">
                        @forelse($recent_users as $user)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <div class="me-3">
                                    {!! $user->getProfilePictureHtml(40) !!}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->getRoleLabel() }}</small>
                                </div>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No recent users</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Recent Tasks</h5>
                    </div>
                    <div class="card-body">
                        @forelse($recent_tasks->take(5) as $task)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $task->title }}</h6>
                                    <small class="text-muted">
                                        @if($task->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="badge bg-info">In Progress</span>
                                        @elseif($task->status === 'done')
                                            <span class="badge bg-success">Done</span>
                                        @endif
                                    </small>
                                </div>
                                <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No recent tasks</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-lift {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .text-purple {
            color: #667eea !important;
        }
    </style>
</x-app-layout>
