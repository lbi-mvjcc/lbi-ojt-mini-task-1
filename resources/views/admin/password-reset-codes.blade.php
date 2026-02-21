@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold">Password Reset Codes</h1>
            <p class="text-muted">Generate and manage password reset codes for users</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Generate Code Form -->
    <div class="card mb-4" style="border-radius: 12px;">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0;">
            <h5 class="mb-0"><i class="bi bi-key me-2"></i>Generate Reset Code</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.generate-reset-code') }}" method="POST">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label for="user_id" class="form-label">Select User</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }}) - {{ $user->getRoleLabel() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>Generate Code
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Codes List -->
    <div class="card" style="border-radius: 12px;">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Recent Reset Codes</h5>
        </div>
        <div class="card-body p-0">
            @if($codes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Code</th>
                                <th>Generated</th>
                                <th>Expires</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($codes as $code)
                                <tr>
                                    <td>
                                        <strong>{{ $code->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $code->user->getRoleLabel() }}</small>
                                    </td>
                                    <td>{{ $code->user->email }}</td>
                                    <td>
                                        <code class="fs-5 fw-bold" style="color: #667eea;">{{ $code->code }}</code>
                                    </td>
                                    <td>{{ $code->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $code->expires_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($code->used)
                                            <span class="badge bg-secondary">Used</span>
                                        @elseif($code->isExpired())
                                            <span class="badge bg-danger">Expired</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3">
                    {{ $codes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-key" style="font-size: 3rem; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No reset codes generated yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    [data-theme="dark"] .card {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .table {
        color: var(--text-color);
    }
    
    [data-theme="dark"] .table-light {
        background-color: var(--card-bg);
        color: var(--text-color);
    }
    
    [data-theme="dark"] .form-select,
    [data-theme="dark"] .form-control {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }
</style>
@endsection
