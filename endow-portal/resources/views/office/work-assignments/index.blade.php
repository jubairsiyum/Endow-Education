@extends('layouts.admin')

@section('page-title', 'Work Assignments')
@section('breadcrumb', 'Home / Office / Work Assignments')

@section('content')
<style>
    /**
     * WORK ASSIGNMENT INDEX PAGE - DESIGN SYSTEM
     * ============================================
     * 
     * Design Methodology: Solid, structured, and role-based interface
     * 
     * Key Features:
     * 1. ROLE-BASED ACCESS: Statistics and data dynamically filtered by user role
     *    - Super Admin/Admin: View all assignments across organization
     *    - Department Manager: View department assignments and own tasks
     *    - Employee: View only assigned tasks
     * 
     * 2. DAILY REPORT INTEGRATION: Seamless integration with daily reporting system
     *    - Assignments linked to daily reports via daily_report_id field
     *    - included_in_report flag tracks report inclusion status
     *    - Visual indicators show which assignments are documented in reports
     *    - Pending/In-progress assignments available for report inclusion
     * 
     * 3. DESIGN PRINCIPLES:
     *    - Solid colors (no excessive gradients)
     *    - Minimal hover effects (subtle opacity changes only)
     *    - Clear visual hierarchy with consistent spacing
     *    - Professional color scheme: Red (#DC143C) and Black (#1a1a1a)
     *    - Clean borders and structured layouts
     */
    
    /* Container & Layout */
    .wa-container {
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
    }
    
    .wa-header {
        background: #1a1a1a;
        padding: 2rem 2.5rem;
        border-radius: 8px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 5px solid #DC143C;
    }
    
    .wa-stat-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        border: 2px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .wa-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--card-color);
    }
    
    .wa-stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0.75rem 0;
        color: #1a1a1a;
        line-height: 1;
        letter-spacing: -1px;
    }
    
    .wa-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748B;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .wa-stat-icon {
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 3rem;
        opacity: 0.08;
    }
    
    .wa-filter-card {
        background: white;
        border-radius: 8px;
        padding: 1.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        border: 2px solid #e9ecef;
    }
    
    .wa-filter-card .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .wa-filter-card .form-select,
    .wa-filter-card .form-control {
        border: 2px solid #dee2e6;
        border-radius: 4px;
        padding: 0.6rem 0.9rem;
        font-size: 0.875rem;
        background-color: white;
    }
    
    .wa-filter-card .form-select:hover,
    .wa-filter-card .form-control:hover {
        border-color: #adb5bd;
    }
    
    .wa-filter-card .form-select:focus,
    .wa-filter-card .form-control:focus {
        border-color: #DC143C;
        box-shadow: 0 0 0 0.15rem rgba(220, 20, 60, 0.1);
        background-color: #ffffff;
    }
    
    .wa-table-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 2px solid #e9ecef;
    }
    
    .wa-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 0.875rem;
    }
    
    .wa-table thead {
        background: #1a1a1a;
        color: white;
    }
    
    .wa-table thead th {
        padding: 1rem 1.25rem;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        white-space: nowrap;
    }
    
    .wa-table tbody tr {
        border-bottom: 1px solid #f1f3f5;
    }
    
    .wa-table tbody td {
        padding: 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .wa-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .wa-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .wa-badge {
        display: inline-block;
        padding: 0.4rem 0.9rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        border-radius: 6px;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    
    .wa-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin-right: 0.75rem;
        border: 2px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .wa-action-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 4px;
        border: none;
        font-weight: 600;
    }
    
    .wa-action-btn:hover {
        opacity: 0.9;
    }
    
    .wa-overdue {
        color: #DC143C;
        font-weight: 700;
        animation: pulse 2s infinite;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .wa-due-soon {
        color: #fd7e14;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    @keyframes pulse {
        0%, 100% { 
            opacity: 1;
        }
        50% { 
            opacity: 0.5;
        }
    }
    
    .wa-empty-state {
        padding: 5rem 2rem;
        text-align: center;
        color: #64748B;
    }
    
    .wa-empty-state i {
        color: #e9ecef;
        font-size: 4rem;
        margin-bottom: 1.5rem;
    }
    
    .wa-empty-state h5 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: #475569;
    }
    
    .wa-empty-state p {
        font-size: 0.95rem;
        color: #94a3b8;
    }
    
    .wa-pagination {
        background: #f8f9fa;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #e9ecef;
    }
    
    .btn-primary-custom {
        background: #DC143C;
        color: white;
        border: none;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 4px;
    }
    
    .btn-primary-custom:hover {
        background: #a00f2d;
        color: white;
    }
    
    .btn-secondary-custom {
        background: #1a1a1a;
        color: white;
        border: none;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 4px;
    }
    
    .btn-secondary-custom:hover {
        background: #2d2d2d;
        color: white;
    }
    
    .btn-light-custom {
        background: white;
        color: #1a1a1a;
        border: 2px solid #dee2e6;
        font-weight: 600;
        padding: 0.55rem 1.4rem;
        border-radius: 4px;
    }
    
    .btn-light-custom:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
        color: #1a1a1a;
    }
    
    @media (max-width: 768px) {
        .wa-container {
            padding: 1rem;
        }
        
        .wa-header {
            padding: 1.5rem;
        }
        
        .wa-header h4 {
            font-size: 1.35rem;
        }
        
        .wa-stat-number {
            font-size: 2rem;
        }
        
        .wa-filter-card {
            padding: 1.25rem;
        }
        
        .wa-table thead th,
        .wa-table tbody td {
            padding: 0.75rem;
            font-size: 0.8rem;
        }
        
        .wa-badge {
            font-size: 0.65rem;
            padding: 0.35rem 0.7rem;
        }
        
        .wa-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }
        
        .wa-action-btn {
            padding: 0.4rem 0.6rem;
        }
    }
    
    /* Daily Report Integration Badge */
    .wa-report-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .wa-report-badge i {
        font-size: 0.65rem;
    }
    
    /* Scrollbar Styling - Branded */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f3f5;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #DC143C;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a00f2d;
    }
