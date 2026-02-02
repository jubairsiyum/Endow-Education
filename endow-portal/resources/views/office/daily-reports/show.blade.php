@extends('layouts.admin')

@section('page-title', 'View Daily Report')
@section('breadcrumb', 'Home / Office / Daily Reports / View')

@section('content')
<link rel="stylesheet" href="{{ asset('css/daily-reports-compact.css') }}">
<div class="container-fluid daily-reports-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="fw-bold mb-2" style="color: #000000;">
                        <i class="fas fa-file-alt me-2" style="color: #DC143C;"></i>
                        Daily Report Details
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock me-1"></i>
                        Submitted on {{ $dailyReport->created_at->format('M d, Y \a\t h:i A') }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $dailyReport)
                        @if(in_array($dailyReport->status, ['draft', 'submitted']))
                        <a href="{{ route('office.daily-reports.edit', $dailyReport) }}" class="btn" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.6rem 1.5rem; border-radius: 0.5rem; font-weight: 600;">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        @endif
                    @endcan
                    <a href="{{ route('office.daily-reports.index') }}" class="btn" style="background-color: #F8F9FA; color: #000000; border: 2px solid #E0E0E0; padding: 0.6rem 1.5rem; border-radius: 0.5rem; font-weight: 600;">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Report Info Card -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                        <div class="card-header border-0 p-4" style="background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%); border-radius: 1rem 1rem 0 0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold" style="color: #FFFFFF;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Report Information
                                </h5>
                                @php
                                    $statusConfig = [
                                        'draft' => ['icon' => 'fa-file', 'color' => '#6c757d', 'bg' => 'rgba(108, 117, 125, 0.1)', 'text' => 'Draft'],
                                        'submitted' => ['icon' => 'fa-paper-plane', 'color' => '#0d6efd', 'bg' => 'rgba(13, 110, 253, 0.1)', 'text' => 'Submitted'],
                                        'pending_review' => ['icon' => 'fa-clock', 'color' => '#ffc107', 'bg' => 'rgba(255, 193, 7, 0.1)', 'text' => 'Pending Review'],
                                        'in_progress' => ['icon' => 'fa-tasks', 'color' => '#17a2b8', 'bg' => 'rgba(23, 162, 184, 0.1)', 'text' => 'In Progress'],
                                        'review' => ['icon' => 'fa-search', 'color' => '#fd7e14', 'bg' => 'rgba(253, 126, 20, 0.1)', 'text' => 'Under Review'],
                                        'approved' => ['icon' => 'fa-check-circle', 'color' => '#28a745', 'bg' => 'rgba(40, 167, 69, 0.1)', 'text' => 'Approved'],
                                        'rejected' => ['icon' => 'fa-times-circle', 'color' => '#dc3545', 'bg' => 'rgba(220, 53, 69, 0.1)', 'text' => 'Rejected'],
                                        'completed' => ['icon' => 'fa-check-double', 'color' => '#198754', 'bg' => 'rgba(25, 135, 84, 0.1)', 'text' => 'Completed'],
                                        'cancelled' => ['icon' => 'fa-ban', 'color' => '#6c757d', 'bg' => 'rgba(108, 117, 125, 0.1)', 'text' => 'Cancelled']
                                    ];
                                    $currentStatus = $statusConfig[$dailyReport->status] ?? $statusConfig['draft'];
                                @endphp
                                <span class="badge px-3 py-2" style="background-color: {{ $currentStatus['bg'] }}; color: {{ $currentStatus['color'] }}; border: 2px solid {{ $currentStatus['color'] }}; font-size: 0.9rem; font-weight: 600;">
                                    <i class="fas {{ $currentStatus['icon'] }} me-2"></i>{{ $currentStatus['text'] }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- Priority Badge -->
                            @if(isset($dailyReport->priority))
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold mb-2">PRIORITY LEVEL</label>
                                <div>
                                    @php
                                        $priorityConfig = [
                                            'urgent' => ['icon' => 'fa-exclamation-circle', 'color' => '#FFFFFF', 'bg' => '#dc3545', 'text' => 'URGENT'],
                                            'high' => ['icon' => 'fa-arrow-up', 'color' => '#FFFFFF', 'bg' => '#fd7e14', 'text' => 'HIGH'],
                                            'normal' => ['icon' => 'fa-equals', 'color' => '#000000', 'bg' => '#E0E0E0', 'text' => 'NORMAL'],
                                            'low' => ['icon' => 'fa-arrow-down', 'color' => '#000000', 'bg' => '#cfe2ff', 'text' => 'LOW']
                                        ];
                                        $priorityBadge = $priorityConfig[$dailyReport->priority] ?? $priorityConfig['normal'];
                                    @endphp
                                    <span class="badge px-3 py-2" style="background-color: {{ $priorityBadge['bg'] }}; color: {{ $priorityBadge['color'] }}; font-size: 0.85rem; font-weight: 700; border-radius: 0.5rem;">
                                        <i class="fas {{ $priorityBadge['icon'] }} me-2"></i>{{ $priorityBadge['text'] }}
                                    </span>
                                </div>
                            </div>
                            @endif

                            <!-- Department -->
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold mb-2">DEPARTMENT</label>
                                <div>
                                    <span class="badge px-3 py-2" style="background-color: rgba(220, 20, 60, 0.1); color: #DC143C; font-size: 0.9rem; border-radius: 0.5rem;">
                                        <i class="fas fa-building me-2"></i>
                                        {{ $dailyReport->department_name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Report Date -->
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold mb-2">REPORT DATE</label>
                                <div class="fw-semibold" style="color: #000000;">
                                    <i class="fas fa-calendar me-2" style="color: #DC143C;"></i>
                                    {{ $dailyReport->report_date->format('l, F d, Y') }}
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold mb-2">TITLE</label>
                                <div class="fw-semibold fs-5" style="color: #000000;">
                                    {{ $dailyReport->title }}
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-0">
                                <label class="text-muted small fw-semibold mb-2">DESCRIPTION</label>
                                <div class="p-4 rounded" style="background-color: #F8F9FA; border-left: 4px solid #DC143C;">
                                    {!! $dailyReport->description !!}
                                </div>
                            </div>

                            <!-- Tags -->
                            @if(isset($dailyReport->tags) && !empty($dailyReport->tags))
                            <div class="mt-4">
                                <label class="text-muted small fw-semibold mb-2">TAGS</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(explode(',', $dailyReport->tags) as $tag)
                                    <span class="badge px-3 py-2" style="background-color: rgba(220, 20, 60, 0.1); color: #DC143C; font-size: 0.85rem; border-radius: 1rem;">
                                        <i class="fas fa-tag me-1"></i>{{ trim($tag) }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Approval/Rejection Actions -->
                    @if($dailyReport->status === 'pending_review' || $dailyReport->status === 'submitted')
                        @can('approve', $dailyReport)
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem; border-left: 4px solid #28a745 !important;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3" style="color: #000000;">
                                    <i class="fas fa-user-check me-2" style="color: #28a745;"></i>
                                    Approval Actions
                                </h5>
                                <p class="text-muted mb-4">Review this report and take appropriate action</p>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <form action="{{ route('office.daily-reports.approve', $dailyReport) }}" method="POST" class="h-100">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-semibold">Approval Comment <span class="text-muted">(Optional)</span></label>
                                                <textarea name="comment" class="form-control" rows="3" placeholder="Add approval comments..." style="border: 2px solid #E0E0E0; border-radius: 0.5rem;"></textarea>
                                            </div>
                                            <button type="submit" class="btn w-100" style="background-color: #28a745; color: #FFFFFF; border: none; padding: 0.75rem; border-radius: 0.5rem; font-weight: 600;">
                                                <i class="fas fa-check-circle me-2"></i>Approve Report
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('office.daily-reports.reject', $dailyReport) }}" method="POST" class="h-100">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-semibold">Rejection Reason <span style="color: #dc3545;">*</span></label>
                                                <textarea name="comment" class="form-control" rows="3" placeholder="Explain why this report is rejected..." required style="border: 2px solid #E0E0E0; border-radius: 0.5rem;"></textarea>
                                            </div>
                                            <button type="submit" class="btn w-100" style="background-color: #dc3545; color: #FFFFFF; border: none; padding: 0.75rem; border-radius: 0.5rem; font-weight: 600;">
                                                <i class="fas fa-times-circle me-2"></i>Reject Report
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan
                    @endif

                    <!-- Comments Section -->
                    @if(isset($dailyReport->comments))
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                        <div class="card-header border-0 p-4" style="background-color: #F8F9FA; border-radius: 1rem 1rem 0 0;">
                            <h5 class="mb-0 fw-bold" style="color: #000000;">
                                <i class="fas fa-comments me-2" style="color: #DC143C;"></i>
                                Comments <span class="badge" style="background-color: #DC143C; color: #FFFFFF;">{{ $dailyReport->comments->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if($dailyReport->comments->count() > 0)
                                @foreach($dailyReport->comments as $comment)
                                <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                                    <div>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px; background-color: #DC143C; color: #FFFFFF;">
                                            {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-0 fw-semibold" style="color: #000000;">{{ $comment->user->name ?? 'Unknown User' }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="p-3 rounded" style="background-color: #F8F9FA; border-left: 3px solid #DC143C;">
                                            {{ $comment->comment }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center mb-0">
                                    <i class="fas fa-inbox me-2"></i>No comments yet
                                </p>
                            @endif

                            <!-- Add Comment Form -->
                            @if(in_array($dailyReport->status, ['submitted', 'pending_review', 'in_progress', 'review']))
                            <form action="{{ route('office.daily-reports.comments', $dailyReport) }}" method="POST" class="mt-4">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Add a Comment</label>
                                    <textarea name="comment" class="form-control mb-3" rows="3" placeholder="Share your thoughts..." required style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"></textarea>
                                </div>
                                <button type="submit" class="btn" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.6rem 1.5rem; border-radius: 0.5rem; font-weight: 600;">
                                    <i class="fas fa-paper-plane me-2"></i>Post Comment
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Attachments Section -->
                    @if(isset($dailyReport->attachments))
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                        <div class="card-header border-0 p-4" style="background-color: #F8F9FA; border-radius: 1rem 1rem 0 0;">
                            <h5 class="mb-0 fw-bold" style="color: #000000;">
                                <i class="fas fa-paperclip me-2" style="color: #DC143C;"></i>
                                Attachments <span class="badge" style="background-color: #DC143C; color: #FFFFFF;">{{ $dailyReport->attachments->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if($dailyReport->attachments->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($dailyReport->attachments as $attachment)
                                    <div class="list-group-item border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="text-center" style="width: 50px;">
                                                <i class="fas fa-file-alt fa-2x" style="color: #DC143C;"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-semibold" style="color: #000000;">{{ $attachment->file_name ?? 'Document' }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-hdd me-1"></i>{{ $attachment->file_size ?? 'N/A' }}
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-clock me-1"></i>{{ $attachment->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="btn btn-sm" style="background-color: rgba(220, 20, 60, 0.1); color: #DC143C; border: none; padding: 0.4rem 1rem; border-radius: 0.5rem; font-weight: 600;">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">
                                    <i class="fas fa-inbox me-2"></i>No attachments
                                </p>
                            @endif

                            <!-- Upload Attachment Form -->
                            @if(in_array($dailyReport->status, ['draft', 'submitted', 'pending_review']))
                                @can('update', $dailyReport)
                                <form action="{{ route('office.daily-reports.attachments', $dailyReport) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Add Attachment</label>
                                        <input type="file" name="attachment" class="form-control mb-3" required style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                                        <small class="text-muted">Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 10MB)</small>
                                    </div>
                                    <button type="submit" class="btn mt-2" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.6rem 1.5rem; border-radius: 0.5rem; font-weight: 600;">
                                        <i class="fas fa-upload me-2"></i>Upload File
                                    </button>
                                </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Review History Section -->
                    @php
                        $reviews = $dailyReport->reviews;
                        // Show reviews if: report is completed OR user is the submitter
                        $canViewReviews = $dailyReport->isCompleted() || $dailyReport->submitted_by === auth()->id() || auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'department_manager', 'office_admin']);
                    @endphp

                    @if($reviews->isNotEmpty() && $canViewReviews)
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header-custom bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-comments text-primary me-2"></i>
                                Review History ({{ $reviews->count() }})
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            @if(!$dailyReport->isCompleted() && $dailyReport->submitted_by === auth()->id())
                            <div class="alert alert-warning border-0 mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <small><strong>Note:</strong> Your manager has provided feedback below. Please review and update your report accordingly. This report is not yet marked as completed.</small>
                            </div>
                            @endif

                            <!-- Timeline Style Reviews -->
                            <div class="review-timeline">
                                @foreach($reviews as $review)
                                <div class="review-item {{ $review->marked_as_completed ? 'review-completed' : 'review-instruction' }} mb-4">
                                    <div class="d-flex gap-3">
                                        <!-- Reviewer Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="user-avatar" style="width: 48px; height: 48px; font-size: 18px;">
                                                @if($review->reviewer->photo_path)
                                                    <img src="{{ asset('storage/' . $review->reviewer->photo_path) }}" alt="{{ $review->reviewer->name }}">
                                                @else
                                                    {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Review Content -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $review->reviewer->name }}</h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $review->reviewed_at->format('M d, Y \a\t h:i A') }}
                                                        <span class="text-muted">•</span>
                                                        {{ $review->reviewed_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                                @if($review->marked_as_completed)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Marked as Completed
                                                </span>
                                                @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-edit me-1"></i>Instructions
                                                </span>
                                                @endif
                                            </div>

                                            @if($review->comment)
                                            <div class="review-comment p-3 rounded {{ $review->marked_as_completed ? 'bg-success-subtle' : 'bg-warning-subtle' }}" style="white-space: pre-wrap; border-left: 3px solid {{ $review->marked_as_completed ? '#198754' : '#ffc107' }};">
                                                {{ $review->comment }}
                                            </div>
                                            @else
                                            <div class="review-comment p-3 rounded bg-light" style="font-style: italic; color: #6c757d;">
                                                No comment provided
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Review Form (for managers/supervisors on submitted reports) -->
                    @can('review', $dailyReport)
                    @if($dailyReport->isAwaitingReview())
                    <div class="card shadow-sm border-0 border-start border-primary border-4">
                        <div class="card-header-custom bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Review This Report
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <div class="alert alert-info border-0 mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <small><strong>Organizational Review:</strong> You are reviewing this report as a supervisor/manager. The employee who submitted this report will receive your feedback.</small>
                            </div>
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
                                    <small class="text-muted">Provide feedback, instructions, or acknowledgment for the submitted report</small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="mark_as_completed" id="mark_as_completed" value="1" {{ old('mark_as_completed') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mark_as_completed">
                                            <strong>Mark as Completed</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Check this to mark the report as completed. If unchecked, your comment will be sent as instructions to the reporting user, and they can continue updating the report.</small>
                                </div>

                                <div class="alert alert-warning border-0 mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <small><strong>Review Guidelines:</strong></small>
                                    <ul class="mb-0 mt-2" style="font-size: 0.85rem;">
                                        <li><strong>Mark as Completed:</strong> Report is finalized and locked from further edits</li>
                                        <li><strong>Don't Mark as Completed:</strong> Employee can continue updating based on your instructions</li>
                                        <li><strong>Note:</strong> You cannot review your own reports - only reports submitted by your team members</li>
                                    </ul>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Submit Review
                                </button>
                            </form>
                        </div>
                    </div>
                    @elseif($dailyReport->isDraft() && $dailyReport->submitted_by === auth()->id())
                    <div class="card shadow-sm border-0 border-start border-warning border-4">
                        <div class="card-header-custom bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-warning me-2"></i>
                                Submit for Review
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <p class="mb-3">This report is in <strong>Draft</strong> status. Once you're ready, submit it for review by your manager/supervisor.</p>
                            <form action="{{ route('office.daily-reports.submit', $dailyReport) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit for Manager Review
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                    @endcan
                    
                    @if($dailyReport->submitted_by === auth()->id() && $dailyReport->isAwaitingReview())
                    <div class="alert alert-info border-0">
                        <i class="fas fa-hourglass-half me-2"></i>
                        <strong>Awaiting Manager Review:</strong> Your report has been submitted and is pending review by your supervisor.
                    </div>
                    @endif
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

    /* Review Timeline Styling */
    .review-timeline {
        position: relative;
    }

    .review-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .review-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 24px;
        top: 60px;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #dee2e6 0%, transparent 100%);
    }

    .review-comment {
        font-size: 14px;
        line-height: 1.6;
    }

    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.15) !important;
    }

    .user-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        border-radius: 50%;
        overflow: hidden;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endsection
