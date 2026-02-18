@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Task</h4>
                </div>
                <div class="card-body p-5">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Developer Assignment Info -->
                    <div class="alert alert-info">
                        <i class="bi bi-people-fill"></i> <strong>Developer Assignment:</strong> 
                        This task is currently assigned to <strong>{{ $developerCount }} developer{{ $developerCount > 1 ? 's' : '' }}</strong>. 
                        Changes will be applied to all assigned developers.
                    </div>

                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   id="title" name="title" placeholder="Enter task title" 
                                   value="{{ old('title', $task->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Enter task description">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg @error('category') is-invalid @enderror" 
                                    id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="frontend" @selected(old('category', $task->category) == 'frontend')>Frontend Development</option>
                                <option value="backend" @selected(old('category', $task->category) == 'backend')>Backend Development</option>
                                <option value="server" @selected(old('category', $task->category) == 'server')>Server Administration</option>
                            </select>
                            <div class="alert alert-warning mt-2">
                                <i class="bi bi-exclamation-triangle"></i> <small><strong>Category Change:</strong> 
                                Changing the category will delete the current task assignments and create new tasks for ALL developers in the new category.</small>
                            </div>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deadline -->
                        <div class="mb-4">
                            <label for="deadline" class="form-label fw-bold">Deadline</label>
                            <input type="date" 
                                   class="form-control form-control-lg @error('deadline') is-invalid @enderror" 
                                   id="deadline" 
                                   name="deadline" 
                                   value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d') : '') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            <small class="form-text text-muted">Optional: Set a deadline for this task (must be after today)</small>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submission Requirements -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Submission Requirements <small class="text-muted">(Optional)</small></label>
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-text text-muted mb-3">
                                        <i class="bi bi-info-circle"></i> 
                                        Select what developers should submit when completing this task. All submissions are optional for developers.
                                    </p>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="requires_file_submission" 
                                                       id="requires_file_submission" value="1" {{ old('requires_file_submission', $task->requires_file_submission) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="requires_file_submission">
                                                    <i class="bi bi-file-earmark text-primary"></i> File Upload
                                                </label>
                                                <small class="form-text text-muted d-block">Documents, files, archives, etc.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="requires_image_submission" 
                                                       id="requires_image_submission" value="1" {{ old('requires_image_submission', $task->requires_image_submission) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="requires_image_submission">
                                                    <i class="bi bi-image text-success"></i> Image Upload
                                                </label>
                                                <small class="form-text text-muted d-block">Screenshots, designs, photos, etc.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="requires_link_submission" 
                                                       id="requires_link_submission" value="1" {{ old('requires_link_submission', $task->requires_link_submission) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="requires_link_submission">
                                                    <i class="bi bi-link-45deg text-info"></i> Link Submission
                                                </label>
                                                <small class="form-text text-muted d-block">URLs, demos, repositories, etc.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submission Instructions -->
                                    <div class="mt-3" id="submission-instructions" style="display: {{ (old('requires_file_submission', $task->requires_file_submission) || old('requires_image_submission', $task->requires_image_submission) || old('requires_link_submission', $task->requires_link_submission)) ? 'block' : 'none' }};">
                                        <label for="submission_instructions" class="form-label fw-bold">
                                            <i class="bi bi-card-text"></i> Submission Instructions
                                        </label>
                                        <textarea class="form-control @error('submission_instructions') is-invalid @enderror" 
                                                  id="submission_instructions" name="submission_instructions" rows="3" 
                                                  placeholder="Provide specific instructions for what developers should submit...">{{ old('submission_instructions', $task->submission_instructions) }}</textarea>
                                        @error('submission_instructions')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Optional: Provide detailed instructions about what should be submitted, file formats, naming conventions, etc.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-3 d-sm-flex justify-content-sm-end">
                            <a href="{{ route('tasks.index') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="bi bi-check-circle"></i> Update Task
                            </button>
                        </div>
                    </form>

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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
