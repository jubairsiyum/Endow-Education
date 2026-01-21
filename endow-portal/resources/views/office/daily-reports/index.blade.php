@extends('layouts.admin')

@section('page-title', 'Daily Reports')
@section('breadcrumb', 'Home / Office / Daily Reports')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="fas fa-file-alt text-primary me-2"></i>
                Daily Reports
            </h2>
            <p class="text-muted mb-0">Track and review daily department reports</p>
        </div>
        @can('create', App\Models\DailyReport::class)
        <a href="{{ route('office.daily-reports.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Submit Report
        </a>
        @endcan
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Total Reports</div>
                        <div class="stat-value">{{ $statistics['total'] }}</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">In Progress</div>
                        <div class="stat-value">{{ $statistics['in_progress'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">In Review</div>
                        <div class="stat-value">{{ $statistics['review'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Completed</div>
                        <div class="stat-value">{{ $statistics['completed'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('office.daily-reports.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>In Review</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('office.daily-reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($reports->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead style="background-color: #1a1a1a;">
                        <tr>
                            <th style="color: white;">Date</th>
                            <th style="color: white;">Department</th>
                            <th style="color: white;">Title</th>
                            <th style="color: white;">Submitted By</th>
                            <th style="color: white;">Status</th>
                            <th style="color: white;">Reviewed By</th>
                            <th class="text-center" style="color: white;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $report->report_date->format('M d, Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $report->report_date->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="badge-custom badge-info-custom">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $report->department_name }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ Str::limit($report->title, 80) }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($report->submittedBy->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $report->submittedBy->name }}</div>
                                        <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($report->status === 'in_progress')
                                    <span class="badge-custom badge-info-custom">
                                        <i class="fas fa-tasks"></i> In Progress
                                    </span>
                                @elseif($report->status === 'review')
                                    <span class="badge-custom badge-warning-custom">
                                        <i class="fas fa-clock"></i> In Review
                                    </span>
                                @else
                                    <span class="badge-custom badge-success-custom">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($report->reviewedBy)
                                    <div class="fw-semibold">{{ $report->reviewedBy->name }}</div>
                                    <small class="text-muted">{{ $report->reviewed_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    @can('view', $report)
                                    <a href="{{ route('office.daily-reports.show', $report) }}"
                                       class="action-btn view"
                                       title="View Report">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan

                                    @can('update', $report)
                                    <a href="{{ route('office.daily-reports.edit', $report) }}"
                                       class="action-btn edit"
                                       title="Edit Report">
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
                                        <button type="submit" class="action-btn delete" title="Delete Report">
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
            <div class="p-3 border-top">
                {{ $reports->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-file-alt text-muted" style="font-size: 64px; opacity: 0.3;"></i>
                <p class="text-muted mt-3 mb-0">No reports found</p>
                @can('create', App\Models\DailyReport::class)
                <a href="{{ route('office.daily-reports.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Submit Your First Report
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
