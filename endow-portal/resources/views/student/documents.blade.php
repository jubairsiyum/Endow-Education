@extends('layouts.student')

@section('page-title', 'Submit Documents')
@section('breadcrumb', 'Home / Submit Documents')

@section('content')
    <!-- Modern Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-danger-light">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box-large bg-white text-danger">
                                    <i class="fas fa-file-upload fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Document Submission Center</h4>
                                    <p class="mb-0 text-muted">Upload your required documents for review and approval</p>
                                    @if($student->targetProgram)
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-graduation-cap me-1"></i>
                                            Program: <strong>{{ $student->targetProgram->name }}</strong>
                                            @if($student->targetUniversity)
                                                at {{ $student->targetUniversity->name }}
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-inline-flex align-items-center gap-2 px-4 py-2 bg-white rounded-pill shadow-sm">
                                <span class="badge bg-danger rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    {{ $completedCount }}
                                </span>
                                <span class="fw-semibold">of {{ $totalCount }} Completed</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Info Pills -->
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-file-pdf text-danger me-1"></i> PDF Files Only
                        </span>
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-weight text-warning me-1"></i> Max 15MB per file
                        </span>
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-shield-alt text-success me-1"></i> Secure Upload
                        </span>
                        @if($student->targetProgram)
                            <span class="badge bg-white text-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> Program-Specific Documents
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($student->targetProgram)
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Note:</strong> The documents shown below are specifically required for the <strong>{{ $student->targetProgram->name }}</strong> program. If your program changes, your required documents may also change.
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            @if($checklistItems->isEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted mb-2">No Documents Required Yet</h5>
                            <p class="text-muted">Your counselor will assign required documents based on your target program.</p>
                        </div>
                    </div>
                </div>
            @else
                @foreach($checklistItems as $index => $item)
                    @php
                        $studentChecklist = $item->studentChecklists->firstWhere('student_id', $student->id);
                        $status = $studentChecklist->status ?? 'pending';
                        $isCompleted = $status === 'completed' || $status === 'approved';
                        $isRejected = $status === 'rejected';
                        $isPending = $status === 'pending';
                        $isSubmitted = $status === 'submitted';
                        $canEdit = $isPending || $isRejected;

                        $statusConfig = [
                            'completed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Approved', 'bg' => 'success'],
                            'approved' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Approved', 'bg' => 'success'],
                            'submitted' => ['class' => 'info', 'icon' => 'clock', 'text' => 'Under Review', 'bg' => 'info'],
                            'rejected' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Needs Revision', 'bg' => 'danger'],
                            'pending' => ['class' => 'warning', 'icon' => 'exclamation-circle', 'text' => 'Not Submitted', 'bg' => 'warning'],
                        ];
                        $config = $statusConfig[$status] ?? $statusConfig['pending'];
                    @endphp

                    <div class="modern-doc-card mb-3 {{ $isCompleted ? 'completed' : '' }}" data-status="{{ $status }}">
                        <div class="doc-card-inner">
                            <!-- Left Side - Step Number & Info -->
                            <div class="doc-left">
                                <div class="step-number {{ $isCompleted ? 'completed' : '' }}">
                                    @if($isCompleted)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <div class="doc-info">
                                    <h6 class="doc-title">{{ $item->title }}</h6>
                                    @if($item->description)
                                        <p class="doc-description">{{ $item->description }}</p>
                                    @endif

                                    <div class="doc-badges">
                                        <span class="status-badge status-{{ $config['bg'] }}">
                                            <i class="fas fa-{{ $config['icon'] }}"></i>
                                            {{ $config['text'] }}
                                        </span>
                                        @if($item->is_required)
                                            <span class="status-badge status-required">Required</span>
                                        @else
                                            <span class="status-badge status-optional">Optional</span>
                                        @endif
                                        @if($item->programs->isNotEmpty())
                                            <span class="status-badge status-program">
                                                <i class="fas fa-graduation-cap"></i>
                                                {{ $item->programs->first()->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side - Upload Area -->
                            <div class="doc-right">
                                @if($studentChecklist && $studentChecklist->document_path)
                                    <!-- Uploaded Document Display -->
                                    <div class="uploaded-doc">
                                        <div class="file-preview">
                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                            <div class="file-details">
                                                <div class="file-name">{{ basename($studentChecklist->document_path) }}</div>
                                                <div class="file-meta">{{ $studentChecklist->updated_at->format('M d, Y \a\t h:i A') }}</div>
                                            </div>
                                        </div>
                                        <div class="file-actions">
                                            <a href="{{ storage_url($studentChecklist->document_path) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteDocument({{ $studentChecklist->id }}, '{{ $status }}')"
                                                    title="Remove Document">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                @endif

                                @if($isRejected && $studentChecklist && $studentChecklist->document_path)
                                    <!-- Compact Rejection Notice with Inline Resubmit -->
                                    <div class="rejection-compact-card">
                                        <div class="rejection-header">
                                            <div class="rejection-icon-wrapper">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </div>
                                            <div class="rejection-content">
                                                <h6 class="rejection-title">Document Rejected</h6>
                                                @if($studentChecklist->feedback)
                                                    <p class="rejection-message">{{ $studentChecklist->feedback }}</p>
                                                @else
                                                    <p class="rejection-message">Please upload a corrected document.</p>
                                                @endif
                                            </div>
                                        </div>

                                        <form action="{{ route('student.checklist.resubmit', $studentChecklist->id) }}"
                                              method="POST"
                                              enctype="multipart/form-data"
                                              class="compact-resubmit-form"
                                              id="resubmit-form-{{ $item->id }}">
                                            @csrf
                                            <div class="compact-upload-row">
                                                <div class="upload-area-compact" id="resubmit-area-{{ $item->id }}">
                                                    <input type="file"
                                                           name="document"
                                                           id="resubmit-input-{{ $item->id }}"
                                                           class="file-input"
                                                           accept=".pdf"
                                                           required
                                                           onchange="handleFileSelect({{ $item->id }}, this, true)">
                                                    <label for="resubmit-input-{{ $item->id }}" class="compact-upload-label">
                                                        <i class="fas fa-paperclip me-2"></i>
                                                        <span class="label-text">Choose corrected file</span>
                                                        <span class="label-hint">PDF only</span>
                                                    </label>
                                                    <div class="selected-file-compact" id="resubmit-selected-{{ $item->id }}" style="display: none;">
                                                        <i class="fas fa-check-circle me-2"></i>
                                                        <span class="filename"></span>
                                                        <button type="button" class="clear-file-compact" onclick="clearResubmitSelection({{ $item->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn-resubmit-compact" id="resubmit-btn-{{ $item->id }}" style="display: none;">
                                                    <i class="fas fa-redo me-1"></i>
                                                    Resubmit
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @elseif($canEdit && !($studentChecklist && $studentChecklist->document_path))
                                    <!-- Initial Upload Form -->
                                    <form action="{{ route('student.checklist.upload', $item->id) }}"
                                          method="POST"
                                          enctype="multipart/form-data"
                                          class="modern-upload-form"
                                          id="upload-form-{{ $item->id }}">
                                        @csrf
                                        <div class="upload-area" id="upload-area-{{ $item->id }}">
                                            <input type="file"
                                                   name="document"
                                                   id="file-input-{{ $item->id }}"
                                                   class="file-input"
                                                   accept=".pdf"
                                                   required
                                                   onchange="handleFileSelect({{ $item->id }}, this)">
                                            <label for="file-input-{{ $item->id }}" class="upload-label">
                                                <div class="upload-icon">
                                                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                                </div>
                                                <div class="upload-text">
                                                    <span class="upload-main">Choose File or Drag & Drop</span>
                                                    <span class="upload-sub">PDF files only, up to 15MB</span>
                                                </div>
                                            </label>
                                            <div class="selected-file" id="selected-file-{{ $item->id }}" style="display: none;">
                                                <i class="fas fa-file-alt"></i>
                                                <span class="filename"></span>
                                                <button type="button" class="clear-file" onclick="clearFileSelection({{ $item->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-upload w-100 mt-2" id="upload-btn-{{ $item->id }}" style="display: none;">
                                            <i class="fas fa-upload me-2"></i>Confirm Upload
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Progress Sidebar -->
        <div class="col-lg-4">
            <div class="card-custom sticky-top" style="top: 85px;">
                <div class="card-header-custom bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Overall Progress</h5>
                </div>
                <div class="card-body-custom text-center">
                    <div class="mb-4">
                        <div class="position-relative d-inline-block">
                            @php
                                $progressPercentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
                                $circumference = 2 * 3.14159 * 65;
                                $dashOffset = $circumference * (1 - ($completedCount / max($totalCount, 1)));
                            @endphp
                            <svg width="150" height="150">
                                <circle cx="75" cy="75" r="65" fill="none" stroke="#f0f0f0" stroke-width="15"/>
                                <circle cx="75" cy="75" r="65" fill="none" stroke="#DC143C" stroke-width="15"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $dashOffset }}"
                                        transform="rotate(-90 75 75)"
                                        style="transition: stroke-dashoffset 1s ease;"
                                        stroke-linecap="round"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="fs-2 fw-bold text-danger" id="completedCount">{{ $completedCount }}</div>
                                <small class="text-muted">of <span id="totalCount">{{ $totalCount }}</span></small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h3 class="fw-bold mb-1" id="progressPercentage">{{ $progressPercentage }}%</h3>
                        <p class="text-muted mb-0">Complete</p>
                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar"
                                 style="width: {{ $progressPercentage }}%; transition: width 1s ease;"
                                 id="progressBar"
                                 aria-valuenow="{{ $progressPercentage }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <div class="fs-4 fw-bold text-success">{{ $completedCount }}</div>
                                <small class="text-muted">Approved</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <div class="fs-4 fw-bold text-warning">{{ $totalCount - $completedCount }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-start">
                        <h6 class="fw-bold mb-3">Tips for Success</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2 small">
                                <i class="fas fa-check text-success me-2"></i>
                                Ensure documents are clear and legible
                            </li>
                            <li class="mb-2 small">
                                <i class="fas fa-check text-success me-2"></i>
                                Use PDF format when possible
                            </li>
                            <li class="mb-2 small">
                                <i class="fas fa-check text-success me-2"></i>
                                Keep file sizes under 15MB
                            </li>
                            <li class="mb-2 small">
                                <i class="fas fa-check text-success me-2"></i>
                                Submit documents in the order listed
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0">Are you sure you want to remove this document? This action cannot be undone and you'll need to upload a new document.</p>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Document
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Modern Gradient Background */
    .bg-gradient-danger-light {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
    }

    .icon-box-large {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(220, 20, 60, 0.1);
    }

    /* Modern Document Card */
    .modern-doc-card {
        background: #fff;
        border-radius: 15px;
        border: 2px solid #e9ecef;
        overflow-x: hidden;
        overflow-y: visible;
        transition: all 0.3s ease;
        max-width: 100%;
    }

    .modern-doc-card:hover {
        border-color: #DC143C;
        box-shadow: 0 8px 25px rgba(220, 20, 60, 0.1);
        transform: translateY(-2px);
    }

    .modern-doc-card.completed {
        border-color: #28a745;
        background: #f8fff8;
    }

    .doc-card-inner {
        display: flex;
        flex-wrap: wrap;
        padding: 20px;
        gap: 20px;
        max-width: 100%;
        overflow-x: hidden;
    }

    .doc-left {
        flex: 1;
        min-width: 0;
        max-width: 100%;
        display: flex;
        gap: 15px;
    }

    .doc-right {
        flex: 1;
        min-width: 0;
        max-width: 100%;
    }

    /* Step Number */
    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #DC143C, #ff1744);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
    }

    .step-number.completed {
        background: linear-gradient(135deg, #28a745, #34ce57);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    /* Document Info */
    .doc-info {
        flex: 1;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }

    .doc-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .doc-description {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 10px;
        line-height: 1.5;
    }

    .doc-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-success {
        background: #d4edda;
        color: #155724;
    }

    .status-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .status-warning {
        background: #fff3cd;
        color: #856404;
    }

    .status-required {
        background: #DC143C;
        color: white;
    }

    .status-optional {
        background: #6c757d;
        color: white;
    }

    .status-program {
        background: #e9ecef;
        color: #495057;
    }

    /* Upload Area */
    .modern-upload-form {
        position: relative;
    }

    .upload-area {
        position: relative;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        background: #f8f9fa;
        transition: all 0.3s ease;
        overflow: hidden;
        cursor: pointer;
    }

    .upload-area:hover {
        border-color: #DC143C;
        background: #fff5f5;
    }

    .upload-area.dragging {
        border-color: #DC143C;
        background: #ffe5e5;
        border-style: solid;
    }

    .file-input {
        display: none;
    }

    .upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
        cursor: pointer;
        margin: 0;
    }

    .upload-icon {
        color: #DC143C;
        margin-bottom: 12px;
        transition: transform 0.3s ease;
    }

    .upload-area:hover .upload-icon {
        transform: translateY(-5px);
    }

    .upload-text {
        text-align: center;
    }

    .upload-main {
        display: block;
        font-weight: 600;
        font-size: 15px;
        color: #1a1a1a;
        margin-bottom: 6px;
    }

    .upload-sub {
        display: block;
        font-size: 13px;
        color: #6c757d;
    }

    .selected-file {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        background: #e8f5e9;
        border-radius: 8px;
        margin: 10px;
    }

    .selected-file i {
        color: #28a745;
        font-size: 24px;
    }

    .selected-file .filename {
        flex: 1;
        font-weight: 600;
        color: #155724;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .clear-file {
        background: #f8d7da;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: #721c24;
    }

    .clear-file:hover {
        background: #DC143C;
        color: white;
    }

    .btn-upload {
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-upload:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
    }

    /* Uploaded Document Display */
    .uploaded-doc {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 15px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        max-width: 100%;
        overflow-x: hidden;
    }

    .uploaded-doc:hover {
        border-color: #DC143C;
        box-shadow: 0 3px 12px rgba(220, 20, 60, 0.08);
    }

    .file-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 100%;
        overflow: hidden;
    }

    .file-preview i {
        font-size: 28px;
    }

    .file-details {
        flex: 1 1 auto;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }

    .file-name {
        font-weight: 600;
        font-size: 13px;
        color: #1a1a1a;
        margin-bottom: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
        word-break: break-all;
    }

    .file-meta {
        font-size: 11px;
        color: #6c757d;
    }

    .file-actions {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
    }

    .file-actions .btn {
        border-radius: 8px;
        padding: 6px 10px;
    }

    /* Rejection Feedback */
    .rejection-feedback {
        background: #f8d7da;
        border-left: 4px solid #DC143C;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: flex;
        align-items: start;
        gap: 10px;
    }

    .rejection-feedback i {
        color: #DC143C;
        margin-top: 2px;
    }

    .rejection-feedback span {
        flex: 1;
        font-size: 13px;
        color: #721c24;
        line-height: 1.5;
    }

    /* Compact Rejection Card - Enhanced Modern Design */
    .rejection-compact-card {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
        border: 2px solid #ffcdd2;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 0;
        box-shadow: 0 4px 15px rgba(220, 20, 60, 0.12);
        transition: all 0.3s ease;
        overflow: visible;
    }

    .rejection-compact-card:hover {
        box-shadow: 0 6px 20px rgba(220, 20, 60, 0.18);
        transform: translateY(-2px);
    }

    .rejection-header {
        display: flex;
        align-items: start;
        gap: 15px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 2px dashed rgba(220, 20, 60, 0.2);
    }

    .rejection-icon-wrapper {
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #DC143C, #ff1744);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(220, 20, 60, 0.35);
    }

    .rejection-content {
        flex: 1;
        min-width: 0;
    }

    .rejection-title {
        font-size: 15px;
        font-weight: 700;
        color: #c62828;
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rejection-message {
        font-size: 13px;
        color: #d32f2f;
        margin: 0;
        line-height: 1.6;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.6);
        padding: 10px 12px;
        border-radius: 8px;
        border-left: 3px solid #DC143C;
    }

    .compact-resubmit-form {
        margin: 0;
    }

    .compact-upload-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-start;
    }

    .upload-area-compact {
        flex: 1 1 auto;
        min-width: 200px;
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .compact-upload-label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        background: white;
        border: 2px dashed #ffcdd2;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 52px;
        box-shadow: 0 2px 6px rgba(220, 20, 60, 0.08);
    }

    .compact-upload-label:hover {
        border-color: #DC143C;
        background: #fff5f5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 20, 60, 0.15);
        border-style: solid;
    }

    .compact-upload-label i {
        color: #DC143C;
        font-size: 18px;
        transition: transform 0.3s ease;
    }

    .compact-upload-label:hover i {
        transform: scale(1.1);
    }

    .label-text {
        font-weight: 600;
        font-size: 14px;
        color: #1a1a1a;
    }

    .label-hint {
        font-size: 11px;
        color: #6c757d;
        margin-left: auto;
        font-weight: 500;
        background: rgba(108, 117, 125, 0.1);
        padding: 4px 10px;
        border-radius: 12px;
    }

    .selected-file-compact {
        display: flex;
        align-items: center;
        padding: 12px 18px;
        background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        border: 2px solid #66bb6a;
        border-radius: 10px;
        color: #2e7d32;
        font-size: 13px;
        font-weight: 600;
        min-height: 52px;
        box-shadow: 0 3px 10px rgba(76, 175, 80, 0.2);
    }

    .selected-file-compact i {
        color: #4caf50;
        font-size: 18px;
    }

    .selected-file-compact .filename {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-right: 10px;
    }

    .clear-file-compact {
        background: rgba(220, 20, 60, 0.15);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #DC143C;
        font-size: 12px;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .clear-file-compact:hover {
        background: #DC143C;
        color: white;
        transform: rotate(90deg);
    }

    .btn-resubmit-compact {
        background: linear-gradient(135deg, #DC143C, #ff1744);
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        box-shadow: 0 4px 15px rgba(220, 20, 60, 0.35);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        min-width: fit-content;
        height: fit-content;
        align-self: flex-start;
    }

    .btn-resubmit-compact:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 20, 60, 0.45);
        background: linear-gradient(135deg, #ff1744, #DC143C);
    }

    .btn-resubmit-compact:active {
        transform: translateY(0);
    }

    /* Progress Sidebar */
    .card-custom {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header-custom {
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .card-body-custom {
        padding: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .doc-card-inner {
            flex-direction: column;
        }

        .doc-left, .doc-right {
            min-width: 0;
            width: 100%;
            max-width: 100%;
        }

        .doc-left {
            flex-direction: column;
        }

        .upload-label {
            padding: 20px 15px;
        }

        .step-number {
            width: 45px;
            height: 45px;
            font-size: 18px;
        }

        /* Compact rejection card responsive */
        .compact-upload-row {
            flex-direction: column;
            align-items: stretch;
        }

        .upload-area-compact {
            width: 100%;
        }

        .btn-resubmit-compact {
            width: 100%;
            min-height: 48px;
        }

        .label-hint {
            display: none;
        }

        .rejection-compact-card {
            padding: 12px;
        }

        .rejection-header {
            gap: 10px;
        }

        .rejection-icon-wrapper {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .rejection-title {
            font-size: 13px;
        }

        .rejection-message {
            font-size: 12px;
        }

        .file-actions .btn {
            padding: 5px 8px;
            font-size: 12px;
        }
    }

    /* Loading State */
    .btn-upload.loading {
        position: relative;
        color: transparent;
        pointer-events: none;
    }

    .btn-upload.loading::after {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 3px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to {transform: rotate(360deg);}
    }
</style>
@endpush

@push('scripts')
<script>
    // Document Upload Script - Manual Button Version
    console.log('Documents page script loading...');

    // Define functions in window scope for inline handlers
    window.deleteDocument = function(checklistId, status) {
        const isApproved = status === 'approved' || status === 'completed';

        Swal.fire({
            title: isApproved ? 'Remove Approved Document?' : 'Remove Document?',
            html: isApproved
                ? '<p class="text-warning mb-2"><i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> This document has been approved.</p><p>Removing it will reset the status and you will need to resubmit it for approval.</p><p class="mt-3">Are you sure you want to proceed?</p>'
                : '<p>Are you sure you want to remove this document?</p><p class="text-muted small">You can upload a new document after removal.</p>',
            icon: isApproved ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: '#DC143C',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Remove It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/student/checklist/${checklistId}`;

                // Show loading
                Swal.fire({
                    title: 'Removing Document',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            }
        });
    };

    window.handleFileSelect = function(itemId, input, isResubmit = false) {
        const file = input.files[0];
        if (!file) return;

        const prefix = isResubmit ? 'resubmit' : 'upload';
        const uploadArea = document.getElementById(`${prefix}-area-${itemId}`);
        const selectedFileDiv = document.getElementById(`${prefix === 'resubmit' ? 'resubmit-selected' : 'selected-file'}-${itemId}`);
        const labelClass = isResubmit ? '.compact-upload-label' : '.upload-label';
        const label = uploadArea.querySelector(labelClass);
        const submitBtn = document.getElementById(`${prefix === 'resubmit' ? 'resubmit-btn' : 'upload-btn'}-${itemId}`);

        // Validate file size (15MB limit)
        const maxSize = 15 * 1024 * 1024;
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: `File size (${(file.size / (1024 * 1024)).toFixed(2)}MB) exceeds 15MB limit. Please choose a smaller file.`,
                confirmButtonColor: '#DC143C'
            });
            input.value = '';
            return;
        }

        // Validate file size is not zero
        if (file.size === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File',
                text: 'The selected file is empty. Please choose a valid file.',
                confirmButtonColor: '#DC143C'
            });
            input.value = '';
            return;
        }

        // Validate file type
        const allowedTypes = ['.pdf'];
        const fileExt = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(fileExt)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please upload PDF files only.',
                confirmButtonColor: '#DC143C'
            });
            input.value = '';
            return;
        }

        // Additional validation: Check file name length
        if (file.name.length > 255) {
            Swal.fire({
                icon: 'error',
                title: 'File Name Too Long',
                text: 'File name must be less than 255 characters. Please rename the file.',
                confirmButtonColor: '#DC143C'
            });
            input.value = '';
            return;
        }

        // Verify file is readable by attempting to read a small portion
        const reader = new FileReader();
        reader.onerror = function() {
            Swal.fire({
                icon: 'error',
                title: 'Cannot Read File',
                text: 'Unable to read the selected file. The file may be corrupted or in use.',
                confirmButtonColor: '#DC143C'
            });
            input.value = '';
        };
        
        reader.onload = function() {
            // File is readable, proceed to show selection
            if (selectedFileDiv && label && submitBtn) {
                selectedFileDiv.style.display = 'flex';
                const filenameSpan = selectedFileDiv.querySelector('.filename');
                if (filenameSpan) {
                    // Truncate long filenames for display
                    const displayName = file.name.length > 40 
                        ? file.name.substring(0, 37) + '...' 
                        : file.name;
                    filenameSpan.textContent = displayName;
                    filenameSpan.title = file.name; // Show full name on hover
                }
                label.style.display = 'none';
                submitBtn.style.display = 'flex';
            }
        };
        
        // Read first 1KB to verify file is accessible
        reader.readAsArrayBuffer(file.slice(0, 1024));
    };

    window.clearFileSelection = function(itemId) {
        const uploadArea = document.getElementById(`upload-area-${itemId}`);
        const input = document.getElementById(`file-input-${itemId}`);
        const selectedFileDiv = document.getElementById(`selected-file-${itemId}`);
        const label = uploadArea.querySelector('.upload-label');
        const submitBtn = document.getElementById(`upload-btn-${itemId}`);

        input.value = '';
        selectedFileDiv.style.display = 'none';
        label.style.display = 'flex';
        if (submitBtn) submitBtn.style.display = 'none';
    };

    window.clearResubmitSelection = function(itemId) {
        const uploadArea = document.getElementById(`resubmit-area-${itemId}`);
        const input = document.getElementById(`resubmit-input-${itemId}`);
        const selectedFileDiv = document.getElementById(`resubmit-selected-${itemId}`);
        const label = uploadArea.querySelector('.compact-upload-label');
        const submitBtn = document.getElementById(`resubmit-btn-${itemId}`);

        input.value = '';
        selectedFileDiv.style.display = 'none';
        if (label) label.style.display = 'flex';
        if (submitBtn) submitBtn.style.display = 'none';
    };

    // Form submission with loading state and timeout handling
    document.querySelectorAll('.modern-upload-form, .compact-resubmit-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-upload, .btn-resubmit-compact');
            const fileInput = this.querySelector('.file-input');

            if (!fileInput.files.length) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'No File Selected',
                    text: 'Please select a file to upload.',
                    confirmButtonColor: '#DC143C'
                });
                return;
            }

            const file = fileInput.files[0];

            // Final validation before submit
            if (file.size > 15 * 1024 * 1024) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'File size exceeds 15MB. Please choose a smaller file.',
                    confirmButtonColor: '#DC143C'
                });
                return;
            }

            // Show upload progress
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

            // Show a timeout warning for large files
            let uploadWarningTimeout;
            const fileSize = file.size / (1024 * 1024); // Size in MB
            
            if (fileSize > 5) {
                // For files larger than 5MB, show a patience message after 10 seconds
                uploadWarningTimeout = setTimeout(() => {
                    Swal.fire({
                        icon: 'info',
                        title: 'Upload in Progress',
                        html: '<p>Uploading large file (' + fileSize.toFixed(2) + 'MB)...</p><p class="text-muted small">Please keep this page open.</p>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                }, 10000);
            }

            // Set a maximum timeout (2 minutes for very large files)
            const maxTimeout = setTimeout(() => {
                if (uploadWarningTimeout) clearTimeout(uploadWarningTimeout);
                Swal.fire({
                    icon: 'warning',
                    title: 'Upload Taking Too Long',
                    html: '<p>The upload is taking longer than expected.</p><p>This might be due to slow internet connection.</p><p class="text-muted small">Please wait or try again later with a better connection.</p>',
                    confirmButtonColor: '#DC143C'
                });
            }, 120000); // 2 minutes

            // Clean up on page unload
            window.addEventListener('beforeunload', function() {
                if (uploadWarningTimeout) clearTimeout(uploadWarningTimeout);
                clearTimeout(maxTimeout);
            });
        });
    });

    // Drag and drop functionality
    document.querySelectorAll('.upload-area').forEach(uploadArea => {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragging');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragging');
            }, false);
        });

        uploadArea.addEventListener('drop', function(e) {
            const fileInput = this.querySelector('.file-input');
            const files = e.dataTransfer.files;

            if (files.length) {
                fileInput.files = files;
                const itemId = fileInput.id.replace('file-input-', '').replace('resubmit-input-', '');
                const isResubmit = fileInput.id.includes('resubmit');
                handleFileSelect(itemId, fileInput, isResubmit);
            }
        }, false);
    });
</script>
@endpush
