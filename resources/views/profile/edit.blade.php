@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-purple: #667eea;
        --primary-purple-dark: #5568d3;
        --secondary-purple: #764ba2;
        --purple-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --purple-light-bg: #f5f3ff;
        --purple-border: #e0d9ff;
    }
    
    [data-theme="dark"] {
        --primary-purple: #8b9cf5;
        --primary-purple-dark: #667eea;
        --secondary-purple: #9d6ec9;
        --purple-gradient: linear-gradient(135deg, #8b9cf5 0%, #9d6ec9 100%);
        --purple-light-bg: #2d2d44;
        --purple-border: #3d3d5c;
    }
    
    .profile-header {
        background: var(--purple-gradient);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.2);
    }
    
    .profile-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .profile-card h3 {
        color: var(--primary-purple);
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .profile-card p {
        color: var(--muted-text);
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-control {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        color: var(--text-color);
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary-purple);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background-color: var(--card-bg);
        color: var(--text-color);
    }
    
    .btn-purple {
        background: var(--purple-gradient);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-purple:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .btn-danger {
        background-color: #dc2626;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
        background-color: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    
    .btn-secondary {
        background-color: var(--card-bg);
        color: var(--text-color);
        border: 1px solid var(--border-color);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background-color: var(--border-color);
        color: var(--text-color);
    }
    
    .text-danger {
        color: #dc2626 !important;
    }
    
    .text-success {
        color: #10b981 !important;
    }
    
    [data-theme="dark"] .form-control {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }
    
    [data-theme="dark"] .form-control:focus {
        background-color: var(--card-bg);
        color: var(--text-color);
    }
    
    .profile-picture-preview {
        transition: all 0.3s ease;
    }
    
    .profile-picture-preview:hover {
        transform: scale(1.05);
    }
    
    #profile_picture {
        cursor: pointer;
    }
    
    #profile_picture:hover {
        border-color: var(--primary-purple);
    }
    
    /* Dark mode fixes */
    [data-theme="dark"] .btn-light {
        background-color: var(--card-bg);
        color: var(--text-color);
        border: 1px solid var(--border-color);
    }
    
    [data-theme="dark"] .btn-light:hover {
        background-color: var(--border-color);
        color: var(--text-color);
    }
    
    [data-theme="dark"] .text-muted {
        color: var(--muted-text) !important;
        background-color: transparent !important;
    }
    
    [data-theme="dark"] small.text-muted {
        background-color: transparent !important;
        color: var(--muted-text) !important;
        padding: 0 !important;
    }
    
    [data-theme="dark"] small {
        background-color: transparent !important;
        color: var(--muted-text) !important;
    }
    
    [data-theme="dark"] .profile-card {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .profile-card h3 {
        color: var(--primary-purple);
    }
    
    [data-theme="dark"] .profile-card p {
        color: var(--muted-text);
    }
    
    /* Fix for accepted formats text */
    [data-theme="dark"] #profile_picture + div {
        color: var(--muted-text) !important;
    }
    
    /* Dark mode scrollbar */
    [data-theme="dark"] ::-webkit-scrollbar {
        width: 12px;
        height: 12px;
    }
    
    [data-theme="dark"] ::-webkit-scrollbar-track {
        background: var(--card-bg);
    }
    
    [data-theme="dark"] ::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 6px;
    }
    
    [data-theme="dark"] ::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
</style>

<div class="container py-4">
    <!-- Page Header -->
    <div class="profile-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="mb-2"><i class="bi bi-person-circle"></i> Profile Settings</h1>
                <p class="mb-0 opacity-90">Manage your account information and preferences</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-light">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Update Profile Information -->
    <div class="profile-card">
        <h3><i class="bi bi-person"></i> Profile Information</h3>
        <p>Update your account's profile information and email address.</p>
        
        @include('profile.partials.update-profile-information-form')
    </div>

    <!-- Update Password -->
    <div class="profile-card">
        <h3><i class="bi bi-shield-lock"></i> Update Password</h3>
        <p>Ensure your account is using a long, random password to stay secure.</p>
        
        @include('profile.partials.update-password-form')
    </div>

    <!-- Delete Account -->
    <div class="profile-card">
        <h3><i class="bi bi-exclamation-triangle text-danger"></i> Delete Account</h3>
        <p>Once your account is deleted, all of its resources and data will be permanently deleted.</p>
        
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
