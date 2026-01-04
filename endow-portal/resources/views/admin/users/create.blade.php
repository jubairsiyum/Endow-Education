@extends('layouts.admin')

@section('page-title', 'Create User')
@section('breadcrumb', 'Home / Users / Create')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="fas fa-user-plus text-primary me-2"></i>
                Create New User
            </h2>
            <p class="text-muted mb-0">Add a new user to the system</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>User Information</h5>
                </div>
                <div class="card-body-custom">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label for="phone" class="form-label fw-semibold">
                                Phone Number
                            </label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="+880 1XXX-XXXXXX">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <small class="form-text text-muted">Minimum 8 characters</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Role -->
                            <div class="col-md-6 mb-4">
                                <label for="role" class="form-label fw-semibold">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-4">
                                <label for="status" class="form-label fw-semibold">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Role Permissions</h5>
                </div>
                <div class="card-body-custom">
                    <div class="mb-3">
                        <h6 class="text-danger fw-bold">
                            <i class="fas fa-shield-alt me-2"></i>Super Admin
                        </h6>
                        <p class="small text-muted mb-0">Full system access including user management, system settings, and all features.</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-warning fw-bold">
                            <i class="fas fa-user-tie me-2"></i>Admin
                        </h6>
                        <p class="small text-muted mb-0">Manage students, applications, documents, and view reports. Cannot manage super admins.</p>
                    </div>
                    <hr>
                    <div>
                        <h6 class="text-success fw-bold">
                            <i class="fas fa-user-friends me-2"></i>Employee
                        </h6>
                        <p class="small text-muted mb-0">Handle student applications, follow-ups, document processing, and basic student management.</p>
                    </div>
                </div>
            </div>

            <div class="card-custom mt-3">
                <div class="card-header-custom bg-warning">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h5>
                </div>
                <div class="card-body-custom">
                    <ul class="small mb-0">
                        <li class="mb-2">Email addresses must be unique</li>
                        <li class="mb-2">Users will be verified automatically</li>
                        <li class="mb-2">Password must be at least 8 characters</li>
                        <li>Inactive users cannot access the system</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media (max-width: 768px) {
        .card-custom {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush
@endsection
