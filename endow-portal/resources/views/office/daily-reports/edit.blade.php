@extends('layouts.admin')

@section('page-title', 'Edit Daily Report')
@section('breadcrumb', 'Home / Office / Daily Reports / Edit')

@section('content')
<link rel="stylesheet" href="{{ asset('css/daily-reports-compact.css') }}">
<div class="container-fluid daily-reports-container">
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
                    <form action="{{ route('office.daily-reports.update', $dailyReport) }}" method="POST" enctype="multipart/form-data">
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
                            <label for="description" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-align-left me-2" style="color: #DC143C;"></i>
                                Report Description <span style="color: #DC143C;">*</span>
                            </label>
                            <div id="quill-editor" style="height: 300px;"></div>
                            <textarea name="description"
                                      id="description"
                                      class="d-none @error('description') is-invalid @enderror"
                                      {{ $dailyReport->isReviewed() ? 'disabled' : '' }}
                                      required>{{ old('description', $dailyReport->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Provide detailed information about your activities</small>
                        </div>

                        <!-- Priority -->
                        <div class="mb-4">
                            <label for="priority" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-flag me-2" style="color: #DC143C;"></i>
                                Priority Level
                            </label>
                            <select name="priority" 
                                    id="priority" 
                                    class="form-select @error('priority') is-invalid @enderror" 
                                    style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                                    {{ $dailyReport->isReviewed() ? 'disabled' : '' }}>
                                <option value="normal" {{ old('priority', $dailyReport->priority ?? 'normal') == 'normal' ? 'selected' : '' }}>
                                    Normal - Standard priority
                                </option>
                                <option value="high" {{ old('priority', $dailyReport->priority) == 'high' ? 'selected' : '' }}>
                                    High - Requires attention
                                </option>
                                <option value="urgent" {{ old('priority', $dailyReport->priority) == 'urgent' ? 'selected' : '' }}>
                                    Urgent - Immediate action needed
                                </option>
                                <option value="low" {{ old('priority', $dailyReport->priority) == 'low' ? 'selected' : '' }}>
                                    Low - Can wait
                                </option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Update priority level if circumstances have changed</small>
                        </div>

                        <!-- Tags -->
                        <div class="mb-4">
                            <label for="tags" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-tags me-2" style="color: #DC143C;"></i>
                                Tags <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="text" 
                                   name="tags" 
                                   id="tags" 
                                   class="form-control @error('tags') is-invalid @enderror"
                                   placeholder="e.g., meeting, client, urgent"
                                   value="{{ old('tags', $dailyReport->tags ?? '') }}"
                                   {{ $dailyReport->isReviewed() ? 'disabled' : '' }}
                                   style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Separate tags with commas</small>
                        </div>

                        <!-- Current Attachments -->
                        @if(isset($dailyReport->attachments) && $dailyReport->attachments->count() > 0)
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-paperclip me-2" style="color: #DC143C;"></i>
                                Current Attachments
                            </label>
                            <div class="list-group">
                                @foreach($dailyReport->attachments as $attachment)
                                <div class="list-group-item d-flex justify-content-between align-items-center" style="border: 1px solid #E0E0E0; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-alt" style="color: #DC143C;"></i>
                                        <span>{{ $attachment->file_name }}</span>
                                        <small class="text-muted">({{ $attachment->file_size }})</small>
                                    </div>
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="btn btn-sm" style="background-color: rgba(220, 20, 60, 0.1); color: #DC143C; border: none;">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Add New Attachments -->
                        @if(!$dailyReport->isReviewed())
                        <div class="mb-4">
                            <label for="attachments" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-paperclip me-2" style="color: #DC143C;"></i>
                                Add New Attachments <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="file" 
                                   name="attachments[]" 
                                   id="attachments" 
                                   class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
                                   multiple
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt"
                                   style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                            @error('attachments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                You can select multiple files. Supported: PDF, Word, Excel, Images (Max: 10MB each)
                            </small>
                            <div id="file-preview" class="mt-3"></div>
                        </div>
                        @endif

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-tasks me-2" style="color: #DC143C;"></i>
                                Submission Type <span style="color: #DC143C;">*</span>
                            </label>
                            <select name="status"
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;"
                                    {{ $dailyReport->isCompleted() ? 'disabled' : '' }}
                                    required>
                                <option value="draft" {{ old('status', $dailyReport->status) == 'draft' ? 'selected' : '' }}>
                                    Save as Draft - Continue later
                                </option>
                                <option value="submitted" {{ old('status', $dailyReport->status) == 'submitted' ? 'selected' : '' }}>
                                    Submit for Approval - Ready for review
                                </option>
                                @if(in_array($dailyReport->status, ['pending_review', 'in_progress', 'review', 'approved', 'completed']))
                                <option value="{{ $dailyReport->status }}" selected>
                                    {{ ucwords(str_replace('_', ' ', $dailyReport->status)) }}
                                </option>
                                @endif
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                {{ $dailyReport->isCompleted() ? 'This report has been completed and cannot be changed' : 'Update status based on your progress' }}
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-end pt-4" style="border-top: 2px solid #E0E0E0;">
                            <a href="{{ route('office.daily-reports.index') }}" class="btn btn-lg" style="background-color: #F8F9FA; color: #000000; border: 2px solid #E0E0E0; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            @if(!$dailyReport->isReviewed())
                            <button type="submit" class="btn btn-lg" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600;">
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

    // File preview for new attachments
    const attachmentsInput = document.getElementById('attachments');
    if (attachmentsInput) {
        attachmentsInput.addEventListener('change', function(e) {
            const previewDiv = document.getElementById('file-preview');
            previewDiv.innerHTML = '';
            
            if (this.files.length > 0) {
                const fileList = document.createElement('div');
                fileList.className = 'mt-2';
                
                Array.from(this.files).forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'alert alert-info d-flex justify-content-between align-items-center mb-2';
                    fileItem.style.padding = '0.5rem 1rem';
                    
                    const fileInfo = document.createElement('div');
                    fileInfo.innerHTML = `
                        <i class="fas fa-file me-2"></i>
                        <strong>${file.name}</strong>
                        <small class="text-muted ms-2">(${(file.size / 1024).toFixed(2)} KB)</small>
                    `;
                    
                    fileItem.appendChild(fileInfo);
                    fileList.appendChild(fileItem);
                });
                
                previewDiv.appendChild(fileList);
            }
        });
    }
    @endif
</script>
@endsection
