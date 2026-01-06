@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #1a1a1a 0%, #333333 100%);">
                    <h3 class="mb-0 fw-bold"><i class="fas fa-sign-in-alt text-danger"></i> Login</h3>
                    <small class="opacity-90">Access your Endow Education account</small>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
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

                        <!-- Student Registration Link -->
                        <div class="mt-4">
                            <div class="card bg-light border-danger">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-user-plus text-danger fa-2x mb-2"></i>
                                    <p class="mb-2 fw-semibold">New Student?</p>
                                    <a href="{{ route('student.register.form') }}" class="btn btn-outline-danger">
                                        <i class="fas fa-graduation-cap"></i> Register for Admission
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
@endsection
