<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login - TaskFlow</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <style>
            :root {
                --primary-purple: #667eea;
                --primary-purple-dark: #5568d3;
                --primary-purple-light: #8b9cf5;
                --secondary-purple: #764ba2;
                --purple-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                --purple-light-bg: #f5f3ff;
                --purple-border: #e0d9ff;
                --bg-color: #ffffff;
                --text-color: #1f2937;
                --card-bg: #ffffff;
                --border-color: #e5e7eb;
                --muted-text: #6b7280;
            }
            
            [data-theme="dark"] {
                --primary-purple: #8b9cf5;
                --primary-purple-dark: #667eea;
                --primary-purple-light: #a5b4f7;
                --secondary-purple: #9d6ec9;
                --purple-gradient: linear-gradient(135deg, #8b9cf5 0%, #9d6ec9 100%);
                --purple-light-bg: #2d2d44;
                --purple-border: #3d3d5c;
                --bg-color: #1a1a2e;
                --text-color: #e5e7eb;
                --card-bg: #16213e;
                --border-color: #2d3748;
                --muted-text: #9ca3af;
            }
            
            body {
                background: var(--purple-gradient);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                transition: background-color 0.3s ease;
            }
            
            [data-theme="dark"] body {
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            }
            
            .auth-container {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
            }
            
            .auth-card {
                background: var(--card-bg);
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 480px;
                width: 100%;
                overflow: hidden;
                animation: slideUp 0.5s ease;
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .auth-header {
                background: var(--purple-gradient);
                padding: 2.5rem 2rem;
                text-align: center;
                color: white;
            }
            
            .auth-header h1 {
                font-size: 2rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
            }
            
            .auth-header p {
                opacity: 0.9;
                margin: 0;
            }
            
            .auth-body {
                padding: 2.5rem 2rem;
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
            
            .form-control {
                border: 2px solid var(--border-color);
                border-radius: 10px;
                padding: 0.75rem 1rem;
                transition: all 0.3s ease;
                background-color: var(--card-bg);
                color: var(--text-color);
            }
            
            .form-control:focus {
                border-color: var(--primary-purple);
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
                background-color: var(--card-bg);
                color: var(--text-color);
            }
            
            .input-group-text {
                background-color: var(--card-bg);
                border: 2px solid var(--border-color);
                border-left: none;
                color: var(--text-color);
            }
            
            .input-group .form-control {
                border-right: none;
            }
            
            .input-group:focus-within .form-control,
            .input-group:focus-within .input-group-text {
                border-color: var(--primary-purple);
            }
            
            .btn-purple {
                background: var(--purple-gradient);
                border: none;
                color: white;
                padding: 0.875rem;
                border-radius: 10px;
                font-weight: 600;
                font-size: 1.1rem;
                transition: all 0.3s ease;
            }
            
            .btn-purple:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
                color: white;
            }
            
            .form-check-input:checked {
                background-color: var(--primary-purple);
                border-color: var(--primary-purple);
            }
            
            .auth-links {
                text-align: center;
                padding-top: 1.5rem;
                border-top: 1px solid var(--border-color);
            }
            
            .auth-links a {
                color: var(--primary-purple);
                text-decoration: none;
                font-weight: 500;
                transition: all 0.2s ease;
            }
            
            .auth-links a:hover {
                color: var(--primary-purple-dark);
                text-decoration: underline;
            }
            
            .theme-toggle {
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
            }
            
            .theme-toggle:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }
            
            .brand-logo {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .alert {
                border-radius: 10px;
                border: none;
            }
            
            [data-theme="dark"] .alert-success {
                background-color: rgba(16, 185, 129, 0.1);
                border: 1px solid rgba(16, 185, 129, 0.3);
                color: #34d399;
            }
            
            .password-toggle {
                background: transparent;
                border: none;
                color: var(--muted-text);
                cursor: pointer;
                padding: 0.5rem;
            }
            
            .password-toggle:hover {
                color: var(--primary-purple);
            }
        </style>
    </head>
    <body>
        <!-- Theme Toggle -->
        <button class="theme-toggle" id="themeToggle" title="Toggle theme">
            <i class="bi bi-moon-stars" id="themeIcon"></i>
        </button>

        <!-- Main content-->
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="brand-logo">
                        <i class="bi bi-check2-square"></i>
                        <span>TaskFlow</span>
                    </div>
                    <h1>Welcome Back!</h1>
                    <p>Sign in to continue to your dashboard</p>
                </div>

                <div class="auth-body">
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope-fill"></i>
                                Email Address
                            </label>
                            <input id="email" 
                                   type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Enter your email"
                                   required 
                                   autofocus 
                                   autocomplete="username" />
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock-fill"></i>
                                Password
                            </label>
                            <div class="input-group">
                                <input id="password" 
                                       type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       placeholder="Enter your password"
                                       required 
                                       autocomplete="current-password" />
                                <span class="input-group-text">
                                    <button class="password-toggle" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                            <label class="form-check-label" for="remember_me">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-purple">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In
                            </button>
                        </div>

                        <div class="auth-links">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">
                                    <i class="bi bi-key"></i> Forgot your password?
                                </a>
                                <span class="text-muted mx-2">•</span>
                            @endif
                            <a href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> Create an account
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
        
        <!-- Password Toggle Script -->
        <script>
            document.getElementById('togglePassword').addEventListener('click', function () {
                const passwordField = document.getElementById('password');
                const eyeIcon = document.getElementById('eyeIcon');
                
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    eyeIcon.className = 'bi bi-eye-slash';
                } else {
                    passwordField.type = 'password';
                    eyeIcon.className = 'bi bi-eye';
                }
            });
        </script>
    </body>
</html>
