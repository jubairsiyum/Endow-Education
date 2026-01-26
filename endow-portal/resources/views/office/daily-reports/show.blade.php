@extends('layouts.admin')

@section('page-title', 'View Daily Report')
@section('breadcrumb', 'Home / Office / Daily Reports / View')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="page-title mb-2">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Daily Report Details
                    </h2>
                    <p class="text-muted mb-0">Submitted on {{ $dailyReport->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $dailyReport)
                    <a href="{{ route('office.daily-reports.edit', $dailyReport) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    @endcan
                    <a href="{{ route('office.daily-reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Report Info Card -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header-custom bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Report Information
                                </h5>
                                @if($dailyReport->status === 'in_progress')
                                    <span class="badge-custom badge-info-custom">
                                        <i class="fas fa-tasks"></i> In Progress
                                    </span>
                                @elseif($dailyReport->status === 'review')
                                    <span class="badge-custom badge-warning-custom">
                                        <i class="fas fa-clock"></i> In Review
                                    </span>
                                @else
                                    <span class="badge-custom badge-success-custom">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body-custom">
                            <!-- Department -->
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold mb-1">DEPARTMENT</label>
                                <div>
                                    <span class="badge-custom badge-info-custom" style="font-size: 14px;">
                                        <i class="fas fa-building me-1"></i>
                                        {{ $dailyReport->department_name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Report Date -->
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold mb-1">REPORT DATE</label>
                                <div class="fw-semibold">
                                    <i class="fas fa-calendar text-primary me-2"></i>
                                    {{ $dailyReport->report_date->format('l, F d, Y') }}
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold mb-1">TITLE</label>
                                <div class="fw-semibold text-dark fs-5">
                                    {{ $dailyReport->title }}
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-0">
                                <label class="text-muted small fw-semibold mb-2">DESCRIPTION</label>
                                <div class="bg-light p-3 rounded ql-editor-content">
                                    {!! $dailyReport->description !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Section (if reviewed) -->
                    @if($dailyReport->isReviewed() && $dailyReport->review_comment)
                    <div class="card shadow-sm border-0 border-start border-success border-4 mb-4">
                        <div class="card-header-custom bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-comment-dots text-success me-2"></i>
                                Review Comment
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <div class="bg-light p-3 rounded" style="white-space: pre-wrap;">{{ $dailyReport->review_comment }}</div>
                        </div>
                    </div>
                    @endif

                    <!-- Review Form (for admins on pending reports) -->
                    @can('review', $dailyReport)
                    @if($dailyReport->isPending())
                    <div class="card shadow-sm border-0 border-start border-primary border-4">
                        <div class="card-header-custom bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Review This Report
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <form action="{{ route('office.daily-reports.review', $dailyReport) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="review_comment" class="form-label">Review Comment (Optional)</label>
                                    <textarea name="review_comment"
                                              id="review_comment"
                                              rows="4"
                                              class="form-control @error('review_comment') is-invalid @enderror"
                                              placeholder="Add feedback, suggestions, or acknowledgment...">{{ old('review_comment') }}</textarea>
                                    @error('review_comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Provide feedback or acknowledgment for the submitted report</small>
                                </div>

                                <div class="alert alert-info border-0 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>Marking as reviewed will lock the report from further edits by the submitter.</small>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Mark as Reviewed
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                    @endcan
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Submitted By Card -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header-custom bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                Submitted By
                            </h6>
                        </div>
                        <div class="card-body-custom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width: 48px; height: 48px; font-size: 18px;">
                                    @if($dailyReport->submittedBy->photo_path)
                                        <img src="{{ asset('storage/' . $dailyReport->submittedBy->photo_path) }}" alt="{{ $dailyReport->submittedBy->name }}">
                                    @else
                                        {{ strtoupper(substr($dailyReport->submittedBy->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $dailyReport->submittedBy->name }}</div>
                                    <small class="text-muted">{{ $dailyReport->submittedBy->email }}</small>
                                    <div class="mt-1">
                                        <span class="badge-custom badge-secondary-custom">
                                            {{ $dailyReport->submittedBy->roles->first()->name ?? 'User' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviewed By Card (if reviewed) -->
                    @if($dailyReport->isReviewed() && $dailyReport->reviewedBy)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header-custom bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-user-check me-2"></i>
                                Reviewed By
                            </h6>
                        </div>
                        <div class="card-body-custom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width: 48px; height: 48px; font-size: 18px;">
                                    @if($dailyReport->reviewedBy->photo_path)
                                        <img src="{{ asset('storage/' . $dailyReport->reviewedBy->photo_path) }}" alt="{{ $dailyReport->reviewedBy->name }}">
                                    @else
                                        {{ strtoupper(substr($dailyReport->reviewedBy->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $dailyReport->reviewedBy->name }}</div>
                                    <small class="text-muted">{{ $dailyReport->reviewedBy->email }}</small>
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $dailyReport->reviewed_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Timeline Card -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header-custom bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Timeline
                            </h6>
                        </div>
                        <div class="card-body-custom">
                            <div class="mb-3">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-plus-circle text-primary mt-1"></i>
                                    <div>
                                        <small class="text-muted">Created</small>
                                        <div class="fw-semibold">{{ $dailyReport->created_at->format('M d, Y h:i A') }}</div>
                                        <small class="text-muted">{{ $dailyReport->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>

                            @if($dailyReport->updated_at != $dailyReport->created_at)
                            <div class="mb-3">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-edit text-warning mt-1"></i>
                                    <div>
                                        <small class="text-muted">Last Updated</small>
                                        <div class="fw-semibold">{{ $dailyReport->updated_at->format('M d, Y h:i A') }}</div>
                                        <small class="text-muted">{{ $dailyReport->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($dailyReport->reviewed_at)
                            <div class="mb-0">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-check-circle text-success mt-1"></i>
                                    <div>
                                        <small class="text-muted">Reviewed</small>
                                        <div class="fw-semibold">{{ $dailyReport->reviewed_at->format('M d, Y h:i A') }}</div>
                                        <small class="text-muted">{{ $dailyReport->reviewed_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Quill content display styling */
    .ql-editor-content {
        font-size: 15px;
        line-height: 1.7;
    }

    .ql-editor-content p {
        margin-bottom: 1rem;
    }

    .ql-editor-content h1, .ql-editor-content h2, .ql-editor-content h3 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .ql-editor-content ul, .ql-editor-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }

    .ql-editor-content li {
        margin-bottom: 0.5rem;
    }

    .ql-editor-content a {
        color: #0d6efd;
        text-decoration: underline;
    }

    .ql-editor-content strong {
        font-weight: 600;
    }
</style>
@endsection
