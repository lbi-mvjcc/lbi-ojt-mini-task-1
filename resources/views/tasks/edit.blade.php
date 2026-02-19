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
    
    .info-card {
        background: var(--purple-light-bg);
        border: 1px solid var(--purple-border);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 0.75rem;
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
</style>

<div class="container py-4">
    <div class="create-task-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-pencil-square me-3" style="font-size: 2.5rem;"></i>
                <div>
                    <h1 class="mb-0 fw-bold">Edit Task</h1>
                    <p class="mb-0 opacity-75">Update task details and assignments</p>
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

        <form action="{{ route('tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Form Card -->
            <div class="form-card">
                <!-- Developer Assignment Info -->
                <div class="form-section">
                    <div class="info-card">
                        <i class="bi bi-people-fill"></i>
                        <strong>Developer Assignment:</strong> This task is currently assigned to <strong>{{ $developerCount }} developer{{ $developerCount > 1 ? 's' : '' }}</strong>. Changes will be applied to all assigned developers.
                    </div>
                </div>

                <!-- Task Details Section -->
                <div class="form-section">
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
                           value="{{ old('title', $task->title) }}" 
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
                                  placeholder="Provide detailed information about the task...">{{ old('description', $task->description) }}</textarea>
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
                                <option value="frontend" @selected(old('category', $task->category) == 'frontend')>Frontend Development</option>
                                <option value="backend" @selected(old('category', $task->category) == 'backend')>Backend Development</option>
                                <option value="server" @selected(old('category', $task->category) == 'server')>Server Administration</option>
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
                                   value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d') : '') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            <small class="form-text text-muted mt-1 d-block">
                                <i class="bi bi-info-circle"></i> Optional: Must be after today
                            </small>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="info-card" style="background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.3);">
                        <i class="bi bi-exclamation-triangle" style="color: #f59e0b;"></i>
                        <strong>Category Change:</strong> Changing the category will delete current assignments and create new tasks for ALL developers in the new category.
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-section">
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('tasks.index') }}" class="btn btn-light-purple">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-purple">
                            <i class="bi bi-check-circle"></i> Update Task
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
