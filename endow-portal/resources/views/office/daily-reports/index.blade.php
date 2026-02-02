@extends('layouts.admin')

@section('page-title', 'Daily Reports')
@section('breadcrumb', 'Home / Office / Daily Reports')

@section('content')
<link rel="stylesheet" href="{{ asset('css/daily-reports-compact.css') }}">
<div class="container-fluid daily-reports-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color: #000000;">
                <i class="fas fa-file-alt me-2" style="color: #DC143C;"></i>
                Daily Reports
            </h4>
            <p class="text-muted mb-0 small">Track and review daily department reports</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasRole('Super Admin'))
            <a href="{{ route('office.daily-reports.export-form') }}" class="btn btn-sm" style="background-color: #28a745; color: #FFFFFF; border: none; padding: 0.4rem 1rem; border-radius: 0.375rem; font-weight: 600;">
                <i class="fas fa-file-pdf me-1"></i>Export PDF
            </a>
            @endif
            @can('create', App\Models\DailyReport::class)
            <a href="{{ route('office.daily-reports.create') }}" class="btn btn-sm" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.4rem 1rem; border-radius: 0.375rem; font-weight: 600;">
                <i class="fas fa-plus me-1"></i>Submit Report
            </a>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="stat-card shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="stat-card-header d-flex justify-content-between align-items-center p-2">
                    <div>
                        <div class="stat-label text-uppercase fw-semibold" style="color: #6C757D; font-size: 0.7rem;">Total Reports</div>
                        <div class="stat-value" style="color: #000000; font-size: 1.5rem; font-weight: bold;">{{ $statistics['total'] }}</div>
                    </div>
                    <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(255, 0, 0, 0.1); width: 35px; height: 35px;">
                        <i class="fas fa-file-alt" style="color: #DC143C; font-size: 0.9rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="stat-card-header d-flex justify-content-between align-items-center p-2">
                    <div>
                        <div class="stat-label text-uppercase fw-semibold" style="color: #6C757D; font-size: 0.7rem;">In Progress</div>
                        <div class="stat-value" style="color: #000000; font-size: 1.5rem; font-weight: bold;">{{ $statistics['in_progress'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(0, 0, 0, 0.1); width: 35px; height: 35px;">
                        <i class="fas fa-tasks" style="color: #000000; font-size: 0.9rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="stat-card-header d-flex justify-content-between align-items-center p-2">
                    <div>
                        <div class="stat-label text-uppercase fw-semibold" style="color: #6C757D; font-size: 0.7rem;">In Review</div>
                        <div class="stat-value" style="color: #000000; font-size: 1.5rem; font-weight: bold;">{{ $statistics['review'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(255, 0, 0, 0.1); width: 35px; height: 35px;">
                        <i class="fas fa-clock" style="color: #DC143C; font-size: 0.9rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="stat-card-header d-flex justify-content-between align-items-center p-2">
                    <div>
                        <div class="stat-label text-uppercase fw-semibold" style="color: #6C757D; font-size: 0.7rem;">Completed</div>
                        <div class="stat-value" style="color: #000000; font-size: 1.5rem; font-weight: bold;">{{ $statistics['completed'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(0, 0, 0, 0.1); width: 35px; height: 35px;">
                        <i class="fas fa-check-circle" style="color: #000000; font-size: 0.9rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3 rounded" style="background-color: #FFFFFF;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('office.daily-reports.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="color: #000000;">Status</label>
                    <select name="status" class="form-select" style="border-color: #E0E0E0;">
                        <option value="">All Status</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>In Review</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" style="color: #000000;">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" style="border-color: #E0E0E0;">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" style="color: #000000;">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" style="border-color: #E0E0E0;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn" style="background-color: #DC143C; color: #FFFFFF; border: none;">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('office.daily-reports.index') }}" class="btn" style="border: 1px solid #000000; color: #000000; background-color: #FFFFFF;">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card shadow-sm border-0 rounded" style="background-color: #FFFFFF;">
        <div class="card-body p-0">
            @if($reports->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="background-color: #000000;">
                        <tr>
                            <th class="px-3 py-2" style="color: #FFFFFF;">Date</th>
                            <th class="px-3 py-2" style="color: #FFFFFF;">Department</th>
                            <th class="px-3 py-2" style="color: #FFFFFF;">Title</th>
                            <th class="px-3 py-2" style="color: #FFFFFF;">Submitted By</th>
                            <th class="px-3 py-2" style="color: #FFFFFF;">Status</th>
                            <th class="px-3 py-2" style="color: #FFFFFF;">Reviewed By</th>
                            <th class="text-center px-3 py-2" style="color: #FFFFFF;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr style="border-bottom: 1px solid #E0E0E0;">
                            <td class="px-3 py-2">
                                <span class="fw-semibold" style="color: #000000;">{{ $report->report_date->format('M d, Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $report->report_date->diffForHumans() }}</small>
                            </td>
                            <td class="px-3 py-2">
                                <span class="badge rounded-pill" style="background-color: #000000; color: #FFFFFF;">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $report->department_name }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="fw-semibold" style="color: #000000;">{{ Str::limit($report->title, 80) }}</div>
                                @if($report->priority && $report->priority !== 'normal')
                                    <span class="badge badge-{{ $report->priority_badge }} mt-1" style="font-size: 10px;">
                                        @if($report->priority === 'urgent')
                                            <i class="fas fa-exclamation-circle"></i> URGENT
                                        @elseif($report->priority === 'high')
                                            <i class="fas fa-arrow-up"></i> HIGH
                                        @else
                                            {{ strtoupper($report->priority) }}
                                        @endif
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: #DC143C; color: #FFFFFF; font-size: 12px; font-weight: bold;">
                                        {{ strtoupper(substr($report->submittedBy->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: #000000;">{{ $report->submittedBy->name }}</div>
                                        <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $needsYourReview = false;
                                    // Check if this report needs review from the current user
                                    if ($report->submitted_by !== auth()->id() && 
                                        $report->isAwaitingReview() && 
                                        auth()->user()->can('review', $report)) {
                                        $needsYourReview = true;
                                    }
                                @endphp
                                
                                @if($needsYourReview)
                                    <span class="badge rounded-pill pulse-badge" style="background-color: #DC143C; color: #FFFFFF; animation: pulse 2s infinite;">
                                        <i class="fas fa-bell"></i> Needs Your Review
                                    </span>
                                @elseif($report->status === 'draft')
                                    <span class="badge rounded-pill bg-secondary">
                                        <i class="fas fa-edit"></i> Draft
                                    </span>
                                @elseif($report->status === 'submitted' || $report->status === 'pending_review')
                                    <span class="badge rounded-pill bg-info text-dark">
                                        <i class="fas fa-paper-plane"></i> Submitted
                                    </span>
                                @elseif($report->status === 'in_progress')
                                    <span class="badge rounded-pill" style="background-color: #000000; color: #FFFFFF;">
                                        <i class="fas fa-tasks"></i> In Progress
                                    </span>
                                @elseif($report->status === 'review')
                                    <span class="badge rounded-pill" style="background-color: #DC143C; color: #FFFFFF;">
                                        <i class="fas fa-clock"></i> In Review
                                    </span>
                                @elseif($report->status === 'approved')
                                    <span class="badge rounded-pill bg-success">
                                        <i class="fas fa-check-circle"></i> Approved
                                    </span>
                                @elseif($report->status === 'rejected')
                                    <span class="badge rounded-pill bg-danger">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </span>
                                @elseif($report->status === 'completed')
                                    <span class="badge rounded-pill" style="background-color: #FFFFFF; color: #000000; border: 1px solid #000000;">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-dark">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                @endif
                                
                                @if($report->submitted_by === auth()->id() && $report->isAwaitingReview())
                                    <br><small class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-info-circle"></i> Your report</small>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($report->reviewedBy)
                                    <div class="fw-semibold" style="color: #000000;">{{ $report->reviewedBy->name }}</div>
                                    <small class="text-muted">{{ $report->reviewed_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="d-flex gap-2 justify-content-center">
                                    @can('view', $report)
                                    <a href="{{ route('office.daily-reports.show', $report) }}"
                                       class="action-btn view rounded-circle d-flex align-items-center justify-content-center"
                                       title="View Report" style="width: 32px; height: 32px; background-color: #F8F9FA; color: #000000; transition: all 0.3s ease;"
                                       onmouseover="this.style.backgroundColor='#000000'; this.style.color='#FFFFFF';"
                                       onmouseout="this.style.backgroundColor='#F8F9FA'; this.style.color='#000000';">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan

                                    @can('update', $report)
                                    <a href="{{ route('office.daily-reports.edit', $report) }}"
                                       class="action-btn edit rounded-circle d-flex align-items-center justify-content-center"
                                       title="Edit Report" style="width: 32px; height: 32px; background-color: #F8F9FA; color: #000000; transition: all 0.3s ease;"
                                       onmouseover="this.style.backgroundColor='#DC143C'; this.style.color='#FFFFFF';"
                                       onmouseout="this.style.backgroundColor='#F8F9FA'; this.style.color='#000000';">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan

                                    @can('delete', $report)
                                    <form action="{{ route('office.daily-reports.destroy', $report) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this report?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete rounded-circle d-flex align-items-center justify-content-center"
                                                title="Delete Report" style="width: 32px; height: 32px; background-color: #F8F9FA; color: #DC143C; border: none; cursor: pointer; transition: all 0.3s ease;"
                                                onmouseover="this.style.backgroundColor='#DC143C'; this.style.color='#FFFFFF';"
                                                onmouseout="this.style.backgroundColor='#F8F9FA'; this.style.color='#DC143C';">
                                            <i class="fas fa-trash"></i>
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
            <div class="p-3 border-top" style="background-color: #F8F9FA;">
                {{ $reports->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-file-alt text-muted" style="font-size: 64px; opacity: 0.3;"></i>
                <p class="text-muted mt-3 mb-0">No reports found</p>
                @can('create', App\Models\DailyReport::class)
                <a href="{{ route('office.daily-reports.create') }}" class="btn mt-3" style="background-color: #DC143C; color: #FFFFFF; border: none;">
                    <i class="fas fa-plus me-2"></i>Submit Your First Report
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
