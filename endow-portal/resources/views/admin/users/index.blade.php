@extends('layouts.admin')

@section('page-title', 'User Management')
@section('breadcrumb', 'Home / Users')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="fas fa-users-cog text-primary me-2"></i>
                User Management
            </h2>
            <p class="text-muted mb-0">Manage system users, roles, and permissions</p>
        </div>
        @can('create users')
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New User
        </a>
        @endcan
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value">{{ $users->total() }}</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Super Admins</div>
                        <div class="stat-value">{{ \App\Models\User::role('Super Admin')->count() }}</div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Admins</div>
                        <div class="stat-value">{{ \App\Models\User::role('Admin')->count() }}</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Employees</div>
                        <div class="stat-value">{{ \App\Models\User::role('Employee')->count() }}</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by name, email, or phone..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Assigned Students</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <small class="text-muted">ID: #{{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-envelope text-muted me-1"></i>
                                    <small>{{ $user->email }}</small>
                                </div>
                                @if($user->phone)
                                <div class="mt-1">
                                    <i class="fas fa-phone text-muted me-1"></i>
                                    <small>{{ $user->phone }}</small>
                                </div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $roleColors = [
                                        'Super Admin' => 'danger',
                                        'Admin' => 'warning',
                                        'Employee' => 'success',
                                        'Student' => 'info',
                                    ];
                                    $roleColor = $roleColors[$user->roles->first()->name ?? ''] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $roleColor }}">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    {{ $user->roles->first()->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-ban me-1"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->assignedStudents->count() > 0)
                                    <span class="badge bg-info">
                                        {{ $user->assignedStudents->count() }} Students
                                    </span>
                                @else
                                    <span class="text-muted small">None</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->created_at->format('M d, Y') }}
                                </small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @can('edit users')
                                    <a href="{{ route('users.show', $user) }}"
                                       class="btn btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="btn btn-outline-warning" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('users.toggle-status', $user) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-info"
                                                title="Toggle Status">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>
                                    @endcan
                                    @can('delete users')
                                    @if(!$user->hasRole('Super Admin'))
                                    <button type="button" class="btn btn-outline-danger"
                                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                            title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($users->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Delete Confirmation Forms -->
@foreach($users as $user)
<form id="delete-form-{{ $user->id }}"
      action="{{ route('users.destroy', $user) }}"
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach

@push('scripts')
<script>
function confirmDelete(userId, userName) {
    Swal.fire({
        title: 'Delete User?',
        html: `
            <p class="mb-3">Are you sure you want to delete <strong>${userName}</strong>?</p>
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                This action cannot be undone. All data associated with this user will be permanently removed.
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Yes, Delete',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
        reverseButtons: true,
        width: '500px'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + userId).submit();
        }
    });
}
</script>
@endpush

@push('styles')
<style>
    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .card-custom {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush
@endsection
