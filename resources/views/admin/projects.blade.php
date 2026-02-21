@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white py-4">
                        <h1 class="mb-2"><i class="bi bi-folder me-2"></i>Project Management</h1>
                        <p class="mb-0 opacity-75">Oversee all projects in the system</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Projects Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.1) 0%, rgba(196, 181, 253, 0.1) 100%); border-bottom: 2px solid rgba(167, 139, 250, 0.3);">
                            <tr>
                                <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Project Name</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Description</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Customer</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Tasks</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Created</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--text-color); border: none;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr>
                                    <td><strong>{{ $project->name }}</strong></td>
                                    <td>{{ Str::limit($project->description, 50) }}</td>
                                    <td>{{ $project->customer->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $project->tasks_count }} tasks</span>
                                    </td>
                                    <td>{{ $project->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('admin.projects.delete', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('This action cannot be undone. Are you sure you want to delete this project? All associated tasks will also be deleted.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No projects found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
