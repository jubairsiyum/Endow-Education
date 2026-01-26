@extends('layouts.admin')

@section('page-title', 'Student Visits')
@section('breadcrumb', 'Home / Student Visits')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-clipboard-list text-danger"></i> Student Visits Management</h4>
            <small class="text-muted">Track and manage student visit records</small>
        </div>
        @can('create users')
        <a href="{{ route('student-visits.create') }}" class="btn btn-danger">
            <i class="fas fa-plus me-1"></i> New Visit Record
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('student-visits.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search by name, phone, email..."
                           value="{{ request('search') }}">
                </div>

                @if(Auth::user()->isAdmin())
                <div class="col-md-2">
                    <select name="employee_id" class="form-select form-select-sm">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <select name="prospective_status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        @foreach(\App\Models\StudentVisit::getStatuses() as $status)
                        <option value="{{ $status }}" {{ request('prospective_status') == $status ? 'selected' : '' }}>
                            {{ \App\Models\StudentVisit::getStatusLabel($status) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           placeholder="From Date"
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           placeholder="To Date"
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-danger btn-sm flex-grow-1">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('student-visits.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Visits Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem;">
                        <th>Student Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Assigned Employee</th>
                        <th>Visit Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $visit->student_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-phone me-1"></i>{{ $visit->phone }}
                            </span>
                        </td>
                        <td>
                            @if($visit->email)
                                <span class="text-muted small">{{ $visit->email }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $visit->status_color ?? 'secondary' }}"
                                  title="{{ $visit->status_description ?? '' }}"
                                  data-bs-toggle="tooltip">
                                {{ $visit->status_label ?? 'Not set' }}
                            </span>
                        </td>
                        <td>
                            @if($visit->employee)
                            <div class="d-flex align-items-center">
                                @if($visit->employee->photo_path && file_exists(public_path('storage/' . $visit->employee->photo_path)))
                                    <img src="{{ asset('storage/' . $visit->employee->photo_path) }}"
                                         alt="{{ $visit->employee->name }}"
                                         class="rounded-circle me-2"
                                         style="width: 28px; height: 28px; object-fit: cover;">
                                @else
                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                        {{ strtoupper(substr($visit->employee->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="small">{{ $visit->employee->name }}</span>
                            </div>
                            @else
                            <span class="text-muted small">Not Assigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                {{ $visit->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-muted" style="font-size: 0.7rem;">
                                {{ $visit->created_at->format('h:i A') }}
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('student-visits.show', $visit) }}?page={{ request('page', 1) }}"
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $visit)
                                <a href="{{ route('student-visits.edit', $visit) }}?page={{ request('page', 1) }}"
                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @if(Auth::user()->hasRole('Super Admin'))
                                @can('delete', $visit)
                                <form action="{{ route('student-visits.destroy', $visit) }}"
                                      method="POST"
                                      class="d-inline"
                                      id="delete-visit-form-{{ $visit->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="page" value="{{ request('page', 1) }}">
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="confirmDeleteVisitIndex({{ $visit->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-clipboard-list fa-3x mb-3 d-block opacity-25"></i>
                            <p class="mb-0">No student visit records found.</p>
                            <a href="{{ route('student-visits.create') }}" class="btn btn-sm btn-danger mt-2">
                                <i class="fas fa-plus me-1"></i> Create First Visit Record
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
        <div class="card-footer bg-white border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 py-2">
                <div class="text-muted small">
                    Showing {{ $visits->firstItem() }} to {{ $visits->lastItem() }} of {{ $visits->total() }} visits
                </div>
                <div class="pagination-wrapper">
                    {{ $visits->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>

@push('scripts')
<style>
    /* Compact pagination styling */
    .pagination-wrapper .pagination {
        margin-bottom: 0;
    }

    .pagination-wrapper .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .pagination-wrapper .page-item:first-child .page-link,
    .pagination-wrapper .page-item:last-child .page-link {
        padding: 0.375rem 0.65rem;
    }

    /* Smaller arrows */
    .pagination-wrapper .page-link svg {
        width: 0.875rem;
        height: 0.875rem;
    }

    /* Active page styling - white text on red background */
    .pagination-wrapper .page-item.active .page-link {
        background-color: #DC143C;
        border-color: #DC143C;
        color: #ffffff !important;
    }

    /* Hover effect for non-active pages */
    .pagination-wrapper .page-link:hover {
        background-color: #fff5f5;
        border-color: #DC143C;
        color: #DC143C;
    }

    /* Disabled state */
    .pagination-wrapper .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .pagination-wrapper .pagination {
            font-size: 0.75rem;
        }

        .pagination-wrapper .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .pagination-wrapper .page-item:first-child .page-link,
        .pagination-wrapper .page-item:last-child .page-link {
            padding: 0.25rem 0.4rem;
        }

        .pagination-wrapper .page-link svg {
            width: 0.75rem;
            height: 0.75rem;
        }
    }
</style>

<script>
    function confirmDeleteVisitIndex(visitId) {
        Swal.fire({
            title: 'Delete Visit Record?',
            text: 'Are you sure you want to delete this visit record? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC143C',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-visit-form-' + visitId).submit();
            }
        });
    }
</style>
@endpush
@endsection
