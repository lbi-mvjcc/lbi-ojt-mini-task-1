<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>TaskFlow - Task Management System</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('css/styles.css') }}?v={{ time() }}" rel="stylesheet" />
        <style>
            :root {
                --primary-purple: #667eea;
                --primary-purple-dark: #5568d3;
                --secondary-purple: #764ba2;
                --bg-color: #ffffff;
                --text-color: #1f2937;
                --card-bg: #ffffff;
                --border-color: #e5e7eb;
                --muted-text: #6b7280;
            }
            
            [data-theme="dark"] {
                --primary-purple: #8b9cf5;
                --primary-purple-dark: #7c8de8;
                --secondary-purple: #9d6ec9;
                --bg-color: #0f172a;
                --text-color: #f1f5f9;
                --card-bg: #1e293b;
                --border-color: #334155;
                --muted-text: #94a3b8;
            }
            
            * {
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            }
            
            html, body {
                background-color: var(--bg-color) !important;
                color: var(--text-color) !important;
                min-height: 100vh;
            }
            
            body.d-flex {
                background-color: var(--bg-color) !important;
            }
            
            main {
                background-color: var(--bg-color) !important;
                flex: 1;
            }
            
            .flex-grow-1 {
                background-color: var(--bg-color) !important;
            }
            
            /* Override Bootstrap defaults */
            .container, .container-fluid {
                background-color: transparent !important;
            }
            
            /* Ensure sections inherit background */
            section {
                background-color: transparent;
            }
            
            .navbar-purple {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            }
            
            /* Navbar - adapt to dark mode */
            [data-theme="dark"] nav.navbar-purple {
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
                border-bottom: 1px solid #334155;
            }
            
            /* Footer - purple in light mode, dark in dark mode */
            footer.navbar-purple {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            }
            
            [data-theme="dark"] footer.navbar-purple {
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
                border-top: 1px solid #334155 !important;
            }
            
            [data-theme="dark"] footer.navbar-purple p {
                color: #94a3b8 !important;
            }
            
            .theme-toggle {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                color: white;
            }
            
            .theme-toggle:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }
            
            .notification-dropdown {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                background-color: var(--card-bg);
            }
            
            .dropdown-menu {
                background-color: var(--card-bg);
                border-color: var(--border-color);
            }
            
            .dropdown-item {
                color: var(--text-color);
            }
            
            .dropdown-item:hover {
                background-color: var(--bg-color);
            }
            
            .dropdown-header {
                color: var(--text-color);
                background-color: var(--card-bg);
            }
            
            .btn-user {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                border-radius: 25px;
                padding: 0.5rem 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: white;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .btn-user:hover {
                background: rgba(255, 255, 255, 0.3);
                color: white;
            }
            
            .btn-user:focus {
                box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
                color: white;
            }
            
            .user-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: white;
                color: var(--primary-purple-dark);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                overflow: hidden;
            }
            
            .user-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .user-name {
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            .user-dropdown {
                min-width: 250px;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                border: 1px solid var(--border-color);
                background-color: var(--card-bg);
            }
            
            .user-dropdown .dropdown-item {
                padding: 0.75rem 1.25rem;
                color: var(--text-color);
                transition: all 0.2s ease;
            }
            
            .user-dropdown .dropdown-item:hover {
                background-color: rgba(102, 126, 234, 0.1);
                color: var(--primary-purple);
            }
            
            .user-dropdown .dropdown-item i {
                width: 20px;
                margin-right: 0.5rem;
            }
            
            .user-dropdown .dropdown-item.text-danger:hover {
                background-color: rgba(239, 68, 68, 0.1);
                color: #dc2626;
            }
            
            .user-info {
                padding: 0.5rem 0;
            }
            
            .user-info strong {
                font-size: 1rem;
                color: var(--text-color);
            }
            
            .user-info small {
                font-size: 0.8rem;
                color: var(--muted-text);
            }
            
            .alert {
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .alert-success {
                background-color: rgba(16, 185, 129, 0.1);
                border-color: rgba(16, 185, 129, 0.3);
                color: #34d399;
            }
            
            [data-theme="dark"] .alert-danger {
                background-color: rgba(239, 68, 68, 0.1);
                border-color: rgba(239, 68, 68, 0.3);
                color: #f87171;
            }
            
            [data-theme="dark"] .alert-info {
                background-color: rgba(59, 130, 246, 0.1);
                border-color: rgba(59, 130, 246, 0.3);
                color: #60a5fa;
            }
            
            [data-theme="dark"] .alert-warning {
                background-color: rgba(245, 158, 11, 0.1);
                border-color: rgba(245, 158, 11, 0.3);
                color: #fbbf24;
            }
            
            [data-theme="dark"] .card {
                background-color: var(--card-bg);
                border-color: var(--border-color);
                color: var(--text-color);
            }
            
            [data-theme="dark"] .text-muted {
                color: var(--muted-text) !important;
            }
            
            [data-theme="dark"] .text-dark {
                color: var(--text-color) !important;
            }
            
            [data-theme="dark"] .bg-light {
                background-color: var(--card-bg) !important;
            }
            
            /* Dark mode specific fixes */
            [data-theme="dark"] .user-avatar {
                background: rgba(139, 156, 245, 0.2);
                color: var(--primary-purple-light);
            }
            
            [data-theme="dark"] .btn-close {
                filter: invert(1) grayscale(100%) brightness(200%);
            }
            
            [data-theme="dark"] h1, 
            [data-theme="dark"] h2, 
            [data-theme="dark"] h3, 
            [data-theme="dark"] h4, 
            [data-theme="dark"] h5, 
            [data-theme="dark"] h6 {
                color: var(--text-color);
            }
            
            [data-theme="dark"] .page-title,
            [data-theme="dark"] .page-subtitle {
                color: var(--text-color);
            }
            
            [data-theme="dark"] .display-6 {
                color: var(--text-color);
            }
            
            [data-theme="dark"] .fw-bold,
            [data-theme="dark"] .fw-semibold {
                color: var(--text-color);
            }
            
            [data-theme="dark"] .list-group-item {
                background-color: var(--card-bg);
                border-color: var(--border-color);
                color: var(--text-color);
            }
            
            [data-theme="dark"] .border-bottom {
                border-color: var(--border-color) !important;
            }
            
            [data-theme="dark"] .dropdown-divider {
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .btn-outline-secondary {
                color: var(--muted-text);
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .btn-outline-secondary:hover {
                background-color: var(--border-color);
                color: var(--text-color);
            }
            
            /* Comprehensive dark mode fixes */
            [data-theme="dark"] .container,
            [data-theme="dark"] main {
                background-color: var(--bg-color) !important;
                color: var(--text-color);
            }
            
            [data-theme="dark"] .flex-grow-1 {
                background-color: var(--bg-color) !important;
            }
            
            [data-theme="dark"] .py-4,
            [data-theme="dark"] .py-5 {
                background-color: var(--bg-color) !important;
            }
            
            /* Force background on all major containers */
            [data-theme="dark"] section,
            [data-theme="dark"] .container-fluid {
                background-color: var(--bg-color) !important;
            }
            
            [data-theme="dark"] p,
            [data-theme="dark"] span,
            [data-theme="dark"] div {
                color: var(--text-color);
            }
            
            [data-theme="dark"] .small {
                color: var(--muted-text);
            }
            
            [data-theme="dark"] .badge {
                background-color: var(--card-bg);
                color: var(--text-color);
                border: 1px solid var(--border-color);
            }
            
            [data-theme="dark"] .shadow,
            [data-theme="dark"] .shadow-sm {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
            }
            
            [data-theme="dark"] input::placeholder,
            [data-theme="dark"] textarea::placeholder {
                color: var(--muted-text);
            }
            
            [data-theme="dark"] .modal-content {
                background-color: var(--card-bg);
                color: var(--text-color);
            }
            
            [data-theme="dark"] .modal-header,
            [data-theme="dark"] .modal-footer {
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] hr {
                border-color: var(--border-color);
                opacity: 1;
            }
            
            [data-theme="dark"] .table {
                color: var(--text-color);
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .table th,
            [data-theme="dark"] .table td {
                border-color: var(--border-color);
            }
            
            /* Additional dark mode improvements */
            [data-theme="dark"] .btn-outline-primary {
                color: var(--primary-purple-light);
                border-color: var(--primary-purple-light);
            }
            
            [data-theme="dark"] .btn-outline-primary:hover {
                background-color: var(--primary-purple-light);
                border-color: var(--primary-purple-light);
                color: #0f172a;
            }
            
            [data-theme="dark"] a {
                color: var(--primary-purple-light);
            }
            
            [data-theme="dark"] a:hover {
                color: var(--primary-purple);
            }
            
            /* Fix gradient text in dark mode */
            [data-theme="dark"] .text-purple {
                color: var(--primary-purple-light) !important;
            }
            
            /* Ensure all icons are visible */
            [data-theme="dark"] i {
                color: inherit;
            }
            .notification-item {
                padding: 12px 16px;
                border-left: 3px solid transparent;
                background-color: var(--card-bg);
                transition: all 0.2s ease;
            }
            .notification-item:hover {
                background-color: var(--bg-color);
            }
            .notification-item.unread {
                background-color: rgba(102, 126, 234, 0.1);
                border-left-color: var(--primary-purple);
            }
            .notification-item.unread:hover {
                background-color: rgba(102, 126, 234, 0.15);
            }
            
            /* Dark mode notification fixes */
            [data-theme="dark"] .notification-item {
                background-color: transparent;
            }
            
            [data-theme="dark"] .notification-item:hover {
                background-color: rgba(102, 126, 234, 0.05);
            }
            
            [data-theme="dark"] .notification-item.unread {
                background-color: rgba(102, 126, 234, 0.15);
                border-left-color: var(--primary-purple);
            }
            
            [data-theme="dark"] .notification-item.unread:hover {
                background-color: rgba(102, 126, 234, 0.2);
            }
            
            [data-theme="dark"] .notification-dropdown {
                background-color: var(--card-bg) !important;
                border: 1px solid var(--border-color);
            }
            
            [data-theme="dark"] .dropdown-menu {
                background-color: var(--card-bg) !important;
                border-color: var(--border-color) !important;
            }
            
            [data-theme="dark"] .dropdown-item {
                color: var(--text-color) !important;
            }
            
            [data-theme="dark"] .dropdown-item:hover {
                background-color: rgba(102, 126, 234, 0.1) !important;
            }
            
            [data-theme="dark"] .dropdown-header {
                color: var(--text-color) !important;
                background-color: transparent !important;
                border-bottom-color: var(--border-color) !important;
            }
            
            [data-theme="dark"] .dropdown-divider {
                border-color: var(--border-color) !important;
            }
            
            [data-theme="dark"] .btn-outline-secondary {
                color: var(--muted-text) !important;
                border-color: var(--border-color) !important;
            }
            
            [data-theme="dark"] .btn-outline-secondary:hover {
                background-color: var(--border-color) !important;
                color: var(--text-color) !important;
            }
            
            [data-theme="dark"] .btn-link {
                color: var(--primary-purple) !important;
            }
            
            [data-theme="dark"] .text-muted {
                color: var(--muted-text) !important;
            }
            
            /* Fix nested dropdown in notifications */
            .notification-item .dropdown {
                position: relative;
            }
            
            .notification-item .dropdown-menu {
                position: absolute;
                z-index: 1050;
                min-width: 150px;
            }
            
            .notification-item .btn-outline-secondary {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
            
            [data-theme="dark"] .notification-item .btn-outline-secondary {
                color: var(--muted-text);
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .notification-item .btn-outline-secondary:hover {
                background-color: rgba(102, 126, 234, 0.1);
                color: var(--text-color);
            }
            
            .notification-title {
                font-weight: 600;
                font-size: 0.9rem;
                margin-bottom: 4px;
                color: var(--text-color);
            }
            .notification-message {
                font-size: 0.8rem;
                color: var(--muted-text);
                margin-bottom: 4px;
                line-height: 1.3;
            }
            .notification-time {
                font-size: 0.7rem;
                color: var(--muted-text);
            }
            .notification-actions {
                display: flex;
                gap: 8px;
                margin-top: 8px;
            }
            .notification-actions .btn {
                font-size: 0.7rem;
                padding: 2px 8px;
            }
            #notification-badge {
                font-size: 0.65rem !important;
                padding: 2px 5px !important;
            }

            /* Responsive navigation improvements */
            @media (max-width: 768px) {
                .notification-dropdown {
                    width: 300px !important;
                    left: -250px !important;
                }
            }
            
            /* CRITICAL: Override Bootstrap's CSS variables for dark mode */
            [data-theme="dark"] body {
                --bs-body-bg: #0f172a !important;
                --bs-body-color: #f1f5f9 !important;
            }
        </style>
    </head>
    <body class="d-flex flex-column min-vh-100" style="background-color: var(--bg-color) !important;">
        <script>
            // Apply theme immediately to prevent flash
            (function() {
                const theme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', theme);
                document.body.style.backgroundColor = theme === 'dark' ? '#0f172a' : '#ffffff';
            })();
        </script>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark navbar-purple">
            <div class="container px-5">
                <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                    <i class="bi bi-check2-square"></i> TaskFlow
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('tasks.index') }}">Task</a></li>
                        @auth
                            @if(auth()->user()->isCustomer())
                                <li class="nav-item"><a class="nav-link" href="{{ route('projects.index') }}">Project</a></li>
                            @endif
                        @endauth
                    </ul>
                    
                    @auth
                    <!-- Search Bar -->
                    <form class="d-flex me-3" method="GET" action="{{ route('search') }}" role="search">
                        <input type="search" name="search" placeholder="Search..." 
                               aria-label="Search" value="{{ request('search') }}" 
                               style="
                                   background: transparent; 
                                   border: none; 
                                   border-bottom: 1px solid rgba(255,255,255,0.5); 
                                   color: white; 
                                   width: 150px; 
                                   padding: 8px 0; 
                                   margin-right: 10px;
                                   outline: none;
                               "
                               onfocus="this.style.borderBottomColor='rgba(255,255,255,0.8)'"
                               onblur="this.style.borderBottomColor='rgba(255,255,255,0.5)'">
                        <button class="btn btn-outline-light" type="submit" style="border: none; background: transparent; color: rgba(255,255,255,0.8);">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    <!-- Theme Toggle -->
                    <button class="theme-toggle me-3" id="themeToggle" title="Toggle theme">
                        <i class="bi bi-moon-stars" id="themeIcon"></i>
                    </button>

                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: transparent;">
                            <i class="bi bi-bell" style="font-size: 1.2rem;"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge" style="display: none; font-size: 0.7rem;">
                                0
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Notifications</span>
                                <button class="btn btn-sm btn-link text-muted p-0" id="mark-all-read" title="Mark all as read">
                                    <i class="bi bi-check-all"></i>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <!-- Success/Error Messages for Notifications -->
                            <div id="notification-messages" style="display: none;"></div>
                            <div id="notification-list">
                                <li class="dropdown-item text-center text-muted py-3">
                                    <i class="bi bi-bell-slash"></i><br>
                                    No notifications
                                </li>
                            </div>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                                    <i class="bi bi-list"></i> View All Notifications
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endauth
                    
                    @auth
                    <!-- User Profile & Logout -->
                    <div class="dropdown">
                        <button class="btn btn-user dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}">
                                @else
                                    <i class="bi bi-person-circle"></i>
                                @endif
                            </div>
                            <span class="user-name">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                            <li class="dropdown-header">
                                <div class="user-info">
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <small class="text-muted d-block">{{ auth()->user()->getRoleLabel() }}</small>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endauth
            </div>
        </nav>
        <!-- Flash Messages -->
        <div class="container px-5 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
        <!-- Main content-->
        <main class="flex-grow-1">
            @yield('content')
        </main>
        <!-- Footer-->
        <footer class="py-4 navbar-purple mt-auto">
            <div class="container px-5"><p class="m-0 text-center text-white">Copyright &copy; TaskFlow 2026</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('js/scripts.js') }}"></script>
        
        <!-- Notification JavaScript -->
        <script>
            // Theme Toggle Functionality
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const htmlElement = document.documentElement;
            
            // Check for saved theme preference or default to 'light'
            const currentTheme = localStorage.getItem('theme') || 'light';
            htmlElement.setAttribute('data-theme', currentTheme);
            updateThemeIcon(currentTheme);
            
            themeToggle.addEventListener('click', function() {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
                
                // Immediately update body background
                document.body.style.backgroundColor = newTheme === 'dark' ? '#0f172a' : '#ffffff';
            });
            
            function updateThemeIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill';
                } else {
                    themeIcon.className = 'bi bi-moon-stars';
                }
            }
            
            // Notification functionality
            let notificationUpdateInterval;
            
            document.addEventListener('DOMContentLoaded', function() {
                // Load notifications on page load
                loadNotifications();
                
                // Set up auto-refresh every 30 seconds
                notificationUpdateInterval = setInterval(loadNotifications, 30000);
                
                // Mark all as read functionality
                document.getElementById('mark-all-read').addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    markAllAsRead();
                });
                
                // Stop the interval when the page is being unloaded
                window.addEventListener('beforeunload', function() {
                    if (notificationUpdateInterval) {
                        clearInterval(notificationUpdateInterval);
                    }
                });
            });
            
            function loadNotifications() {
                fetch('{{ route("notifications.recent") }}')
                    .then(response => response.json())
                    .then(data => {
                        updateNotificationBadge(data.unread_count);
                        updateNotificationList(data.notifications);
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                    });
            }
            
            function updateNotificationBadge(count) {
                const badge = document.getElementById('notification-badge');
                
                if (typeof count === 'undefined') {
                    // If no count provided, calculate from current unread notifications
                    const unreadNotifications = document.querySelectorAll('.notification-item.unread');
                    count = unreadNotifications.length;
                }
                
                if (count > 0) {
                    badge.textContent = count > 10 ? '10+' : count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
            
            function updateNotificationList(notifications) {
                const list = document.getElementById('notification-list');
                
                if (notifications.length === 0) {
                    list.innerHTML = `
                        <li class="dropdown-item text-center text-muted py-3">
                            <i class="bi bi-bell-slash"></i><br>
                            No notifications
                        </li>
                    `;
                    return;
                }
                
                let html = '';
                notifications.forEach(notification => {
                    const unreadClass = notification.is_read ? '' : 'unread';
                    const readIcon = notification.is_read ? 'bi-envelope-open' : 'bi-envelope';
                    
                    html += `
                        <li class="notification-item ${unreadClass}" data-notification-id="${notification.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="notification-title">${notification.title}</div>
                                    <div class="notification-message">${notification.message}</div>
                                    <div class="notification-time">
                                        <i class="bi ${readIcon}"></i> ${notification.time_ago}
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        ${!notification.is_read ? `
                                            <li><button class="dropdown-item" onclick="markAsReadFromElement(this); event.stopPropagation(); return false;">
                                                <i class="bi bi-check"></i> Mark as read
                                            </button></li>
                                        ` : ''}
                                        <li><button class="dropdown-item" onclick="viewNotificationFromElement(this); event.stopPropagation(); return false;">
                                            <i class="bi bi-eye"></i> View
                                        </button></li>
                                        <li><button class="dropdown-item text-danger" onclick="deleteNotificationFromElement(this); event.stopPropagation(); return false;">
                                            <i class="bi bi-trash"></i> Delete
                                        </button></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    `;
                });
                
                list.innerHTML = html;
                
                // Reinitialize Bootstrap dropdowns for the notification items
                setTimeout(() => {
                    const dropdownElements = list.querySelectorAll('[data-bs-toggle="dropdown"]');
                    dropdownElements.forEach(element => {
                        new bootstrap.Dropdown(element);
                    });
                }, 100);
            }
            
            function showNotificationMessage(message, type = 'success') {
                const messagesDiv = document.getElementById('notification-messages');
                
                // Check if the element exists, if not create it
                if (!messagesDiv) {
                    console.warn('notification-messages element not found, creating it...');
                    const notificationList = document.getElementById('notification-list');
                    if (notificationList) {
                        const messagesContainer = document.createElement('div');
                        messagesContainer.id = 'notification-messages';
                        messagesContainer.style.display = 'none';
                        notificationList.parentNode.insertBefore(messagesContainer, notificationList);
                    } else {
                        console.error('Cannot create notification-messages: notification-list not found');
                        return;
                    }
                }
                
                const targetDiv = document.getElementById('notification-messages');
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
                
                targetDiv.innerHTML = `
                    <li class="px-3 py-2">
                        <div class="alert ${alertClass} alert-dismissible fade show mb-0 py-2" role="alert">
                            <i class="bi ${icon}"></i> ${message}
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" onclick="this.closest('li').style.display='none'"></button>
                        </div>
                    </li>
                `;
                targetDiv.style.display = 'block';
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    if (targetDiv) {
                        targetDiv.style.display = 'none';
                    }
                }, 5000);
            }
            
            function markAsRead(notificationId) {
                console.log('Attempting to mark as read notification:', notificationId);
                
                // Validate notification ID
                if (!notificationId || notificationId === 'undefined' || notificationId === 'null') {
                    console.error('Invalid notification ID:', notificationId);
                    alert('Error: Invalid notification ID');
                    return;
                }
                
                const baseUrl = '{{ url("/notifications") }}';
                const markReadUrl = `${baseUrl}/${notificationId}/mark-read`;
                console.log('Mark as read URL:', markReadUrl);
                
                fetch(markReadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('MarkAsRead response status:', response.status);
                    if (response.ok) {
                        // Refresh the page to show success message and updated notifications
                        location.reload();
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    alert('Error marking notification as read');
                });
            }
            
            // Alternative function that gets ID from DOM element
            function markAsReadFromElement(element) {
                const notificationElement = element.closest('[data-notification-id]');
                if (notificationElement) {
                    const notificationId = notificationElement.getAttribute('data-notification-id');
                    console.log('Found notification ID from DOM for mark as read:', notificationId);
                    markAsRead(notificationId);
                } else {
                    console.error('Could not find notification element');
                    alert('Error: Could not identify notification to mark as read');
                }
            }
            
            function viewNotification(notificationId) {
                if (!notificationId || notificationId === 'undefined' || notificationId === 'null') {
                    console.error('Invalid notification ID:', notificationId);
                    alert('Error: Invalid notification ID');
                    return;
                }
                window.location.href = `{{ url('/notifications') }}/${notificationId}`;
            }
            
            // Alternative function that gets ID from DOM element
            function viewNotificationFromElement(element) {
                const notificationElement = element.closest('[data-notification-id]');
                if (notificationElement) {
                    const notificationId = notificationElement.getAttribute('data-notification-id');
                    console.log('Found notification ID from DOM for view:', notificationId);
                    viewNotification(notificationId);
                } else {
                    console.error('Could not find notification element');
                    alert('Error: Could not identify notification to view');
                }
            }
            
            function markAllAsRead() {
                if (confirm('Mark all notifications as read?')) {
                    fetch('{{ route("notifications.markAllAsRead") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('MarkAllAsRead response status:', response.status);
                        if (response.ok) {
                            // Refresh the page to show success message and updated notifications
                            location.reload();
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error marking all notifications as read:', error);
                        alert('Error marking notifications as read');
                    });
                }
            }
            
            function deleteNotification(notificationId) {
                console.log('Attempting to delete notification:', notificationId);
                
                // Validate notification ID
                if (!notificationId || notificationId === 'undefined' || notificationId === 'null') {
                    console.error('Invalid notification ID:', notificationId);
                    alert('Error: Invalid notification ID');
                    return;
                }
                
                if (confirm('Are you sure you want to delete this notification?')) {
                    // Construct the URL more safely
                    const baseUrl = '{{ url("/notifications") }}';
                    const deleteUrl = `${baseUrl}/${notificationId}`;
                    console.log('DELETE URL:', deleteUrl);
                    
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Delete response status:', response.status);
                        console.log('Delete response URL:', response.url);
                        
                        if (response.ok) {
                            // Refresh the page to show success message and updated notifications
                            location.reload();
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting notification:', error);
                        alert('Error deleting notification: ' + error.message);
                    });
                }
            }
            
            // Alternative delete function that gets ID from DOM element
            function deleteNotificationFromElement(element) {
                const notificationElement = element.closest('[data-notification-id]');
                if (notificationElement) {
                    const notificationId = notificationElement.getAttribute('data-notification-id');
                    console.log('Found notification ID from DOM:', notificationId);
                    deleteNotification(notificationId);
                } else {
                    console.error('Could not find notification element');
                    alert('Error: Could not identify notification to delete');
                }
            }
            
            function deleteAllNotifications() {
                if (confirm('Are you sure you want to delete ALL notifications? This cannot be undone.')) {
                    fetch('{{ route("notifications.destroyAll") }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Delete all response status:', response.status);
                        if (response.ok) {
                            // Refresh the page to show success message and updated notifications
                            location.reload();
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting all notifications:', error);
                        alert('Error deleting all notifications');
                    });
                }
            }
        </script>
    </body>
</html>