@extends('layouts.admin')

@section('page-title', 'Create Checklist Item')
@section('breadcrumb', 'Home / Checklist Items / Create')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2"></i>Add Checklist Item</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('checklist-items.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Document Name <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control @error('title') is-invalid @enderror"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       placeholder="e.g., Passport Copy, Transcripts"
                       required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Enter the name of the document students need to upload</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description"
                          name="description"
                          rows="4"
                          placeholder="Provide detailed instructions or requirements...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Optional: Add instructions for students about this document</small>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input"
                           type="checkbox"
                           id="is_required"
                           name="is_required"
                           value="1"
                           {{ old('is_required') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_required">
                        Required Document
                    </label>
                </div>
                <small class="form-text text-muted d-block ms-4">Students must upload this document to complete their application</small>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input"
                           type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">
                        Active
                    </label>
                </div>
                <small class="form-text text-muted d-block ms-4">Only active items will be assigned to new students</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save me-2"></i> Create Checklist Item
                </button>
                <a href="{{ route('checklist-items.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
