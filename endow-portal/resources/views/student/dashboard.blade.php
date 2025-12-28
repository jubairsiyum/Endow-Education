@extends('layouts.student')

@section('page-title', 'My Dashboard')
@section('breadcrumb', 'Home / Dashboard')

@section('content')
    <!-- Account Status Alert -->
    @if($student->account_status === 'pending')
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-clock fa-2x text-warning"></i>
            <div>
                <h5 class="mb-1 fw-bold">Account Pending Approval</h5>
                <p class="mb-0">Your account is currently under review. You'll be notified once it's been approved.</p>
            </div>
        </div>
    </div>
    @elseif($student->account_status === 'rejected')
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-times-circle fa-2x text-danger"></i>
            <div>
                <h5 class="mb-1 fw-bold">Account Not Approved</h5>
                <p class="mb-0">Unfortunately, your account was not approved. Please contact support for more information.</p>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-success border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-check-circle fa-2x text-success"></i>
            <div>
                <h5 class="mb-1 fw-bold">Welcome Back, {{ $student->name }}!</h5>
                <p class="mb-0">Your account is active. Track your application progress below.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-md-6">
            <div class="card-custom">
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Application Status</p>
                            <h3 class="mb-0 fw-bold">{{ ucfirst($student->status) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard-check fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-custom">
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Documents Progress</p>
                            <h3 class="mb-0 fw-bold">{{ $checklistProgress['percentage'] ?? 0 }}%</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-file-alt fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-custom">
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pending Documents</p>
                            <h3 class="mb-0 fw-bold">{{ $checklistProgress['pending'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-custom">
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Completed</p>
                            <h3 class="mb-0 fw-bold">{{ $checklistProgress['completed'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-double fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile & Program Info -->
    <div class="row g-4 mt-2">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user text-danger me-2"></i>My Profile Information</h5>
                        <a href="{{ route('student.profile.edit') }}" class="btn btn-sm btn-primary-custom">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </a>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Full Name</label>
                            <div class="fw-semibold">{{ $student->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Email Address</label>
                            <div class="fw-semibold">{{ $student->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Phone Number</label>
                            <div class="fw-semibold">{{ $student->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Country</label>
                            <div class="fw-semibold">{{ $student->country }}</div>
                        </div>
                        @if($student->targetUniversity)
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Target University</label>
                            <div class="fw-semibold">{{ $student->targetUniversity->name }}</div>
                        </div>
                        @endif
                        @if($student->targetProgram)
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Target Program</label>
                            <div class="fw-semibold">{{ $student->targetProgram->name }} ({{ ucfirst($student->targetProgram->level) }})</div>
                        </div>
                        @endif
                        @if($student->assignedUser)
                        <div class="col-12">
                            <label class="text-muted mb-1 small">Assigned Counselor</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-weight: 600;">
                                    {{ strtoupper(substr($student->assignedUser->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $student->assignedUser->name }}</div>
                                    <small class="text-muted">{{ $student->assignedUser->email }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom bg-danger bg-opacity-10 border-0">
                <div class="card-body-custom">
                    <div class="text-center mb-3">
                        <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px; font-weight: 600;">
                            {{ $checklistProgress['percentage'] ?? 0 }}%
                        </div>
                    </div>
                    <h5 class="text-center fw-bold mb-3">Document Submission Progress</h5>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: {{ $checklistProgress['percentage'] ?? 0 }}%;">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-sm mb-3">
                        <span class="text-muted">{{ $checklistProgress['completed'] ?? 0 }} of {{ $checklistProgress['total'] ?? 0 }} completed</span>
                    </div>
                    <a href="{{ route('student.documents') }}" class="btn btn-primary-custom w-100">
                        <i class="fas fa-file-upload me-2"></i>Submit Documents
                    </a>
                </div>
            </div>

            @if($student->targetProgram)
            <div class="card-custom mt-3">
                <div class="card-body-custom">
                    <h6 class="fw-bold mb-3"><i class="fas fa-graduation-cap text-danger me-2"></i>Program Details</h6>
                    <div class="mb-2">
                        <small class="text-muted">Duration</small>
                        <div class="fw-semibold">{{ $student->targetProgram->duration }}</div>
                    </div>
                    @if($student->targetProgram->tuition_fee)
                    <div>
                        <small class="text-muted">Tuition Fee</small>
                        <div class="fw-semibold text-danger">{{ $student->targetProgram->formatted_tuition_fee }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-bolt text-danger me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('student.documents') }}" class="text-decoration-none">
                                <div class="p-3 bg-light rounded text-center hover-card">
                                    <i class="fas fa-file-upload fa-2x text-danger mb-2"></i>
                                    <div class="fw-semibold small">Upload Documents</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('student.profile.edit') }}" class="text-decoration-none">
                                <div class="p-3 bg-light rounded text-center hover-card">
                                    <i class="fas fa-user-edit fa-2x text-primary mb-2"></i>
                                    <div class="fw-semibold small">Update Profile</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('student.faq') }}" class="text-decoration-none">
                                <div class="p-3 bg-light rounded text-center hover-card">
                                    <i class="fas fa-question-circle fa-2x text-info mb-2"></i>
                                    <div class="fw-semibold small">View FAQ</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('student.emergency-contact') }}" class="text-decoration-none">
                                <div class="p-3 bg-light rounded text-center hover-card">
                                    <i class="fas fa-phone-alt fa-2x text-success mb-2"></i>
                                    <div class="fw-semibold small">Emergency Contact</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background: white !important;
    }
</style>
@endpush