</style>

<div class="container-fluid wa-container">
    <!-- Professional Header -->
    <div class="wa-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-2">
                    <i class="fas fa-tasks me-2"></i>
                    Work Assignments Management
                </h4>
                <p class="mb-0">
                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
                        <i class="fas fa-shield-alt me-1"></i> Admin View: All assignments across all departments
                    @elseif(auth()->user()->hasRole('department_manager') || auth()->user()->isManagerOfAnyDepartment())
                        <i class="fas fa-users-cog me-1"></i> Manager View: Assignments in your departments
                    @else
                        <i class="fas fa-user-check me-1"></i> Employee View: Your assigned tasks
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('office.work-assignments.my-assignments') }}" class="btn btn-light-custom btn-sm">
                    <i class="fas fa-user me-2"></i>My Tasks
                </a>
                @can('create', App\Models\WorkAssignment::class)
                <a href="{{ route('office.work-assignments.create') }}" class="btn btn-primary-custom btn-sm">
                    <i class="fas fa-plus-circle me-2"></i>Create New Task
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Daily Report Integration Info -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border-left: 4px solid #0c5460!important;">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle me-3" style="font-size: 1.5rem; color: #0c5460; margin-top: 0.25rem;"></i>
                    <div>
                        <h6 class="mb-2 fw-bold" style="color: #0c5460;">
                            <i class="fas fa-link me-2"></i>Daily Report Integration
                        </h6>
                        <p class="mb-0 small" style="color: #0a4f5c; line-height: 1.6;">
                            <strong>Work assignments are automatically integrated with daily reports.</strong> When assigned users create their daily reports, 
                            pending and in-progress tasks are available for inclusion. Completed assignments in reports help track productivity 
                            and maintain comprehensive work documentation. The <strong>"Report Status"</strong> column indicates which assignments 
                            have been included in daily reports.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-Based Statistics Dashboard -->
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="wa-stat-card" style="--card-color: #1a1a1a;">
                <div class="wa-stat-label">Total Tasks</div>
                <div class="wa-stat-number">{{ $statistics['total'] }}</div>
                <i class="fas fa-tasks wa-stat-icon" style="color: #1a1a1a;"></i>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="wa-stat-card" style="--card-color: #ffc107;">
                <div class="wa-stat-label">Pending</div>
                <div class="wa-stat-number">{{ $statistics['pending'] }}</div>
                <i class="fas fa-clock wa-stat-icon" style="color: #ffc107;"></i>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="wa-stat-card" style="--card-color: #0d6efd;">
                <div class="wa-stat-label">In Progress</div>
                <div class="wa-stat-number">{{ $statistics['in_progress'] }}</div>
                <i class="fas fa-spinner wa-stat-icon" style="color: #0d6efd;"></i>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="wa-stat-card" style="--card-color: #198754;">
                <div class="wa-stat-label">Completed</div>
                <div class="wa-stat-number">{{ $statistics['completed'] }}</div>
                <i class="fas fa-check-circle wa-stat-icon" style="color: #198754;"></i>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="wa-stat-card" style="--card-color: #DC143C;">
                <div class="wa-stat-label">Overdue</div>
                <div class="wa-stat-number">{{ $statistics['overdue'] }}</div>
                <i class="fas fa-exclamation-triangle wa-stat-icon" style="color: #DC143C;"></i>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="wa-stat-card" style="--card-color: #6c757d;">
                <div class="wa-stat-label">On Hold</div>
                <div class="wa-stat-number">{{ $statistics['on_hold'] ?? 0 }}</div>
                <i class="fas fa-pause-circle wa-stat-icon" style="color: #6c757d;"></i>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="wa-filter-card">
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-filter me-2" style="color: #DC143C; font-size: 1.25rem;"></i>
            <h5 class="mb-0 fw-bold" style="color: #1a1a1a;">Filter Assignments</h5>
        </div>
        <form method="GET" action="{{ route('office.work-assignments.index') }}" class="row g-3 align-items-end">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">🔍 All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>🔄 In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                    <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>⏸️ On Hold</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select form-select-sm">
                    <option value="">🔍 All Priorities</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>🟠 High</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>🔵 Normal</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>⚪ Low</option>
                </select>
            </div>
            @if(!empty($departments) && count($departments) > 0)
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label">Department</label>
                <select name="department" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept['id'] }}" {{ request('department') == $dept['id'] ? 'selected' : '' }}>
                        {{ $dept['name'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <button type="submit" class="btn btn-secondary-custom btn-sm w-100">
                    <i class="fas fa-search me-2"></i>Apply Filters
                </button>
            </div>
            @if(request()->hasAny(['status', 'priority', 'department', 'start_date', 'end_date']))
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="{{ route('office.work-assignments.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="fas fa-sync-alt me-2"></i>Reset Filters
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Professional Data Table -->
    <div class="wa-table-card">
        @if($assignments->count() > 0)
        <div class="table-responsive">
            <table class="wa-table">
                <thead>
                    <tr>
                        <th style="width: 4%;"><i class="fas fa-hashtag me-1"></i> #</th>
                        <th style="width: 26%;"><i class="fas fa-clipboard-list me-2"></i>Task Details</th>
                        <th style="width: 13%;"><i class="fas fa-user me-2"></i>Assigned To</th>
                        <th style="width: 10%;"><i class="fas fa-building me-2"></i>Department</th>
                        <th style="width: 8%;" class="text-center"><i class="fas fa-flag me-2"></i>Priority</th>
                        <th style="width: 9%;" class="text-center"><i class="fas fa-dot-circle me-2"></i>Status</th>
                        <th style="width: 10%;" class="text-center"><i class="fas fa-calendar-alt me-2"></i>Due Date</th>
                        <th style="width: 12%;" class="text-center"><i class="fas fa-file-alt me-2"></i>Report Status</th>
                        <th style="width: 8%;" class="text-center"><i class="fas fa-cog me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $index => $assignment)
                    <tr>
                        <td class="text-muted fw-bold" style="font-size: 0.9rem;">{{ $assignments->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">{{ Str::limit($assignment->title, 50) }}</div>
                            <small class="text-muted d-flex align-items-center" style="gap: 0.25rem;">
                                <i class="fas fa-calendar-alt"></i>
                                <span>{{ $assignment->assigned_date->format('M d, Y') }}</span>
                            </small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="wa-avatar" style="background: #DC143C; color: white;">
                                    {{ strtoupper(substr($assignment->assignedTo->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.875rem;">{{ Str::limit($assignment->assignedTo->name, 18) }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($assignment->assignedTo->email, 22) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($assignment->department)
                            <span class="wa-badge" style="background-color: #f1f3f5; color: #495057;">
                                <i class="fas fa-building me-1"></i>{{ Str::limit($assignment->department->name, 15) }}
                            </span>
                            @else
                            <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $priorityConfig = [
                                    'urgent' => ['bg' => '#dc3545', 'color' => '#ffffff', 'icon' => 'fa-exclamation-circle'],
                                    'high' => ['bg' => '#fd7e14', 'color' => '#ffffff', 'icon' => 'fa-arrow-up'],
                                    'normal' => ['bg' => '#0d6efd', 'color' => '#ffffff', 'icon' => 'fa-minus'],
                                    'low' => ['bg' => '#6c757d', 'color' => '#ffffff', 'icon' => 'fa-arrow-down'],
                                ];
                                $pConfig = $priorityConfig[$assignment->priority] ?? $priorityConfig['normal'];
                            @endphp
                            <span class="wa-badge" style="background-color: {{ $pConfig['bg'] }}; color: {{ $pConfig['color'] }};">
                                <i class="fas {{ $pConfig['icon'] }} me-1"></i>{{ strtoupper($assignment->priority) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $statusConfig = [
                                    'pending' => ['bg' => '#ffc107', 'color' => '#000000', 'icon' => 'fa-clock'],
                                    'in_progress' => ['bg' => '#0dcaf0', 'color' => '#000000', 'icon' => 'fa-spinner'],
                                    'completed' => ['bg' => '#198754', 'color' => '#ffffff', 'icon' => 'fa-check-circle'],
                                    'on_hold' => ['bg' => '#6c757d', 'color' => '#ffffff', 'icon' => 'fa-pause'],
                                    'cancelled' => ['bg' => '#dc3545', 'color' => '#ffffff', 'icon' => 'fa-times-circle'],
                                ];
                                $sConfig = $statusConfig[$assignment->status] ?? $statusConfig['pending'];
                            @endphp
                            <span class="wa-badge" style="background-color: {{ $sConfig['bg'] }}; color: {{ $sConfig['color'] }};">
                                <i class="fas {{ $sConfig['icon'] }} me-1"></i>{{ strtoupper(str_replace('_', ' ', $assignment->status)) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($assignment->due_date)
                                @if($assignment->isOverdue())
                                    <span class="wa-overdue">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <strong>{{ $assignment->due_date->format('M d, Y') }}</strong>
                                    </span>
                                @elseif($assignment->isDueSoon())
                                    <span class="wa-due-soon">
                                        <i class="fas fa-clock"></i> 
                                        <strong>{{ $assignment->due_date->format('M d, Y') }}</strong>
                                    </span>
                                @else
                                    <span class="text-muted" style="font-weight: 500;">
                                        <i class="fas fa-calendar-check"></i> {{ $assignment->due_date->format('M d, Y') }}
                                    </span>
                                @endif
                            @else
                                <span class="text-muted small">No deadline</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($assignment->included_in_report && $assignment->daily_report_id)
                                <span class="wa-report-badge" title="Included in Daily Report #{{ $assignment->daily_report_id }}">
                                    <i class="fas fa-check-circle"></i>
                                    <span>In Report</span>
                                </span>
                            @else
                                <span class="badge bg-light text-muted" style="font-size: 0.7rem; font-weight: 600;">
                                    <i class="fas fa-minus-circle me-1"></i>Not Included
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('office.work-assignments.show', $assignment) }}" 
                                   class="btn wa-action-btn" 
                                   style="background: #0dcaf0; color: #ffffff;" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $assignment)
                                <a href="{{ route('office.work-assignments.edit', $assignment) }}" 
                                   class="btn wa-action-btn" 
                                   style="background: #ffc107; color: #000000;" 
                                   title="Edit Assignment">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete', $assignment)
                                <form action="{{ route('office.work-assignments.destroy', $assignment) }}" 
                                      method="POST" 
                                      class="d-inline" 
                                      onsubmit="return confirm('⚠️ Are you sure you want to delete this assignment? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn wa-action-btn" 
                                            style="background: #dc3545; color: #ffffff;" 
                                            title="Delete Assignment">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($assignments->hasPages())
        <div class="wa-pagination">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Showing <strong>{{ $assignments->firstItem() }}</strong> to <strong>{{ $assignments->lastItem() }}</strong> of <strong>{{ $assignments->total() }}</strong> assignments
                </div>
                <div>
                    {{ $assignments->links() }}
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="wa-empty-state">
            <i class="fas fa-tasks"></i>
            <h5>No Work Assignments Found</h5>
            <p class="mb-4">There are no assignments matching your criteria. Try adjusting your filters or create a new assignment.</p>
            @can('create', App\Models\WorkAssignment::class)
            <a href="{{ route('office.work-assignments.create') }}" class="btn btn-primary-custom">
                <i class="fas fa-plus-circle me-2"></i>Create First Assignment
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection
