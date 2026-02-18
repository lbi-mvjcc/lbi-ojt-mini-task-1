@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-dark">My Projects</h1>
            <p class="lead text-muted">View all your active projects</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Create New Task
            </a>
        </div>
    </div>

    @if($projects->count() > 0)
        <div class="row">
            @foreach($projects as $project)
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-folder"></i> {{ $project->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Project Stats -->
                            <div class="row mb-4">
                                <div class="col-3 text-center">
                                    <h6 class="text-muted mb-1">Unique Tasks</h6>
                                    <h4 class="mb-0">{{ $project->tasks()->select('title', 'project_id', 'category')->distinct()->count() }}</h4>
                                </div>
                                <div class="col-3 text-center">
                                    <h6 class="text-muted mb-1">In Progress</h6>
                                    <h4 class="mb-0">{{ $project->tasks()->where('status', 'in_progress')->count() }}</h4>
                                </div>
                                <div class="col-3 text-center">
                                    <h6 class="text-muted mb-1">In Review</h6>
                                    <h4 class="mb-0">{{ $project->tasks()->where('status', 'in_review')->count() }}</h4>
                                </div>
                                <div class="col-3 text-center">
                                    <h6 class="text-muted mb-1">Completed</h6>
                                    <h4 class="mb-0">{{ $project->tasks()->where('status', 'done')->count() }}</h4>
                                </div>
                            </div>

                            <hr>

                            <!-- Recent Tasks -->
                            @if($project->tasks()->count() > 0)
                                <h6 class="fw-bold mb-3">Recent Tasks</h6>
                                <div style="max-height: 200px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush">
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
                                            <li class="list-group-item px-0 py-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <p class="mb-1"><small><strong>{{ Str::limit($task->title, 25) }}</strong></small></p>
                                                        <small class="text-muted">{{ $taskDeveloperCount }} developer{{ $taskDeveloperCount > 1 ? 's' : '' }} • {{ ucfirst($task->category) }}</small>
                                                    </div>
                                                    <small>
                                                        @if($task->status === 'pending')
                                                            <span class="badge bg-secondary">Pending</span>
                                                        @elseif($task->status === 'in_progress')
                                                            <span class="badge bg-warning text-dark">In Progress</span>
                                                        @elseif($task->status === 'in_review')
                                                            <span class="badge bg-info text-white">In Review</span>
                                                        @elseif($task->status === 'done')
                                                            <span class="badge bg-success">Done</span>
                                                        @else
                                                            <span class="badge bg-secondary">Unknown</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <p class="text-muted text-center py-3">No tasks in this project yet</p>
                            @endif
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View All Tasks
                            </a>
                            <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-plus"></i> Add Task
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-folder-x" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-4">No Projects Yet</h4>
                <p class="text-muted mb-4">You haven't created any projects or tasks yet.</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Create Your First Task
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
