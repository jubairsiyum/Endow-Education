@extends('layouts.admin')

@section('page-title', 'Submit Daily Report')
@section('breadcrumb', 'Home / Office / Daily Reports / Create')

@section('content')
<style>
    /* Scoped Daily Report Styles - Won't affect global layout */
    .daily-report-page .dr-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        padding: 1.75rem;
        border-radius: 12px;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.25);
    }
    
    .daily-report-page .dr-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    
    .daily-report-page .dr-card-header {
        background: #f9fafb;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 12px 12px 0 0;
    }
    
    .daily-report-page .dr-label {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .daily-report-page .dr-input,
    .daily-report-page .dr-select {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
        font-size: 0.9375rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        width: 100%;
    }
    
    .daily-report-page .dr-input:focus,
    .daily-report-page .dr-select:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        outline: none;
    }
    
    .daily-report-page .dr-input:disabled {
        background-color: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }
    
    .daily-report-page .required-star {
        color: #dc2626;
        margin-left: 0.125rem;
    }
    
    .daily-report-page .dr-hint {
        font-size: 0.8125rem;
        color: #6b7280;
        margin-top: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .daily-report-page .dr-icon-badge {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        font-size: 0.8125rem;
        flex-shrink: 0;
    }
    
    .daily-report-page .dr-guidelines {
        background: #fff5f5;
        border-left: 4px solid #dc3545;
        border-radius: 8px;
        padding: 1.25rem;
    }
    
    .daily-report-page .dr-guidelines ul {
        margin: 0.5rem 0 0 0;
        padding-left: 1.25rem;
    }
    
    .daily-report-page .dr-guidelines li {
        margin-bottom: 0.5rem;
        color: #374151;
        font-size: 0.9rem;
    }
    
    .daily-report-page .dr-btn {
        padding: 0.625rem 1.75rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        border: none;
    }
    
    .daily-report-page .dr-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .daily-report-page .dr-btn-primary {
        background: #dc3545;
        color: white;
    }
    
    .daily-report-page .dr-btn-primary:hover {
        background: #c82333;
        color: white;
    }
    
    .daily-report-page .dr-btn-secondary {
        background: white;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    
    .daily-report-page .dr-btn-secondary:hover {
        background: #f9fafb;
        color: #374151;
    }
    
    .daily-report-page .dr-btn-back {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 0.5rem 1rem;
        border-radius: 7px;
        font-weight: 500;
    }
    
    .daily-report-page .dr-btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
        color: white;
    }
    
    .daily-report-page .dr-file-preview {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 7px;
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .daily-report-page .dr-quill-wrapper {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .daily-report-page .dr-quill-wrapper:focus-within {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }
    
    .daily-report-page .ql-toolbar.ql-snow {
        background: #f9fafb;
        border: none !important;
        border-bottom: 1px solid #e5e7eb !important;
    }
    
    .daily-report-page .ql-container.ql-snow {
        border: none !important;
        font-size: 15px;
    }
    
    .daily-report-page .ql-editor {
        min-height: 220px;
    }
    
    .daily-report-page .dr-section {
        padding: 1.5rem;
    }
    
    .daily-report-page .dr-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 1.5rem 0;
    }
    
    .daily-report-page .dr-footer {
        background: #f9fafb;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        border-radius: 0 0 12px 12px;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .daily-report-page .dr-header {
            padding: 1.25rem;
        }
        .daily-report-page .dr-section {
            padding: 1rem;
        }
        .daily-report-page .dr-btn {
            width: 100%;
            justify-content: center;
        }
        .daily-report-page .dr-footer {
            padding: 1rem;
        }
    }
</style>

<div class="daily-report-page">
<div class="container-fluid px-3 py-3">
    
    <!-- Header -->
    <div class="dr-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="mb-2 fw-bold">📝 Submit Daily Report</h3>
                <p class="mb-0 opacity-90" style="font-size: 0.95rem;">Document your daily activities and accomplishments</p>
            </div>
            <a href="{{ route('office.daily-reports.index') }}" class="dr-btn dr-btn-back">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">
            <div class="dr-card">
                <div class="dr-card-header">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-edit me-2" style="color: #dc3545;"></i>
                        Report Information
                    </h5>
                </div>
                
                <form action="{{ route('office.daily-reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="dr-section">
                        <!-- Department -->
                        <div class="mb-4">
                            <label class="dr-label">
                                <span class="dr-icon-badge"><i class="fas fa-building"></i></span>
                                Department
                            </label>
                            <input type="text" class="dr-input" value="{{ auth()->user()->department?->name ?? 'No Department Assigned' }}" disabled>
                            <div class="dr-hint">
                                <i class="fas fa-info-circle"></i> Auto-assigned based on your profile
                            </div>
                        </div>

                        <!-- Report Date -->
                        <div class="mb-4">
                            <label class="dr-label">
                                <span class="dr-icon-badge"><i class="fas fa-calendar-alt"></i></span>
                                Report Date <span class="required-star">*</span>
                            </label>
                            <input type="date" name="report_date" id="report_date" class="dr-input @error('report_date') is-invalid @enderror" value="{{ old('report_date', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="dr-hint">
                                <i class="fas fa-clock"></i> Cannot select future dates
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label class="dr-label">
                                <span class="dr-icon-badge"><i class="fas fa-heading"></i></span>
                                Report Title <span class="required-star">*</span>
                            </label>
                            <input type="text" name="title" class="dr-input @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g., Daily Activities Summary - Marketing Team" maxlength="255" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="dr-hint">
                                <i class="fas fa-lightbulb"></i> Brief summary of today's activities
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="dr-label">
                                <span class="dr-icon-badge"><i class="fas fa-align-left"></i></span>
                                Report Description <span class="required-star">*</span>
                            </label>
                            <div class="dr-quill-wrapper">
                                <div id="quill-editor" style="height: 250px;"></div>
                            </div>
                            <textarea name="description" id="description" class="d-none @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="dr-hint">
                                <i class="fas fa-pen"></i> Include achievements, challenges, meetings, and tasks completed
                            </div>
                        </div>

                        <!-- Work Assignments Section -->
                        <div class="mb-4">
                            <label class="dr-label">
                                <span class="dr-icon-badge"><i class="fas fa-tasks"></i></span>
                                My Assigned Tasks & Completion Status
                                @if(isset($workAssignments) && $workAssignments->count() > 0)
                                <span class="badge" style="background-color: #dc3545; color: white; font-size: 0.7rem; padding: 0.35rem 0.65rem; border-radius: 12px; margin-left: 0.5rem;">
                                    {{ $workAssignments->count() }} {{ $workAssignments->count() === 1 ? 'Task' : 'Tasks' }} Available
                                </span>
                                @endif
                            </label>
                            <div class="dr-hint mb-3">
                                <i class="fas fa-info-circle"></i> Select the work assignments you want to include in this report. Completed tasks will be automatically marked. All pending, in-progress, and completed tasks (not yet reported) are shown here.
                            </div>
                            
                            @if(isset($workAssignments) && $workAssignments->count() > 0)
                            
                            <div class="work-assignments-list" style="max-height: 480px; overflow-y: auto; border: 2px solid #e5e7eb; border-radius: 8px; padding: 1.25rem; background-color: #f9fafb;">
                                <div class="alert alert-primary mb-3" style="background-color: #e3f2fd; border-color: #90caf9; border-radius: 6px; padding: 0.75rem 1rem;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Found {{ $workAssignments->count() }} task(s)</strong> - Check tasks to include them in your report
                                </div>
                                
                                @foreach($workAssignments as $assignment)
                                <div class="work-assignment-item mb-3 p-3" style="background-color: #ffffff; border-radius: 6px; border-left: 4px solid {{ $assignment->isOverdue() ? '#dc3545' : ($assignment->priority === 'urgent' ? '#dc3545' : ($assignment->priority === 'high' ? '#fd7e14' : '#0d6efd')) }}; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" type="checkbox" name="work_assignments[]" value="{{ $assignment->id }}" id="wa_{{ $assignment->id }}" {{ $assignment->status === 'completed' ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark" for="wa_{{ $assignment->id }}" style="font-size: 0.95rem;">
                                                    {{ $assignment->title }}
                                                </label>
                                            </div>
                                            <div class="ms-4 mt-1">
                                                <small class="text-muted d-block mb-2">{{ Str::limit($assignment->description, 100) }}</small>
                                                
                                                <!-- Completion Status Checkbox -->
                                                <div class="form-check form-check-inline mb-2">
                                                    <input class="form-check-input" type="checkbox" name="task_completed[{{ $assignment->id }}]" value="1" id="completed_{{ $assignment->id }}" 
                                                        {{ $assignment->status === 'completed' ? 'checked disabled' : 'disabled' }}
                                                        style="{{ $assignment->status === 'completed' ? '' : 'cursor: not-allowed; opacity: 0.5;' }}">
                                                    <label class="form-check-label" for="completed_{{ $assignment->id }}" style="font-size: 0.875rem; {{ $assignment->status === 'completed' ? '' : 'color: #6c757d;' }}">
                                                        <i class="fas fa-{{ $assignment->status === 'completed' ? 'check-circle' : 'info-circle' }} me-1" style="color: {{ $assignment->status === 'completed' ? '#198754' : '#6c757d' }};"></i>
                                                        <strong>{{ $assignment->status === 'completed' ? 'Already Completed' : 'Mark as Completed' }}</strong>
                                                        @if($assignment->status !== 'completed')
                                                        <small class="d-block text-muted" style="font-weight: normal; font-size: 0.75rem; margin-top: 0.25rem;">
                                                            Tasks must be completed in Work Assignments module first
                                                        </small>
                                                        @endif
                                                    </label>
                                                </div>
                                                
                                                <!-- Task Details -->
                                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                                    <span class="badge badge-sm" style="background-color: {{ $assignment->priority === 'urgent' ? '#dc3545' : ($assignment->priority === 'high' ? '#fd7e14' : ($assignment->priority === 'normal' ? '#0d6efd' : '#6c757d')) }}; color: #FFFFFF; font-size: 0.7rem;">
                                                        <i class="fas fa-flag"></i> {{ strtoupper($assignment->priority) }}
                                                    </span>
                                                    <span class="badge badge-sm" style="background-color: {{ $assignment->status === 'completed' ? '#198754' : ($assignment->status === 'in_progress' ? '#0dcaf0' : '#ffc107') }}; color: {{ $assignment->status === 'completed' ? '#FFFFFF' : '#000000' }}; font-size: 0.7rem;">
                                                        <i class="fas fa-{{ $assignment->status === 'completed' ? 'check-circle' : ($assignment->status === 'in_progress' ? 'spinner' : 'clock') }}"></i> {{ strtoupper(str_replace('_', ' ', $assignment->status)) }}
                                                    </span>
                                                    @if($assignment->due_date)
                                                        @if($assignment->isOverdue())
                                                        <span class="badge badge-sm" style="background-color: #dc3545; color: #FFFFFF; font-size: 0.7rem;">
                                                            <i class="fas fa-exclamation-triangle"></i> OVERDUE
                                                        </span>
                                                        @elseif($assignment->isDueSoon())
                                                        <span class="badge badge-sm" style="background-color: #ffc107; color: #000000; font-size: 0.7rem;">
                                                            <i class="fas fa-clock"></i> DUE SOON
                                                        </span>
                                                        @endif
                                                    @endif
                                                    
                                                    @if($assignment->assignedBy)
                                                    <small class="text-muted">
                                                        <i class="fas fa-user-tie"></i> From: {{ $assignment->assignedBy->name }}
                                                    </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if($assignment->due_date)
                                        <div class="text-end ms-2" style="min-width: 70px;">
                                            <small class="text-muted fw-semibold d-block">Due Date</small>
                                            <small class="text-dark fw-bold">{{ $assignment->due_date->format('M d, Y') }}</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="alert alert-info mt-3 mb-0" style="background-color: #e7f3ff; border-color: #b6d9f7; border-radius: 6px;">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Tip:</strong> Completed tasks are automatically included. To mark a task as completed, update it in the Work Assignments module first, then include it here.
                            </div>
                            
                            @else
                            <!-- No assignments available message -->
                            <div class="alert alert-warning" style="background-color: #fff8e1; border-color: #ffd54f; border-radius: 8px; border-left: 4px solid #ffa726;">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle me-3" style="color: #f57c00; font-size: 1.5rem; margin-top: 0.25rem;"></i>
                                    <div>
                                        <h6 class="mb-2 fw-bold" style="color: #e65100;">
                                            <i class="fas fa-clipboard-check me-2"></i>No Work Assignments Available
                                        </h6>
                                        <p class="mb-2" style="color: #6d4c41; font-size: 0.9rem;">
                                            You don't have any work assignments (pending, in-progress, or completed) available to include in this report.
                                        </p>
                                        <p class="mb-0" style="color: #6d4c41; font-size: 0.875rem;">
                                            <strong>This could mean:</strong><br>
                                            • All your work assignments are already included in previous reports 📋<br>
                                            • No tasks have been assigned to you recently 📌<br>
                                            • All completed tasks have been reported ✅
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="dr-divider"></div>

                        <!-- Hidden Status Field - Always Submit for Review -->
                        <input type="hidden" name="status" value="submitted">

                        <!-- Tags -->
                        <div class="mb-4">
                            <label class="dr-label">
                                <span class="dr-icon-badge"><i class="fas fa-tags"></i></span>
                                Tags <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="text" name="tags" class="dr-input @error('tags') is-invalid @enderror" placeholder="e.g., meeting, client, urgent" value="{{ old('tags') }}">
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="dr-hint">
                                <i class="fas fa-tag"></i> Separate with commas
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="mb-4">
                            <label class="dr-label">
                                <span class="dr-icon-badge"><i class="fas fa-paperclip"></i></span>
                                Attachments <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="file" name="attachments[]" id="attachments" class="dr-input @error('attachments') is-invalid @enderror" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt">
                            @error('attachments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="dr-hint">
                                <i class="fas fa-info-circle"></i> PDF, Word, Excel, Images • Max 10MB each
                            </div>
                            <div id="file-preview" class="mt-3"></div>
                        </div>

                        <!-- Guidelines -->
                        <div class="dr-guidelines">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-clipboard-list me-2" style="color: #dc3545;"></i>
                                Quick Guidelines
                            </h6>
                            <ul>
                                <li>✍️ Be specific about activities and achievements</li>
                                <li>🎯 Mention key metrics or results achieved</li>
                                <li>⚠️ Note any challenges and how you addressed them</li>
                                <li>📋 List action items or follow-ups required</li>
                                <li>✅ All reports are submitted for manager review automatically</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="dr-footer">
                        <div class="d-flex gap-3 justify-content-end flex-wrap">
                            <a href="{{ route('office.daily-reports.index') }}" class="dr-btn dr-btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="dr-btn dr-btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Report for Review
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        },
        placeholder: 'Describe your daily activities, achievements, challenges, and action items in detail...'
    });

    const oldContent = document.getElementById('description').value;
    if (oldContent) quill.root.innerHTML = oldContent;

    const form = document.querySelector('form');
    form.addEventListener('submit', () => {
        document.getElementById('description').value = quill.root.innerHTML;
    });
    
    quill.on('text-change', () => {
        document.getElementById('description').value = quill.root.innerHTML;
    });

    document.getElementById('attachments').addEventListener('change', function(e) {
        const preview = document.getElementById('file-preview');
        preview.innerHTML = '';
        
        if (this.files.length > 0) {
            Array.from(this.files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'dr-file-preview';
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file" style="color: #dc3545;"></i>
                        <div>
                            <div class="fw-semibold">${file.name}</div>
                            <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                        </div>
                    </div>
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                `;
                preview.appendChild(item);
            });
        }
    });
</script>
@endsection
