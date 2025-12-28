@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white py-4" style="background-color: #DC143C;">
                    <h3 class="mb-0 fw-bold"><i class="fas fa-user-graduate"></i> Student Login</h3>
                    <small class="opacity-90">Access your student portal</small>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('student.login.submit') }}" id="studentLoginForm">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold"><i class="fas fa-envelope text-danger"></i> Email Address</label>
                            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                placeholder="Enter your registered email">
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
                    </form>

                    <hr class="my-4">

                    <!-- Registration and Admin Login Links -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card bg-light border">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-user-plus text-danger fa-2x mb-2"></i>
                                    <p class="mb-2 small fw-semibold">New Student?</p>
                                    <a href="{{ route('student.register.form') }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-graduation-cap"></i> Register for Admission
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-user-shield text-dark fa-2x mb-2"></i>
                                    <p class="mb-2 small fw-semibold">Staff Member?</p>
                                    <a href="{{ route('admin.login') }}" class="btn btn-outline-dark btn-sm">
                                        <i class="fas fa-sign-in-alt"></i> Admin Login
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
