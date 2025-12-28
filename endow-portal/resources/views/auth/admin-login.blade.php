@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white py-4" style="background-color: #1a1a1a;">
                    <h3 class="mb-0 fw-bold"><i class="fas fa-user-shield text-danger"></i> Admin / Employee Login</h3>
                    <small class="opacity-90">Access your Endow Education admin account</small>
                </div>

                <div class="card-body p-4">
                    <!-- Demo Credentials -->
                    <div class="alert alert-light border-danger mb-4">
                        <h6 class="alert-heading text-danger"><i class="fas fa-info-circle"></i> Demo Credentials</h6>
                        <p class="mb-3 small"><strong>Click on any credential to auto-fill the login form:</strong></p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100 demo-login"
                                    data-email="superadmin@endowglobal.com"
                                    data-password="password">
                                    <i class="fas fa-user-shield"></i> Super Admin
                                </button>
                                <small class="text-muted d-block mt-1">superadmin@endowglobal.com</small>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-sm btn-outline-dark w-100 demo-login"
                                    data-email="admin@endowglobal.com"
                                    data-password="password">
                                    <i class="fas fa-user-tie"></i> Admin
                                </button>
                                <small class="text-muted d-block mt-1">admin@endowglobal.com</small>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 demo-login"
                                    data-email="employee@endowglobal.com"
                                    data-password="password">
                                    <i class="fas fa-user"></i> Employee
                                </button>
                                <small class="text-muted d-block mt-1">employee@endowglobal.com</small>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block"><i class="fas fa-key"></i> Password for all accounts: <strong>password</strong></small>
                    </div>

                    <hr class="my-4">

                    <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold"><i class="fas fa-envelope text-danger"></i> Email Address</label>
                            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                placeholder="Enter your email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold"><i class="fas fa-lock text-danger"></i> Password</label>
                            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                                name="password" required autocomplete="current-password"
                                placeholder="Enter your password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="text-muted text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="fas fa-question-circle"></i> {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        @endif

                        <!-- Student Login Link -->
                        <div class="mt-4">
                            <div class="card bg-light border-danger">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-user-graduate text-danger fa-2x mb-2"></i>
                                    <p class="mb-2 fw-semibold">Are you a Student?</p>
                                    <a href="{{ route('student.login') }}" class="btn btn-outline-danger btn-sm me-2">
                                        <i class="fas fa-sign-in-alt"></i> Student Login
                                    </a>
                                    <a href="{{ route('student.register.form') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-user-plus"></i> Register
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill login form when demo credentials are clicked
    document.querySelectorAll('.demo-login').forEach(button => {
        button.addEventListener('click', function() {
            const email = this.getAttribute('data-email');
            const password = this.getAttribute('data-password');

            document.getElementById('email').value = email;
            document.getElementById('password').value = password;

            // Remove any existing validation classes
            document.getElementById('email').classList.remove('is-invalid');
            document.getElementById('password').classList.remove('is-invalid');

            // Highlight the button briefly
            this.classList.add('active');
            setTimeout(() => {
                this.classList.remove('active');
            }, 200);
        });
    });
});
</script>
@endsection
