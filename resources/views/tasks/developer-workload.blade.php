@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-dark">Developer Workload</h1>
            <p class="lead text-muted">Monitor task distribution across all developers</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-arrow-left"></i> Back to Tasks
            </a>
        </div>
    </div>

    @if($developers->count() > 0)
        @foreach($developers as $role => $roleDevs)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people-fill"></i> 
                        {{ ucfirst(str_replace('_', ' ', $role)) }}s 
                        <span class="badge bg-light text-dark">{{ $roleDevs->count() }} developers</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($roleDevs as $developer)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-person-circle" style="font-size: 3rem; color: #007bff;"></i>
                                        </div>
                                        @php
                                            $roleDisplayNames = [
                                                'frontend_dev' => 'Frontend Developer',
                                                'backend_dev' => 'Backend Developer', 
                                                'server_admin' => 'Server Administrator'
                                            ];
                                            $displayName = $roleDisplayNames[$role] ?? ucfirst(str_replace('_', ' ', $role));
                                        @endphp
                                        <h6 class="card-title">{{ $displayName }}</h6>
                                        <p class="text-muted small">{{ $loop->iteration }} of {{ $roleDevs->count() }}</p>
                                        
                                        <hr>
                                        
                                        <!-- Task Statistics -->
                                        <div class="row text-center">
                                            <div class="col-12 mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Total Tasks:</small>
                                                    <strong>{{ $developer->tasks_assigned_count }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Pending:</small>
                                                    <span class="badge bg-secondary">{{ $developer->pending_tasks_count }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">In Progress:</small>
                                                    <span class="badge bg-warning text-dark">{{ $developer->in_progress_tasks_count }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">In Review:</small>
                                                    <span class="badge bg-info text-white">{{ $developer->in_review_tasks_count }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Completed:</small>
                                                    <span class="badge bg-success">{{ $developer->completed_tasks_count }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <!-- Workload Indicator -->
                                        <div class="mt-3">
                                            @php
                                                $activeTasksCount = $developer->pending_tasks_count + $developer->in_progress_tasks_count + $developer->in_review_tasks_count;
                                            @endphp
                                            
                                            @if($activeTasksCount == 0)
                                                <span class="badge bg-success w-100">Available</span>
                                            @elseif($activeTasksCount <= 2)
                                                <span class="badge bg-info w-100">Light Load</span>
                                            @elseif($activeTasksCount <= 5)
                                                <span class="badge bg-warning w-100">Moderate Load</span>
                                            @else
                                                <span class="badge bg-danger w-100">Heavy Load</span>
                                            @endif
                                        </div>
                                        
                                        <small class="text-muted d-block mt-2">
                                            Joined {{ $developer->created_at->format('M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Summary Statistics -->
        <div class="card border-0 shadow-sm">
            <div class="card-header text-white" style="background-color: #212529;">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Distribution Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @php
                        $totalDevs = $developers->flatten()->count();
                        $totalTasks = $developers->flatten()->sum('tasks_assigned_count');
                        $avgTasksPerDev = $totalDevs > 0 ? round($totalTasks / $totalDevs, 1) : 0;
                        $totalPending = $developers->flatten()->sum('pending_tasks_count');
                        $totalInProgress = $developers->flatten()->sum('in_progress_tasks_count');
                        $totalCompleted = $developers->flatten()->sum('completed_tasks_count');
                    @endphp
                    
                    <div class="col-md-3 mb-3">
                        <h4 class="text-primary">{{ $totalDevs }}</h4>
                        <small class="text-muted">Total Developers</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <h4 class="text-primary">{{ $totalTasks }}</h4>
                        <small class="text-muted">Total Tasks Assigned</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <h4 class="text-primary">{{ $avgTasksPerDev }}</h4>
                        <small class="text-muted">Avg Tasks per Developer</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <h4 class="text-primary">{{ $totalPending + $totalInProgress }}</h4>
                        <small class="text-muted">Active Tasks</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">Task Status Distribution</h6>
                        <div class="d-flex justify-content-around">
                            <div class="text-center">
                                <span class="badge bg-secondary fs-6">{{ $totalPending }}</span>
                                <div><small>Pending</small></div>
                            </div>
                            <div class="text-center">
                                <span class="badge bg-warning fs-6">{{ $totalInProgress }}</span>
                                <div><small>In Progress</small></div>
                            </div>
                            <div class="text-center">
                                <span class="badge bg-info fs-6">{{ $totalInReview }}</span>
                                <div><small>In Review</small></div>
                            </div>
                            <div class="text-center">
                                <span class="badge bg-success fs-6">{{ $totalCompleted }}</span>
                                <div><small>Completed</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-person-x" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-4">No Developers Registered</h4>
                <p class="text-muted">No developers have registered yet. Tasks cannot be assigned until developers join the system.</p>
            </div>
        </div>
    @endif
</div>
@endsection