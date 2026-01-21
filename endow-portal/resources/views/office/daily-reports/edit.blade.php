@extends('layouts.admin')

@section('page-title', 'Edit Daily Report')
@section('breadcrumb', 'Home / Office / Daily Reports / Edit')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <h2 class="page-title mb-2">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Daily Report
                </h2>
                <p class="text-muted mb-0">Update your daily report details</p>
            </div>

            <!-- Alert if reviewed -->
            @if($dailyReport->isReviewed())
            <div class="alert alert-warning border-0 mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Notice:</strong> This report has already been reviewed and cannot be edited.
            </div>
            @endif

            <!-- Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Report Details
                    </h5>
                </div>
                <div class="card-body-custom">
                    <form action="{{ route('office.daily-reports.update', $dailyReport) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Department (Auto-assigned) -->
                        <div class="mb-3">
                            <label for="department_display" class="form-label">Department</label>
                            <input type="text"
                                   id="department_display"
                                   class="form-control"
                                   value="{{ $dailyReport->department?->name ?? 'No Department Assigned' }}"
                                   readonly
                                   disabled>
                            <small class="text-muted">Department is set based on report creator</small>
                        </div>

                        <!-- Report Date -->
                        <div class="mb-3">
                            <label for="report_date" class="form-label">Report Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="report_date"
                                   id="report_date"
                                   class="form-control @error('report_date') is-invalid @enderror"
                                   value="{{ old('report_date', $dailyReport->report_date->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   {{ $dailyReport->isReviewed() ? 'disabled' : '' }}
                                   required>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Report Title <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $dailyReport->title) }}"
                                   maxlength="255"
                                   {{ $dailyReport->isReviewed() ? 'disabled' : '' }}
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Report Description <span class="text-danger">*</span></label>
                            <div id="quill-editor" style="height: 300px;"></div>
                            <textarea name="description"
                                      id="description"
                                      class="d-none @error('description') is-invalid @enderror"
                                      {{ $dailyReport->isReviewed() ? 'disabled' : '' }}
                                      required>{{ old('description', $dailyReport->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status"
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    {{ $dailyReport->isCompleted() ? 'disabled' : '' }}
                                    required>
                                <option value="in_progress" {{ old('status', $dailyReport->status) == 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="review" {{ old('status', $dailyReport->status) == 'review' ? 'selected' : '' }}>
                                    Ready for Review
                                </option>
                                <option value="completed" {{ old('status', $dailyReport->status) == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Update the status based on your progress</small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('office.daily-reports.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            @if(!$dailyReport->isReviewed())
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Report
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 250px;
        font-size: 15px;
    }
    .ql-toolbar {
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }
    .ql-container {
        border-radius: 0 0 8px 8px;
    }
</style>
@endsection

@section('scripts')
<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    // Initialize Quill Editor
    @if($dailyReport->isReviewed())
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        readOnly: true,
        modules: {
            toolbar: false
        },
        placeholder: 'This report has been reviewed and cannot be edited'
    });
    @else
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        readOnly: false,
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['link'],
                ['clean']
            ]
        },
        placeholder: 'Detailed description of daily activities, achievements, challenges, and action items...'
    });
    @endif

    // Set initial content
    const descriptionContent = document.getElementById('description').value;
    if (descriptionContent) {
        quill.root.innerHTML = descriptionContent;
    }

    // Sync Quill content to hidden textarea on form submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            document.getElementById('description').value = quill.root.innerHTML;
        });
    }

    // Real-time sync for validation
    @if(!$dailyReport->isReviewed())
    quill.on('text-change', function() {
        document.getElementById('description').value = quill.root.innerHTML;
    });
    @endif
</script>
@endsection
