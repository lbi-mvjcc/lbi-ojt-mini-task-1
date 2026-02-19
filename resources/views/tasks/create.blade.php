@extends('layouts.app')

@section('content')
<style>
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
    
    .create-task-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .page-header {
        background: var(--purple-gradient);
        border-radius: 16px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
    }
    
    .form-card {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--purple-border);
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.1);
        transition: all 0.3s ease;
    }
    
    .form-card:hover {
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
    }
    
    .form-section {
        padding: 2rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-label i {
        color: var(--primary-purple);
    }
    
    .form-control, .form-select {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        background-color: var(--card-bg);
        color: var(--text-color);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-purple);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        background-color: var(--card-bg);
        color: var(--text-color);
    }
    
    .form-control::placeholder {
        color: var(--muted-text);
    }
    
    .info-card {
        background: var(--purple-light-bg);
        border: 1px solid var(--purple-border);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 0.75rem;
    }
    
    .info-card i {
        color: var(--primary-purple);
    }
    
    .project-tag {
        display: inline-block;
        background: var(--purple-light-bg);
        color: var(--primary-purple);
        border: 1px solid var(--purple-border);
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        margin: 0.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .project-tag:hover {
        background: var(--primary-purple);
        color: white;
        transform: translateY(-2px);
    }
    
    .submission-card {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .submission-card:hover {
        border-color: var(--primary-purple-light);
    }
    
    .checkbox-card {
        background: var(--purple-light-bg);
        border: 2px solid var(--purple-border);
        border-radius: 10px;
        padding: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .checkbox-card:hover {
        border-color: var(--primary-purple);
        transform: translateY(-2px);
    }
    
    .checkbox-card input:checked ~ label {
        color: var(--primary-purple);
        font-weight: 600;
    }
    
    .btn-purple {
        background: var(--purple-gradient);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-purple:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .btn-light-purple {
        background: var(--card-bg);
        border: 2px solid var(--primary-purple);
        color: var(--primary-purple);
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-light-purple:hover {
        background: var(--purple-light-bg);
        transform: translateY(-2px);
    }
    
    .required-badge {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    [data-theme="dark"] .form-control,
    [data-theme="dark"] .form-select {
        background-color: var(--bg-color);
        color: var(--text-color);
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .form-control:focus,
    [data-theme="dark"] .form-select:focus {
        background-color: var(--card-bg);
        color: var(--text-color);
    }
</style>

<div class="container py-4">
    <div class="create-task-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-plus-circle-fill me-3" style="font-size: 2.5rem;"></i>
                <div>
                    <h1 class="mb-0 fw-bold">Create New Task</h1>
                    <p class="mb-0 opacity-75">Start tracking a new task and assign it to developers</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf

            <!-- Form Card -->
            <div class="form-card">
                <!-- Project Section -->
                <div class="form-section">
                    <label for="project_name" class="form-label">
                        <i class="bi bi-folder-fill"></i>
                        Project Name
                        <span class="required-badge">Required</span>
                    </label>
                    <input type="text" 
                           id="project_name" 
                           name="project_name" 
                           class="form-control @error('project_name') is-invalid @enderror"
                           placeholder="Enter or select your project name..."
                           value="{{ old('project_name', '') }}"
                           list="existing_projects"
                           required>
                    
                    @if(!empty($existingProjects))
                        <datalist id="existing_projects">
                            @foreach($existingProjects as $project)
                                <option value="{{ $project }}">{{ $project }}</option>
                            @endforeach
                        </datalist>
                        
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-lightbulb"></i> Quick select from existing projects:
                            </small>
                            <div>
                                @foreach($existingProjects as $project)
                                    <span class="project-tag" onclick="document.getElementById('project_name').value='{{ $project }}'">
                                        <i class="bi bi-folder"></i> {{ $project }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <small class="form-text text-muted mt-2 d-block">
                            <i class="bi bi-info-circle"></i> Create your first project by entering a new project name
                        </small>
                    @endif
                    
                    @error('project_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Task Details Section -->
                <div class="form-section">
                    <label for="title" class="form-label">
                        <i class="bi bi-card-text"></i>
                        Task Title
                        <span class="required-badge">Required</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           placeholder="e.g., Fix login bug, Update homepage design" 
                           value="{{ old('title') }}" 
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="mt-4">
                        <label for="description" class="form-label">
                            <i class="bi bi-align-left"></i>
                            Description
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Provide detailed information about the task...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Category & Deadline Section -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="category" class="form-label">
                                <i class="bi bi-tag-fill"></i>
                                Category
                                <span class="required-badge">Required</span>
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category" 
                                    required>
                                <option value="">Select Category</option>
                                <option value="frontend" @selected(old('category') == 'frontend')>
                                    Frontend Developer
                                </option>
                                <option value="backend" @selected(old('category') == 'backend')>
                                    Backend Developer
                                </option>
                                <option value="server" @selected(old('category') == 'server')>
                                    Server Administrator
                                </option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="deadline" class="form-label">
                                <i class="bi bi-calendar-event"></i>
                                Deadline
                            </label>
                            <input type="date" 
                                   class="form-control @error('deadline') is-invalid @enderror" 
                                   id="deadline" 
                                   name="deadline" 
                                   value="{{ old('deadline') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            <small class="form-text text-muted mt-1 d-block">
                                <i class="bi bi-info-circle"></i> Optional: Must be after today
                            </small>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="info-card">
                        <i class="bi bi-people-fill"></i>
                        <strong>Auto-Assignment:</strong> This task will be automatically assigned to <strong>ALL</strong> developers in the selected category. Each developer will receive their own copy to work on independently.
                    </div>
                </div>

                <!-- Submission Requirements Section -->
                <div class="form-section">
                    <label class="form-label">
                        <i class="bi bi-cloud-upload"></i>
                        Submission Requirements
                        <span class="badge bg-secondary ms-2" style="font-size: 0.7rem;">Optional</span>
                    </label>
                    
                    <div class="submission-card">
                        <p class="text-muted mb-3">
                            <i class="bi bi-info-circle"></i> 
                            Select what developers should submit when completing this task. All submissions are optional for developers.
                        </p>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="checkbox-card">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="requires_file_submission" 
                                               id="requires_file_submission" 
                                               value="1" 
                                               {{ old('requires_file_submission') ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="requires_file_submission">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-zip text-primary me-2" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <div class="fw-semibold">File Upload</div>
                                                    <small class="text-muted">Documents, archives</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="checkbox-card">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="requires_image_submission" 
                                               id="requires_image_submission" 
                                               value="1" 
                                               {{ old('requires_image_submission') ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="requires_image_submission">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-image text-success me-2" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <div class="fw-semibold">Image Upload</div>
                                                    <small class="text-muted">Screenshots, designs</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="checkbox-card">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="requires_link_submission" 
                                               id="requires_link_submission" 
                                               value="1" 
                                               {{ old('requires_link_submission') ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="requires_link_submission">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-link-45deg text-info me-2" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <div class="fw-semibold">Link Submission</div>
                                                    <small class="text-muted">URLs, repositories</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submission Instructions -->
                        <div class="mt-4" id="submission-instructions" style="display: none;">
                            <label for="submission_instructions" class="form-label">
                                <i class="bi bi-card-text"></i>
                                Submission Instructions
                            </label>
                            <textarea class="form-control @error('submission_instructions') is-invalid @enderror" 
                                      id="submission_instructions" 
                                      name="submission_instructions" 
                                      rows="3" 
                                      placeholder="Provide specific instructions: file formats, naming conventions, what to include...">{{ old('submission_instructions') }}</textarea>
                            @error('submission_instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-1 d-block">
                                <i class="bi bi-lightbulb"></i> Be specific about requirements to help developers submit correctly
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-section">
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('tasks.index') }}" class="btn btn-light-purple">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-purple">
                            <i class="bi bi-check-circle"></i> Create Task
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Show/hide submission instructions based on checkbox selection
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = [
            document.getElementById('requires_file_submission'),
            document.getElementById('requires_image_submission'),
            document.getElementById('requires_link_submission')
        ];
        const instructionsDiv = document.getElementById('submission-instructions');

        function toggleInstructions() {
            const anyChecked = checkboxes.some(checkbox => checkbox.checked);
            instructionsDiv.style.display = anyChecked ? 'block' : 'none';
        }

        // Add event listeners to all checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleInstructions);
        });

        // Initial check on page load
        toggleInstructions();
    });
</script>
@endsection
