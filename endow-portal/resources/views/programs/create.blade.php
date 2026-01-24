@extends('layouts.admin')

@section('page-title', 'Add Program')
@section('breadcrumb', 'Home / Configuration / Programs / Create')

@section('content')
<div style="width: 100%; max-width: 100%;">
    <div class="mb-3">
        <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Programs
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-graduation-cap me-2"></i>Add New Program</h5>
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

            <form action="{{ route('programs.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">University <span class="text-danger">*</span></label>
                        <select name="university_id" class="form-select @error('university_id') is-invalid @enderror" required>
                            <option value="">Select University</option>
                            @foreach($universities as $university)
                                <option value="{{ $university->id }}" {{ old('university_id', request('university_id')) == $university->id ? 'selected' : '' }}>
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
                               value="{{ old('name') }}" required placeholder="e.g., Master of Business Administration">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Program Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}" required placeholder="e.g., MBA-001">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Level <span class="text-danger">*</span></label>
                        <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                            <option value="">Select Level</option>
                            <option value="undergraduate" {{ old('level') == 'undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                            <option value="postgraduate" {{ old('level') == 'postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                            <option value="phd" {{ old('level') == 'phd' ? 'selected' : '' }}>PhD</option>
                            <option value="diploma" {{ old('level') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                            <option value="certificate" {{ old('level') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                        </select>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control @error('duration') is-invalid @enderror"
                               value="{{ old('duration') }}" placeholder="e.g., 2 years">
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="order" class="form-control @error('order') is-invalid @enderror"
                               value="{{ old('order', 0) }}" min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Tuition Fee</label>
                        <div class="input-group">
                            <select name="currency" class="form-select @error('currency') is-invalid @enderror" style="max-width: 100px;">
                                <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="KRW" {{ old('currency') == 'KRW' ? 'selected' : '' }}>KRW</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD</option>
                                <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD</option>
                            </select>
                            <input type="number" name="tuition_fee" class="form-control @error('tuition_fee') is-invalid @enderror"
                                   value="{{ old('tuition_fee') }}" step="0.01" min="0" placeholder="0.00">
                        </div>
                        @error('tuition_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Status
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4" placeholder="Brief description about the program...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr>
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-calendar-alt text-danger me-2"></i>Document Submission Deadlines</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Default Deadline for All Documents</label>
                                <input type="date" name="default_deadline" class="form-control @error('default_deadline') is-invalid @enderror"
                                       value="{{ old('default_deadline') }}">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> 
                                    Set a default deadline for all documents in this program. 
                                    Individual documents can override this deadline below.
                                </small>
                                @error('default_deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-list text-danger me-2"></i>Required Checklist Items</h6>
                        <p class="text-muted small mb-3">Select the documents required for this program. Students will see only these checklist items. Optionally set specific deadlines for individual documents.</p>

                        @if($checklistItems->isEmpty())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No checklist items available. Please <a href="{{ route('checklist-items.create') }}" class="alert-link">create checklist items</a> first.
                            </div>
                        @else
                            <div id="checklistItemsContainer">
                                @foreach($checklistItems as $item)
                                    <div class="card mb-2 border">
                                        <div class="card-body p-3">
                                            <div class="row align-items-start">
                                                <div class="col-md-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input checklist-toggle" type="checkbox" name="checklist_items[]"
                                                               value="{{ $item->id }}" id="checklist_{{ $item->id }}" data-item-id="{{ $item->id }}"
                                                               {{ in_array($item->id, old('checklist_items', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="checklist_{{ $item->id }}">
                                                            <strong>{{ $item->title }}</strong>
                                                            @if($item->is_required)
                                                                <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Required</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    @if($item->description)
                                                        <small class="text-muted d-block">{{ $item->description }}</small>
                                                    @endif
                                                </div>
                                                <div class="col-md-3 text-md-end">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar"></i>
                                                        <a href="javascript:void(0)" class="deadline-link" data-item-id="{{ $item->id }}">
                                                            Set deadline
                                                        </a>
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="deadline-section mt-3 p-3 bg-light rounded d-none" id="deadline_{{ $item->id }}">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input deadline-toggle" type="checkbox" 
                                                           name="document_deadlines[{{ $loop->index }}][has_specific]"
                                                           id="deadline_toggle_{{ $item->id }}" data-item-id="{{ $item->id }}">
                                                    <label class="form-check-label" for="deadline_toggle_{{ $item->id }}">
                                                        Use specific deadline for this document
                                                    </label>
                                                </div>
                                                <input type="hidden" name="document_deadlines[{{ $loop->index }}][checklist_item_id]" 
                                                       value="{{ $item->id }}">
                                                <div class="mt-2 deadline-date-group d-none">
                                                    <label class="form-label small">Specific Deadline Date</label>
                                                    <input type="date" 
                                                           name="document_deadlines[{{ $loop->index }}][specific_deadline]"
                                                           class="form-control form-control-sm"
                                                           value="{{ old('document_deadlines.' . $loop->index . '.specific_deadline') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-1"></i> Create Program
                    </button>
                    <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle deadline section visibility
    document.querySelectorAll('.deadline-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.itemId;
            const section = document.getElementById('deadline_' + itemId);
            section.classList.toggle('d-none');
        });
    });

    // Toggle date picker visibility when checkbox is checked/unchecked
    document.querySelectorAll('.deadline-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const dateGroup = this.closest('.deadline-section').querySelector('.deadline-date-group');
            if (this.checked) {
                dateGroup.classList.remove('d-none');
            } else {
                dateGroup.classList.add('d-none');
            }
        });
    });

    // Sync checklist toggle with deadline section
    document.querySelectorAll('.checklist-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const deadlineSection = document.getElementById('deadline_' + itemId);
            if (!this.checked && !deadlineSection.classList.contains('d-none')) {
                deadlineSection.classList.add('d-none');
                // Uncheck the deadline toggle too
                const toggle = deadlineSection.querySelector('.deadline-toggle');
                if (toggle) {
                    toggle.checked = false;
                    toggle.dispatchEvent(new Event('change'));
                }
            }
        });
    });
});
</script>
@endsection
