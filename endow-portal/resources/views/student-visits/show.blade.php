@extends('layouts.admin')

@section('page-title', 'View Student Visit')
@section('breadcrumb', 'Home / Student Visits / View')

@section('content')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('student-visits.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Visits
        </a>
        <div class="btn-group">
            @if(Auth::user()->isAdmin() || $studentVisit->employee_id == Auth::id())
            <a href="{{ route('student-visits.edit', $studentVisit) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form action="{{ route('student-visits.destroy', $studentVisit) }}"
                  method="POST"
                  class="d-inline"
                  id="delete-visit-form">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-danger"
                        onclick="confirmDeleteVisit()">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Student Information -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-user text-danger me-2"></i>Student Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Full Name</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user fa-lg"></i>
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $studentVisit->student_name }}</h4>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Phone Number</label>
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded me-2 p-2">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <a href="tel:{{ $studentVisit->phone }}" class="text-dark fw-semibold text-decoration-none">
                                    {{ $studentVisit->phone }}
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Email Address</label>
                            <div class="d-flex align-items-center">
                                @if($studentVisit->email)
                                <div class="icon-box bg-info bg-opacity-10 text-info rounded me-2 p-2">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <a href="mailto:{{ $studentVisit->email }}" class="text-dark text-decoration-none">
                                    {{ $studentVisit->email }}
                                </a>
                                @else
                                <span class="text-muted">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visit Notes -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-sticky-note text-danger me-2"></i>Visit Notes
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($studentVisit->notes)
                        <div class="notes-content">
                            {!! $studentVisit->notes !!}
                        </div>
                    @else
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="fas fa-file-alt fa-2x mb-3 d-block opacity-25"></i>
                            No notes recorded for this visit
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <!-- Visit Details -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Visit Details
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 pb-3 border-bottom">
                            <div class="small text-muted mb-1">Visit Date & Time</div>
                            <div class="fw-semibold">
                                <i class="fas fa-calendar-alt text-danger me-2"></i>
                                {{ $studentVisit->created_at->format('F d, Y') }}
                            </div>
                            <div class="text-muted small">
                                {{ $studentVisit->created_at->format('h:i A') }}
                            </div>
                        </li>

                        <li class="mb-3 pb-3 border-bottom">
                            <div class="small text-muted mb-1">Assigned Employee</div>
                            @if($studentVisit->employee)
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.7rem;">
                                    {{ strtoupper(substr($studentVisit->employee->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $studentVisit->employee->name }}</div>
                                    <div class="text-muted small">{{ $studentVisit->employee->email }}</div>
                                </div>
                            </div>
                            @else
                            <div class="text-muted">Not Assigned</div>
                            @endif
                        </li>

                        <li class="mb-3 pb-3 border-bottom">
                            <div class="small text-muted mb-1">Last Updated</div>
                            <div class="fw-semibold">
                                {{ $studentVisit->updated_at->format('F d, Y') }}
                            </div>
                            <div class="text-muted small">
                                {{ $studentVisit->updated_at->format('h:i A') }}
                            </div>
                        </li>

                        <li>
                            <div class="small text-muted mb-1">Record ID</div>
                            <div class="fw-semibold font-monospace">#{{ str_pad($studentVisit->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="tel:{{ $studentVisit->phone }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-phone me-2"></i>Call Student
                        </a>
                        @if($studentVisit->email)
                        <a href="mailto:{{ $studentVisit->email }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-envelope me-2"></i>Send Email
                        </a>
                        @endif
                        @if(Auth::user()->isAdmin() || $studentVisit->employee_id == Auth::id())
                        <a href="{{ route('student-visits.edit', $studentVisit) }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit Record
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .notes-content {
        font-size: 15px;
        line-height: 1.8;
        color: #334155;
    }

    .notes-content ul,
    .notes-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .notes-content p {
        margin-bottom: 1rem;
    }

    .notes-content a {
        color: #DC143C;
        text-decoration: underline;
    }

    .notes-content strong {
        font-weight: 600;
    }

    .icon-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmDeleteVisit() {
        Swal.fire({
            title: 'Delete Visit Record?',
            text: 'Are you sure you want to delete this visit record? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC143C',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-visit-form').submit();
            }
        });
    }
</script>
@endpush
