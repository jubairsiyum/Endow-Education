@extends('layouts.admin')

@section('page-title', 'Create Department')
@section('breadcrumb', 'Home / Office / Departments / Create')

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="page-header-modern mb-4" style="background: linear-gradient(135deg, #DC143C 0%, #A52A2A 100%); padding: 2rem; border-radius: 1rem; color: #FFFFFF; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
        <a href="{{ route('office.departments.index') }}" class="btn shadow-sm" style="background-color: #FFFFFF; color: #000000; border: 1px solid #E0E0E0;">
            <i class="fas fa-arrow-left me-2" style="color: #DC143C;"></i>Back to Departments
        </a>
        <div class="mt-3">
            <h1 class="display-6 fw-bold mb-2">
                <i class="fas fa-plus-circle me-3"></i>
                Create New Department
            </h1>
            <p class="mb-0 opacity-75">Add a new department to your organization</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Modern Form Card -->
            <div class="modern-form-card shadow-sm rounded" style="background-color: #FFFFFF; padding: 2.5rem; border: 1px solid #E0E0E0;">
                <form action="{{ route('office.departments.store') }}" method="POST" id="departmentForm">
                    @csrf

                    <!-- Department Name -->
                    <div class="form-group-modern mb-4">
                        <label class="form-label-modern fw-semibold" style="color: #000000; display: flex; align-items: center;">
                            <i class="fas fa-building me-2" style="color: #DC143C;"></i>
                            Department Name *
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g., Human Resources"
                               style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                               required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Department Code -->
                    <div class="form-group-modern mb-4">
                        <label class="form-label-modern fw-semibold" style="color: #000000; display: flex; align-items: center;">
                            <i class="fas fa-tag me-2" style="color: #DC143C;"></i>
                            Department Code *
                        </label>
                        <input type="text"
                               name="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}"
                               placeholder="e.g., HR"
                               maxlength="20"
                               style="text-transform: uppercase; border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                               required>
                        <small class="text-muted" style="font-size: 0.875rem;">Unique identifier (max 20 characters)</small>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-group-modern mb-4">
                        <label class="form-label-modern fw-semibold" style="color: #000000; display: flex; align-items: center;">
                            <i class="fas fa-align-left me-2" style="color: #DC143C;"></i>
                            Description
                        </label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="4"
                                  style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                                  placeholder="Describe the department's role and responsibilities...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Manager Selection -->
                    <div class="form-group-modern mb-4">
                        <label class="form-label-modern fw-semibold" style="color: #000000; display: flex; align-items: center;">
                            <i class="fas fa-user-tie me-2" style="color: #DC143C;"></i>
                            Department Manager
                        </label>
                        <select name="manager_id" class="form-select @error('manager_id') is-invalid @enderror" style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                            <option value="">Select a manager (optional)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.875rem;">Assign a team lead or manager</small>
                        @error('manager_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Row for Icon and Color -->
                    <div class="row">
                        <!-- Icon Selection -->
                        <div class="col-md-6">
                            <div class="form-group-modern mb-4">
                                <label class="form-label-modern fw-semibold" style="color: #000000; display: flex; align-items: center;">
                                    <i class="fas fa-icons me-2" style="color: #DC143C;"></i>
                                    Icon
                                </label>
                                <input type="text"
                                       name="icon"
                                       class="form-control @error('icon') is-invalid @enderror"
                                       value="{{ old('icon', 'fas fa-building') }}"
                                       placeholder="fas fa-building"
                                       style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                                       id="iconInput">
                                <small class="text-muted" style="font-size: 0.875rem;">
                                    FontAwesome class
                                    <a href="https://fontawesome.com/icons" target="_blank" style="color: #DC143C;">
                                        <i class="fas fa-external-link-alt"></i> Browse icons
                                    </a>
                                </small>
                                <div class="icon-preview mt-2 rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #DC143C 0%, #A52A2A 100%); color: #FFFFFF; font-size: 1.75rem;">
                                    <i class="{{ old('icon', 'fas fa-building') }}" id="iconPreview"></i>
                                </div>
                                @error('icon')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Color Selection -->
                        <div class="col-md-6">
                            <div class="form-group-modern mb-4">
                                <label class="form-label-modern fw-semibold" style="color: #000000; display: flex; align-items: center;">
                                    <i class="fas fa-palette me-2" style="color: #DC143C;"></i>
                                    Department Color
                                </label>
                                <div class="color-picker-wrapper d-flex gap-3 align-items-center">
                                    <input type="color"
                                           name="color"
                                           class="form-control-color @error('color') is-invalid @enderror"
                                           value="{{ old('color', '#DC143C') }}"
                                           style="width: 80px; height: 50px; border: 2px solid #E0E0E0; border-radius: 0.5rem; cursor: pointer;"
                                           id="colorInput">
                                    <input type="text"
                                           class="form-control"
                                           value="{{ old('color', '#DC143C') }}"
                                           style="flex: 1; text-transform: uppercase; font-weight: 600; border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                                           id="colorText"
                                           readonly>
                                </div>
                                <small class="text-muted" style="font-size: 0.875rem;">Choose a brand color for this department</small>
                                @error('color')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="form-group-modern mb-4">
                        <div class="form-check rounded p-3" style="background-color: #F8F9FA; border: 2px solid #E0E0E0;">
                            <input type="checkbox"
                                   name="is_active"
                                   class="form-check-input"
                                   id="isActive"
                                   value="1"
                                   style="width: 50px; height: 26px; cursor: pointer;"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label ms-3" for="isActive" style="cursor: pointer;">
                                <i class="fas fa-toggle-on me-2" style="color: #DC143C;"></i>
                                <span class="fw-bold" style="color: #000000;">Active Department</span>
                                <small class="d-block text-muted mt-1">Enable this department for immediate use</small>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-actions d-flex gap-3 mt-4 pt-4" style="border-top: 2px solid #E0E0E0;">
                        <button type="submit" class="btn btn-lg" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600;">
                            <i class="fas fa-save me-2"></i>Create Department
                        </button>
                        <a href="{{ route('office.departments.index') }}" class="btn btn-lg" style="background-color: #F8F9FA; color: #000000; border: 2px solid #E0E0E0; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600;">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="col-lg-4">
            <div class="preview-card sticky-top shadow-sm rounded" style="top: 20px; background-color: #FFFFFF; padding: 1.5rem; border: 1px solid #E0E0E0;">
                <h5 class="preview-title fw-bold mb-4" style="color: #000000;">
                    <i class="fas fa-eye me-2" style="color: #DC143C;"></i>Live Preview
                </h5>
                <div class="department-preview rounded overflow-hidden shadow-sm" id="departmentPreview">
                    <div class="preview-header p-4 text-center" id="previewHeader" style="background: linear-gradient(135deg, #DC143C 0%, #DC143Cdd 100%); color: #FFFFFF;">
                        <div class="preview-icon-wrapper">
                            <i class="fas fa-building" id="previewIcon"></i>
                        </div>
                        <h4 class="preview-dept-title" id="previewName">Department Name</h4>
                        <span class="preview-dept-code" id="previewCode">CODE</span>
                    </div>
                    <div class="preview-body">
                        <p class="preview-description" id="previewDescription">Department description will appear here...</p>
                        <div class="preview-status" id="previewStatus">
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .preview-icon-wrapper {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin: 0 auto 1rem;
    }
    .preview-dept-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .preview-dept-code {
        background: rgba(255, 255, 255, 0.25);
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .preview-body {
        padding: 1.5rem;
        background: white;
    }
    .preview-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    .preview-status {
        text-align: center;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Icon preview update
        const iconInput = document.getElementById('iconInput');
        const iconPreview = document.getElementById('iconPreview');
        const previewIcon = document.getElementById('previewIcon');

        iconInput.addEventListener('input', function() {
            iconPreview.className = this.value || 'fas fa-building';
            previewIcon.className = this.value || 'fas fa-building';
        });

        // Color preview update
        const colorInput = document.getElementById('colorInput');
        const colorText = document.getElementById('colorText');
        const previewHeader = document.getElementById('previewHeader');

        colorInput.addEventListener('input', function() {
            colorText.value = this.value;
            previewHeader.style.background = `linear-gradient(135deg, ${this.value} 0%, ${this.value}dd 100%)`;
        });

        // Name preview update
        const nameInput = document.querySelector('input[name="name"]');
        const previewName = document.getElementById('previewName');

        nameInput.addEventListener('input', function() {
            previewName.textContent = this.value || 'Department Name';
        });

        // Code preview update
        const codeInput = document.querySelector('input[name="code"]');
        const previewCode = document.getElementById('previewCode');

        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            previewCode.textContent = this.value || 'CODE';
        });

        // Description preview update
        const descriptionInput = document.querySelector('textarea[name="description"]');
        const previewDescription = document.getElementById('previewDescription');

        descriptionInput.addEventListener('input', function() {
            previewDescription.textContent = this.value || 'Department description will appear here...';
        });

        // Status preview update
        const isActiveInput = document.getElementById('isActive');
        const previewStatus = document.getElementById('previewStatus');

        isActiveInput.addEventListener('change', function() {
            if (this.checked) {
                previewStatus.innerHTML = '<span class="badge bg-success">Active</span>';
            } else {
                previewStatus.innerHTML = '<span class="badge bg-secondary">Inactive</span>';
            }
        });
    });
</script>
@endsection
