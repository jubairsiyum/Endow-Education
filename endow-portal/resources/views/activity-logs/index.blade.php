@extends('layouts.admin')

@section('page-title', 'Activity Logs')
@section('breadcrumb', 'Home / Activity Logs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-history text-danger"></i> Activity Logs</h4>
            <small class="text-muted">Monitor all system activities and student actions</small>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('activity-logs.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="log_name" class="form-label">Activity Type</label>
                        <select class="form-select" id="log_name" name="log_name">
                            <option value="">All Types</option>
                            @foreach($logTypes as $type)
                            <option value="{{ $type }}" {{ request('log_name') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="form-select" id="student_id" name="student_id">
                            <option value="">All Students</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="ip_address" class="form-label">IP Address</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address"
                               value="{{ request('ip_address') }}" placeholder="e.g., 192.168">
                    </div>

                    <div class="col-md-8">
                        <label for="search" class="form-label">Search Description</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Search in activity description...">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-custom me-2">
                            <i class="fas fa-search me-2"></i> Filter
                        </button>
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Date & Time</th>
                            <th style="width: 120px;">Type</th>
                            <th>Description</th>
                            <th style="width: 150px;">User</th>
                            <th style="width: 130px;">IP Address</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    {{ $log->created_at->format('g:i A') }}
                                </small>
                            </td>
                            <td>
                                @php
                                    $typeColors = [
                                        'student' => 'primary',
                                        'document' => 'info',
                                        'authentication' => 'success',
                                        'checklist' => 'warning'
                                    ];
                                    $color = $typeColors[$log->log_name] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst($log->log_name) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $log->description }}</strong>
                                @if($log->subject)
                                <br>
                                <small class="text-muted">
                                    @if($log->subject_type == 'App\\Models\\Student')
                                    <i class="fas fa-user me-1"></i>Student: {{ $log->subject->name ?? 'N/A' }}
                                    @elseif($log->subject_type == 'App\\Models\\StudentDocument')
                                    <i class="fas fa-file me-1"></i>Document: {{ $log->subject->original_name ?? 'N/A' }}
                                    @endif
                                </small>
                                @endif
                            </td>
                            <td>
                                @if($log->causer)
                                <div>{{ $log->causer->name }}</div>
                                <small class="text-muted">{{ class_basename($log->causer_type) }}</small>
                                @else
                                <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                @if($log->ip_address)
                                <code>{{ $log->ip_address }}</code>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('activity-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No activity logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top bg-white">
            <div class="text-muted small">
                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} records
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection
