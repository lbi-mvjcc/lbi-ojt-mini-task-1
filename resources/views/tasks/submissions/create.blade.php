@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
                    <li class="breadcrumb-item active" aria-current="page">Upload Submission</li>
                </ol>
            </nav>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-cloud-upload"></i> Upload Submission
                    </h4>
                    <small class="opacity-75">{{ $task->title }}</small>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Task Submission Requirements -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-muted mb-3">
                            <i class="bi bi-clipboard-check"></i> Submission Requirements
                        </h6>
                        <div class="row">
                            @if($task->requires_file_submission)
                                <div class="col-md-4 mb-2">
                                    <span class="badge bg-primary fs-6">
                                        <i class="bi bi-file-earmark"></i> Files Accepted
                                    </span>
                                </div>
                            @endif
                            @if($task->requires_image_submission)
                                <div class="col-md-4 mb-2">
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-image"></i> Images Accepted
                                    </span>
                                </div>
                            @endif
                            @if($task->requires_link_submission)
                                <div class="col-md-4 mb-2">
                                    <span class="badge bg-info fs-6">
                                        <i class="bi bi-link-45deg"></i> Links Accepted
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if($task->submission_instructions)
                            <div class="mt-3">
                                <h6 class="fw-bold text-muted">Instructions:</h6>
                                <div class="bg-light p-3 rounded">
                                    {{ $task->submission_instructions }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <form id="submissionForm" action="{{ route('tasks.submissions.store', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Success/Error Messages -->
                        <div id="submission-messages" style="display: none;"></div>

                        <!-- Submission Type -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Submission Type <span class="text-danger">*</span></label>
                            <div class="row">
                                @if($task->requires_file_submission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="type_file" 
                                                   value="file" {{ old('type') === 'file' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="type_file">
                                                <i class="bi bi-file-earmark text-primary"></i> File Upload
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                @if($task->requires_image_submission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="type_image" 
                                                   value="image" {{ old('type') === 'image' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="type_image">
                                                <i class="bi bi-image text-success"></i> Image Upload
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                @if($task->requires_link_submission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="type_link" 
                                                   value="link" {{ old('type') === 'link' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="type_link">
                                                <i class="bi bi-link-45deg text-info"></i> Link Submission
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Submission Title <small class="text-muted">(Optional)</small></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" placeholder="Enter a descriptive title for your submission" 
                                   value="{{ old('title') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description <small class="text-muted">(Optional)</small></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Describe what you're submitting...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-4" id="file-section" style="display: none;">
                            <label for="file" class="form-label fw-bold">
                                <span id="file-label">Choose File</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file" accept="*/*">
                            
                            <!-- File name display -->
                            <div id="file-name-display" class="mt-2" style="display: none;">
                                <small class="text-muted">
                                    <i class="bi bi-file-earmark"></i> 
                                    Selected: <span id="selected-file-name" class="fw-bold"></span>
                                    <span id="selected-file-size" class="text-muted"></span>
                                </small>
                            </div>
                            
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="file-help">Maximum file size: 100MB for files, 50MB for images</span>
                            </div>
                        </div>

                        <!-- Link URL Section -->
                        <div class="mb-4" id="link-section" style="display: none;">
                            <label for="link_url" class="form-label fw-bold">Link URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('link_url') is-invalid @enderror" 
                                   id="link_url" name="link_url" placeholder="https://example.com" 
                                   value="{{ old('link_url') }}">
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the full URL including http:// or https://</div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-3 d-md-flex justify-content-md-end">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-light">
                                <i class="bi bi-arrow-left"></i> Back to Task
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-cloud-upload"></i> 
                                <span class="submit-text">Upload Submission</span>
                                <span class="loading-text d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Uploading...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const fileSection = document.getElementById('file-section');
    const linkSection = document.getElementById('link-section');
    const fileLabel = document.getElementById('file-label');
    const fileHelp = document.getElementById('file-help');
    const submissionForm = document.getElementById('submissionForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = submitBtn.querySelector('.submit-text');
    const loadingText = submitBtn.querySelector('.loading-text');
    const messagesDiv = document.getElementById('submission-messages');
    const fileInput = document.getElementById('file');
    const fileNameDisplay = document.getElementById('file-name-display');
    const selectedFileName = document.getElementById('selected-file-name');
    const selectedFileSize = document.getElementById('selected-file-size');

    // Handle file selection and display filename
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            selectedFileName.textContent = file.name;
            
            // Format file size
            const fileSize = formatFileSize(file.size);
            selectedFileSize.textContent = ` (${fileSize})`;
            
            fileNameDisplay.style.display = 'block';
        } else {
            fileNameDisplay.style.display = 'none';
        }
    });

    // Helper function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function updateSubmissionForm() {
        const selectedType = document.querySelector('input[name="type"]:checked');
        
        // Hide all sections first
        fileSection.style.display = 'none';
        linkSection.style.display = 'none';

        if (selectedType) {
            switch (selectedType.value) {
                case 'file':
                    fileSection.style.display = 'block';
                    fileLabel.textContent = 'Choose File';
                    fileHelp.textContent = 'Maximum file size: 100MB. All file types supported (except executables for security)';
                    document.getElementById('file').accept = '*/*';
                    break;
                case 'image':
                    fileSection.style.display = 'block';
                    fileLabel.textContent = 'Choose Image';
                    fileHelp.textContent = 'Maximum file size: 50MB. Supports: JPEG, PNG, GIF, WebP, HEIC, TIFF, BMP, SVG, and more';
                    document.getElementById('file').accept = 'image/*';
                    break;
                case 'link':
                    linkSection.style.display = 'block';
                    break;
            }
        }
    }

    // Add event listeners to radio buttons
    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateSubmissionForm);
    });

    // Initial setup
    updateSubmissionForm();

    // Handle form submission with AJAX
    submissionForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        submitBtn.disabled = true;
        submitText.classList.add('d-none');
        loadingText.classList.remove('d-none');
        messagesDiv.style.display = 'none';

        // Create FormData object to handle file uploads
        const formData = new FormData(submissionForm);

        fetch(submissionForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]').value
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // Handle redirect or non-JSON response
                if (response.redirected || response.status === 302 || response.ok) {
                    // Show quick success message and redirect
                    messagesDiv.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> Submission uploaded successfully! Redirecting...
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    messagesDiv.style.display = 'block';
                    messagesDiv.scrollIntoView({ behavior: 'smooth' });
                    
                    setTimeout(() => {
                        window.location.href = '{{ route("tasks.show", $task) }}';
                    }, 1000);
                    return { success: true, redirect: true };
                } else {
                    throw new Error('Upload failed with status: ' + response.status);
                }
            }
        })
        .then(data => {
            if (data && data.success) {
                // Redirect immediately, backend will set flash message
                window.location.href = '{{ route("tasks.show", $task) }}';
                return;
            }
            
            if (data && data.redirect) {
                return; // Already handled redirect in previous step
            }
            
            if (data && data.errors) {
                // Show validation errors with enhanced formatting
                let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                errorHtml += '<i class="bi bi-exclamation-triangle"></i> <strong>Please correct the following errors:</strong><ul class="mb-0 mt-2">';
                
                Object.entries(data.errors).forEach(([field, errors]) => {
                    errors.forEach(error => {
                        errorHtml += `<li><strong>${field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ')}:</strong> ${error}</li>`;
                    });
                });
                
                errorHtml += '</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                messagesDiv.innerHTML = errorHtml;
                messagesDiv.style.display = 'block';
                messagesDiv.scrollIntoView({ behavior: 'smooth' });
            } else {
                // Show generic error for unexpected responses
                messagesDiv.innerHTML = `
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> Upload completed but no confirmation received. Please check if your submission was processed.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                messagesDiv.style.display = 'block';
                messagesDiv.scrollIntoView({ behavior: 'smooth' });
            }
        })
        .catch(error => {
            console.error('Submission error:', error);
            
            // Show detailed error message with better formatting
            let errorMessage = 'An error occurred while uploading your submission.';
            
            if (error.message.includes('size') || error.message.includes('large')) {
                errorMessage = 'The file is too large. Please choose a smaller file and try again.';
            } else if (error.message.includes('type') || error.message.includes('format')) {
                errorMessage = 'The file type is not supported. Please choose a different file format.';
            } else if (error.message.includes('network') || error.message.includes('fetch')) {
                errorMessage = 'Network error. Please check your connection and try again.';
            }
            
            messagesDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <strong>Upload Failed</strong><br>
                    ${errorMessage}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            messagesDiv.style.display = 'block';
            messagesDiv.scrollIntoView({ behavior: 'smooth' });
        })
        .finally(() => {
            // Hide loading state
            submitBtn.disabled = false;
            submitText.classList.remove('d-none');
            loadingText.classList.add('d-none');
        });
    });
});
</script>

@endsection