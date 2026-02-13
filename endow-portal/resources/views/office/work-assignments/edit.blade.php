@extends('layouts.admin')

@section('page-title', 'Edit Work Assignment')
@section('breadcrumb', 'Home / Office / Work Assignments / Edit')

@section('content')
<style>
    /* Professional Compact Edit Page Styles - Red & Black Theme */
    .wa-edit-container {
        padding: 0.5rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .wa-edit-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
        padding: 1.25rem 1.5rem;
        border-radius: 12px;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(220, 20, 60, 0.15);
        border-bottom: 3px solid #DC143C;
    }
    
    .wa-edit-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid #E2E8F0;
    }
    
    .wa-section {
        padding: 1.5rem;
        border-bottom: 1px solid #E2E8F0;
    }
    
    .wa-section:last-child {
        border-bottom: none;
    }
    
    .wa-section-title {
        font-size: 0.875rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #1a1a1a;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #DC143C;
        display: inline-block;
    }
    
    .wa-form-label {
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.4rem;
        font-size: 0.8rem;
        display: block;
    }
    
    .wa-form-control {
        border: 1.5px solid #ced4da;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        width: 100%;
    }
    
    .wa-form-control:focus {
        border-color: #DC143C;
        box-shadow: 0 0 0 0.2rem rgba(220, 19, 60, 0.15);
        outline: none;
    }
    
    .wa-required {
        color: #dc3545;
        font-weight: 700;
    }
    
    .wa-field-icon {
        color: #DC143C;
        font-size: 0.85rem;
        margin-right: 0.4rem;
    }
    
    .wa-form-hint {
        font-size: 0.7rem;
        color: #6c757d;
        margin-top: 0.25rem;
        font-style: italic;
    }
    
    .wa-action-bar {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #dee2e6;
    }
    
    .wa-btn {
        padding: 0.5rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .wa-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
</style>

<div class="container-fluid wa-edit-container">
    <!-- Professional Header -->
    <div class="wa-edit-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1 fw-bold">
                    <i class="fas fa-edit me-2"></i>
                    Edit Work Assignment
                </h5>
                <p class="mb-0 opacity-90" style="font-size: 0.85rem;">Update task details and modify assignment information</p>
            </div>
            <a href="{{ route('office.work-assignments.show', $workAssignment) }}" class="btn btn-light btn-sm wa-btn">
                <i class="fas fa-arrow-left me-1"></i>Back to Details
            </a>
        </div>
    </div>

    <!-- Compact Edit Form -->
    <form action="{{ route('office.work-assignments.update', $workAssignment) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-3">
            <!-- Left Column - Main Details -->
            <div class="col-lg-8">
                <div class="wa-edit-card">
                    <!-- Task Information Section -->
                    <div class="wa-section">
                        <div class="wa-section-title">
                            <i class="fas fa-info-circle me-2"></i>Task Information
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="wa-form-label">
                                    <i class="fas fa-heading wa-field-icon"></i>
                                    Task Title <span class="wa-required">*</span>
                                </label>
                                <input type="text" name="title" 
                                       class="wa-form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $workAssignment->title) }}" 
                                       maxlength="255" 
                                       placeholder="Enter a clear and descriptive task title"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="wa-form-label">
                                    <i class="fas fa-align-left wa-field-icon"></i>
                                    Description <span class="wa-required">*</span>
                                </label>
                                <textarea name="description" rows="5" 
                                          class="wa-form-control @error('description') is-invalid @enderror"
                                          placeholder="Provide detailed description of the task, objectives, and deliverables" 
                                          required>{{ old('description', $workAssignment->description) }}</textarea>
                                <div class="wa-form-hint">Include all necessary details for successful task completion</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Details Section -->
                    <div class="wa-section">
                        <div class="wa-section-title">
                            <i class="fas fa-user-plus me-2"></i>Assignment Details
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="wa-form-label">
                                    <i class="fas fa-user wa-field-icon"></i>
                                    Assign To <span class="wa-required">*</span>
                                </label>
                                <select name="assigned_to" 
                                        class="wa-form-control @error('assigned_to') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee['id'] }}" 
                                            {{ old('assigned_to', $workAssignment->assigned_to) == $employee['id'] ? 'selected' : '' }}>
                                        {{ $employee['name'] }} ({{ $employee['email'] }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="wa-form-label">
                                    <i class="fas fa-building wa-field-icon"></i>
                                    Department
                                </label>
                                <select name="department_id" 
                                        class="wa-form-control @error('department_id') is-invalid @enderror">
                                    <option value="">-- Select Department (Optional) --</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept['id'] }}" 
                                            {{ old('department_id', $workAssignment->department_id) == $dept['id'] ? 'selected' : '' }}>
                                        {{ $dept['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Priority & Timeline -->
            <div class="col-lg-4">
                <div class="wa-edit-card">
                    <!-- Priority & Timeline Section -->
                    <div class="wa-section">
                        <div class="wa-section-title">
                            <i class="fas fa-sliders-h me-2"></i>Priority & Timeline
                        </div>
                        
                        <div class="mb-3">
                            <label class="wa-form-label">
                                <i class="fas fa-flag wa-field-icon"></i>
                                Priority Level <span class="wa-required">*</span>
                            </label>
                            <select name="priority" 
                                    class="wa-form-control @error('priority') is-invalid @enderror" 
                                    required>
                                <option value="low" {{ old('priority', $workAssignment->priority) == 'low' ? 'selected' : '' }}>
                                    🟢 Low Priority
                                </option>
                                <option value="normal" {{ old('priority', $workAssignment->priority) == 'normal' ? 'selected' : '' }}>
                                    🔵 Normal Priority
                                </option>
                                <option value="high" {{ old('priority', $workAssignment->priority) == 'high' ? 'selected' : '' }}>
                                    🟠 High Priority
                                </option>
                                <option value="urgent" {{ old('priority', $workAssignment->priority) == 'urgent' ? 'selected' : '' }}>
                                    🔴 Urgent Priority
                                </option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label class="wa-form-label">
                                <i class="fas fa-calendar-alt wa-field-icon"></i>
                                Due Date
                            </label>
                            <input type="date" name="due_date" 
                                   class="wa-form-control @error('due_date') is-invalid @enderror" 
                                   value="{{ old('due_date', $workAssignment->due_date?->format('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}">
                            <div class="wa-form-hint">Leave empty for no deadline</div>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Quick Info Section -->
                    <div class="wa-section" style="background-color: #f8f9fa;">
                        <div class="wa-section-title" style="border-bottom-color: #6c757d;">
                            <i class="fas fa-info-circle me-2"></i>Assignment Info
                        </div>
                        
                        <div class="small">
                            <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom: 1px solid #dee2e6;">
                                <span class="text-muted">Created By:</span>
                                <span class="fw-semibold">{{ $workAssignment->assignedBy->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom: 1px solid #dee2e6;">
                                <span class="text-muted">Created On:</span>
                                <span class="fw-semibold">{{ $workAssignment->assigned_date->format('M d, Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom: 1px solid #dee2e6;">
                                <span class="text-muted">Current Status:</span>
                                <span class="badge" style="background-color: #0dcaf0; color: #000;">
                                    {{ strtoupper(str_replace('_', ' ', $workAssignment->status)) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Last Updated:</span>
                                <span class="fw-semibold">{{ $workAssignment->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="wa-edit-card mt-3">
            <div class="wa-action-bar">
                <div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        All fields marked with <span class="wa-required">*</span> are required
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('office.work-assignments.show', $workAssignment) }}" 
                       class="btn btn-outline-secondary wa-btn">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn wa-btn" 
                            style="background-color: #DC143C; color: #ffffff; border: none;">
                        <i class="fas fa-save me-1"></i>Update Assignment
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
