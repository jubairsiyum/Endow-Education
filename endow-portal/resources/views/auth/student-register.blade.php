@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-danger text-white py-4">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-user-graduate"></i> Student Registration</h4>
                    <small class="opacity-90">Please fill in your details as per your passport</small>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.register') }}">
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-user"></i> Personal Information</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name (as per passport) <span class="text-danger">*</span></label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="surname" class="form-label">Surname <span class="text-danger">*</span></label>
                                    <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror"
                                           name="surname" value="{{ old('surname') }}" required>
                                    @error('surname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="given_names" class="form-label">Given Names</label>
                                    <input id="given_names" type="text" class="form-control @error('given_names') is-invalid @enderror"
                                           name="given_names" value="{{ old('given_names') }}">
                                    @error('given_names')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input id="date_of_birth" type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                           name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Family Information Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-users"></i> Family Information</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="father_name" class="form-label">Father's Name <span class="text-danger">*</span></label>
                                    <input id="father_name" type="text" class="form-control @error('father_name') is-invalid @enderror"
                                           name="father_name" value="{{ old('father_name') }}" required>
                                    @error('father_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="mother_name" class="form-label">Mother's Name <span class="text-danger">*</span></label>
                                    <input id="mother_name" type="text" class="form-control @error('mother_name') is-invalid @enderror"
                                           name="mother_name" value="{{ old('mother_name') }}" required>
                                    @error('mother_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-address-book"></i> Contact Information</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                                           name="address" value="{{ old('address') }}" required>
                                    @error('address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input id="city" type="text" class="form-control @error('city') is-invalid @enderror"
                                           name="city" value="{{ old('city') }}" required>
                                    @error('city')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input id="postal_code" type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                           name="postal_code" value="{{ old('postal_code') }}">
                                    @error('postal_code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                    <input id="country" type="text" class="form-control @error('country') is-invalid @enderror"
                                           name="country" value="{{ old('country') }}" required>
                                    @error('country')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Passport & Nationality Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-passport"></i> Passport & Nationality</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="passport_number" class="form-label">Passport Number</label>
                                    <input id="passport_number" type="text" class="form-control @error('passport_number') is-invalid @enderror"
                                           name="passport_number" value="{{ old('passport_number') }}">
                                    @error('passport_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
                                    <input id="nationality" type="text" class="form-control @error('nationality') is-invalid @enderror"
                                           name="nationality" value="{{ old('nationality') }}" required>
                                    @error('nationality')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information will be added after approval by counselor -->

                        <!-- Account Security Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-lock"></i> Account Security</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                           name="password" required autocomplete="new-password" minlength="8">
                                    <small class="form-text text-muted">Minimum 8 characters</small>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input id="password_confirmation" type="password" class="form-control"
                                           name="password_confirmation" required autocomplete="new-password" minlength="8">
                                    <small class="form-text text-muted">Re-enter your password</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('student.login') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Login
                            </a>
                            <button type="submit" class="btn btn-danger btn-lg px-5">
                                <i class="fas fa-paper-plane"></i> Submit Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
