@extends('layouts.admin')

@section('page-title', 'Students')
@section('breadcrumb', 'Home / Students / All Students')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Students</h1>
            <p class="page-subtitle">Manage and track all student applications</p>
        </div>
        @can('create students')
        <a href="{{ route('students.create') }}" class="btn btn-primary-custom">
            <i class="fas fa-plus me-2"></i> Add New Student
        </a>
        @endcan
    </div>

    <!-- Filters -->
    <div class="card-custom mb-4">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('students.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, phone..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
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
                    <label class="form-label">Account Status</label>
                    <select name="account_status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('account_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('account_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('account_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                <div class="col-md-3">
                    <label class="form-label">Assigned To</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">All Counselors</option>
                        @foreach($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Account</th>
                        @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                        <th>Assigned To</th>
                        @endif
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <strong>{{ $student->name }}</strong>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ $student->created_at->format('M d, Y') }}
                            </small>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->phone }}</td>
                        <td>{{ $student->country }}</td>
                        <td>{{ $student->course }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'new' => 'info',
                                    'contacted' => 'secondary',
                                    'processing' => 'warning',
                                    'applied' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $color = $statusColors[$student->status] ?? 'secondary';
                            @endphp
                            <span class="badge-custom badge-{{ $color }}-custom">
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
                            <span class="badge-custom badge-{{ $accountColor }}-custom">
                                {{ ucfirst($student->account_status) }}
                            </span>
                        </td>
                        @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                        <td>
                            @if($student->assignedUser)
                                {{ $student->assignedUser->name }}
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            @php
                                $progress = $student->checklist_progress['percentage'] ?? 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress" style="width: 80px; height: 8px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $progress }}%; background: var(--primary-color);"
                                         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $progress }}%</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('students.show', $student) }}" class="action-btn view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $student)
                                <a href="{{ route('students.edit', $student) }}" class="action-btn edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete', $student)
                                <form action="{{ route('students.destroy', $student) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this student?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Delete">
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
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No students found</p>
                            @can('create students')
                            <a href="{{ route('students.create') }}" class="btn btn-primary-custom mt-3">
                                <i class="fas fa-plus me-2"></i> Add Your First Student
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="card-body-custom border-top">
            {{ $students->links() }}
        </div>
        @endif
    </div>
@endsection
