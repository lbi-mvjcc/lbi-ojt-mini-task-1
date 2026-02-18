<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login - Task Management System</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="#">Task Management</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main content-->
        <main class="flex-shrink-0">
            <section class="py-5">
                <div class="container px-5">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card shadow-lg">
                                <div class="card-body p-5">
                                    <h2 class="card-title text-center mb-4">Login</h2>

                                    <!-- Session Status -->
                                    @if (session('status'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('status') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <!-- Email Address -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                                            @error('email')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" />
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                                            <label class="form-check-label" for="remember_me">
                                                Remember me
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 mb-3">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                Log in
                                            </button>
                                        </div>

                                        <div class="text-center">
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="text-decoration-none me-3">
                                                    Forgot your password?
                                                </a>
                                            @endif
                                            <span class="text-muted">|</span>
                                            <a href="{{ route('register') }}" class="text-decoration-none ms-3">
                                                Don't have an account? Register
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer-->
        <footer class="py-5 bg-dark mt-5">
            <div class="container px-5"><p class="m-0 text-center text-white">Copyright &copy; Task Management System 2026</p></div>
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        
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
