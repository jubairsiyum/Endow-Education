@extends('layouts.admin')

@section('page-title', 'View Daily Report')
@section('breadcrumb', 'Home / Office / Daily Reports / View')

@section('content')
<style>
    /* Scoped Daily Report View Styles */
    .dr-view-page {
        padding: 1rem 0;
    }
    
    .dr-view-page .dr-view-header {
        background: #DC143C;
        padding: 1rem;
        border-radius: 4px;
        color: white;
        margin-bottom: 1rem;
        border-bottom: 3px solid #000000;
    }
    
    .dr-view-page .dr-card {
        background: white;
        border-radius: 4px;
        border: 1px solid #e5e7eb;
        margin-bottom: 0.875rem;
        overflow: hidden;
    }
    
    .dr-view-page .dr-card-header {
        background: #f8f9fa;
        padding: 0.75rem 0.875rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .dr-view-page .dr-card-body {
        padding: 0.875rem;
    }
    
    .dr-view-page .dr-label {
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .dr-view-page .dr-value {
        color: #1f2937;
        font-weight: 500;
    }
    
    .dr-view-page .dr-badge {
        padding: 0.25rem 0.625rem;
        border-radius: 3px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .dr-view-page .dr-status-draft { background: #f3f4f6; color: #374151; }
    .dr-view-page .dr-status-submitted { background: #dbeafe; color: #1d4ed8; }
    .dr-view-page .dr-status-review { background: #fef3c7; color: #92400e; }
    .dr-view-page .dr-status-approved { background: #d1fae5; color: #065f46; }
    .dr-view-page .dr-status-rejected { background: #fee2e2; color: #991b1b; }
    .dr-view-page .dr-status-completed { background: #d1fae5; color: #065f46; }
    
    .dr-view-page .dr-priority-urgent { background: #dc3545; color: white; }
    .dr-view-page .dr-priority-high { background: #fd7e14; color: white; }
    .dr-view-page .dr-priority-normal { background: #e5e7eb; color: #374151; }
    .dr-view-page .dr-priority-low { background: #dbeafe; color: #1e40af; }
    
    .dr-view-page .dr-btn {
        padding: 0.5rem 0.875rem;
        border-radius: 3px;
        font-weight: 600;
        font-size: 0.8125rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        text-decoration: none;
        border: none;
        transition: opacity 0.2s;
    }
    
    .dr-view-page .dr-btn:hover {
        opacity: 0.85;
    }
    
    .dr-view-page .dr-btn-primary {
        background: #DC143C;
        color: white;
    }
    
    .dr-view-page .dr-btn-primary:hover {
        background: #000000;
        color: white;
    }
    
    .dr-view-page .dr-btn-success {
        background: #10b981;
        color: white;
    }
    
    .dr-view-page .dr-btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .dr-view-page .dr-btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    
    .dr-view-page .dr-description-box {
        background: #f8f9fa;
        border-left: 3px solid #DC143C;
        padding: 0.75rem;
        line-height: 1.5;
    }
    
    .dr-view-page .dr-comment-item {
        background: #f8f9fa;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        border-left: 3px solid #DC143C;
    }
    
    .dr-view-page .dr-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #DC143C;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    
    .dr-view-page .dr-timeline-item {
        display: flex;
        gap: 0.5rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .dr-view-page .dr-timeline-item:last-child {
        border-bottom: none;
    }
    
    .dr-view-page .dr-timeline-icon {
        width: 24px;
        height: 24px;
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.65rem;
    }
    
    .dr-view-page .dr-input,
    .dr-view-page .dr-textarea {
        border: 1px solid #ced4da;
        border-radius: 3px;
        padding: 0.5rem;
        font-size: 0.8125rem;
        width: 100%;
    }
    
    .dr-view-page .dr-input:focus,
    .dr-view-page .dr-textarea:focus {
        border-color: #DC143C;
        outline: none;
    }
    
    .dr-view-page .dr-approval-card {
        border: 2px solid #DC143C;
        background: #ffffff;
    }
    
    .dr-view-page .dr-attachment-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.875rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }
    
    .dr-view-page .dr-attachment-item:hover {
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    @media (max-width: 768px) {
        .dr-view-page .dr-view-header {
            padding: 0.875rem;
        }
        .dr-view-page .dr-card-body {
            padding: 0.75rem;
        }
    }
</style>

<div class="dr-view-page">
<div class="container-fluid px-2">
    
    <!-- Header -->
    <div class="dr-view-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Daily Report Details</h5>
                <small class="opacity-90">{{ $dailyReport->created_at->format('M d, Y') }}</small>
            </div>
            <div class="d-flex gap-2">
                @can('update', $dailyReport)
                    @if(in_array($dailyReport->status, ['draft', 'submitted']))
                    <a href="{{ route('office.daily-reports.edit', $dailyReport) }}" class="dr-btn dr-btn-primary">
                        <i class="fas fa-edit"></i>Edit
                    </a>
                    @endif
                @endcan
                <a href="{{ route('office.daily-reports.index') }}" class="dr-btn dr-btn-secondary">
                    <i class="fas fa-arrow-left"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            
            <!-- Report Info Card -->
            <div class="dr-card">
                <div class="dr-card-header">
                    <h6 class="mb-0 fw-bold" style="color: #1f2937; font-size: 0.875rem;">
                        <i class="fas fa-file-alt me-1" style="color: #DC143C;"></i>
                        Report Information
                    </h6>
                    @php
                        $statusConfig = [
                            'draft' => ['icon' => 'fa-file', 'class' => 'dr-status-draft', 'text' => 'Draft'],
                            'submitted' => ['icon' => 'fa-paper-plane', 'class' => 'dr-status-submitted', 'text' => 'Submitted'],
                            'pending_review' => ['icon' => 'fa-clock', 'class' => 'dr-status-review', 'text' => 'Pending Review'],
                            'in_progress' => ['icon' => 'fa-tasks', 'class' => 'dr-status-review', 'text' => 'In Progress'],
                            'review' => ['icon' => 'fa-search', 'class' => 'dr-status-review', 'text' => 'Under Review'],
                            'approved' => ['icon' => 'fa-check-circle', 'class' => 'dr-status-approved', 'text' => 'Approved'],
                            'rejected' => ['icon' => 'fa-times-circle', 'class' => 'dr-status-rejected', 'text' => 'Rejected'],
                            'completed' => ['icon' => 'fa-check-double', 'class' => 'dr-status-completed', 'text' => 'Completed'],
                            'cancelled' => ['icon' => 'fa-ban', 'class' => 'dr-status-draft', 'text' => 'Cancelled']
                        ];
                        $currentStatus = $statusConfig[$dailyReport->status] ?? $statusConfig['draft'];
                    @endphp
                    <span class="dr-badge {{ $currentStatus['class'] }}">
                        <i class="fas {{ $currentStatus['icon'] }}"></i>{{ $currentStatus['text'] }}
                    </span>
                </div>
                <div class="dr-card-body">
                    <div class="row g-3">
                        <!-- Department -->
                        <div class="col-md-6">
                            <span class="dr-label">Department</span>
                            <div class="dr-value">
                                <i class="fas fa-building me-2" style="color: #DC143C;"></i>
                                {{ $dailyReport->department_name }}
                            </div>
                        </div>
                        
                        <!-- Report Date -->
                        <div class="col-md-6">
                            <span class="dr-label">Report Date</span>
                            <div class="dr-value">
                                <i class="fas fa-calendar me-2" style="color: #DC143C;"></i>
                                {{ $dailyReport->report_date->format('M d, Y') }}
                            </div>
                        </div>
                        
                        <!-- Tags -->
                        @if(isset($dailyReport->tags) && !empty($dailyReport->tags))
                        <div class="col-md-6">
                            <span class="dr-label">Tags</span>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(explode(',', $dailyReport->tags) as $tag)
                                <span class="badge" style="background: rgba(220, 20, 60, 0.1); color: #DC143C; font-size: 0.75rem; padding: 0.25rem  0.625rem; border-radius: 4px;">
                                    <i class="fas fa-tag me-1"></i>{{ trim($tag) }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Title -->
                        <div class="col-12">
                            <span class="dr-label">Title</span>
                            <div class="dr-value fs-5 fw-bold">
                                {{ $dailyReport->title }}
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="col-12">
                            <span class="dr-label">Description</span>
                            <div class="dr-description-box">
                                {!! $dailyReport->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Assignments Section -->
            @if($dailyReport->workAssignments && $dailyReport->workAssignments->count() > 0)
            <div class="dr-card">
                <div class="dr-card-header">
                    <h6 class="mb-0 fw-bold" style="color: #1f2937; font-size: 0.875rem;">
                        <i class="fas fa-tasks me-1" style="color: #DC143C;"></i>
                        Linked Work Assignments
                    </h6>
                    <span class="dr-badge" style="background: #DC143C; color: white;">{{ $dailyReport->workAssignments->count() }}</span>
                </div>
                <div class="dr-card-body">
                    <div class="work-assignments-list">
                        @foreach($dailyReport->workAssignments as $assignment)
                        <div class="work-assignment-item mb-3 p-3" style="background-color: #f9fafb; border-radius: 6px; border-left: 4px solid {{ $assignment->status === 'completed' ? '#198754' : ($assignment->priority === 'urgent' ? '#dc3545' : ($assignment->priority === 'high' ? '#fd7e14' : '#0d6efd')) }}; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-2 fw-bold text-dark" style="font-size: 0.95rem;">
                                        <i class="fas fa-{{ $assignment->status === 'completed' ? 'check-circle text-success' : 'spinner text-info' }} me-2"></i>
                                        {{ $assignment->title }}
                                    </h6>
                                    <p class="text-muted mb-2" style="font-size: 0.875rem;">{{ Str::limit($assignment->description, 150) }}</p>
                                    
                                    <!-- Task Details -->
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="badge badge-sm" style="background-color: {{ $assignment->priority === 'urgent' ? '#dc3545' : ($assignment->priority === 'high' ? '#fd7e14' : ($assignment->priority === 'normal' ? '#0d6efd' : '#6c757d')) }}; color: #FFFFFF; font-size: 0.7rem;">
                                            <i class="fas fa-flag"></i> {{ strtoupper($assignment->priority) }}
                                        </span>
                                        <span class="badge badge-sm" style="background-color: {{ $assignment->status === 'completed' ? '#198754' : ($assignment->status === 'in_progress' ? '#0dcaf0' : '#ffc107') }}; color: {{ $assignment->status === 'completed' ? '#FFFFFF' : '#000000' }}; font-size: 0.7rem;">
                                            <i class="fas fa-{{ $assignment->status === 'completed' ? 'check-circle' : ($assignment->status === 'in_progress' ? 'spinner' : 'clock') }}"></i> {{ strtoupper(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
                                        @if($assignment->assignedBy)
                                        <small class="text-muted">
                                            <i class="fas fa-user-tie"></i> Assigned by: <strong>{{ $assignment->assignedBy->name }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    
                                    @if($assignment->completed_at)
                                    <div class="mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i><strong>Completed on:</strong> {{ $assignment->completed_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                                
                                @if($assignment->due_date)
                                <div class="text-end ms-3" style="min-width: 90px;">
                                    <small class="text-muted fw-semibold d-block" style="font-size: 0.7rem;">Due Date</small>
                                    <small class="text-dark fw-bold d-block">{{ $assignment->due_date->format('M d, Y') }}</small>
                                    @if($assignment->isOverdue() && $assignment->status !== 'completed')
                                    <span class="badge" style="background-color: #dc3545; color: #FFFFFF; font-size: 0.65rem; margin-top: 0.25rem;">
                                        <i class="fas fa-exclamation-triangle"></i> OVERDUE
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0" style="background-color: #e7f3ff; border-color: #b6d9f7; border-radius: 6px; padding: 0.75rem;">
                        <i class="fas fa-info-circle me-2"></i>
                        <small><strong>Note:</strong> These work assignments were included when this daily report was submitted.</small>
                    </div>
                </div>
            </div>
            @endif

            <!-- Manager Review Section -->
            @if($dailyReport->status === 'pending_review' || $dailyReport->status === 'submitted')
                @can('approve', $dailyReport)
                <div class="dr-approval-card dr-card">
                    <div class="dr-card-header" style="background: #DC143C; border: none;">
                        <h6 class="mb-0 fw-bold" style="color: #FFFFFF; font-size: 0.9rem;">
                            <i class="fas fa-tasks me-2"></i>Review & Decision
                        </h6>
                        <span class="badge bg-white text-danger" style="font-size: 0.7rem; font-weight: 700;">ACTION REQUIRED</span>
                    </div>
                    <div class="dr-card-body">
                        <div class="row g-2">
                            <!-- Approve Option -->
                            <div class="col-md-6">
                                <div class="border p-2 h-100" style="background: #f8f9fa;">
                                    <form action="{{ route('office.daily-reports.approve', $dailyReport) }}" method="POST">
                                        @csrf
                                        <h6 class="mb-2 fw-bold" style="font-size: 0.875rem;">
                                            <i class="fas fa-check-circle text-success me-1"></i>Approve
                                        </h6>
                                        <div class="mb-2">
                                            <label class="form-label mb-1" style="font-size: 0.75rem; color: #6c757d;">Notes (Optional)</label>
                                            <textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Add comments..." style="font-size: 0.8rem;"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold" style="font-size: 0.8rem;">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Reject Option -->
                            <div class="col-md-6">
                                <div class="border border-danger p-2 h-100" style="background: #fff5f5;">
                                    <form action="{{ route('office.daily-reports.reject', $dailyReport) }}" method="POST">
                                        @csrf
                                        <h6 class="mb-2 fw-bold" style="font-size: 0.875rem;">
                                            <i class="fas fa-times-circle text-danger me-1"></i>Reject
                                        </h6>
                                        <div class="mb-2">
                                            <label class="form-label mb-1 text-danger" style="font-size: 0.75rem; font-weight: 600;">Reason <span>*</span></label>
                                            <textarea name="comment" class="form-control form-control-sm border-danger" rows="2" placeholder="Explain required changes..." required style="font-size: 0.8rem;"></textarea>
                                            <small class="text-danger" style="font-size: 0.7rem;">Required</small>
                                        </div>
                                        <button type="submit" class="btn btn-sm w-100 fw-bold" style="background: #DC143C; color: white; font-size: 0.8rem;">
                                            <i class="fas fa-times me-1"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            @endif

            <!-- Comments Section -->
            @if(isset($dailyReport->comments))
            <div class="dr-card">
                <div class="dr-card-header">
                    <h6 class="mb-0 fw-bold" style="color: #1f2937; font-size: 0.875rem;">
                        <i class="fas fa-comments me-1" style="color: #DC143C;"></i>
                        Comments
                    </h6>
                    <span class="dr-badge" style="background: #DC143C; color: white;">{{ $dailyReport->comments->count() }}</span>
                </div>
                <div class="dr-card-body">
                    @if($dailyReport->comments->count() > 0)
                        @foreach($dailyReport->comments as $comment)
                        <div class="d-flex gap-3 mb-3">
                            <div class="dr-user-avatar">
                                {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="mb-0 fw-semibold" style="font-size: 0.9375rem;">{{ $comment->user->name ?? 'Unknown User' }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $comment->created_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="dr-comment-item">
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
                        <label class="dr-label">Add a Comment</label>
                        <textarea name="comment" class="dr-textarea mb-3" rows="3" placeholder="Share your thoughts..." required></textarea>
                        <button type="submit" class="dr-btn dr-btn-primary">
                            <i class="fas fa-paper-plane"></i>Post Comment
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif

            <!-- Attachments Section -->
            @if(isset($dailyReport->attachments))
            <div class="dr-card">
                <div class="dr-card-header">
                    <h6 class="mb-0 fw-bold" style="color: #1f2937; font-size: 0.875rem;">
                        <i class="fas fa-paperclip me-1" style="color: #DC143C;"></i>
                        Attachments
                    </h6>
                    <span class="dr-badge" style="background: #DC143C; color: white;">{{ $dailyReport->attachments->count() }}</span>
                </div>
                <div class="dr-card-body">
                    @if($dailyReport->attachments->count() > 0)
                        @foreach($dailyReport->attachments as $attachment)
                        <div class="dr-attachment-item">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-file-alt fa-2x" style="color: #DC143C;"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold" style="font-size: 0.9rem;">{{ $attachment->file_name ?? 'Document' }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-hdd me-1"></i>{{ $attachment->file_size ?? 'N/A' }} • 
                                        <i class="fas fa-calendar me-1"></i>{{ $attachment->created_at->format('M d, Y') }}
                                    </small>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="dr-btn dr-btn-secondary" style="font-size: 0.8125rem; padding: 0.375rem 0.875rem;">
                                <i class="fas fa-download"></i>Download
                            </a>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center mb-0">
                            <i class="fas fa-inbox me-2"></i>No attachments
                        </p>
                    @endif

                    <!-- Upload Attachment Form -->
                    @if(in_array($dailyReport->status, ['draft', 'submitted', 'pending_review']))
                        @can('update', $dailyReport)
                        <form action="{{ route('office.daily-reports.attachments', $dailyReport) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <label class="dr-label">Add Attachment</label>
                            <input type="file" name="attachment" class="dr-input mb-2" required>
                            <small class="text-muted d-block mb-3">PDF, DOC, XLS, JPG, PNG (Max: 10MB)</small>
                            <button type="submit" class="dr-btn dr-btn-primary">
                                <i class="fas fa-upload"></i>Upload File
                            </button>
                        </form>
                        @endcan
                    @endif
                </div>
            </div>
            @endif

            <!-- Review History Section (Keeping existing functionality) -->
            @php
                $reviews = $dailyReport->reviews;
                $canViewReviews = $dailyReport->isCompleted() || $dailyReport->submitted_by === auth()->id() || auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'department_manager', 'office_admin']);
            @endphp

            @if($reviews->isNotEmpty() && $canViewReviews)
            <div class="dr-card">
                <div class="dr-card-header">
                    <h6 class="mb-0 fw-bold" style="color: #1f2937; font-size: 0.875rem;">
                        <i class="fas fa-history me-1" style="color: #DC143C;"></i>
                        Review History
                    </h6>
                    <span class="dr-badge" style="background: #DC143C; color: white;">{{ $reviews->count() }}</span>
                </div>
                <div class="dr-card-body">
                    @if(!$dailyReport->isCompleted() && $dailyReport->submitted_by === auth()->id())
                    <div class="alert alert-warning border-0 mb-3" style="font-size: 0.875rem;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Your manager has provided feedback below. Please review and update your report accordingly.
                    </div>
                    @endif

                    @foreach($reviews as $review)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex gap-3">
                            <div class="dr-user-avatar">
                                {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $review->reviewer->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $review->reviewed_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                    @if($review->marked_as_completed)
                                    <span class="dr-badge dr-status-completed">
                                        <i class="fas fa-check-circle"></i>Completed
                                    </span>
                                    @else
                                    <span class="dr-badge dr-status-review">
                                        <i class="fas fa-edit"></i>Instructions
                                    </span>
                                    @endif
                                </div>
                                @if($review->comment)
                                <div class="dr-comment-item" style="border-left-color: {{ $review->marked_as_completed ? '#10b981' : '#f59e0b' }}; background: {{ $review->marked_as_completed ? '#f0fdf4' : '#fffbeb' }};">
                                    {{ $review->comment }}
                                </div>
                                @else
                                <p class="text-muted fst-italic mb-0" style="font-size: 0.875rem;">No comment provided</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Draft Submission Form (for report owners only) -->
            @can('review', $dailyReport)
            @if($dailyReport->isDraft() && $dailyReport->submitted_by === auth()->id())
            <div class="dr-card" style="border: 2px solid #f59e0b;">
                <div class="dr-card-header" style="background: #fffbeb; border-color: #fcd34d;">
                    <h6 class="mb-0 fw-bold" style="color: #92400e;">
                        <i class="fas fa-info-circle me-2"></i>
                        Submit for Review
                    </h6>
                </div>
                <div class="dr-card-body">
                    <p class="mb-3" style="font-size: 0.9rem;">This report is in <strong>Draft</strong> status. Submit it for manager review when ready.</p>
                    <form action="{{ route('office.daily-reports.submit', $dailyReport) }}" method="POST">
                        @csrf
                        <button type="submit" class="dr-btn dr-btn-primary">
                            <i class="fas fa-paper-plane"></i>Submit for Manager Review
                        </button>
                    </form>
                </div>
            </div>
            @endif
            @endcan
            
            @if($dailyReport->submitted_by === auth()->id() && $dailyReport->isAwaitingReview())
            <div class="alert alert-info border-0" style="font-size: 0.875rem;">
                <i class="fas fa-hourglass-half me-2"></i>
                <strong>Awaiting Manager Review:</strong> Your report has been submitted and is pending review by your supervisor.
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Submitted By Card -->
            <div class="dr-card">
                <div class="dr-card-header">
                    <h6 class="mb-0 fw-semibold" style="color: #1f2937; font-size: 0.875rem;">
                        <i class="fas fa-user me-1"></i>Submitted By
                    </h6>
                </div>
                <div class="dr-card-body">
                    <div class="d-flex align-items-center gap-2">
                        <div class="dr-user-avatar">
                            {{ strtoupper(substr($dailyReport->submittedBy->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size: 0.875rem;">{{ $dailyReport->submittedBy->name }}</div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $dailyReport->submittedBy->email }}</small>
                            <span class="badge" style="background: #f8f9fa; color: #DC143C; border: 1px solid #DC143C; font-size: 0.7rem;">
                                {{ $dailyReport->submittedBy->roles->first()->name ?? 'User' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviewed By Card (if reviewed) -->
            @if($dailyReport->isReviewed() && $dailyReport->reviewedBy)
            <div class="dr-card">
                <div class="dr-card-header">
                    <h6 class="mb-0 fw-semibold" style="color: #1f2937; font-size: 0.875rem;">
                        <i class="fas fa-user-check me-1"></i>Reviewed By
                    </h6>
                </div>
                <div class="dr-card-body">
                    <div class="d-flex align-items-center gap-2">
                        <div class="dr-user-avatar">
                            {{ strtoupper(substr($dailyReport->reviewedBy->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size: 0.875rem;">{{ $dailyReport->reviewedBy->name }}</div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $dailyReport->reviewedBy->email }}</small>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                {{ $dailyReport->reviewed_at->format('M d, Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline Card -->
            <div class="dr-card">
                <div class="dr-card-header">
                    <h6 class="mb-0 fw-semibold" style="color: #1f2937; font-size: 0.875rem;">
                        <i class="fas fa-history me-1"></i>Timeline
                    </h6>
                </div>
                <div class="dr-card-body">
                    <div class="dr-timeline-item">
                        <div class="dr-timeline-icon" style="background: #dbeafe; color: #1d4ed8;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Created</small>
                            <div class="fw-semibold" style="font-size: 0.875rem;">{{ $dailyReport->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>

                    @if($dailyReport->updated_at != $dailyReport->created_at)
                    <div class="dr-timeline-item">
                        <div class="dr-timeline-icon" style="background: #fef3c7; color: #92400e;">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Last Updated</small>
                            <div class="fw-semibold" style="font-size: 0.875rem;">{{ $dailyReport->updated_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($dailyReport->reviewed_at)
                    <div class="dr-timeline-item">
                        <div class="dr-timeline-icon" style="background: #d1fae5; color: #065f46;">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Reviewed</small>
                            <div class="fw-semibold" style="font-size: 0.875rem;">{{ $dailyReport->reviewed_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
