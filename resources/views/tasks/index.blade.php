@extends('layouts.app')

@section('content')
<div class="container py-5">
    @if(auth()->user()->isCustomer())
        <!-- CUSTOMER VIEW: Created Tasks -->
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-md-6">
                <h1 class="display-6 fw-bold text-dark">Tasks</h1>
                <p class="lead text-muted">Manage all your created tasks</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <form method="POST" action="{{ route('tasks.check-deadlines') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning btn-lg" title="Check for tasks that are due soon or overdue">
                            <i class="bi bi-clock-history"></i> Check Deadlines
                        </button>
                    </form>
                    <a href="{{ route('tasks.developer-workload') }}" class="btn btn-outline-info btn-lg">
                        <i class="bi bi-bar-chart"></i> Developer Workload
                    </a>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Create New Task
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- All Tasks organized by Category -->
        @if($tasks->count() > 0)
            @php
                $tasksByCategory = $tasks->groupBy('category');
                $categoryLabels = [
                    'frontend' => ['title' => 'Frontend Development', 'icon' => 'bi-code-slash', 'color' => 'primary'],
                    'backend' => ['title' => 'Backend Development', 'icon' => 'bi-server', 'color' => 'success'], 
                    'server' => ['title' => 'Server Administration', 'icon' => 'bi-hdd-stack', 'color' => 'warning']
                ];
            @endphp

            @foreach($categoryLabels as $category => $config)
                @if($tasksByCategory->has($category))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="{{ $config['icon'] }}"></i> {{ $config['title'] }}
                                <span class="badge bg-light text-dark ms-2">{{ $tasksByCategory[$category]->count() }} task{{ $tasksByCategory[$category]->count() > 1 ? 's' : '' }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">Task</th>
                                            <th class="border-0">Project</th>
                                            <th class="border-0">Developers</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Progress</th>
                                            <th class="border-0">Created</th>
                                            <th class="border-0">Deadline</th>
                                            <th class="border-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tasksByCategory[$category] as $task)
                                            <tr>
                                                <td class="align-middle">
                                                    <div>
                                                        <h6 class="mb-1">{{ Str::limit($task->title, 25) }}</h6>
                                                        @if($task->description)
                                                            <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <strong>{{ $task->project->name }}</strong>
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $developerRole = '';
                                                        switch($task->category) {
                                                            case 'frontend':
                                                                $developerRole = 'Frontend Developer';
                                                                break;
                                                            case 'backend':
                                                                $developerRole = 'Backend Developer';
                                                                break;
                                                            case 'server':
                                                                $developerRole = 'Server Admin';
                                                                break;
                                                            default:
                                                                $developerRole = 'Developer';
                                                        }
                                                    @endphp
                                                    <span class="badge bg-info">{{ $task->developer_count }} dev{{ $task->developer_count > 1 ? 's' : '' }} {{ $developerRole }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    @if($task->primary_status === 'pending')
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @elseif($task->primary_status === 'in_progress')
                                                        <span class="badge bg-warning text-dark">In Progress</span>
                                                    @elseif($task->primary_status === 'in_review')
                                                        <span class="badge bg-info text-white">In Review</span>
                                                    @else
                                                        <span class="badge bg-success">Done</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if($task->developer_count > 1)
                                                        <div class="d-flex gap-1 flex-wrap">
                                                            @if($task->status_counts->get('pending', 0) > 0)
                                                                <small><span class="badge bg-secondary">{{ $task->status_counts->get('pending') }}P</span></small>
                                                            @endif
                                                            @if($task->status_counts->get('in_progress', 0) > 0)
                                                                <small><span class="badge bg-warning text-dark">{{ $task->status_counts->get('in_progress') }}IP</span></small>
                                                            @endif
                                                            @if($task->status_counts->get('in_review', 0) > 0)
                                                                <small><span class="badge bg-info text-white">{{ $task->status_counts->get('in_review') }}IR</span></small>
                                                            @endif
                                                            @if($task->status_counts->get('done', 0) > 0)
                                                                <small><span class="badge bg-success">{{ $task->status_counts->get('done') }}D</span></small>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <small class="text-muted">Single dev</small>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <small class="text-muted">{{ $task->created_at->format('M d, Y') }}</small>
                                                </td>
                                                <td class="align-middle">
                                                    @if($task->all_tasks->first()->deadline)
                                                        <small class="text-muted">{{ $task->all_tasks->first()->deadline->format('M d, Y') }}</small>
                                                        @if($task->all_tasks->first()->deadline->isPast())
                                                            <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Overdue</small>
                                                        @elseif($task->all_tasks->first()->deadline->isToday())
                                                            <br><small class="text-warning"><i class="bi bi-clock"></i> Due today</small>
                                                        @endif
                                                    @else
                                                        <small class="text-muted">No deadline</small>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-outline-primary btn-sm" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')" title="Delete">
                                                                <i class="bi bi-trash"></i>
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
                @endif
            @endforeach
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-4">No Tasks Created Yet</h4>
                    <p class="text-muted mb-4">Create your first task and start assigning work to developers.</p>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Create First Task
                    </a>
                </div>
            </div>
        @endif

    @elseif(auth()->user()->isDeveloper())
        <!-- DEVELOPER VIEW: Assigned Tasks -->
        <div class="row mb-5">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold text-dark">My Assigned Tasks</h1>
                <p class="lead text-muted">{{ auth()->user()->getRoleLabel() }} - Complete your assigned tasks</p>
            </div>
        </div>

        <!-- Error Message -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($assignedTasks->count() > 0)
            <!-- Task Stats -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Total Tasks</h6>
                            <h3 class="mb-0">{{ $assignedTasks->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Pending</h6>
                            <h3 class="mb-0 text-secondary">{{ $assignedTasks->where('status', 'pending')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">In Progress</h6>
                            <h3 class="mb-0 text-warning">{{ $assignedTasks->where('status', 'in_progress')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">In Review</h6>
                            <h3 class="mb-0 text-info">{{ $assignedTasks->where('status', 'in_review')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second row for Completed -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Completed</h6>
                            <h3 class="mb-0 text-success">{{ $assignedTasks->where('status', 'done')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Cards -->
            <div class="row">
                @foreach($assignedTasks as $task)
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-0">
                                            <i class="bi bi-list-check"></i> {{ Str::limit($task->title, 30) }}
                                        </h5>
                                    </div>
                                    <span class="badge bg-light @if($task->status === 'pending') text-secondary @elseif($task->status === 'in_progress') text-warning @elseif($task->status === 'in_review') text-info @else text-success @endif">
                                        {{ ucfirst($task->category) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Description -->
                                @if($task->description)
                                    <p class="text-muted mb-3">
                                        {{ Str::limit($task->description, 70) }}
                                    </p>
                                @endif

                                <hr>

                                <!-- Task Info -->
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h6 class="text-muted mb-1">Project</h6>
                                        <p class="mb-0"><small><strong>{{ $task->project->name }}</strong></small></p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted mb-1">Created By</h6>
                                        <p class="mb-0"><small>{{ $task->createdBy->name }}</small></p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h6 class="text-muted mb-1">Status</h6>
                                        <p class="mb-0">
                                            @if($task->status === 'pending')
                                                <span class="badge bg-secondary">Pending</span>
                                            @elseif($task->status === 'in_progress')
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            @elseif($task->status === 'in_review')
                                                <span class="badge bg-info text-white">In Review</span>
                                            @else
                                                <span class="badge bg-success">Done</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted mb-1">Created</h6>
                                        <p class="mb-0"><small>{{ $task->created_at->format('M d, Y') }}</small></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm @if($task->status === 'pending') btn-outline-secondary @elseif($task->status === 'in_progress') btn-outline-warning @elseif($task->status === 'in_review') btn-outline-info @else btn-outline-success @endif dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-pencil"></i> Update Status
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form method="POST" action="{{ route('tasks.updateStatus', $task) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-clock"></i> Pending
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('tasks.updateStatus', $task) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-arrow-repeat"></i> In Progress
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('tasks.updateStatus', $task) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="in_review">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-search"></i> In Review
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('tasks.updateStatus', $task) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="done">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-check-circle"></i> Done
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-4">No Assigned Tasks</h4>
                    <p class="text-muted">You don't have any assigned tasks at the moment. Check back later!</p>
                </div>
            </div>
        @endif

    @else
        <div class="alert alert-danger">
            <strong>Error:</strong> Invalid user role
        </div>
    @endif
</div>
@endsection