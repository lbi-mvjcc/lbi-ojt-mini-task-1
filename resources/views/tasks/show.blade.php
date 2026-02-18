@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-info-circle"></i> Task Details</h4>
                        <a href="{{ route('tasks.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-5">
                    <!-- Project -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Project</h6>
                        </div>
                        <div class="col-md-9">
                            <p class="fs-5">{{ $task->project->name }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Title -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Title</h6>
                        </div>
                        <div class="col-md-9">
                            <p class="fs-5">{{ $task->title }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Description -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Description</h6>
                        </div>
                        <div class="col-md-9">
                            <p class="fs-5">{{ $task->description ?? 'No description provided' }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Category -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Category</h6>
                        </div>
                        <div class="col-md-9">
                            <span class="badge bg-info fs-6">{{ ucfirst($task->category) }}</span>
                        </div>
                    </div>

                    <hr>

                    <!-- Status -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Status</h6>
                        </div>
                        <div class="col-md-9">
                            @if($task->status === 'pending')
                                <span class="badge bg-secondary fs-6">Pending</span>
                            @elseif($task->status === 'in_progress')
                                <span class="badge bg-warning fs-6">In Progress</span>
                            @elseif($task->status === 'in_review')
                                <span class="badge bg-info fs-6">In Review</span>
                            @else
                                <span class="badge bg-success fs-6">Done</span>
                            @endif
                        </div>
                    </div>

                    @if(!auth()->user()->isCustomer())
                    <!-- Task Assignment (Developer View Only) -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Assignment</h6>
                        </div>
                        <div class="col-md-9">
                            <p class="fs-5"><span class="badge bg-info">Assigned to you</span></p>
                        </div>
                    </div>

                    <hr>
                    @else
                    <!-- Developer Assignment Info (Customer View Only) -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Assigned Developers</h6>
                        </div>
                        <div class="col-md-9">
                            @if(isset($allTaskInstances))
                                <div class="mb-3">
                                    <span class="badge bg-primary fs-6">{{ $allTaskInstances->count() }} developer{{ $allTaskInstances->count() > 1 ? 's' : '' }} assigned</span>
                                </div>
                                
                                <div class="row">
                                    @foreach($allTaskInstances as $index => $taskInstance)
                                        <div class="col-md-6 mb-2">
                                            <div class="card border">
                                                <div class="card-body p-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <small class="fw-bold">
                                                                @php
                                                                    $roleLabels = [
                                                                        'frontend_dev' => 'Frontend Developer',
                                                                        'backend_dev' => 'Backend Developer',
                                                                        'server_admin' => 'Server Admin'
                                                                    ];
                                                                    $roleLabel = $roleLabels[$taskInstance->assignedTo->role] ?? 'Developer';
                                                                @endphp
                                                                {{ $roleLabel }} {{ $index + 1 }}
                                                            </small>
                                                        </div>
                                                        <div>
                                                            @if($taskInstance->status === 'pending')
                                                                <span class="badge bg-secondary">Pending</span>
                                                            @elseif($taskInstance->status === 'in_progress')
                                                                <span class="badge bg-warning text-dark">In Progress</span>
                                                            @elseif($taskInstance->status === 'in_review')
                                                                <span class="badge bg-info text-white">In Review</span>
                                                            @else
                                                                <span class="badge bg-success">Done</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>
                    @endif

                    <!-- Created By -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Created By</h6>
                        </div>
                        <div class="col-md-9">
                            <p class="fs-5">{{ $task->createdBy->name }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Created At -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Created At</h6>
                        </div>
                        <div class="col-md-9">
                            <p class="fs-5">{{ $task->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Deadline -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted fw-bold">Deadline</h6>
                        </div>
                        <div class="col-md-9">
                            @if($task->deadline)
                                <p class="fs-5">
                                    {{ $task->deadline->format('M d, Y') }}
                                    @if($task->deadline->isPast())
                                        <span class="badge bg-danger ms-2"><i class="bi bi-exclamation-triangle"></i> Overdue</span>
                                    @elseif($task->deadline->isToday())
                                        <span class="badge bg-warning ms-2"><i class="bi bi-clock"></i> Due today</span>
                                    @elseif($task->deadline->diffInDays() <= 3)
                                        <span class="badge bg-info ms-2"><i class="bi bi-clock"></i> Due soon</span>
                                    @endif
                                </p>
                            @else
                                <p class="fs-5 text-muted">No deadline set</p>
                            @endif
                        </div>
                    </div>

                    <!-- Submissions -->
                    @if($task->hasSubmissionRequirements())
                        <hr>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <h6 class="text-muted fw-bold">Submissions</h6>
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                    @if($task->requires_file_submission)
                                        <span class="badge bg-primary fs-6">
                                            <i class="bi bi-file-earmark"></i> Files Accepted
                                        </span>
                                    @endif
                                    @if($task->requires_image_submission)
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-image"></i> Images Accepted
                                        </span>
                                    @endif
                                    @if($task->requires_link_submission)
                                        <span class="badge bg-info fs-6">
                                            <i class="bi bi-link-45deg"></i> Links Accepted
                                        </span>
                                    @endif
                                </div>

                                @if($task->submission_instructions)
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Instructions:</strong> {{ $task->submission_instructions }}
                                        </small>
                                    </div>
                                @endif

                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        @php
                                            $submissionCount = $task->submissions()->count();
                                        @endphp
                                        <span class="text-muted">
                                            {{ $submissionCount }} submission{{ $submissionCount !== 1 ? 's' : '' }} uploaded
                                        </span>
                                    </div>
                                    
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tasks.submissions.index', $task) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i> View All
                                        </a>
                                        @can('upload', $task)
                                            <a href="{{ route('tasks.submissions.create', $task) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-cloud-upload"></i> Upload
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <!-- Actions -->
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-end">
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Are you sure you want to delete this task?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
