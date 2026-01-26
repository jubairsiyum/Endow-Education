@extends('layouts.admin')

@section('page-title', 'Create Role')
@section('breadcrumb', 'Home / Admin / Roles / Create')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="mb-4">
                <h2 class="page-title mb-2">
                    <i class="fas fa-user-shield text-primary me-2"></i>
                    Create New Role
                </h2>
                <p class="text-muted mb-0">Define a new role and assign module permissions</p>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <!-- Role Basic Info Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Role Information
                        </h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Office Manager, Department Head, Staff"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Choose a clear, descriptive name for this role</small>
                        </div>
                    </div>
                </div>

                <!-- Module Permissions Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header-custom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-cubes me-2"></i>
                                Module Permissions
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions()">
                                    <i class="fas fa-check-double me-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllPermissions()">
                                    <i class="fas fa-times me-1"></i>Deselect All
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        @if(count($modules) > 0)
                        <div class="row g-4">
                            @foreach($modules as $moduleKey => $module)
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
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox permission-{{ $moduleKey }}"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission['id'] }}"
                                                   id="perm_{{ $permission['id'] }}"
                                                   {{ collect(old('permissions', []))->contains($permission['id']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permission['id'] }}">
                                                {{ $permission['display_name'] }}
                                                <br><small class="text-muted">{{ $permission['name'] }}</small>
                                            </label>
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

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-end mb-4">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
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

    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
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

    function selectAllPermissions() {
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const moduleToggles = document.querySelectorAll('.module-toggle');

        allCheckboxes.forEach(cb => cb.checked = true);
        moduleToggles.forEach(cb => cb.checked = true);
    }

    function deselectAllPermissions() {
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
    });
</script>
@endsection
