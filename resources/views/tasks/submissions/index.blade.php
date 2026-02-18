@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('tasks.index') }}" class="text-decoration-none">
                            <i class="bi bi-list-task"></i> Tasks
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                            {{ $task->title }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Submissions</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="display-6 mb-1">
                        <i class="bi bi-cloud-upload text-primary"></i> Submissions
                    </h2>
                    <p class="text-muted mb-0">{{ $task->title }}</p>
                </div>

                @can('upload', $task)
                    @if($task->hasSubmissionRequirements())
                        <a href="{{ route('tasks.submissions.create', $task) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Upload Submission
                        </a>
                    @endif
                @endcan
            </div>

            <!-- Submission Requirements Summary -->
            @if($task->hasSubmissionRequirements())
                <div class="card mb-4 border-info">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle text-info"></i> Submission Requirements
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex flex-wrap gap-2">
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
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Instructions:</strong> {{ $task->submission_instructions }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="text-muted small">
                                    {{ $submissions->count() }} submission{{ $submissions->count() !== 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary" role="alert">
                    <i class="bi bi-info-circle"></i> 
                    No submission requirements have been set for this task.
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Submissions List -->
            @if($submissions->count() > 0)
                <div class="row">
                    @foreach($submissions as $submission)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($submission->type === 'file')
                                            <i class="bi bi-file-earmark text-primary"></i>
                                        @elseif($submission->type === 'image')  
                                            <i class="bi bi-image text-success"></i>
                                        @else
                                            <i class="bi bi-link-45deg text-info"></i>
                                        @endif
                                        
                                        <span class="fw-bold ms-1">
                                            {{ $submission->title ?: 'Untitled Submission' }}
                                        </span>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($submission->type === 'file' || $submission->type === 'image')
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="{{ route('tasks.submissions.download', [$task, $submission]) }}">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="{{ $submission->link_url }}" 
                                                       target="_blank" rel="noopener">
                                                        <i class="bi bi-box-arrow-up-right"></i> Open Link
                                                    </a>
                                                </li>
                                            @endif
                                            @can('delete', $submission)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('tasks.submissions.destroy', [$task, $submission]) }}" 
                                                          method="POST" class="d-inline submission-delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>

                                <div class="card-body">
                                    @if($submission->description)
                                        <p class="card-text mb-3">{{ $submission->description }}</p>
                                    @endif

                                    <div class="row text-muted small">
                                        <div class="col-12 mb-2">
                                            @if($submission->type === 'file' || $submission->type === 'image')
                                                <i class="bi bi-file-earmark"></i>
                                                <strong>Filename:</strong> {{ $submission->display_filename }}
                                            @else
                                                <i class="bi bi-link-45deg"></i>
                                                <strong>URL:</strong>
                                                <a href="{{ $submission->link_url }}" 
                                                   target="_blank" 
                                                   rel="noopener" 
                                                   class="text-decoration-none ms-1">
                                                    {{ Str::limit($submission->link_url, 50) }}
                                                    <i class="bi bi-box-arrow-up-right small"></i>
                                                </a>
                                            @endif
                                        </div>

                                        @if($submission->type === 'file' || $submission->type === 'image')
                                            <div class="col-12 mb-2">
                                                <i class="bi bi-hdd"></i>
                                                <strong>Size:</strong> {{ number_format($submission->file_size / 1024, 2) }} KB
                                            </div>
                                        @endif

                                        <div class="col-12 mb-2">
                                            <i class="bi bi-person"></i>
                                            <strong>Uploaded by:</strong>
                                            @if($submission->user->role === 'customer')
                                                {{ $submission->user->name }}
                                            @else
                                                {{ ucwords(str_replace('_', ' ', $submission->user->role)) }} 
                                                ({{ $submission->user->name }})
                                            @endif
                                        </div>

                                        <div class="col-12">
                                            <i class="bi bi-clock"></i>
                                            <strong>Uploaded:</strong> {{ $submission->created_at->format('M j, Y g:i A') }}
                                        </div>
                                    </div>
                                </div>

                                @if($submission->type === 'image')
                                    <div class="card-footer p-0">
                                        <img src="{{ Storage::url($submission->file_path) }}" 
                                             alt="Submission Image" 
                                             class="img-fluid w-100" 
                                             style="max-height: 200px; object-fit: cover;"
                                             loading="lazy">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-inbox display-1 mb-3 d-block"></i>
                        <h5>No submissions yet</h5>
                        <p class="mb-4">There are no submissions for this task.</p>
                        
                        @can('upload', $task)
                            @if($task->hasSubmissionRequirements())
                                <a href="{{ route('tasks.submissions.create', $task) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Upload First Submission
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Back to Task Button -->
            <div class="mt-4">
                <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Task
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle submission delete forms with confirmation
    const deleteForms = document.querySelectorAll('.submission-delete-form');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to delete this submission? This action cannot be undone.')) {
                this.submit();
            }
        });
    });
});
</script>

@endsection