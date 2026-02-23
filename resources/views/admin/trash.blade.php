@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="mb-2"><i class="bi bi-trash me-2"></i>Trash / Deleted Items</h1>
                            <p class="mb-0 opacity-75">Restore or permanently delete items</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
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

    <!-- Deleted Users Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.1) 0%, rgba(196, 181, 253, 0.1) 100%); border-bottom: 2px solid rgba(167, 139, 250, 0.3);">
            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Deleted Users ({{ $deletedUsers->count() }})</h5>
        </div>
        <div class="card-body">
            @if($deletedUsers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.1) 0%, rgba(196, 181, 253, 0.1) 100%); border-bottom: 2px solid rgba(167, 139, 250, 0.2);">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Deleted At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deletedUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->getRoleLabel() }}</span>
                                    </td>
                                    <td>{{ $user->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('This will PERMANENTLY delete this user. This action cannot be undone!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash-fill"></i> Delete Forever
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                    <p class="text-muted mt-2">No deleted users</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Deleted Projects Section -->
    <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.1) 0%, rgba(196, 181, 253, 0.1) 100%); border-bottom: 2px solid rgba(167, 139, 250, 0.3);">
            <h5 class="mb-0"><i class="bi bi-folder me-2"></i>Deleted Projects ({{ $deletedProjects->count() }})</h5>
        </div>
        <div class="card-body">
            @if($deletedProjects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.1) 0%, rgba(196, 181, 253, 0.1) 100%); border-bottom: 2px solid rgba(167, 139, 250, 0.2);">
                            <tr>
                                <th>Project Name</th>
                                <th>Customer</th>
                                <th>Description</th>
                                <th>Deleted At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deletedProjects as $project)
                                <tr>
                                    <td><strong>{{ $project->name }}</strong></td>
                                    <td>{{ $project->customer->name ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($project->description, 50) }}</td>
                                    <td>{{ $project->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.projects.restore', $project->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.projects.force-delete', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('This will PERMANENTLY delete this project. This action cannot be undone!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash-fill"></i> Delete Forever
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                    <p class="text-muted mt-2">No deleted projects</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
