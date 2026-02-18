<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="TaskFlow - Modern task management system for teams" />
        <meta name="author" content="" />
        <title>TaskFlow - Task Management System</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <style>
            :root {
                --primary-purple: #667eea;
                --primary-purple-dark: #5568d3;
                --secondary-purple: #764ba2;
                --purple-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            [data-theme="dark"] {
                --primary-purple: #8b9cf5;
                --primary-purple-dark: #667eea;
                --secondary-purple: #9d6ec9;
                --purple-gradient: linear-gradient(135deg, #8b9cf5 0%, #9d6ec9 100%);
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            
            .navbar-purple {
                background: var(--purple-gradient) !important;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            
            .hero-section {
                background: var(--purple-gradient);
                min-height: 90vh;
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
            }
            
            .hero-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>');
                animation: float 20s linear infinite;
            }
            
            @keyframes float {
                from { transform: translateY(0); }
                to { transform: translateY(-100px); }
            }
            
            .hero-content {
                position: relative;
                z-index: 1;
            }
            
            .hero-title {
                font-size: 3.5rem;
                font-weight: 800;
                color: white;
                margin-bottom: 1.5rem;
                line-height: 1.2;
            }
            
            .hero-subtitle {
                font-size: 1.25rem;
                color: rgba(255, 255, 255, 0.9);
                margin-bottom: 2rem;
            }
            
            .btn-hero {
                padding: 1rem 2.5rem;
                font-size: 1.1rem;
                font-weight: 600;
                border-radius: 50px;
                transition: all 0.3s ease;
                border: none;
            }
            
            .btn-hero-primary {
                background: white;
                color: var(--primary-purple);
            }
            
            .btn-hero-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                color: var(--primary-purple);
            }
            
            .btn-hero-outline {
                background: transparent;
                color: white;
                border: 2px solid white;
            }
            
            .btn-hero-outline:hover {
                background: white;
                color: var(--primary-purple);
                transform: translateY(-3px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }
            
            .feature-card {
                background: white;
                border-radius: 20px;
                padding: 2.5rem;
                height: 100%;
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
            }
            
            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
            }
            
            .feature-icon {
                width: 80px;
                height: 80px;
                border-radius: 20px;
                background: var(--purple-gradient);
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1.5rem;
                font-size: 2rem;
                color: white;
            }
            
            .feature-title {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
                color: #1f2937;
            }
            
            .feature-description {
                color: #6b7280;
                line-height: 1.6;
            }
            
            .stats-section {
                background: linear-gradient(135deg, #f5f3ff 0%, #e0d9ff 100%);
                padding: 5rem 0;
            }
            
            .stat-card {
                text-align: center;
                padding: 2rem;
            }
            
            .stat-number {
                font-size: 3rem;
                font-weight: 800;
                background: var(--purple-gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            .stat-label {
                color: #6b7280;
                font-weight: 600;
                margin-top: 0.5rem;
            }
            
            .cta-section {
                background: var(--purple-gradient);
                padding: 5rem 0;
                color: white;
            }
            
            .theme-toggle-home {
                position: fixed;
                top: 1.5rem;
                right: 1.5rem;
                background: rgba(255, 255, 255, 0.2);
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                color: white;
                font-size: 1.2rem;
                backdrop-filter: blur(10px);
                z-index: 1000;
            }
            
            .theme-toggle-home:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }
            
            [data-theme="dark"] body {
                background: #1a1a2e;
                color: #e5e7eb;
            }
            
            [data-theme="dark"] .feature-card {
                background: #16213e;
                border-color: #2d3748;
            }
            
            [data-theme="dark"] .feature-title {
                color: #e5e7eb;
            }
            
            [data-theme="dark"] .stats-section {
                background: linear-gradient(135deg, #2d2d44 0%, #3d3d5c 100%);
            }
            
            footer {
                background: #1f2937;
                color: white;
                padding: 2rem 0;
            }
        </style>
    </head>
    <body>
        <!-- Theme Toggle -->
        <button class="theme-toggle-home" id="themeToggle" title="Toggle theme">
            <i class="bi bi-moon-stars" id="themeIcon"></i>
        </button>

        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark navbar-purple">
            <div class="container px-5">
                <a class="navbar-brand fw-bold" href="/">
                    <i class="bi bi-check2-square"></i> TaskFlow
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="btn btn-light px-4 py-2 fw-bold" href="{{ route('login') }}" style="border-radius: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-light px-4 py-2 fw-bold" href="{{ route('register') }}" style="border-radius: 25px; border-width: 2px;">
                                <i class="bi bi-person-plus me-1"></i> Sign Up
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container px-5">
                <div class="row align-items-center">
                    <div class="col-lg-6 hero-content">
                        <h1 class="hero-title">Manage Tasks Efficiently with TaskFlow</h1>
                        <p class="hero-subtitle">
                            Streamline your workflow, collaborate with your team, and track progress in real-time with our modern task management platform.
                        </p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a class="btn btn-hero btn-hero-primary" href="{{ route('register') }}">
                                <i class="bi bi-rocket-takeoff me-2"></i>
                                Get Started Free
                            </a>
                            <a class="btn btn-hero btn-hero-outline" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block text-center">
                        <i class="bi bi-kanban" style="font-size: 20rem; color: rgba(255, 255, 255, 0.2);"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="container px-5">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="stat-card">
                            <div class="stat-number">1000+</div>
                            <div class="stat-label">Tasks Completed</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="stat-card">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Active Teams</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-number">99%</div>
                            <div class="stat-label">Satisfaction Rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5" id="features">
            <div class="container px-5 my-5">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Powerful Features</h2>
                    <p class="lead text-muted">Everything you need to manage tasks effectively</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-list-check"></i>
                            </div>
                            <h3 class="feature-title">Task Management</h3>
                            <p class="feature-description">
                                Create, assign, and track tasks with ease. Organize your work with categories and priorities.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3 class="feature-title">Team Collaboration</h3>
                            <p class="feature-description">
                                Work seamlessly with your team. Assign tasks to developers and track their progress in real-time.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h3 class="feature-title">Progress Tracking</h3>
                            <p class="feature-description">
                                Monitor task status and completion rates with intuitive dashboards and activity timelines.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-bell"></i>
                            </div>
                            <h3 class="feature-title">Smart Notifications</h3>
                            <p class="feature-description">
                                Stay updated with real-time notifications for task assignments, updates, and deadlines.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-moon-stars"></i>
                            </div>
                            <h3 class="feature-title">Dark Mode</h3>
                            <p class="feature-description">
                                Work comfortably at any time with our beautiful dark mode that's easy on the eyes.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h3 class="feature-title">Secure & Reliable</h3>
                            <p class="feature-description">
                                Your data is safe with enterprise-grade security and reliable cloud infrastructure.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section" id="about">
            <div class="container px-5">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8">
                        <h2 class="display-5 fw-bold mb-4">Ready to Get Started?</h2>
                        <p class="lead mb-5">
                            Join teams worldwide who are already using TaskFlow to streamline their workflow and boost productivity.
                        </p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a class="btn btn-hero btn-hero-primary" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-2"></i>
                                Create Free Account
                            </a>
                            <a class="btn btn-hero btn-hero-outline" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="container px-5">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0">
                            <i class="bi bi-check2-square"></i> TaskFlow &copy; 2026
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="mb-0">
                            Made with <i class="bi bi-heart-fill text-danger"></i> for productive teams
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Theme Toggle Script -->
        <script>
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
            });
            
            function updateThemeIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill';
                } else {
                    themeIcon.className = 'bi bi-moon-stars';
                }
            }
        </script>
    </body>
</html>
