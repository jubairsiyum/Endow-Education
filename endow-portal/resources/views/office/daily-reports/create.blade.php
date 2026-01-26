@extends('layouts.admin')

@section('page-title', 'Submit Daily Report')
@section('breadcrumb', 'Home / Office / Daily Reports / Create')

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="page-header-modern mb-4" style="background: linear-gradient(135deg, #DC143C 0%, #A52A2A 100%); padding: 2rem; border-radius: 1rem; color: #FFFFFF; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
        <a href="{{ route('office.daily-reports.index') }}" class="btn shadow-sm" style="background-color: #FFFFFF; color: #000000; border: 1px solid #E0E0E0;">
            <i class="fas fa-arrow-left me-2" style="color: #DC143C;"></i>Back to Reports
        </a>
        <div class="mt-3">
            <h1 class="display-6 fw-bold mb-2">
                <i class="fas fa-file-alt me-3"></i>
                Submit Daily Report
            </h1>
            <p class="mb-0 opacity-75">Submit your department's daily activities and updates</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Form Card -->
            <div class="card shadow-sm border-0 rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <div class="card-header p-4" style="background-color: #F8F9FA; border-bottom: 2px solid #E0E0E0;">
                    <h5 class="mb-0 fw-bold" style="color: #000000;">
                        <i class="fas fa-edit me-2" style="color: #DC143C;"></i>
                        Report Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('office.daily-reports.store') }}" method="POST">
                        @csrf

                        <!-- Department (Auto-assigned) -->
                        <div class="mb-4">
                            <label for="department_display" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-building me-2" style="color: #DC143C;"></i>
                                Department
                            </label>
                            <input type="text"
                                   id="department_display"
                                   class="form-control"
                                   value="{{ auth()->user()->department?->name ?? 'No Department Assigned' }}"
                                   style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem; background-color: #F8F9FA;"
                                   readonly
                                   disabled>
                            <small class="text-muted">Your department is assigned by your superior</small>
                        </div>

                        <!-- Report Date -->
                        <div class="mb-4">
                            <label for="report_date" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-calendar me-2" style="color: #DC143C;"></i>
                                Report Date <span style="color: #DC143C;">*</span>
                            </label>
                            <input type="date"
                                   name="report_date"
                                   id="report_date"
                                   class="form-control @error('report_date') is-invalid @enderror"
                                   value="{{ old('report_date', now()->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                                   required>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Cannot select future dates</small>
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-heading me-2" style="color: #DC143C;"></i>
                                Report Title <span style="color: #DC143C;">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   placeholder="e.g., Daily Activities Summary - Marketing Team"
                                   maxlength="255"
                                   style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Brief summary of today's activities</small>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-align-left me-2" style="color: #DC143C;"></i>
                                Report Description <span style="color: #DC143C;">*</span>
                            </label>
                            <div id="quill-editor" style="height: 300px; border: 2px solid #E0E0E0; border-radius: 0.5rem;"></div>
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
                            <label for="status" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-tasks me-2" style="color: #DC143C;"></i>
                                Status <span style="color: #DC143C;">*</span>
                            </label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;" required>
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
                        <div class="alert border-0 mb-4 rounded" style="background-color: rgba(220, 20, 60, 0.1); border-left: 4px solid #DC143C !important;">
                            <h6 class="alert-heading fw-bold" style="color: #000000;">
                                <i class="fas fa-info-circle me-2" style="color: #DC143C;"></i>
                                Report Guidelines
                            </h6>
                            <ul class="mb-0 small" style="color: #000000;">
                                <li>Be specific and detailed about activities performed</li>
                                <li>Mention any challenges faced and how they were addressed</li>
                                <li>Include key metrics or results achieved (if applicable)</li>
                                <li>List any action items or follow-ups required</li>
                                <li>Reports cannot be edited once reviewed by admin</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-end pt-4" style="border-top: 2px solid #E0E0E0;">
                            <a href="{{ route('office.daily-reports.index') }}" class="btn btn-lg" style="background-color: #F8F9FA; color: #000000; border: 2px solid #E0E0E0; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-lg" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600;">
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
        background: #F8F9FA;
        border: 2px solid #E0E0E0 !important;
        border-bottom: none !important;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .ql-container {
        border: 2px solid #E0E0E0 !important;
        border-radius: 0 0 0.5rem 0.5rem;
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
