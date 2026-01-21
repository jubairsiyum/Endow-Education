@extends('layouts.admin')

@section('page-title', $department->name)
@section('breadcrumb', 'Home / Office / Departments / ' . $department->name)

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="{{ $department->icon }} me-2" style="color: {{ $department->color }}"></i>
                {{ $department->name }}
            </h2>
            <p class="text-muted mb-0">{{ $department->description }}</p>
        </div>
        <a href="{{ route('office.departments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Departments
        </a>
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

    <div class="row">
        <!-- Department Info -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header" style="background: {{ $department->color }}; color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Department Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">CODE</label>
                        <div class="fw-bold">{{ $department->code }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">STATUS</label>
                        <div>
                            @if($department->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">TEAM SIZE</label>
                        <div class="fw-bold">{{ $department->users->count() }} Members</div>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small fw-semibold mb-2">DEPARTMENT MANAGER</label>
                        <form action="{{ route('office.departments.update-manager', $department) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="input-group">
                                <select name="manager_id" class="form-select" required>
                                    <option value="">Select Manager</option>
                                    @foreach($department->users as $user)
                                    <option value="{{ $user->id }}" {{ $department->manager_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Team Members ({{ $department->users->count() }})
                        </h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                            <i class="fas fa-user-plus me-2"></i>Assign User
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($department->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="user-avatar" style="width: 36px; height: 36px; background: {{ $department->color }}; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                @if($department->manager_id == $user->id)
                                                <small class="badge bg-success">Manager</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->roles->count() > 0)
                                        <span class="badge bg-info">{{ $user->roles->first()->name }}</span>
                                        @else
                                        <span class="text-muted">No Role</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('office.departments.remove-user', $department) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Remove {{ $user->name }} from {{ $department->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-users text-muted" style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="text-muted mt-3">No team members assigned yet</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                            <i class="fas fa-user-plus me-2"></i>Assign First Member
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign User Modal -->
<div class="modal fade" id="assignUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $department->color }}; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Assign User to {{ $department->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('office.departments.assign-user', $department) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select User</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">Choose a user...</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} - {{ $user->email }}
                                @if($user->department_id && $user->department_id != $department->id)
                                (Currently in {{ $user->department->name }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Note: If user is already in another department, they will be moved to this department
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Assign User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
