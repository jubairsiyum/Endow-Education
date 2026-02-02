@extends('layouts.admin')

@section('page-title', 'Role Details')
@section('breadcrumb', 'Home / Admin / Roles / View')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-2">
                        <i class="fas fa-user-shield text-primary me-2"></i>
                        {{ $role->name }}
                    </h2>
                    <p class="text-muted mb-0">Complete role details and permissions overview</p>
                </div>
                <div class="d-flex gap-2">
                    @if(!in_array($role->name, $protectedRoles))
                    <a href="{{ route('admin.roles.clone', $role) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-copy me-2"></i>Clone
                    </a>
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Role
                    </a>
                    @endif
                </div>
            </div>

            @if(in_array($role->name, $protectedRoles))
            <div class="alert alert-info border-0 mb-4">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Protected Role:</strong> This role is protected by the system and cannot be modified or deleted to ensure system stability.
            </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stat-card bg-primary-subtle">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">{{ $role->permissions->count() }}</div>
                            <div class="stat-label">Total Permissions</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-success-subtle">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">{{ $role->users->count() }}</div>
                            <div class="stat-label">Assigned Users</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-info-subtle">
                        <div class="stat-icon bg-info">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">{{ count($modules) }}</div>
                            <div class="stat-label">Available Modules</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card {{ in_array($role->name, $protectedRoles) ? 'bg-warning-subtle' : 'bg-secondary-subtle' }}">
                        <div class="stat-icon {{ in_array($role->name, $protectedRoles) ? 'bg-warning' : 'bg-secondary' }}">
                            <i class="fas fa-{{ in_array($role->name, $protectedRoles) ? 'shield-alt' : 'cog' }}"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">{{ in_array($role->name, $protectedRoles) ? 'Yes' : 'No' }}</div>
                            <div class="stat-label">Protected</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Module Permissions Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-cubes me-2"></i>
                        Permissions by Module
                    </h5>
                </div>
                <div class="card-body-custom">
                    @if(count($modules) > 0)
                    <div class="row g-4">
                        @foreach($modules as $moduleKey => $module)
                        @php
                            $modulePermissionIds = collect($module['permissions'])->pluck('id')->toArray();
                            $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                            $grantedPermissions = array_intersect($modulePermissionIds, $rolePermissionIds);
                            $hasPermissions = !empty($grantedPermissions);
                        @endphp
                        <div class="col-md-6">
                            <div class="module-permission-card p-3 border rounded {{ $hasPermissions ? 'border-primary' : '' }}">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="module-icon {{ $hasPermissions ? 'bg-primary text-white' : '' }}">
                                        <i class="{{ $module['icon'] }} {{ $hasPermissions ? '' : 'text-primary' }}" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $module['name'] }}</h6>
                                        <small class="text-muted">{{ $module['description'] }}</small>
                                    </div>
                                    <div>
                                        <span class="badge {{ $hasPermissions ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ count($grantedPermissions) }}/{{ count($module['permissions']) }}
                                        </span>
                                    </div>
                                </div>

                                @if(count($module['permissions']) > 0)
                                <div class="permissions-list ps-4">
                                    @foreach($module['permissions'] as $permission)
                                    @php
                                        $isGranted = in_array($permission['id'], $rolePermissionIds);
                                    @endphp
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-{{ $isGranted ? 'check-circle text-success' : 'times-circle text-muted' }}"></i>
                                        <div class="flex-grow-1">
                                            <div class="{{ $isGranted ? 'fw-medium' : 'text-muted' }}">
                                                {{ $permission['display_name'] }}
                                            </div>
                                            <small class="text-muted">{{ $permission['name'] }}</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-muted mb-0 small ps-4">No permissions available for this module</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning border-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No modules found. Please sync permissions first.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Users with This Role Card -->
            @if($role->users->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Users with This Role ({{ $role->users->count() }})
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->user_type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($user->status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View User">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.roles.user-permissions', $user) }}" class="btn btn-sm btn-outline-info" title="Manage Permissions">
                                                <i class="fas fa-key"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info border-0 mb-4">
                <i class="fas fa-info-circle me-2"></i>
                No users are currently assigned to this role.
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex gap-2 justify-content-between mb-4">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Roles
                </a>
                @if(!in_array($role->name, $protectedRoles))
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role? Users with this role will lose associated permissions.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash-alt me-2"></i>Delete Role
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-radius: 12px;
        background: white;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        color: white;
        font-size: 24px;
    }

    .stat-details {
        flex-grow: 1;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 13px;
        color: #718096;
        font-weight: 500;
    }

    .module-permission-card {
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .module-permission-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .module-permission-card.border-primary {
        background: #eff6ff;
    }

    .module-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .module-icon.bg-primary {
        border-color: var(--primary);
    }

    .permissions-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .permissions-list::-webkit-scrollbar {
        width: 4px;
    }

    .permissions-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .bg-primary-subtle {
        background-color: #eff6ff !important;
    }

    .bg-success-subtle {
        background-color: #f0fdf4 !important;
    }

    .bg-info-subtle {
        background-color: #f0f9ff !important;
    }

    .bg-warning-subtle {
        background-color: #fffbeb !important;
    }

    .bg-secondary-subtle {
        background-color: #f8f9fa !important;
    }
</style>
@endsection
