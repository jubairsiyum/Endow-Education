@extends('layouts.student')

@section('page-title', 'Edit Profile')
@section('breadcrumb', 'Home / Profile / Edit')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-user-edit text-danger me-2"></i>Edit Your Profile</h5>
                </div>
                <div class="card-body-custom">
                    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $student->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $student->email) }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $student->phone) }}"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date"
                                           class="form-control @error('date_of_birth') is-invalid @enderror"
                                           id="date_of_birth"
                                           name="date_of_birth"
                                           value="{{ old('date_of_birth', $student->date_of_birth) }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select @error('gender') is-invalid @enderror"
                                            id="gender"
                                            name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('country') is-invalid @enderror"
                                           id="country"
                                           name="country"
                                           value="{{ old('country', $student->country) }}"
                                           required>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address"
                                              name="address"
                                              rows="2">{{ old('address', $student->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Passport Information Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="fas fa-passport me-2"></i>Passport Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="passport_number" class="form-label">Passport Number</label>
                                    <input type="text"
                                           class="form-control @error('passport_number') is-invalid @enderror"
                                           id="passport_number"
                                           name="passport_number"
                                           value="{{ old('passport_number', $student->passport_number) }}">
                                    @error('passport_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="passport_expiry_date" class="form-label">Passport Expiry Date</label>
                                    <input type="date"
                                           class="form-control @error('passport_expiry_date') is-invalid @enderror"
                                           id="passport_expiry_date"
                                           name="passport_expiry_date"
                                           value="{{ old('passport_expiry_date', $student->passport_expiry_date) }}">
                                    @error('passport_expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Education Background Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="fas fa-graduation-cap me-2"></i>Education Background
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="highest_qualification" class="form-label">Highest Qualification</label>
                                    <select class="form-select @error('highest_qualification') is-invalid @enderror"
                                            id="highest_qualification"
                                            name="highest_qualification">
                                        <option value="">Select Qualification</option>
                                        <option value="high_school" {{ old('highest_qualification', $student->highest_qualification) === 'high_school' ? 'selected' : '' }}>High School</option>
                                        <option value="bachelors" {{ old('highest_qualification', $student->highest_qualification) === 'bachelors' ? 'selected' : '' }}>Bachelor's Degree</option>
                                        <option value="masters" {{ old('highest_qualification', $student->highest_qualification) === 'masters' ? 'selected' : '' }}>Master's Degree</option>
                                        <option value="phd" {{ old('highest_qualification', $student->highest_qualification) === 'phd' ? 'selected' : '' }}>PhD</option>
                                    </select>
                                    @error('highest_qualification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="previous_institution" class="form-label">Previous Institution</label>
                                    <input type="text"
                                           class="form-control @error('previous_institution') is-invalid @enderror"
                                           id="previous_institution"
                                           name="previous_institution"
                                           value="{{ old('previous_institution', $student->previous_institution) }}">
                                    @error('previous_institution')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Target Program Information (Read-only) -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="fas fa-university me-2"></i>Target Program
                            </h6>
                            <div class="alert alert-info border-0">
                                <small><i class="fas fa-info-circle me-1"></i> Target university and program are assigned by your counselor. Contact support to request changes.</small>
                            </div>
                            <div class="row g-3">
                                @if($student->targetUniversity)
                                <div class="col-md-6">
                                    <label class="form-label">Target University</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $student->targetUniversity->name }}"
                                           readonly>
                                </div>
                                @endif
                                @if($student->targetProgram)
                                <div class="col-md-6">
                                    <label class="form-label">Target Program</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $student->targetProgram->name }} ({{ ucfirst($student->targetProgram->level) }})"
                                           readonly>
                                </div>
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Emergency Contact Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                    <input type="text"
                                           class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                           id="emergency_contact_name"
                                           name="emergency_contact_name"
                                           value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}">
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                                    <input type="text"
                                           class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                           id="emergency_contact_phone"
                                           name="emergency_contact_phone"
                                           value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}">
                                    @error('emergency_contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                                    <input type="text"
                                           class="form-control @error('emergency_contact_relationship') is-invalid @enderror"
                                           id="emergency_contact_relationship"
                                           name="emergency_contact_relationship"
                                           value="{{ old('emergency_contact_relationship', $student->emergency_contact_relationship) }}"
                                           placeholder="e.g., Parent, Spouse, Sibling">
                                    @error('emergency_contact_relationship')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
