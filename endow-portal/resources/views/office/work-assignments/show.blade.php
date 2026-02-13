@extends('layouts.admin')

@section('page-title', 'Assignment Details')
@section('breadcrumb', 'Home / Office / Work Assignments / Details')

@section('content')
<style>
    /* Professional Assignment Details Page Styles - Red & Black Theme */
    .wa-details-container {
        padding: 0.5rem;
        max-width: 1600px;
        margin: 0 auto;
    }
    
    .wa-details-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
        padding: 1.5rem;
        border-radius: 12px;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(220, 20, 60, 0.15);
        border-bottom: 3px solid #DC143C;
    }
    
    .wa-alert-status {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
        font-size: 0.9rem;
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .wa-alert-overdue {
        background-color: #ffe5e5;
        border-left-color: #dc3545;
    }
    
    .wa-alert-due-soon {
        background-color: #fff3cd;
        border-left-color: #ffc107;
    }
    
    .wa-detail-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    
    .wa-detail-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1rem 1.25rem;
        border-bottom: 2px solid #DC143C;
    }
    
    .wa-detail-card-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .wa-detail-card-body {
        padding: 1.25rem;
    }
    
    .wa-info-row {
        display: flex;
        flex-direction: column;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .wa-info-row:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .wa-info-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.4rem;
    }
    
    .wa-info-value {
        font-size: 0.9rem;
        color: #1a1a1a;
        font-weight: 600;
    }
    
    .wa-user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .wa-user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .wa-user-details {
        flex: 1;
    }
    
    .wa-user-name {
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.15rem;
    }
    
    .wa-user-email {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .wa-badge-pro {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    
    .wa-description-box {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 6px;
        border-left: 3px solid #DC143C;
        white-space: pre-wrap;
        font-size: 0.9rem;
        line-height: 1.6;
    }
    
    .wa-notes-box {
        background-color: #fffbf0;
        padding: 1rem;
        border-radius: 6px;
        border-left: 3px solid #ffc107;
        margin-bottom: 1rem;
    }
    
    .wa-notes-box:last-child {
        margin-bottom: 0;
    }
    
    .wa-notes-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: #856404;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }
    
    .wa-notes-content {
        font-size: 0.875rem;
        color: #1a1a1a;
        white-space: pre-wrap;
        line-height: 1.5;
    }
    
    .wa-form-control-pro {
        border: 1.5px solid #ced4da;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .wa-form-control-pro:focus {
        border-color: #DC143C;
        box-shadow: 0 0 0 0.2rem rgba(220, 19, 60, 0.15);
        outline: none;
    }
    
    .wa-form-label-pro {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.4rem;
        display: block;
    }
    
    .wa-btn-pro {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.2s;
        border: none;
    }
    
    .wa-btn-pro:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .wa-action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
</style>

<div class="container-fluid wa-details-container">
    <!-- Professional Header -->
    <div class="wa-details-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-clipboard-list fa-lg"></i>
                    <h5 class="mb-0 fw-bold">{{ $workAssignment->title }}</h5>
                </div>
                <p class="mb-0 opacity-90" style="font-size: 0.875rem;">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Assigned on {{ $workAssignment->assigned_date->format('F d, Y') }}
                    @if($workAssignment->due_date)
                        <span class="mx-2">•</span>
                        <i class="fas fa-clock me-1"></i>
                        Due {{ $workAssignment->due_date->format('F d, Y') }}
                    @endif
                </p>
            </div>
            <div class="wa-action-buttons">
                <a href="{{ route('office.work-assignments.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
                @can('update', $workAssignment)
                <a href="{{ route('office.work-assignments.edit', $workAssignment) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                @endcan
                @can('delete', $workAssignment)
                <form action="{{ route('office.work-assignments.destroy', $workAssignment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if($workAssignment->isOverdue())
    <div class="wa-alert-status wa-alert-overdue">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-exclamation-triangle fa-2x" style="color: #dc3545;"></i>
            <div>
                <div class="fw-bold mb-1" style="color: #dc3545; font-size: 1rem;">
                    <i class="fas fa-exclamation-circle me-1"></i>This Task is Overdue!
                </div>
                <p class="mb-0" style="color: #721c24;">
                    The due date was {{ $workAssignment->due_date->format('F d, Y') }} 
                    ({{ $workAssignment->due_date->diffForHumans() }})
                </p>
            </div>
        </div>
    </div>
    @elseif($workAssignment->isDueSoon())
    <div class="wa-alert-status wa-alert-due-soon">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-clock fa-2x" style="color: #ffc107;"></i>
            <div>
                <div class="fw-bold mb-1" style="color: #856404; font-size: 1rem;">
                    <i class="fas fa-hourglass-half me-1"></i>Deadline Approaching
                </div>
                <p class="mb-0" style="color: #856404;">
                    This task is due on {{ $workAssignment->due_date->format('F d, Y') }} 
                    ({{ $workAssignment->due_date->diffForHumans() }})
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Left Column - Primary Information -->
        <div class="col-lg-8">
            <!-- Task Overview Card -->
            <div class="wa-detail-card">
                <div class="wa-detail-card-header">
                    <h6 class="wa-detail-card-title">
                        <i class="fas fa-info-circle" style="color: #DC143C;"></i>
                        Task Overview
                    </h6>
                </div>
                <div class="wa-detail-card-body">
                    <div class="row g-3">
                        <!-- Assigned To -->
                        <div class="col-md-6">
                            <div class="wa-info-row">
                                <div class="wa-info-label">
                                    <i class="fas fa-user-check me-1"></i>Assigned To
                                </div>
                                <div class="wa-info-value">
                                    <div class="wa-user-info">
                                        <div class="wa-user-avatar" style="background: linear-gradient(135deg, #DC143C 0%, #a00f2a 100%); color: white;">
                                            {{ strtoupper(substr($workAssignment->assignedTo->name, 0, 1)) }}
                                        </div>
                                        <div class="wa-user-details">
                                            <div class="wa-user-name">{{ $workAssignment->assignedTo->name }}</div>
                                            <div class="wa-user-email">
                                                <i class="fas fa-envelope me-1"></i>{{ $workAssignment->assignedTo->email }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned By -->
                        <div class="col-md-6">
                            <div class="wa-info-row">
                                <div class="wa-info-label">
                                    <i class="fas fa-user-tie me-1"></i>Assigned By
                                </div>
                                <div class="wa-info-value">
                                    <div class="wa-user-info">
                                        <div class="wa-user-avatar" style="background: linear-gradient(135deg, #DC143C 0%, #1a1a1a 100%); color: white;">
                                            {{ strtoupper(substr($workAssignment->assignedBy->name, 0, 1)) }}
                                        </div>
                                        <div class="wa-user-details">
                                            <div class="wa-user-name">{{ $workAssignment->assignedBy->name }}</div>
                                            <div class="wa-user-email">
                                                <i class="fas fa-envelope me-1"></i>{{ $workAssignment->assignedBy->email }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-3 col-sm-6">
                            <div class="wa-info-row">
                                <div class="wa-info-label">
                                    <i class="fas fa-flag me-1"></i>Priority Level
                                </div>
                                <div class="wa-info-value">
                                    @php
                                        $priorityConfig = [
                                            'urgent' => ['bg' => '#dc3545', 'color' => '#ffffff', 'icon' => 'fa-exclamation-circle'],
                                            'high' => ['bg' => '#fd7e14', 'color' => '#ffffff', 'icon' => 'fa-arrow-up'],
                                            'normal' => ['bg' => '#0d6efd', 'color' => '#ffffff', 'icon' => 'fa-minus'],
                                            'low' => ['bg' => '#6c757d', 'color' => '#ffffff', 'icon' => 'fa-arrow-down'],
                                        ];
                                        $pConfig = $priorityConfig[$workAssignment->priority] ?? $priorityConfig['normal'];
                                    @endphp
                                    <span class="wa-badge-pro" style="background-color: {{ $pConfig['bg'] }}; color: {{ $pConfig['color'] }};">
                                        <i class="fas {{ $pConfig['icon'] }} me-1"></i>{{ strtoupper($workAssignment->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-3 col-sm-6">
                            <div class="wa-info-row">
                                <div class="wa-info-label">
                                    <i class="fas fa-tasks me-1"></i>Current Status
                                </div>
                                <div class="wa-info-value">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['bg' => '#ffc107', 'color' => '#000000', 'icon' => 'fa-clock'],
                                            'in_progress' => ['bg' => '#0dcaf0', 'color' => '#000000', 'icon' => 'fa-spinner'],
                                            'completed' => ['bg' => '#198754', 'color' => '#ffffff', 'icon' => 'fa-check-circle'],
                                            'on_hold' => ['bg' => '#6c757d', 'color' => '#ffffff', 'icon' => 'fa-pause'],
                                            'cancelled' => ['bg' => '#dc3545', 'color' => '#ffffff', 'icon' => 'fa-times-circle'],
                                        ];
                                        $sConfig = $statusConfig[$workAssignment->status] ?? $statusConfig['pending'];
                                    @endphp
                                    <span class="wa-badge-pro" style="background-color: {{ $sConfig['bg'] }}; color: {{ $sConfig['color'] }};">
                                        <i class="fas {{ $sConfig['icon'] }} me-1"></i>{{ strtoupper(str_replace('_', ' ', $workAssignment->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="col-md-3 col-sm-6">
                            <div class="wa-info-row">
                                <div class="wa-info-label">
                                    <i class="fas fa-building me-1"></i>Department
                                </div>
                                <div class="wa-info-value">
                                    @if($workAssignment->department)
                                        <span class="wa-badge-pro" style="background: linear-gradient(135deg, #1a1a1a 0%, #1E293B 100%); color: #ffffff; font-weight: 700;">
                                            <i class="fas fa-sitemap me-1"></i>{{ $workAssignment->department->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Completion Status -->
                        @if($workAssignment->completed_at)
                        <div class="col-md-3 col-sm-6">
                            <div class="wa-info-row">
                                <div class="wa-info-label">
                                    <i class="fas fa-check-double me-1"></i>Completed On
                                </div>
                                <div class="wa-info-value">
                                    <span class="text-success">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        {{ $workAssignment->completed_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Task Description Card -->
            <div class="wa-detail-card">
                <div class="wa-detail-card-header">
                    <h6 class="wa-detail-card-title">
                        <i class="fas fa-align-left" style="color: #DC143C;"></i>
                        Task Description
                    </h6>
                </div>
                <div class="wa-detail-card-body">
                    <div class="wa-description-box">
                        {{ $workAssignment->description }}
                    </div>
                </div>
            </div>

            <!-- Notes & Feedback Card -->
            @if($workAssignment->employee_notes || $workAssignment->completion_notes || $workAssignment->manager_feedback)
            <div class="wa-detail-card">
                <div class="wa-detail-card-header">
                    <h6 class="wa-detail-card-title">
                        <i class="fas fa-sticky-note" style="color: #DC143C;"></i>
                        Notes & Feedback
                    </h6>
                </div>
                <div class="wa-detail-card-body">
                    @if($workAssignment->employee_notes)
                    <div class="wa-notes-box">
                        <div class="wa-notes-title">
                            <i class="fas fa-user me-1"></i>Employee Notes
                        </div>
                        <div class="wa-notes-content">{{ $workAssignment->employee_notes }}</div>
                    </div>
                    @endif

                    @if($workAssignment->completion_notes)
                    <div class="wa-notes-box">
                        <div class="wa-notes-title">
                            <i class="fas fa-check-circle me-1"></i>Completion Notes
                        </div>
                        <div class="wa-notes-content">{{ $workAssignment->completion_notes }}</div>
                    </div>
                    @endif

                    @if($workAssignment->manager_feedback)
                    <div class="wa-notes-box" style="background-color: #e7f3ff; border-left-color: #0d6efd;">
                        <div class="wa-notes-title" style="color: #084298;">
                            <i class="fas fa-comments me-1"></i>Manager Feedback
                        </div>
                        <div class="wa-notes-content">{{ $workAssignment->manager_feedback }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Actions & Additional Info -->
        <div class="col-lg-4">
            <!-- Status Update Form -->
            @can('updateStatus', $workAssignment)
            @if(!$workAssignment->isCompleted() && $workAssignment->status !== 'cancelled')
            <div class="wa-detail-card">
                <div class="wa-detail-card-header">
                    <h6 class="wa-detail-card-title">
                        <i class="fas fa-sync-alt" style="color: #DC143C;"></i>
                        Update Status
                    </h6>
                </div>
                <div class="wa-detail-card-body">
                    <form action="{{ route('office.work-assignments.update-status', $workAssignment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="wa-form-label-pro">
                                <i class="fas fa-list me-1"></i>New Status
                            </label>
                            <select name="status" class="form-select wa-form-control-pro" required>
                                <option value="pending" {{ $workAssignment->status == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="in_progress" {{ $workAssignment->status == 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="completed" {{ $workAssignment->status == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                                <option value="on_hold" {{ $workAssignment->status == 'on_hold' ? 'selected' : '' }}>
                                    On Hold
                                </option>
                                <option value="cancelled" {{ $workAssignment->status == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="wa-form-label-pro">
                                <i class="fas fa-comment me-1"></i>Status Notes (Optional)
                            </label>
                            <textarea name="notes" class="form-control wa-form-control-pro" rows="3" 
                                      placeholder="Add any notes about this status change..."></textarea>
                        </div>
                        <button type="submit" class="btn wa-btn-pro w-100" style="background-color: #DC143C; color: #ffffff;">
                            <i class="fas fa-check-circle me-1"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif
            @endcan

            <!-- Employee Notes Form -->
            @if($workAssignment->assigned_to == auth()->id() && auth()->user()->can('addNotes', $workAssignment))
            <div class="wa-detail-card">
                <div class="wa-detail-card-header">
                    <h6 class="wa-detail-card-title">
                        <i class="fas fa-pen" style="color: #DC143C;"></i>
                        Your Notes
                    </h6>
                </div>
                <div class="wa-detail-card-body">
                    <form action="{{ route('office.work-assignments.add-notes', $workAssignment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="wa-form-label-pro">
                                <i class="fas fa-sticky-note me-1"></i>Task Notes
                            </label>
                            <textarea name="employee_notes" class="form-control wa-form-control-pro" rows="4" 
                                      placeholder="Add or update your notes about this task..." 
                                      required>{{ $workAssignment->employee_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn wa-btn-pro w-100" style="background-color: #0dcaf0; color: #000000;">
                            <i class="fas fa-save me-1"></i>Save Notes
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Manager Feedback Form -->
            @if($workAssignment->assigned_by == auth()->id() && auth()->user()->can('update', $workAssignment))
            <div class="wa-detail-card">
                <div class="wa-detail-card-header">
                    <h6 class="wa-detail-card-title">
                        <i class="fas fa-comment-dots" style="color: #DC143C;"></i>
                        Provide Feedback
                    </h6>
                </div>
                <div class="wa-detail-card-body">
                    <form action="{{ route('office.work-assignments.add-feedback', $workAssignment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="wa-form-label-pro">
                                <i class="fas fa-comments me-1"></i>Manager Feedback
                            </label>
                            <textarea name="manager_feedback" class="form-control wa-form-control-pro" rows="4" 
                                      placeholder="Provide feedback to the employee..." 
                                      required>{{ $workAssignment->manager_feedback }}</textarea>
                        </div>
                        <button type="submit" class="btn wa-btn-pro w-100" style="background-color: #198754; color: #ffffff;">
                            <i class="fas fa-paper-plane me-1"></i>Send Feedback
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Timeline Info Card -->
            <div class="wa-detail-card">
                <div class="wa-detail-card-header">
                    <h6 class="wa-detail-card-title">
                        <i class="fas fa-history" style="color: #DC143C;"></i>
                        Timeline
                    </h6>
                </div>
                <div class="wa-detail-card-body">
                    <div class="wa-info-row">
                        <div class="wa-info-label">
                            <i class="fas fa-calendar-plus me-1"></i>Created
                        </div>
                        <div class="wa-info-value">
                            {{ $workAssignment->assigned_date->format('M d, Y') }}
                            <small class="text-muted d-block">{{ $workAssignment->assigned_date->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    @if($workAssignment->due_date)
                    <div class="wa-info-row">
                        <div class="wa-info-label">
                            <i class="fas fa-calendar-check me-1"></i>Due Date
                        </div>
                        <div class="wa-info-value">
                            {{ $workAssignment->due_date->format('M d, Y') }}
                            <small class="text-muted d-block">{{ $workAssignment->due_date->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endif
                    
                    <div class="wa-info-row">
                        <div class="wa-info-label">
                            <i class="fas fa-clock me-1"></i>Last Updated
                        </div>
                        <div class="wa-info-value">
                            {{ $workAssignment->updated_at->format('M d, Y H:i') }}
                            <small class="text-muted d-block">{{ $workAssignment->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    @if($workAssignment->completed_at)
                    <div class="wa-info-row">
                        <div class="wa-info-label">
                            <i class="fas fa-check-double me-1"></i>Completed
                        </div>
                        <div class="wa-info-value text-success">
                            {{ $workAssignment->completed_at->format('M d, Y H:i') }}
                            <small class="text-muted d-block">{{ $workAssignment->completed_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Linked Daily Report -->
            @if($workAssignment->dailyReport)
            <div class="wa-detail-card">
                <div class="wa-detail-card-header">
                    <h6 class="wa-detail-card-title">
                        <i class="fas fa-link" style="color: #DC143C;"></i>
                        Linked Report
                    </h6>
                </div>
                <div class="wa-detail-card-body">
                    <p class="small text-muted mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        This task is included in a daily report
                    </p>
                    <a href="{{ route('office.daily-reports.show', $workAssignment->dailyReport) }}" 
                       class="btn wa-btn-pro w-100" style="background: linear-gradient(135deg, #1a1a1a 0%, #1E293B 100%); color: #ffffff; font-weight: 700; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                        <i class="fas fa-file-alt me-1"></i>View Daily Report
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
