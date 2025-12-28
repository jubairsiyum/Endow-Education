@extends('layouts.admin')

@section('page-title', 'Create Checklist Item')
@section('breadcrumb', 'Home / Checklist Items / Create')

@section('content')
<div class="card-custom">
    <div class="card-body">
        <form action="{{ route('checklist-items.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control @error('title') is-invalid @enderror"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Name of the document or requirement</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description"
                          name="description"
                          rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Detailed instructions for this checklist item</small>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           id="is_required"
                           name="is_required"
                           value="1"
                           {{ old('is_required') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_required">
                        Required Document
                    </label>
                    <small class="form-text text-muted d-block">Students must upload this document to complete their application</small>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                    <small class="form-text text-muted d-block">Only active items will be assigned to new students</small>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom">
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
