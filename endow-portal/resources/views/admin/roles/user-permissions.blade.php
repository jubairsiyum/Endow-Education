@extends('layouts.admin')

@section('page-title', 'User Permissions')
@section('breadcrumb', 'Home / Admin / Users / Permissions')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="mb-4">
                <h2 class="page-title mb-2">
                    <i class="fas fa-user-lock text-primary me-2"></i>
                    Manage User Permissions: {{ $user->name }}
                </h2>
                <p class="text-muted mb-0">Assign or remove direct permissions for this specific user</p>
            </div>

            <!-- User Info Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body-custom">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="user-avatar-large">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <div class="text-muted mb-2">{{ $user->email }}</div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-info">{{ $user->user_type }}</span>
                                @foreach($user->roles as $role)
                                <span class="badge bg-primary">
                                    <i class="fas fa-user-shield me-1"></i>{{ $role->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="stat-box-value text-primary">{{ $user->roles->count() }}</div>
                                        <div class="stat-box-label">Roles</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="stat-box-value text-success">{{ $user->permissions->count() }}</div>
                                        <div class="stat-box-label">Direct Permissions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="alert alert-info border-0 mb-4">
                <div class="d-flex gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-2"><strong>Understanding User Permissions</strong></h6>
                        <ul class="mb-0 small">
                            <li><strong>Role-Based Permissions:</strong> Automatically inherited from assigned roles ({{ $user->roles->pluck('name')->join(', ') ?: 'None' }})</li>
                            <li><strong>Direct Permissions:</strong> Specific permissions assigned directly to this user (shown below)</li>
                            <li><strong>Combined Access:</strong> User has access to all permissions from both roles AND direct assignments</li>
                            <li><strong>Special Access:</strong> Use direct permissions to grant temporary or exception-based access without creating a new role</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.roles.update-user-permissions', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Permission Summary Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header-custom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                Permission Summary
                            </h5>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="summary-card bg-primary-subtle">
                                    <div class="summary-icon bg-primary">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <div class="summary-value">{{ $rolePermissions->count() }}</div>
                                        <div class="summary-label">From Roles</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card bg-success-subtle">
                                    <div class="summary-icon bg-success">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div>
                                        <div class="summary-value">{{ $directPermissions->count() }}</div>
                                        <div class="summary-label">Direct Permissions</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card bg-info-subtle">
                                    <div class="summary-icon bg-info">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <div class="summary-value">{{ $user->getAllPermissions()->count() }}</div>
                                        <div class="summary-label">Total Access</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Module Permissions Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header-custom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-cubes me-2"></i>
                                Module-Based Permissions
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllDirect()">
                                    <i class="fas fa-check-double me-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllDirect()">
                                    <i class="fas fa-times me-1"></i>Deselect All
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        @if(count($modules) > 0)
                        <div class="row g-4">
                            @foreach($modules as $moduleKey => $module)
                            @php
                                $modulePermissionIds = collect($module['permissions'])->pluck('id')->toArray();
                                $directPermissionIds = $directPermissions->pluck('id')->toArray();
                                $rolePermissionIds = $rolePermissions->pluck('id')->toArray();
                            @endphp
                            <div class="col-md-6">
                                <div class="module-permission-card p-3 border rounded">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="module-icon">
                                            <i class="{{ $module['icon'] }} text-primary" style="font-size: 24px;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold">{{ $module['name'] }}</h6>
                                            <small class="text-muted">{{ $module['description'] }}</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input module-toggle"
                                                   type="checkbox"
                                                   id="module_{{ $moduleKey }}"
                                                   onchange="toggleModule('{{ $moduleKey }}')">
                                            <label class="form-check-label small" for="module_{{ $moduleKey }}">
                                                All
                                            </label>
                                        </div>
                                    </div>

                                    @if(count($module['permissions']) > 0)
                                    <div class="permissions-list ps-4">
                                        @foreach($module['permissions'] as $permission)
                                        @php
                                            $hasFromRole = in_array($permission['id'], $rolePermissionIds);
                                            $hasDirect = in_array($permission['id'], $directPermissionIds);
                                        @endphp
                                        <div class="permission-item mb-2 {{ $hasFromRole ? 'from-role' : '' }}">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox permission-{{ $moduleKey }}"
                                                       type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $permission['id'] }}"
                                                       id="perm_{{ $permission['id'] }}"
                                                       {{ $hasDirect ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission['id'] }}">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="flex-grow-1">
                                                            <div>{{ $permission['display_name'] }}</div>
                                                            <small class="text-muted">{{ $permission['name'] }}</small>
                                                        </div>
                                                        @if($hasFromRole)
                                                        <span class="badge bg-primary badge-sm" title="Already granted via role">
                                                            <i class="fas fa-shield-alt me-1"></i>Role
                                                        </span>
                                                        @endif
                                                        @if($hasDirect)
                                                        <span class="badge bg-success badge-sm" title="Directly assigned">
                                                            <i class="fas fa-user-check me-1"></i>Direct
                                                        </span>
                                                        @endif
                                                    </div>
                                                </label>
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

                <!-- Permission Legend -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body-custom">
                        <h6 class="mb-3"><i class="fas fa-book me-2"></i>Permission Legend</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary"><i class="fas fa-shield-alt me-1"></i>Role</span>
                                    <small class="text-muted">Inherited from assigned roles</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success"><i class="fas fa-user-check me-1"></i>Direct</span>
                                    <small class="text-muted">Assigned directly to this user</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="permission-item from-role" style="width: 30px; height: 20px; border-radius: 4px;"></div>
                                    <small class="text-muted">Already has via role</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-between mb-4">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to User
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .user-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 28px;
        margin: 0 auto;
    }

    .stat-box {
        text-align: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .stat-box-value {
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-box-label {
        font-size: 12px;
        color: #718096;
        font-weight: 500;
    }

    .summary-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 10px;
    }

    .summary-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: white;
        font-size: 20px;
    }

    .summary-value {
        font-size: 24px;
        font-weight: 700;
        color: #1a202c;
        line-height: 1;
        margin-bottom: 4px;
    }

    .summary-label {
        font-size: 13px;
        color: #718096;
        font-weight: 500;
    }

    .module-permission-card {
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .module-permission-card:hover {
        background: #e9ecef;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
    }

    .permissions-list {
        max-height: 350px;
        overflow-y: auto;
    }

    .permissions-list::-webkit-scrollbar {
        width: 4px;
    }

    .permissions-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .permission-item {
        padding: 0.5rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .permission-item:hover {
        background: white;
    }

    .permission-item.from-role {
        background: #eff6ff;
    }

    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .badge-sm {
        font-size: 10px;
        padding: 2px 6px;
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
</style>
@endsection

@section('scripts')
<script>
    function toggleModule(moduleKey) {
        const checkbox = document.getElementById(`module_${moduleKey}`);
        const permissions = document.querySelectorAll(`.permission-${moduleKey}`);

        permissions.forEach(perm => {
            perm.checked = checkbox.checked;
        });
    }

    function selectAllDirect() {
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const moduleToggles = document.querySelectorAll('.module-toggle');

        allCheckboxes.forEach(cb => cb.checked = true);
        moduleToggles.forEach(cb => cb.checked = true);
    }

    function deselectAllDirect() {
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const moduleToggles = document.querySelectorAll('.module-toggle');

        allCheckboxes.forEach(cb => cb.checked = false);
        moduleToggles.forEach(cb => cb.checked = false);
    }

    // Auto-toggle module checkbox when individual permissions change
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Extract module key from class
                const classes = this.className.split(' ');
                const moduleClass = classes.find(c => c.startsWith('permission-'));
                if (moduleClass) {
                    const moduleKey = moduleClass.replace('permission-', '');
                    const moduleCheckbox = document.getElementById(`module_${moduleKey}`);
                    const modulePermissions = document.querySelectorAll(`.permission-${moduleKey}`);
                    const allChecked = Array.from(modulePermissions).every(p => p.checked);
                    const someChecked = Array.from(modulePermissions).some(p => p.checked);

                    moduleCheckbox.checked = allChecked;
                    moduleCheckbox.indeterminate = someChecked && !allChecked;
                }
            });
        });

        // Set initial state of module toggles
        document.querySelectorAll('.module-toggle').forEach(toggle => {
            const moduleKey = toggle.id.replace('module_', '');
            const modulePermissions = document.querySelectorAll(`.permission-${moduleKey}`);
            const allChecked = Array.from(modulePermissions).every(p => p.checked);
            const someChecked = Array.from(modulePermissions).some(p => p.checked);

            toggle.checked = allChecked;
            toggle.indeterminate = someChecked && !allChecked;
        });
    });
</script>
@endsection
