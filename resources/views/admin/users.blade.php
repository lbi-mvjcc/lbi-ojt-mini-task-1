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
                                <h1 class="mb-2"><i class="bi bi-people me-2"></i>User Management</h1>
                                <p class="mb-0 opacity-75">Manage all system users</p>
                            </div>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-light">
                                <i class="bi bi-person-plus me-2"></i>Create User
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Users Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tasks Created</th>
                                <th>Tasks Assigned</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                {!! $user->getProfilePictureHtml(32) !!}
                                            </div>
                                            <strong>{{ $user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-danger">{{ $user->getRoleLabel() }}</span>
                                        @elseif($user->role === 'customer')
                                            <span class="badge bg-primary">{{ $user->getRoleLabel() }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $user->getRoleLabel() }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->tasks_created_count }}</td>
                                    <td>{{ $user->tasks_assigned_count }}</td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('This action cannot be undone. Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No users found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
