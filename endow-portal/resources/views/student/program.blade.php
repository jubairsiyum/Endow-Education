@extends('layouts.student')

@section('page-title', 'My Program')
@section('breadcrumb', 'Home / My Program')

@section('content')
    @if($student->targetProgram && $student->targetUniversity)
    <div class="row">
        <!-- Program Overview Card -->
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body-custom">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        @if($student->targetUniversity->logo)
                        <img src="{{ asset('storage/' . $student->targetUniversity->logo) }}" 
                             alt="{{ $student->targetUniversity->name }}" 
                             class="rounded"
                             style="width: 80px; height: 80px; object-fit: contain;">
                        @else
                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center"
                             style="width: 80px; height: 80px; font-size: 32px; font-weight: 700;">
                            {{ strtoupper(substr($student->targetUniversity->name, 0, 1)) }}
                        </div>
                        @endif
                        <div class="flex-grow-1">
                            <h3 class="fw-bold mb-2">{{ $student->targetProgram->name }}</h3>
                            <div class="d-flex align-items-center gap-2 text-muted mb-2">
                                <i class="fas fa-university"></i>
                                <span class="fw-semibold">{{ $student->targetUniversity->name }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="badge bg-primary">{{ ucfirst($student->targetProgram->level) }}</span>
                                @if($student->targetProgram->code)
                                <span class="badge bg-secondary">{{ $student->targetProgram->code }}</span>
                                @endif
                                @if($student->status)
                                @php
                                    $statusColors = [
                                        'new' => 'info',
                                        'in_progress' => 'warning',
                                        'submitted' => 'primary',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        'enrolled' => 'success'
                                    ];
                                    $statusColor = $statusColors[$student->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    <i class="fas fa-circle-notch me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $student->status)) }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($student->targetProgram->description)
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Program Description</h5>
                        <p class="text-muted mb-0">{{ $student->targetProgram->description }}</p>
                    </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded p-3">
                                    <i class="fas fa-clock text-primary fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Duration</p>
                                    <h6 class="fw-bold mb-0">{{ $student->targetProgram->duration ?? 'Not specified' }}</h6>
                                </div>
                            </div>
                        </div>

                        @if($student->targetProgram->tuition_fee)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded p-3">
                                    <i class="fas fa-dollar-sign text-success fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Tuition Fee</p>
                                    <h6 class="fw-bold mb-0">
                                        {{ $student->targetProgram->currency ?? '$' }} {{ number_format($student->targetProgram->tuition_fee, 2) }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded p-3">
                                    <i class="fas fa-map-marker-alt text-danger fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Location</p>
                                    <h6 class="fw-bold mb-0">
                                        {{ $student->targetUniversity->city }}, {{ $student->targetUniversity->country }}
                                    </h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded p-3">
                                    <i class="fas fa-graduation-cap text-warning fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Level</p>
                                    <h6 class="fw-bold mb-0 text-capitalize">{{ $student->targetProgram->level }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Information -->
            <div class="card-custom">
                <div class="card-body-custom">
                    <h5 class="fw-bold mb-3">About {{ $student->targetUniversity->name }}</h5>
                    
                    @if($student->targetUniversity->description)
                    <p class="text-muted mb-4">{{ $student->targetUniversity->description }}</p>
                    @endif

                    <div class="d-flex gap-3 flex-wrap">
                        @if($student->targetUniversity->website)
                        <a href="{{ $student->targetUniversity->website }}" 
                           target="_blank" 
                           class="btn btn-primary-custom">
                            <i class="fas fa-globe me-2"></i>Visit Website
                        </a>
                        @endif
                        
                        <a href="{{ route('student.universities') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-university me-2"></i>View All Universities
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Progress Sidebar -->
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-body-custom">
                    <h5 class="fw-bold mb-3">Application Progress</h5>
                    
                    @php
                        $totalItems = $student->checklists->count();
                        $completedItems = $student->checklists->where('status', 'approved')->count();
                        $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
                    @endphp

                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <svg width="120" height="120">
                                <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                <circle cx="60" cy="60" r="50" fill="none" stroke="var(--primary)" stroke-width="8"
                                        stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                                        stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - $percentage / 100) }}"
                                        transform="rotate(-90 60 60)"
                                        style="transition: stroke-dashoffset 0.5s ease;"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h3 class="fw-bold mb-0">{{ $percentage }}%</h3>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Completed Items</span>
                            <span class="fw-semibold">{{ $completedItems }} / {{ $totalItems }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('student.documents') }}" class="btn btn-primary-custom w-100">
                        <i class="fas fa-file-upload me-2"></i>Submit Documents
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card-custom">
                <div class="card-body-custom">
                    <h6 class="fw-bold mb-3">Quick Actions</h6>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-tachometer-alt me-2"></i>View Dashboard
                        </a>
                        <a href="{{ route('student.profile.edit') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('student.faq') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-question-circle me-2"></i>Get Help
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- No Program Assigned -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-graduation-cap text-muted" style="font-size: 64px;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">No Program Assigned Yet</h4>
                    <p class="text-muted mb-4">
                        You don't have a program assigned to your account yet. Please contact your counselor or admissions office for assistance.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('student.universities') }}" class="btn btn-primary-custom">
                            <i class="fas fa-university me-2"></i>Browse Universities
                        </a>
                        <a href="{{ route('student.emergency-contact') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-headset me-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>@endsection

@push('styles')
<style>
    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 12px;
    }

    .bg-primary {
        background-color: var(--primary) !important;
    }

    .text-primary {
        color: var(--primary) !important;
    }

    .progress-bar {
        background-color: var(--primary);
    }
</style>
@endpush
