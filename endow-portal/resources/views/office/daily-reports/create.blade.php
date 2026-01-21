@extends('layouts.admin')

@section('page-title', 'Submit Daily Report')
@section('breadcrumb', 'Home / Office / Daily Reports / Create')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <h2 class="page-title mb-2">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Submit Daily Report
                </h2>
                <p class="text-muted mb-0">Submit your department's daily activities and updates</p>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Report Details
                    </h5>
                </div>
                <div class="card-body-custom">
                    <form action="{{ route('office.daily-reports.store') }}" method="POST">
                        @csrf

                        <!-- Department (Auto-assigned) -->
                        <div class="mb-3">
                            <label for="department_display" class="form-label">Department</label>
                            <input type="text"
                                   id="department_display"
                                   class="form-control"
                                   value="{{ auth()->user()->department?->name ?? 'No Department Assigned' }}"
                                   readonly
                                   disabled>
                            <small class="text-muted">Your department is assigned by your superior</small>
                        </div>

                        <!-- Report Date -->
                        <div class="mb-3">
                            <label for="report_date" class="form-label">Report Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="report_date"
                                   id="report_date"
                                   class="form-control @error('report_date') is-invalid @enderror"
                                   value="{{ old('report_date', now()->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   required>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Cannot select future dates</small>
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Report Title <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   placeholder="e.g., Daily Activities Summary - Marketing Team"
                                   maxlength="255"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Brief summary of today's activities</small>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Report Description <span class="text-danger">*</span></label>
                            <div id="quill-editor" style="height: 300px;"></div>
                            <textarea name="description"
                                      id="description"
                                      class="d-none @error('description') is-invalid @enderror"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Include achievements, challenges, meetings attended, tasks completed, etc.</small>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="in_progress" {{ old('status', 'in_progress') == 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>
                                    Ready for Review
                                </option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Set the current status of your tasks</small>
                        </div>

                        <!-- Guidelines Box -->
                        <div class="alert alert-info border-0 mb-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Report Guidelines
                            </h6>
                            <ul class="mb-0 small">
                                <li>Be specific and detailed about activities performed</li>
                                <li>Mention any challenges faced and how they were addressed</li>
                                <li>Include key metrics or results achieved (if applicable)</li>
                                <li>List any action items or follow-ups required</li>
                                <li>Reports cannot be edited once reviewed by admin</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('office.daily-reports.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Report
                            </button>
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
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
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

    // Set initial content if exists
    const oldDescription = document.getElementById('description').value;
    if (oldDescription) {
        quill.root.innerHTML = oldDescription;
    }

    // Sync Quill content to hidden textarea on form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        document.getElementById('description').value = quill.root.innerHTML;
    });

    // Real-time sync for validation
    quill.on('text-change', function() {
        document.getElementById('description').value = quill.root.innerHTML;
    });
</script>
@endsection
