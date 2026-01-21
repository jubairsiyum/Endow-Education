@extends('layouts.admin')

@section('page-title', 'Create Department')
@section('breadcrumb', 'Home / Office / Departments / Create')

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="page-header-modern mb-4">
        <a href="{{ route('office.departments.index') }}" class="btn btn-light shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Back to Departments
        </a>
        <div class="mt-3">
            <h1 class="display-6 fw-bold mb-2">
                <i class="fas fa-plus-circle text-primary me-3"></i>
                Create New Department
            </h1>
            <p class="text-muted mb-0">Add a new department to your organization</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Modern Form Card -->
            <div class="modern-form-card">
                <form action="{{ route('office.departments.store') }}" method="POST" id="departmentForm">
                    @csrf

                    <!-- Department Name -->
                    <div class="form-group-modern mb-4">
                        <label class="form-label-modern">
                            <i class="fas fa-building me-2 text-primary"></i>
                            Department Name *
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control-modern @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g., Human Resources"
                               required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Department Code -->
                    <div class="form-group-modern mb-4">
                        <label class="form-label-modern">
                            <i class="fas fa-tag me-2 text-primary"></i>
                            Department Code *
                        </label>
                        <input type="text"
                               name="code"
                               class="form-control-modern @error('code') is-invalid @enderror"
                               value="{{ old('code') }}"
                               placeholder="e.g., HR"
                               maxlength="20"
                               style="text-transform: uppercase;"
                               required>
                        <small class="form-text-modern">Unique identifier (max 20 characters)</small>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-group-modern mb-4">
                        <label class="form-label-modern">
                            <i class="fas fa-align-left me-2 text-primary"></i>
                            Description
                        </label>
                        <textarea name="description"
                                  class="form-control-modern @error('description') is-invalid @enderror"
                                  rows="4"
                                  placeholder="Describe the department's role and responsibilities...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Manager Selection -->
                    <div class="form-group-modern mb-4">
                        <label class="form-label-modern">
                            <i class="fas fa-user-tie me-2 text-primary"></i>
                            Department Manager
                        </label>
                        <select name="manager_id" class="form-control-modern @error('manager_id') is-invalid @enderror">
                            <option value="">Select a manager (optional)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text-modern">Assign a team lead or manager</small>
                        @error('manager_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Row for Icon and Color -->
                    <div class="row">
                        <!-- Icon Selection -->
                        <div class="col-md-6">
                            <div class="form-group-modern mb-4">
                                <label class="form-label-modern">
                                    <i class="fas fa-icons me-2 text-primary"></i>
                                    Icon
                                </label>
                                <input type="text"
                                       name="icon"
                                       class="form-control-modern @error('icon') is-invalid @enderror"
                                       value="{{ old('icon', 'fas fa-building') }}"
                                       placeholder="fas fa-building"
                                       id="iconInput">
                                <small class="form-text-modern">
                                    FontAwesome class
                                    <a href="https://fontawesome.com/icons" target="_blank" class="text-primary">
                                        <i class="fas fa-external-link-alt"></i> Browse icons
                                    </a>
                                </small>
                                <div class="icon-preview mt-2">
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
                                <label class="form-label-modern">
                                    <i class="fas fa-palette me-2 text-primary"></i>
                                    Department Color
                                </label>
                                <div class="color-picker-wrapper">
                                    <input type="color"
                                           name="color"
                                           class="form-control-color-modern @error('color') is-invalid @enderror"
                                           value="{{ old('color', '#667eea') }}"
                                           id="colorInput">
                                    <input type="text"
                                           class="form-control-modern color-text-input"
                                           value="{{ old('color', '#667eea') }}"
                                           id="colorText"
                                           readonly>
                                </div>
                                <small class="form-text-modern">Choose a brand color for this department</small>
                                @error('color')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="form-group-modern mb-4">
                        <div class="form-check-modern">
                            <input type="checkbox"
                                   name="is_active"
                                   class="form-check-input-modern"
                                   id="isActive"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label-modern" for="isActive">
                                <i class="fas fa-toggle-on me-2 text-success"></i>
                                <span class="fw-bold">Active Department</span>
                                <small class="d-block text-muted mt-1">Enable this department for immediate use</small>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary-modern btn-lg">
                            <i class="fas fa-save me-2"></i>Create Department
                        </button>
                        <a href="{{ route('office.departments.index') }}" class="btn btn-light-modern btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="col-lg-4">
            <div class="preview-card sticky-top" style="top: 20px;">
                <h5 class="preview-title">
                    <i class="fas fa-eye me-2"></i>Live Preview
                </h5>
                <div class="department-preview" id="departmentPreview">
                    <div class="preview-header" id="previewHeader" style="background: linear-gradient(135deg, #667eea 0%, #667eeadd 100%);">
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
    .page-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 20px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .modern-form-card {
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .form-label-modern {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .form-control-modern {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-text-modern {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: block;
    }

    .icon-preview {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
    }

    .color-picker-wrapper {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .form-control-color-modern {
        width: 80px;
        height: 50px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        cursor: pointer;
        padding: 0.25rem;
    }

    .color-text-input {
        flex: 1;
        text-transform: uppercase;
        font-weight: 600;
    }

    .form-check-modern {
        background: #f8f9fa;
        padding: 1.25rem;
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-check-modern:hover {
        border-color: #667eea;
        background: #667eea05;
    }

    .form-check-input-modern {
        width: 50px;
        height: 26px;
        border-radius: 50px;
        cursor: pointer;
    }

    .form-check-label-modern {
        margin-left: 1rem;
        cursor: pointer;
        flex: 1;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #e9ecef;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-light-modern {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .btn-light-modern:hover {
        background: #e9ecef;
        color: #212529;
    }

    .preview-card {
        background: white;
        padding: 1.5rem;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .preview-title {
        color: #212529;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .department-preview {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
    }

    .preview-header {
        padding: 2rem;
        color: white;
        text-align: center;
    }

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
