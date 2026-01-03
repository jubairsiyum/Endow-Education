@extends('layouts.student')

@section('page-title', 'View Profile')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Student Profile</h1>
                    <p class="text-muted">View your profile information</p>
                </div>
                <div>
                    <a href="{{ route('student.profile.edit', $student) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Profile Photo and Quick Info -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            @if($student->activeProfilePhoto)
                                <img src="{{ $student->activeProfilePhoto->photo_url }}" 
                                     alt="Profile Photo" 
                                     class="profile-photo mb-3">
                            @else
                                <div class="profile-photo-placeholder mb-3">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif

                            <h4 class="mb-1">{{ $student->name }}</h4>
                            <p class="text-muted mb-2">{{ $student->email }}</p>
                            
                            @if($student->registration_id)
                                <span class="badge bg-primary mb-3">{{ $student->registration_id }}</span>
                            @endif

                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Account Status:</span>
                                    <span class="badge bg-{{ $student->account_status === 'approved' ? 'success' : ($student->account_status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($student->account_status) }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Application Status:</span>
                                    <span class="badge bg-info">{{ ucfirst($student->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($student->profile)
                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Profile Completion</h5>
                            @php
                                $completion = $student->profile->getCompletionPercentage();
                            @endphp
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $completion }}%">
                                    {{ $completion }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Detailed Information -->
                <div class="col-lg-8">
                    <!-- Personal Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Full Name</label>
                                    <p class="mb-0 fw-semibold">{{ $student->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Email</label>
                                    <p class="mb-0 fw-semibold">{{ $student->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Phone</label>
                                    <p class="mb-0 fw-semibold">{{ $student->phone }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Date of Birth</label>
                                    <p class="mb-0 fw-semibold">{{ $student->date_of_birth?->format('F d, Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Gender</label>
                                    <p class="mb-0 fw-semibold">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Nationality</label>
                                    <p class="mb-0 fw-semibold">{{ $student->nationality ?? 'N/A' }}</p>
                                </div>
                                @if($student->father_name)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Father's Name</label>
                                    <p class="mb-0 fw-semibold">{{ $student->father_name }}</p>
                                </div>
                                @endif
                                @if($student->mother_name)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Mother's Name</label>
                                    <p class="mb-0 fw-semibold">{{ $student->mother_name }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">Address</label>
                                    <p class="mb-0 fw-semibold">{{ $student->address ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small">City</label>
                                    <p class="mb-0 fw-semibold">{{ $student->city ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small">Country</label>
                                    <p class="mb-0 fw-semibold">{{ $student->country }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small">Postal Code</label>
                                    <p class="mb-0 fw-semibold">{{ $student->postal_code ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passport Information -->
                    @if($student->passport_number || $student->passport_expiry_date)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-passport me-2"></i>Passport Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Passport Number</label>
                                    <p class="mb-0 fw-semibold">{{ $student->passport_number ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Expiry Date</label>
                                    <p class="mb-0 fw-semibold">{{ $student->passport_expiry_date?->format('F d, Y') ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Academic Profile -->
                    @if($student->profile && $student->profile->isComplete())
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic Profile</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($student->profile->academic_level)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Academic Level</label>
                                    <p class="mb-0 fw-semibold">{{ $student->profile->academic_level }}</p>
                                </div>
                                @endif
                                @if($student->profile->major)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Major</label>
                                    <p class="mb-0 fw-semibold">{{ $student->profile->major }}</p>
                                </div>
                                @endif
                                @if($student->profile->minor)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Minor</label>
                                    <p class="mb-0 fw-semibold">{{ $student->profile->minor }}</p>
                                </div>
                                @endif
                                @if($student->profile->gpa)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">GPA</label>
                                    <p class="mb-0 fw-semibold">{{ $student->profile->formatted_gpa }}</p>
                                </div>
                                @endif
                                @if($student->profile->bio)
                                <div class="col-12 mb-3">
                                    <label class="text-muted small">Bio</label>
                                    <p class="mb-0">{{ $student->profile->bio }}</p>
                                </div>
                                @endif
                                @if($student->profile->interests)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Interests</label>
                                    <p class="mb-0">{{ $student->profile->interests }}</p>
                                </div>
                                @endif
                                @if($student->profile->skills)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Skills</label>
                                    <p class="mb-0">{{ $student->profile->skills }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Emergency Contact -->
                    @if($student->emergency_contact_name || $student->emergency_contact_phone)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small">Contact Name</label>
                                    <p class="mb-0 fw-semibold">{{ $student->emergency_contact_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small">Contact Phone</label>
                                    <p class="mb-0 fw-semibold">{{ $student->emergency_contact_phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small">Relationship</label>
                                    <p class="mb-0 fw-semibold">{{ $student->emergency_contact_relationship ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-photo {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f0f0f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .profile-photo-placeholder {
        width: 200px;
        height: 200px;
        margin: 0 auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        color: white;
        border: 4px solid #f0f0f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .card {
        border: none;
        border-radius: 10px;
    }

    .card-header {
        border-bottom: 2px solid #f0f0f0;
        padding: 1.25rem;
    }
</style>
@endsection
