@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <!-- Demo Credentials -->
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Demo Credentials</h5>
                        <p class="mb-2"><strong>Click on any credential to auto-fill the login form:</strong></p>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary w-100 demo-login"
                                    data-email="superadmin@endowglobal.com"
                                    data-password="password">
                                    <i class="fas fa-user-shield"></i> Super Admin
                                </button>
                                <small class="text-muted d-block mt-1">superadmin@endowglobal.com</small>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-success w-100 demo-login"
                                    data-email="admin@endowglobal.com"
                                    data-password="password">
                                    <i class="fas fa-user-tie"></i> Admin
                                </button>
                                <small class="text-muted d-block mt-1">admin@endowglobal.com</small>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-info w-100 demo-login"
                                    data-email="employee@endowglobal.com"
                                    data-password="password">
                                    <i class="fas fa-user"></i> Employee
                                </button>
                                <small class="text-muted d-block mt-1">employee@endowglobal.com</small>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-warning w-100 demo-login"
                                    data-email="student@endowglobal.com"
                                    data-password="password">
                                    <i class="fas fa-user-graduate"></i> Student
                                </button>
                                <small class="text-muted d-block mt-1">student@endowglobal.com</small>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block"><i class="fas fa-key"></i> Password for all accounts: <strong>password</strong></small>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Student Registration Link -->
                        <div class="row mt-3">
                            <div class="col-md-8 offset-md-4">
                                <div class="alert alert-light border">
                                    <i class="fas fa-user-plus"></i> New Student?
                                    <a href="{{ route('student.register.form') }}" class="alert-link">Register here</a>
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

            // Focus on the password field
            document.getElementById('password').focus();
        });
    });
});
</script>
@endsection
