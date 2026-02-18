<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Register - Task Management System</title>
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
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main content-->
        <main class="flex-shrink-0">
            <section class="py-5">
                <div class="container px-5">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card shadow-lg">
                                <div class="card-body p-5">
                                    <h2 class="card-title text-center mb-4">Create Account</h2>

                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf

                                        <!-- Name -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                                            @error('name')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Email Address -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username" />
                                            @error('email')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Role -->
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select id="role" class="form-select @error('role') is-invalid @enderror" name="role" required>
                                                <option value="" disabled selected>Select a role</option>
                                                <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                                                <option value="frontend_dev" {{ old('role') === 'frontend_dev' ? 'selected' : '' }}>Frontend Developer</option>
                                                <option value="backend_dev" {{ old('role') === 'backend_dev' ? 'selected' : '' }}>Backend Developer</option>
                                                <option value="server_admin" {{ old('role') === 'server_admin' ? 'selected' : '' }}>Server Admin</option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" />
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="eyeIcon1"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" />
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                    <i class="bi bi-eye" id="eyeIcon2"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Sign Up
                            </button>
                        </div>

                        <div class="text-center">
                                            Already have an account?
                                            <a href="{{ route('login') }}" class="text-decoration-none">
                                                Login here
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
        
        <!-- Password Toggle Scripts -->
        <script>
            // Toggle for Password field
            document.getElementById('togglePassword').addEventListener('click', function () {
                const passwordField = document.getElementById('password');
                const eyeIcon = document.getElementById('eyeIcon1');
                
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    eyeIcon.className = 'bi bi-eye-slash';
                } else {
                    passwordField.type = 'password';
                    eyeIcon.className = 'bi bi-eye';
                }
            });
            
            // Toggle for Password Confirmation field
            document.getElementById('togglePasswordConfirmation').addEventListener('click', function () {
                const passwordConfirmationField = document.getElementById('password_confirmation');
                const eyeIcon = document.getElementById('eyeIcon2');
                
                if (passwordConfirmationField.type === 'password') {
                    passwordConfirmationField.type = 'text';
                    eyeIcon.className = 'bi bi-eye-slash';
                } else {
                    passwordConfirmationField.type = 'password';
                    eyeIcon.className = 'bi bi-eye';
                }
            });
        </script>
    </body>
</html>
