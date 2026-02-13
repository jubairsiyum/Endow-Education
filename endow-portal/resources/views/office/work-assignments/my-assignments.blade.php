@extends('layouts.admin')

@section('page-title', 'My Work Assignments')
@section('breadcrumb', 'Home / Office / My Assignments')

@section('content')
<style>
    /* Work Assignment Styles */
    .work-assignments-container {
        padding: 1rem;
    }
    
    .assignment-card {
        border-left: 4px solid;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .assignment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    
    .assignment-card.priority-urgent {
        border-left-color: #dc3545;
    }
    
    .assignment-card.priority-high {
        border-left-color: #ffc107;
    }
    
    .assignment-card.priority-normal {
        border-left-color: #0dcaf0;
    }
    
    .assignment-card.priority-low {
        border-left-color: #6c757d;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-weight: 600;
    }
    
    .overdue-badge {
        background-color: #dc3545;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: bold;
    }
</style>

<div class="container-fluid work-assignments-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color: #000000;">
                <i class="fas fa-user-check me-2" style="color: #DC143C;"></i>
                My Work Assignments
            </h4>
            <p class="text-muted mb-0 small">Track your assigned tasks and progress</p>
        </div>
        <a href="{{ route('office.work-assignments.index') }}" class="btn btn-sm" style="background-color: #000000; color: #FFFFFF; border: none; padding: 0.4rem 1rem; border-radius: 0.375rem; font-weight: 600;">
            <i class="fas fa-arrow-left me-1"></i>All Assignments
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="stat-card shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="d-flex justify-content-between align-items-center p-2">
                    <div>
                        <div class="text-uppercase fw-semibold" style="color: #6C757D; font-size: 0.7rem;">My Tasks</div>
                        <div style="color: #000000; font-size: 1.5rem; font-weight: bold;">{{ $statistics['total'] }}</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(0, 0, 0, 0.1); width: 35px; height: 35px;">
                        <i class="fas fa-tasks" style="color: #000000; font-size: 0.9rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="d-flex justify-content-between align-items-center p-2">
                    <div>
                        <div class="text-uppercase fw-semibold" style="color: #6C757D; font-size: 0.7rem;">Pending</div>
                        <div style="color: #ffc107; font-size: 1.5rem; font-weight: bold;">{{ $statistics['pending'] }}</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(255, 193, 7, 0.1); width: 35px; height: 35px;">
                        <i class="fas fa-clock" style="color: #ffc107; font-size: 0.9rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="d-flex justify-content-between align-items-center p-2">
                    <div>
                        <div class="text-uppercase fw-semibold" style="color: #6C757D; font-size: 0.7rem;">In Progress</div>
                        <div style="color: #0dcaf0; font-size: 1.5rem; font-weight: bold;">{{ $statistics['in_progress'] }}</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(13, 202, 240, 0.1); width: 35px; height: 35px;">
                        <i class="fas fa-spinner" style="color: #0dcaf0; font-size: 0.9rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="d-flex justify-content-between align-items-center p-2">
                    <div>
                        <div class="text-uppercase fw-semibold" style="color: #6C757D; font-size: 0.7rem;">Completed</div>
                        <div style="color: #198754; font-size: 1.5rem; font-weight: bold;">{{ $statistics['completed'] }}</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(25, 135, 84, 0.1); width: 35px; height: 35px;">
                        <i class="fas fa-check-circle" style="color: #198754; font-size: 0.9rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3 rounded" style="background-color: #FFFFFF;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('office.work-assignments.my-assignments') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="color: #000000;">Status</label>
                    <select name="status" class="form-select" style="border-color: #E0E0E0;">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="color: #000000;">Priority</label>
                    <select name="priority" class="form-select" style="border-color: #E0E0E0;">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn" style="background-color: #DC143C; color: #FFFFFF; border: none;">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('office.work-assignments.my-assignments') }}" class="btn" style="border: 1px solid #000000; color: #000000; background-color: #FFFFFF;">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments List -->
    @if($assignments->count() > 0)
    <div class="row">
        @foreach($assignments as $assignment)
        <div class="col-md-6 mb-3">
            <div class="card assignment-card priority-{{ $assignment->priority }} shadow-sm" style="border: 1px solid #E0E0E0;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0" style="color: #000000;">{{ $assignment->title }}</h6>
                        @php
                            $statusColors = [
                                'pending' => 'background-color: #ffc107; color: #000000;',
                                'in_progress' => 'background-color: #0dcaf0; color: #000000;',
                                'completed' => 'background-color: #198754; color: #FFFFFF;',
                                'on_hold' => 'background-color: #6c757d; color: #FFFFFF;',
                            ];
                        @endphp
                        <span class="status-badge" style="{{ $statusColors[$assignment->status] ?? '' }}">
                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                        </span>
                    </div>

                    <p class="text-muted small mb-2">{{ Str::limit($assignment->description, 100) }}</p>

                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @php
                            $priorityBadges = [
                                'urgent' => 'background-color: #dc3545; color: #FFFFFF;',
                                'high' => 'background-color: #ffc107; color: #000000;',
                                'normal' => 'background-color: #0dcaf0; color: #000000;',
                                'low' => 'background-color: #6c757d; color: #FFFFFF;',
                            ];
                        @endphp
                        <span class="badge" style="{{ $priorityBadges[$assignment->priority] ?? '' }}">
                            <i class="fas fa-flag"></i> {{ ucfirst($assignment->priority) }} Priority
                        </span>
                        @if($assignment->department)
                        <span class="badge" style="background-color: #000000; color: #FFFFFF;">
                            <i class="fas fa-building"></i> {{ $assignment->department->name }}
                        </span>
                        @endif
                    </div>

                    <div class="small text-muted mb-3">
                        <div><i class="fas fa-user me-1"></i> Assigned by: <strong>{{ $assignment->assignedBy->name }}</strong></div>
                        <div><i class="fas fa-calendar me-1"></i> Assigned: {{ $assignment->assigned_date->format('M d, Y') }}</div>
                        @if($assignment->due_date)
                        <div>
                            <i class="fas fa-calendar-check me-1"></i> Due: 
                            @if($assignment->isOverdue())
                                <span class="overdue-badge">
                                    OVERDUE - {{ $assignment->due_date->format('M d, Y') }}
                                </span>
                            @elseif($assignment->isDueSoon())
                                <span style="color: #ffc107; font-weight: 600;">
                                    {{ $assignment->due_date->format('M d, Y') }} (Due Soon)
                                </span>
                            @else
                                <strong>{{ $assignment->due_date->format('M d, Y') }}</strong>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('office.work-assignments.show', $assignment) }}" class="btn btn-sm flex-fill" style="background-color: #0dcaf0; color: #000000;">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                        @if(!$assignment->isCompleted() && auth()->user()->can('updateStatus', $assignment))
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm dropdown-toggle" style="background-color: #DC143C; color: #FFFFFF;" data-bs-toggle="dropdown">
                                <i class="fas fa-edit"></i> Update
                            </button>
                            <ul class="dropdown-menu">
                                @if($assignment->status !== 'in_progress')
                                <li>
                                    <form action="{{ route('office.work-assignments.update-status', $assignment) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-play me-1"></i> Start Working
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if($assignment->status !== 'completed')
                                <li>
                                    <form action="{{ route('office.work-assignments.update-status', $assignment) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-check me-1"></i> Mark as Completed
                                        </button>
                                    </form>
                                </li>
                                @endif
                                <li>
                                    <form action="{{ route('office.work-assignments.update-status', $assignment) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="on_hold">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-pause me-1"></i> Put On Hold
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $assignments->links() }}
    </div>
    @else
    <div class="card shadow-sm border-0 rounded">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h5>All Caught Up!</h5>
            <p class="text-muted">You don't have any work assignments matching the current filters.</p>
        </div>
    </div>
    @endif
</div>
@endsection
