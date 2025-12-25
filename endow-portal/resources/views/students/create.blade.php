@extends('layouts.admin')

@section('page-title', 'Add New Student')
@section('breadcrumb', 'Home / Students / Create')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Add New Student</h1>
        <p class="page-subtitle">Fill in the details to register a new student</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('students.store') }}" method="POST">
                @csrf

                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5>Personal Information</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" value="{{ old('country') }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5>Academic Information</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="course" class="form-label">Course/Program <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('course') is-invalid @enderror" 
                                       id="course" name="course" value="{{ old('course') }}" 
                                       placeholder="e.g., Bachelor of Computer Science" required>
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="4" 
                                          placeholder="Any additional information about the student...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-save me-2"></i> Create Student
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5><i class="fas fa-info-circle me-2"></i> Information</h5>
                </div>
                <div class="card-body-custom">
                    <p><strong>What happens next?</strong></p>
                    <ul class="ps-3">
                        <li class="mb-2">Student will be created with "Pending" account status</li>
                        <li class="mb-2">A checklist will be automatically initialized</li>
                        <li class="mb-2">You will be assigned as the counselor</li>
                        <li class="mb-2">Student can be approved/rejected by admins</li>
                    </ul>

                    <hr>

                    <p class="mb-2"><strong>Required Fields:</strong></p>
                    <ul class="ps-3 mb-0">
                        <li>Full Name</li>
                        <li>Email Address</li>
                        <li>Phone Number</li>
                        <li>Country</li>
                        <li>Course/Program</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
