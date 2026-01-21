@extends('layouts.admin')

@section('page-title', 'Role Management')
@section('breadcrumb', 'Home / Admin / Roles & Permissions')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="fas fa-user-shield text-primary me-2"></i>
                Role & Permission Management
            </h2>
            <p class="text-muted mb-0">Manage system roles and assign module permissions</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.roles.sync-permissions') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary" title="Sync permissions from config">
                    <i class="fas fa-sync me-2"></i>Sync Permissions
                </button>
            </form>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Role
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Total Roles</div>
                        <div class="stat-value">{{ $statistics['total_roles'] }}</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Total Permissions</div>
                        <div class="stat-value">{{ $statistics['total_permissions'] }}</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Users with Roles</div>
                        <div class="stat-value">{{ $statistics['total_users_with_roles'] }}</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Protected Roles</div>
                        <div class="stat-value">{{ $statistics['protected_roles'] }}</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                All Roles
            </h5>
        </div>
        <div class="card-body p-0">
            @if($roles->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Permissions Count</th>
                            <th>Users Count</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-user-tag text-primary"></i>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $role->name }}</div>
                                        @if(in_array($role->name, $protectedRoles))
                                            <small class="text-muted">
                                                <i class="fas fa-lock me-1"></i>System Protected
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-custom badge-info-custom">
                                    <i class="fas fa-key me-1"></i>
                                    {{ $role->permissions_count }} {{ Str::plural('Permission', $role->permissions_count) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-custom badge-secondary-custom">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $role->users()->count() }} {{ Str::plural('User', $role->users()->count()) }}
                                </span>
                            </td>
                            <td>
                                @if(in_array($role->name, $protectedRoles))
                                    <span class="badge-custom badge-warning-custom">
                                        <i class="fas fa-shield-alt"></i> Protected
                                    </span>
                                @else
                                    <span class="badge-custom badge-success-custom">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('admin.roles.show', $role) }}"
                                       class="action-btn view"
                                       title="View Role">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(!in_array($role->name, $protectedRoles))
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="action-btn edit"
                                       title="Edit Role">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.roles.destroy', $role) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete Role">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button"
                                            class="action-btn delete"
                                            title="Protected role cannot be deleted"
                                            disabled
                                            style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-user-shield text-muted" style="font-size: 64px; opacity: 0.3;"></i>
                <p class="text-muted mt-3 mb-0">No roles found</p>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Create Your First Role
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="alert alert-info border-0 mt-4">
        <h6 class="alert-heading">
            <i class="fas fa-info-circle me-2"></i>
            About Roles & Permissions
        </h6>
        <ul class="mb-0 small">
            <li><strong>Roles</strong> are groups of permissions assigned to users</li>
            <li><strong>Permissions</strong> control access to specific modules and actions</li>
            <li><strong>Protected roles</strong> (like Super Admin and Student) cannot be modified or deleted</li>
            <li><strong>Users</strong> can have one role plus additional direct permissions</li>
            <li><strong>Sync Permissions</strong> button updates permissions from the config file</li>
        </ul>
    </div>
</div>
@endsection
