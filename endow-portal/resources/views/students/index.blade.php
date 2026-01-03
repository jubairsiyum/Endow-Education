@extends('layouts.admin')

@section('page-title', 'Students')
@section('breadcrumb', 'Home / Students / All Students')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-users text-danger"></i> Students Management</h4>
            <small class="text-muted">Manage and track all student applications</small>
        </div>
        @can('create students')
        <a href="{{ route('students.create') }}" class="btn btn-danger">
            <i class="fas fa-plus me-1"></i> Add Student
        </a>
        @endcan
    </div>

    <!-- Compact Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('students.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, email, phone..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Applied</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="account_status" class="form-select form-select-sm">
                        <option value="">Account Status</option>
                        <option value="pending" {{ request('account_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('account_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('account_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                <div class="col-md-3">
                    <select name="assigned_to" class="form-select form-select-sm">
                        <option value="">All Counselors</option>
                        @foreach($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-danger btn-sm flex-grow-1">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Compact Students Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem;">
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Country</th>
                        <th>Program</th>
                        <th>Status</th>
                        <th>Account</th>
                        @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                        <th>Assigned</th>
                        @endif
                        <th>Progress</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark" style="font-size: 0.875rem;">{{ $student->name }}</strong>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-calendar-alt me-1"></i>{{ $student->created_at->format('M d, Y') }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td style="font-size: 0.8rem;">
                            <div><i class="fas fa-envelope text-muted me-1"></i>{{ Str::limit($student->email, 25) }}</div>
                            <div><i class="fas fa-phone text-muted me-1"></i>{{ $student->phone }}</div>
                        </td>
                        <td style="font-size: 0.8rem;">{{ $student->country }}</td>
                        <td style="font-size: 0.8rem;">{{ Str::limit($student->course, 20) }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'new' => 'primary',
                                    'contacted' => 'info',
                                    'processing' => 'warning',
                                    'applied' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $color = $statusColors[$student->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}" style="font-size: 0.7rem;">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $accountColors = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $accountColor = $accountColors[$student->account_status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $accountColor }}" style="font-size: 0.7rem;">
                                {{ ucfirst($student->account_status) }}
                            </span>
                        </td>
                        @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                        <td style="font-size: 0.8rem;">
                            @if($student->assignedUser)
                                <span class="text-dark">{{ Str::limit($student->assignedUser->name, 15) }}</span>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            @php
                                $progress = $student->checklist_progress['percentage'] ?? 0;
                                $progressColor = $progress >= 75 ? 'success' : ($progress >= 50 ? 'warning' : 'danger');
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress" style="width: 70px; height: 6px;">
                                    <div class="progress-bar bg-{{ $progressColor }}" role="progressbar"
                                         style="width: {{ $progress }}%;"
                                         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $progress }}%</small>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary btn-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $student)
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete', $student)
                                <form action="{{ route('students.destroy', $student) }}" method="POST"
                                      class="d-inline" id="delete-student-form-{{ $student->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                            onclick="confirmDeleteStudent({{ $student->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="my-4">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No students found</p>
                                @can('create students')
                                <a href="{{ route('students.create') }}" class="btn btn-danger mt-3">
                                    <i class="fas fa-plus me-1"></i> Add Your First Student
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="card-body p-3 border-top bg-light">
            {{ $students->links() }}
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function confirmDeleteStudent(studentId) {
            Swal.fire({
                title: 'Delete Student?',
                text: 'Are you sure you want to delete this student? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC143C',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-student-form-' + studentId).submit();
                }
            });
        }
    </script>
    @endpush
@endsection
