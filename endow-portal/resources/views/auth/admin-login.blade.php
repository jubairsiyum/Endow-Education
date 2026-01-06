@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4">
                    <div class="mb-4">
                        <div class="d-inline-block p-3 rounded-circle" style="background: linear-gradient(135deg, #DC143C 0%, #B0102F 100%); box-shadow: 0 8px 32px rgba(220, 20, 60, 0.3);">
                            <i class="fas fa-shield-alt fa-3x text-white"></i>
                        </div>
                    </div>
                    <h2 class="text-white fw-bold mb-2">Admin Portal</h2>
                    <p class="text-white-50 mb-0">Endow Connect Management System</p>
                </div>

                <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-semibold mb-2" style="color: #1a1a1a;">Secure Access</h4>
                            <p class="text-muted small mb-0">Please enter your credentials to continue</p>
                        </div>

                        <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger border-0 mb-4" style="background-color: #fff5f5; color: #DC143C; border-radius: 12px;">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-exclamation-circle mt-1 me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1">Authentication Failed</strong>
                                        <small>{{ $errors->first() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-dark">
                                <i class="fas fa-envelope" style="color: #DC143C;"></i> Email Address
                            </label>
                            <input id="email"
                                   type="email"
                                   class="form-control form-control-lg @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   autofocus
                                   placeholder="admin@example.com"
                                   style="border-radius: 10px; border: 2px solid #e9ecef; padding: 14px 18px;">
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold text-dark">
                                <i class="fas fa-lock" style="color: #DC143C;"></i> Password
                            </label>
                            <div class="position-relative">
                                <input id="password"
                                       type="password"
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="Enter your secure password"
                                       style="border-radius: 10px; border: 2px solid #e9ecef; padding: 14px 18px; padding-right: 50px;">
                                <button type="button"
                                        class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted"
                                        id="togglePassword"
                                        style="text-decoration: none; z-index: 10;">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="remember"
                                       id="remember"
                                       {{ old('remember') ? 'checked' : '' }}
                                       style="border: 2px solid #DC143C;">
                                <label class="form-check-label text-dark" for="remember">
                                    Keep me signed in
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" class="btn btn-lg fw-semibold"
                                    style="background: linear-gradient(135deg, #DC143C 0%, #B0102F 100%);
                                           color: white;
                                           border: none;
                                           border-radius: 10px;
                                           padding: 14px;
                                           box-shadow: 0 4px 16px rgba(220, 20, 60, 0.3);
                                           transition: all 0.3s ease;">
                                <i class="fas fa-sign-in-alt me-2"></i> Sign In Securely
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mb-4">
                                <a class="text-decoration-none"
                                   href="{{ route('password.request') }}"
                                   style="color: #DC143C; font-weight: 500; transition: all 0.2s ease;">
                                    <i class="fas fa-question-circle"></i> Forgot Your Password?
                                </a>
                            </div>
                        @endif

                        <div class="pt-3 border-top">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-user-graduate" style="color: #DC143C; font-size: 2rem;"></i>
                                </div>
                                <p class="text-muted mb-3 small">Looking for student portal?</p>
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    <a href="{{ route('student.login') }}"
                                       class="btn btn-outline-dark btn-sm"
                                       style="border-radius: 8px; border-width: 2px;">
                                        <i class="fas fa-sign-in-alt"></i> Student Login
                                    </a>
                                    <a href="{{ route('student.register.form') }}"
                                       class="btn btn-outline-secondary btn-sm"
                                       style="border-radius: 8px; border-width: 2px;">
                                        <i class="fas fa-user-plus"></i> New Student
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="text-center mt-4">
                <p class="text-white-50 small mb-2">
                    <i class="fas fa-shield-alt"></i> Secured with industry-standard encryption
                </p>
                <p class="text-white-50 small mb-0">
                    &copy; {{ date('Y') }} Endow Connect. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    /* Login Form Enhancements */
    #loginForm .form-control:focus {
        border-color: #DC143C;
        box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.15);
    }

    #loginForm .form-check-input:checked {
        background-color: #DC143C;
        border-color: #DC143C;
    }

    #loginForm .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.15);
    }

    #loginForm button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
    }

    #loginForm button[type="submit"]:active {
        transform: translateY(0);
    }

    #loginForm a:hover {
        color: #B0102F !important;
    }

    /* Card styling */
    .card {
        backdrop-filter: blur(10px);
    }

    /* Password toggle button */
    #togglePassword {
        background: none;
        border: none;
        padding: 0 15px;
    }

    #togglePassword:hover {
        color: #DC143C !important;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .card-body {
            padding: 2rem 1.5rem !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (togglePassword && passwordInput && toggleIcon) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle icon
            if (type === 'password') {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        });
    }

    // Form validation feedback
    const form = document.getElementById('loginForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Authenticating...';
                submitBtn.disabled = true;
            }
        });
    }

    // Auto-focus on first input with error
    const firstError = document.querySelector('.is-invalid');
    if (firstError) {
        firstError.focus();
    }
});
</script>
@endsection
