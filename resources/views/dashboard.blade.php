@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-dark">Dashboard</h1>
            <p class="lead text-muted">Welcome to Task Management System</p>
        </div>
        <div class="col-md-4 text-end">
            @if(auth()->user()->isCustomer())
                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Create New Task
                </a>
            @endif
        </div>
    </div>

    <!-- Check if customer has projects -->
    @if(auth()->user()->isCustomer())
        @php
            $projects = auth()->user()->projects;
        @endphp

        @if($projects->isEmpty())
            <!-- No Projects Alert -->
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>No Projects Found!</strong> 
                <p class="mb-0 mt-2">You need to have projects assigned to you before you can create tasks. Please contact your administrator to assign a project to you.</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-folder-x" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-4">No Projects Yet</h4>
                    <p class="text-muted">Once an administrator assigns you a project, you'll be able to start creating and managing tasks.</p>
                </div>
            </div>
        @else
            <!-- Projects and Tasks Summary -->
            <div class="row mb-4">
                <!-- Projects Summary -->
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">My Projects</h6>
                                    <h3 class="mb-0">{{ auth()->user()->projects()->has('tasks')->count() }}</h3>
                                </div>
                                <i class="bi bi-folder" style="font-size: 2rem; color: #0d6efd;"></i>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All Projects <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Summary -->
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Developer Tasks</h6>
                                    <h3 class="mb-0">{{ auth()->user()->tasksCreated()->count() }}</h3>
                                </div>
                                <i class="bi bi-list-check" style="font-size: 2rem; color: #198754;"></i>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All Tasks <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @elseif(auth()->user()->isDeveloper())
        <!-- Developer view - Show Assigned Tasks -->
        @php
            $assignedTasks = auth()->user()->tasksAssigned()->latest()->get();
            $roleName = auth()->user()->getRoleLabel();
        @endphp

        <!-- Welcome Message -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle"></i>
            <strong>Welcome, {{ auth()->user()->name }}!</strong>
            <p class="mb-0 mt-2">You are logged in as <strong>{{ $roleName }}</strong></p>
        </div>

        <!-- Assigned Tasks Summary -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Tasks</h6>
                                <h3 class="mb-0">{{ $assignedTasks->count() }}</h3>
                            </div>
                            <i class="bi bi-list-check" style="font-size: 2rem; color: #0d6efd;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Pending</h6>
                                <h3 class="mb-0">{{ $assignedTasks->where('status', 'pending')->count() }}</h3>
                            </div>
                            <i class="bi bi-clock" style="font-size: 2rem; color: #6c757d;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">In Progress</h6>
                                <h3 class="mb-0">{{ $assignedTasks->where('status', 'in_progress')->count() }}</h3>
                            </div>
                            <i class="bi bi-arrow-repeat" style="font-size: 2rem; color: #ffc107;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">In Review</h6>
                                <h3 class="mb-0">{{ $assignedTasks->where('status', 'in_review')->count() }}</h3>
                            </div>
                            <i class="bi bi-search" style="font-size: 2rem; color: #0dcaf0;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Second row for completed -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Completed</h6>
                                <h3 class="mb-0">{{ $assignedTasks->where('status', 'done')->count() }}</h3>
                            </div>
                            <i class="bi bi-check-circle" style="font-size: 2rem; color: #198754;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Full Task Details -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('tasks.index') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-arrow-right"></i> View All Assigned Tasks
                </a>
            </div>
        </div>
    @else
            <!-- Generic Non-developer view -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <h4>Welcome {{ auth()->user()->name }}!</h4>
                    <p class="text-muted">You are logged in as: <strong>{{ auth()->user()->getRoleLabel() }}</strong></p>
                </div>
            </div>
    @endif
</div>
@endsection
