@extends('layouts.admin')

@section('page-title', 'Consultant Evaluations')
@section('breadcrumb', 'Home / System / Consultant Evaluations')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="fas fa-star text-warning me-2"></i>
                Consultant Evaluations
            </h2>
            <p class="text-muted mb-0">View student feedback on consultant performance</p>
        </div>
        <a href="{{ route('admin.consultant-evaluations.export', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-file-export me-2"></i>Export to CSV
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Total Evaluations</div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">This Month</div>
                        <div class="stat-value">{{ $stats['this_month'] }}</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Excellent Ratings</div>
                        <div class="stat-value">{{ $stats['excellent'] }}</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Below Average</div>
                        <div class="stat-value">{{ $stats['below_average'] }}</div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-custom mb-4">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.consultant-evaluations.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Consultant</label>
                        <select name="consultant_id" class="form-select">
                            <option value="">All Consultants</option>
                            @foreach($consultants as $consultant)
                            <option value="{{ $consultant->id }}" {{ request('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                {{ $consultant->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Rating</label>
                        <select name="rating" class="form-select">
                            <option value="">All Ratings</option>
                            <option value="excellent" {{ request('rating') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="good" {{ request('rating') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="neutral" {{ request('rating') == 'neutral' ? 'selected' : '' }}>Neutral</option>
                            <option value="average" {{ request('rating') == 'average' ? 'selected' : '' }}>Average</option>
                            <option value="below_average" {{ request('rating') == 'below_average' ? 'selected' : '' }}>Below Average</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.consultant-evaluations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Evaluations List -->
    <div class="card-custom">
        <div class="card-header-custom bg-light">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Evaluations ({{ $evaluations->total() }})</h5>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Consultant</th>
                            <th>Question</th>
                            <th>Rating</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluations as $evaluation)
                        <tr>
                            <td>
                                <small>{{ $evaluation->created_at->format('M d, Y') }}</small><br>
                                <small class="text-muted">{{ $evaluation->created_at->format('g:i A') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-info text-white" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($evaluation->student->name ?? 'N', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $evaluation->student->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($evaluation->consultant->name ?? 'N', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $evaluation->consultant->name ?? 'N/A' }}</div>
                                        <a href="{{ route('admin.consultant-evaluations.show', $evaluation->consultant_id) }}" class="text-decoration-none small">
                                            View All
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ Str::limit($evaluation->question->question ?? 'N/A', 50) }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $evaluation->rating_color }}">
                                    {{ $evaluation->rating_label }}
                                </span>
                            </td>
                            <td>
                                @if($evaluation->comment)
                                <div class="small text-muted" style="max-width: 200px;">
                                    {{ Str::limit($evaluation->comment, 50) }}
                                </div>
                                @else
                                <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No evaluations found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($evaluations->hasPages())
        <div class="card-footer-custom bg-light">
            {{ $evaluations->appends(request()->all())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.stat-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    padding: 20px;
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #212529;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.primary { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
.stat-icon.success { background: rgba(25, 135, 84, 0.1); color: #198754; }
.stat-icon.warning { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
.stat-icon.danger { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
.stat-icon.info { background: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
</style>
@endsection
