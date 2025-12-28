@extends('layouts.admin')

@section('page-title', 'Edit Program')
@section('breadcrumb', 'Home / Configuration / Programs / Edit')

@section('content')
<div style="width: 100%; max-width: 100%;">
    <div class="mb-3">
        <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Programs
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i>Edit Program</h5>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
            <div class="alert alert-danger">
                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('programs.update', $program) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">University <span class="text-danger">*</span></label>
                        <select name="university_id" class="form-select @error('university_id') is-invalid @enderror" required>
                            <option value="">Select University</option>
                            @foreach($universities as $university)
                                <option value="{{ $university->id }}" {{ old('university_id', $program->university_id) == $university->id ? 'selected' : '' }}>
                                    {{ $university->name }} ({{ $university->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('university_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Program Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $program->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Program Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $program->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Level <span class="text-danger">*</span></label>
                        <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                            <option value="">Select Level</option>
                            <option value="undergraduate" {{ old('level', $program->level) == 'undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                            <option value="postgraduate" {{ old('level', $program->level) == 'postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                            <option value="phd" {{ old('level', $program->level) == 'phd' ? 'selected' : '' }}>PhD</option>
                            <option value="diploma" {{ old('level', $program->level) == 'diploma' ? 'selected' : '' }}>Diploma</option>
                            <option value="certificate" {{ old('level', $program->level) == 'certificate' ? 'selected' : '' }}>Certificate</option>
                        </select>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control @error('duration') is-invalid @enderror"
                               value="{{ old('duration', $program->duration) }}">
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="order" class="form-control @error('order') is-invalid @enderror"
                               value="{{ old('order', $program->order) }}" min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Tuition Fee</label>
                        <div class="input-group">
                            <select name="currency" class="form-select @error('currency') is-invalid @enderror" style="max-width: 100px;">
                                <option value="USD" {{ old('currency', $program->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency', $program->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ old('currency', $program->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="AUD" {{ old('currency', $program->currency) == 'AUD' ? 'selected' : '' }}>AUD</option>
                                <option value="CAD" {{ old('currency', $program->currency) == 'CAD' ? 'selected' : '' }}>CAD</option>
                            </select>
                            <input type="number" name="tuition_fee" class="form-control @error('tuition_fee') is-invalid @enderror"
                                   value="{{ old('tuition_fee', $program->tuition_fee) }}" step="0.01" min="0">
                        </div>
                        @error('tuition_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Status
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4">{{ old('description', $program->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr>
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-list text-danger me-2"></i>Required Checklist Items</h6>
                        <p class="text-muted small mb-3">Select the documents required for this program.</p>

                        @php
                            $selectedItems = old('checklist_items', $program->checklistItems->pluck('id')->toArray());
                        @endphp

                        @if($checklistItems->isEmpty())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No checklist items available.
                            </div>
                        @else
                            <div class="row g-2">
                                @foreach($checklistItems as $item)
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded">
                                            <input class="form-check-input" type="checkbox" name="checklist_items[]"
                                                   value="{{ $item->id }}" id="checklist_{{ $item->id }}"
                                                   {{ in_array($item->id, $selectedItems) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="checklist_{{ $item->id }}">
                                                <strong>{{ $item->title }}</strong>
                                                @if($item->is_required)
                                                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Required</span>
                                                @endif
                                                @if($item->description)
                                                    <br><small class="text-muted">{{ Str::limit($item->description, 60) }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Program
                    </button>
                    <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
