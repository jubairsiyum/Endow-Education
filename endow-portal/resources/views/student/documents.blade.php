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
                            <i class="fas fa-file-pdf text-danger me-1"></i> PDF, JPG, PNG Accepted
                        </span>
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-weight text-warning me-1"></i> Max 10MB per file
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
                                            <a href="{{ asset('storage/' . $studentChecklist->document_path) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($canEdit)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteDocument({{ $studentChecklist->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if($isRejected && $studentChecklist->feedback)
                                        <div class="rejection-feedback">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span>{{ $studentChecklist->feedback }}</span>
                                        </div>
                                    @endif
                                @endif

                                @if($isRejected && $studentChecklist && $studentChecklist->document_path)
                                    <!-- Resubmit Form for Rejected Documents -->
                                    <div class="alert alert-warning mb-3">
                                        <strong><i class="fas fa-info-circle me-2"></i>Document Rejected</strong>
                                        <p class="mb-2 small">Please review the feedback and upload a corrected document.</p>
                                    </div>
                                    <form action="{{ route('student.checklist.resubmit', $studentChecklist->id) }}"
                                          method="POST"
                                          enctype="multipart/form-data"
                                          class="modern-upload-form"
                                          id="resubmit-form-{{ $item->id }}">
                                        @csrf
                                        <div class="upload-area" id="resubmit-area-{{ $item->id }}">
                                            <input type="file"
                                                   name="document"
                                                   id="resubmit-input-{{ $item->id }}"
                                                   class="file-input"
                                                   accept=".pdf,.jpg,.jpeg,.png"
                                                   required
                                                   onchange="handleFileSelect({{ $item->id }}, this, true)">
                                            <label for="resubmit-input-{{ $item->id }}" class="upload-label">
                                                <div class="upload-icon">
                                                    <i class="fas fa-redo-alt fa-2x"></i>
                                                </div>
                                                <div class="upload-text">
                                                    <span class="upload-main">Resubmit Corrected Document</span>
                                                    <span class="upload-sub">PDF, JPG, PNG up to 10MB</span>
                                                </div>
                                            </label>
                                            <div class="selected-file" id="resubmit-selected-{{ $item->id }}" style="display: none;">
                                                <i class="fas fa-file-alt"></i>
                                                <span class="filename"></span>
                                                <button type="button" class="clear-file" onclick="clearResubmitSelection({{ $item->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-upload w-100 mt-2">
                                            <i class="fas fa-redo me-2"></i>
                                            Resubmit Document
                                        </button>
                                    </form>
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
                                                   accept=".pdf,.jpg,.jpeg,.png"
                                                   required
                                                   onchange="handleFileSelect({{ $item->id }}, this)">
                                            <label for="file-input-{{ $item->id }}" class="upload-label">
                                                <div class="upload-icon">
                                                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                                </div>
                                                <div class="upload-text">
                                                    <span class="upload-main">{{ $studentChecklist && $studentChecklist->document_path ? 'Replace Document' : 'Choose File or Drag & Drop' }}</span>
                                                    <span class="upload-sub">PDF, JPG, PNG up to 10MB</span>
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
                                        <button type="submit" class="btn btn-danger btn-upload w-100 mt-2">
                                            <i class="fas fa-upload me-2"></i>
                                            Upload Document
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
                            <svg width="150" height="150">
                                <circle cx="75" cy="75" r="65" fill="none" stroke="#f0f0f0" stroke-width="15"/>
                                <circle cx="75" cy="75" r="65" fill="none" stroke="#DC143C" stroke-width="15"
                                        stroke-dasharray="{{ 2 * 3.14159 * 65 }}"
                                        stroke-dashoffset="{{ 2 * 3.14159 * 65 * (1 - ($completedCount / max($totalCount, 1))) }}"
                                        transform="rotate(-90 75 75)"
                                        stroke-linecap="round"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="fs-2 fw-bold text-danger">{{ $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0 }}%</div>
                                <small class="text-muted">Complete</small>
                            </div>
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
                                Keep file sizes under 10MB
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
        overflow: hidden;
        transition: all 0.3s ease;
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
    }

    .doc-left {
        flex: 1;
        min-width: 300px;
        display: flex;
        gap: 15px;
    }

    .doc-right {
        flex: 1;
        min-width: 300px;
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
        margin-bottom: 10px;
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
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .upload-sub {
        display: block;
        font-size: 12px;
        color: #6c757d;
    }

    .selected-file {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px 20px;
        background: white;
        border-radius: 8px;
        margin: 10px;
    }

    .selected-file i {
        color: #DC143C;
        font-size: 20px;
    }

    .selected-file .filename {
        flex: 1;
        font-weight: 600;
        color: #1a1a1a;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .clear-file {
        background: #f8d7da;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .clear-file:hover {
        background: #DC143C;
        color: white;
    }

    .btn-upload {
        font-weight: 600;
        padding: 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
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
        padding: 15px;
        margin-bottom: 15px;
    }

    .file-preview {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }

    .file-details {
        flex: 1;
    }

    .file-name {
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .file-actions {
        display: flex;
        gap: 8px;
    }

    .file-actions .btn {
        border-radius: 8px;
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
            min-width: 100%;
        }

        .upload-label {
            padding: 20px 15px;
        }

        .step-number {
            width: 45px;
            height: 45px;
            font-size: 18px;
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
    function deleteDocument(checklistId) {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const form = document.getElementById('deleteForm');
        form.action = `/student/checklist/${checklistId}`;
        modal.show();
    }

    function handleFileSelect(itemId, input, isResubmit = false) {
        const file = input.files[0];
        if (file) {
            const prefix = isResubmit ? 'resubmit' : 'upload';
            const uploadArea = document.getElementById(`${prefix}-area-${itemId}`);
            const selectedFileDiv = document.getElementById(`${prefix === 'resubmit' ? 'resubmit-selected' : 'selected-file'}-${itemId}`);
            const label = uploadArea.querySelector('.upload-label');

            // Show selected file
            selectedFileDiv.style.display = 'flex';
            selectedFileDiv.querySelector('.filename').textContent = file.name;
            label.style.display = 'none';

            // Validate file size
            if (file.size > 10 * 1024 * 1024) {
                alert('File size exceeds 10MB limit. Please choose a smaller file.');
                if (isResubmit) {
                    clearResubmitSelection(itemId);
                } else {
                    clearFileSelection(itemId);
                }
                return;
            }

            // Validate file type
            const allowedTypes = ['.pdf', '.jpg', '.jpeg', '.png'];
            const fileExt = '.' + file.name.split('.').pop().toLowerCase();
            if (!allowedTypes.includes(fileExt)) {
                alert('Invalid file type. Please upload PDF, JPG, or PNG files only.');
                if (isResubmit) {
                    clearResubmitSelection(itemId);
                } else {
                    clearFileSelection(itemId);
                }
                return;
            }
        }
    }

    function clearFileSelection(itemId) {
        const uploadArea = document.getElementById(`upload-area-${itemId}`);
        const input = document.getElementById(`file-input-${itemId}`);
        const selectedFileDiv = document.getElementById(`selected-file-${itemId}`);
        const label = uploadArea.querySelector('.upload-label');

        input.value = '';
        selectedFileDiv.style.display = 'none';
        label.style.display = 'flex';
    }

    function clearResubmitSelection(itemId) {
        const uploadArea = document.getElementById(`resubmit-area-${itemId}`);
        const input = document.getElementById(`resubmit-input-${itemId}`);
        const selectedFileDiv = document.getElementById(`resubmit-selected-${itemId}`);
        const label = uploadArea.querySelector('.upload-label');

        input.value = '';
        selectedFileDiv.style.display = 'none';
        label.style.display = 'flex';
    }

    // Form submission with loading state
    document.querySelectorAll('.modern-upload-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-upload');
            const fileInput = this.querySelector('.file-input');

            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Please select a file to upload.');
                return;
            }

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
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
