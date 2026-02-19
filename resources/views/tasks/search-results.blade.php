@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-6 fw-bold text-dark">
                <i class="bi bi-search"></i> Search Results
            </h1>
            <p class="lead text-muted">Results for: "<strong>{{ $query }}</strong>"</p>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Tasks Results -->
    @if($tasks->count() > 0)
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="fw-bold text-primary mb-3">
                    <i class="bi bi-check2-square"></i> Tasks ({{ $tasks->count() }} found)
                </h3>
            </div>
        </div>

        @if(auth()->user()->isCustomer())
            <!-- Customer Task Results -->
            @php
                $tasksByCategory = $tasks->groupBy('category');
                $categoryLabels = [
                    'frontend' => ['title' => 'Frontend Developer', 'icon' => 'bi-code-slash', 'color' => 'black'],
                    'backend' => ['title' => 'Backend Developer', 'icon' => 'bi-server', 'color' => 'black'], 
                    'server' => ['title' => 'Server Administrator', 'icon' => 'bi-hdd-stack', 'color' => 'black'],
                ];
            @endphp

            @foreach($tasksByCategory as $category => $categoryTasks)
                @php $categoryInfo = $categoryLabels[$category] ?? ['title' => ucfirst($category), 'icon' => 'bi-gear', 'color' => 'secondary']; @endphp
                
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-{{ $categoryInfo['color'] }} mb-3">
                            <i class="{{ $categoryInfo['icon'] }}"></i> {{ $categoryInfo['title'] }}
                        </h4>
                    </div>
                </div>

                <div class="row">
                    @foreach($categoryTasks as $task)
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-{{ $categoryInfo['color'] }} text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">{{ $task->title }}</h5>
                                            <small><i class="bi bi-folder"></i> {{ $task->project->name ?? 'No Project' }}</small>
                                        </div>
                                        @php
                                            $statusBadges = [
                                                'pending' => 'secondary',
                                                'in_progress' => 'primary', 
                                                'in_review' => 'warning',
                                                'done' => 'success'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'in_progress' => 'In Progress',
                                                'in_review' => 'In Review', 
                                                'done' => 'Completed'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusBadges[$task->primary_status] ?? 'secondary' }}">
                                            {{ $statusLabels[$task->primary_status] ?? ucfirst(str_replace('_', ' ', $task->primary_status)) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <p class="text-muted mb-3">{{ Str::limit($task->description, 100) }}</p>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Developers Assigned:</small>
                                            <div class="fw-bold">{{ $task->developer_count }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Created:</small>
                                            <div class="fw-bold">{{ $task->created_at->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                    
                                    @if($task->assigned_developers)
                                        <div class="mb-3">
                                            <small class="text-muted">Assigned to:</small>
                                            <div class="fw-bold">{{ $task->assigned_developers }}</div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-footer bg-light">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-outline-{{ $categoryInfo['color'] }} btn-sm">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

        @else
            <!-- Developer Task Results -->
            <div class="row">
                @foreach($tasks as $task)
                    <div class="col-lg-6 mb-4">
                        @php
                            $categoryInfo = $categoryLabels[$task->category] ?? ['title' => ucfirst($task->category), 'icon' => 'bi-gear', 'color' => 'secondary'];
                        @endphp
                        
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-{{ $categoryInfo['color'] }} text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">{{ $task->title }}</h5>
                                        <small><i class="bi bi-folder"></i> {{ $task->project->name ?? 'No Project' }}</small>
                                    </div>
                                    @php
                                        $statusBadges = [
                                            'pending' => 'secondary',
                                            'in_progress' => 'primary', 
                                            'in_review' => 'warning',
                                            'done' => 'success'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusBadges[$task->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <p class="text-muted mb-3">{{ Str::limit($task->description, 100) }}</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Created by:</small>
                                        <div class="fw-bold">{{ $task->createdBy->name ?? 'Unknown' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Due Date:</small>
                                        <div class="fw-bold">
                                            {{ $task->deadline ? $task->deadline->format('M d, Y') : 'No deadline' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-outline-{{ $categoryInfo['color'] }} btn-sm">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-{{ $categoryInfo['color'] }} btn-sm">
                                        <i class="bi bi-pencil"></i> Update Status
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="row mb-5">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No tasks found matching your search criteria.
                </div>
            </div>
        </div>
    @endif

    <!-- Projects Results -->
    @if($projects->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold text-success mb-3">
                    <i class="bi bi-folder"></i> Projects ({{ $projects->count() }} found)
                </h3>
            </div>
        </div>

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
                            <!-- Project Description -->
                            @if($project->description)
                                <p class="text-muted mb-3">{{ Str::limit($project->description, 150) }}</p>
                            @endif
                            
                            <!-- Project Stats -->
                            <div class="row mb-4">
                                <div class="col-3 text-center">
                                    <h6 class="text-muted mb-1">Total Tasks</h6>
                                    <h4 class="mb-0">{{ $project->tasks->count() }}</h4>
                                </div>
                                <div class="col-3 text-center">
                                    <h6 class="text-muted mb-1">In Progress</h6>
                                    <h4 class="mb-0">{{ $project->tasks->where('status', 'in_progress')->count() }}</h4>
                                </div>
                                <div class="col-3 text-center">
                                    <h6 class="text-muted mb-1">In Review</h6>
                                    <h4 class="mb-0">{{ $project->tasks->where('status', 'in_review')->count() }}</h4>
                                </div>
                                <div class="col-3 text-center">
                                    <h6 class="text-muted mb-1">Completed</h6>
                                    <h4 class="mb-0">{{ $project->tasks->where('status', 'done')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-light">
                            @if(auth()->user()->isCustomer())
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-dark btn-sm">
                                    <i class="bi bi-eye"></i> View Project Details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @if($tasks->count() == 0)
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No projects found matching your search criteria.
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- No Results Message -->
    @if($tasks->count() == 0 && $projects->count() == 0)
        <div class="row">
            <div class="col-12 text-center">
                <div class="py-5">
                    <i class="bi bi-search" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h3 class="text-muted mt-3">No Results Found</h3>
                    <p class="text-muted">Try adjusting your search terms or browse all 
                        <a href="{{ route('tasks.index') }}">tasks</a>
                        @if(auth()->user()->isCustomer())
                            and <a href="{{ route('projects.index') }}">projects</a>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection