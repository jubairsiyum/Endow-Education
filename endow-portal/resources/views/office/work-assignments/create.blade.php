@extends('layouts.admin')

@section('page-title', 'Create Work Assignment')
@section('breadcrumb', 'Home / Office / Work Assignments / Create')

@section('content')
<style>
    .wa-page {
        padding: 1rem;
    }
    
    .wa-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    
    .wa-card-header {
        background: #f9fafb;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 12px 12px 0 0;
    }
    
    .wa-label {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .wa-input, .wa-select, .wa-textarea {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
        font-size: 0.9375rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        width: 100%;
    }
    
    .wa-input:focus, .wa-select:focus, .wa-textarea:focus {
        border-color: #DC143C;
        box-shadow: 0 0 0 3px rgba(220, 19, 60, 0.1);
        outline: none;
    }
    
    .required-star {
        color: #dc2626;
        margin-left: 0.125rem;
    }
    
    .wa-hint {
        font-size: 0.8125rem;
        color: #6b7280;
        margin-top: 0.375rem;
    }
    
    .wa-icon-badge {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: rgba(220, 19, 60, 0.1);
        color: #DC143C;
        font-size: 0.8125rem;
    }
</style>

<div class="container-fluid wa-page">
    <!-- Header -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold" style="color: #000000;">
                    <i class="fas fa-plus-circle me-2" style="color: #DC143C;"></i>
                    Create Work Assignment
                </h4>
                <p class="text-muted mb-0 small">Assign a new task to an employee</p>
            </div>
            <a href="{{ route('office.work-assignments.index') }}" class="btn btn-sm" style="background-color: #6c757d; color: #FFFFFF; border: none; padding: 0.4rem 1rem; border-radius: 0.375rem;">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">
            <div class="wa-card">
                <div class="wa-card-header">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-tasks me-2" style="color: #DC143C;"></i>
                        Assignment Details
                    </h5>
                </div>
                
                <form action="{{ route('office.work-assignments.store') }}" method="POST">
                    @csrf
                    
                    <div class="p-4">
                        <!-- Task Title -->
                        <div class="mb-4">
                            <label class="wa-label">
                                <span class="wa-icon-badge"><i class="fas fa-heading"></i></span>
                                Task Title <span class="required-star">*</span>
                            </label>
                            <input type="text" name="title" class="wa-input @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" 
                                   placeholder="e.g., Prepare Monthly Sales Report" 
                                   maxlength="255" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="wa-label">
                                <span class="wa-icon-badge"><i class="fas fa-align-left"></i></span>
                                Description <span class="required-star">*</span>
                            </label>
                            <textarea name="description" rows="4" class="wa-textarea @error('description') is-invalid @enderror" 
                                      placeholder="Provide detailed instructions and requirements..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="wa-hint">
                                <i class="fas fa-info-circle"></i> Be clear and specific about what needs to be done
                            </div>
                        </div>

                        <div class="row">
                            <!-- Assign To -->
                            <div class="col-md-6 mb-4">
                                <label class="wa-label">
                                    <span class="wa-icon-badge"><i class="fas fa-user"></i></span>
                                    Assign To <span class="required-star">*</span>
                                </label>
                                <select name="assigned_to" class="wa-select @error('assigned_to') is-invalid @enderror" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee['id'] }}" {{ old('assigned_to') == $employee['id'] ? 'selected' : '' }}>
                                        {{ $employee['name'] }} ({{ $employee['email'] }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="col-md-6 mb-4">
                                <label class="wa-label">
                                    <span class="wa-icon-badge"><i class="fas fa-building"></i></span>
                                    Department
                                </label>
                                <select name="department_id" class="wa-select @error('department_id') is-invalid @enderror">
                                    <option value="">Select Department (Optional)</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept['id'] }}" {{ old('department_id') == $dept['id'] ? 'selected' : '' }}>
                                        {{ $dept['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Priority -->
                            <div class="col-md-6 mb-4">
                                <label class="wa-label">
                                    <span class="wa-icon-badge"><i class="fas fa-flag"></i></span>
                                    Priority <span class="required-star">*</span>
                                </label>
                                <select name="priority" class="wa-select @error('priority') is-invalid @enderror" required>
                                    <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="wa-hint">
                                    <i class="fas fa-lightbulb"></i> Set appropriate priority for task urgency
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6 mb-4">
                                <label class="wa-label">
                                    <span class="wa-icon-badge"><i class="fas fa-calendar-alt"></i></span>
                                    Due Date
                                </label>
                                <input type="date" name="due_date" class="wa-input @error('due_date') is-invalid @enderror" 
                                       value="{{ old('due_date') }}" 
                                       min="{{ now()->format('Y-m-d') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="wa-hint">
                                    <i class="fas fa-clock"></i> Optional: Set a deadline for this task
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Date (hidden, defaults to today) -->
                        <input type="hidden" name="assigned_date" value="{{ now()->format('Y-m-d') }}">
                    </div>

                    <!-- Footer -->
                    <div class="border-top p-4" style="background-color: #f9fafb;">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('office.work-assignments.index') }}" class="btn" style="border: 1px solid #000000; color: #000000; background-color: #FFFFFF; padding: 0.5rem 1.5rem;">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.5rem 1.5rem;">
                                <i class="fas fa-check me-1"></i>Create Assignment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
