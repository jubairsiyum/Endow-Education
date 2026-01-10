@extends('layouts.admin')

@section('page-title', $student->name)
@section('breadcrumb', 'Home / Students / ' . $student->name)

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--text-secondary);
        font-weight: 500;
        padding: 1rem 1.5rem;
    }

    .nav-tabs-custom .nav-link.active {
        border-bottom-color: var(--primary-color);
        color: var(--primary-color);
        background: transparent;
    }

    .nav-tabs-custom {
        border-bottom: 1px solid var(--border-color);
    }

    .timeline-item {
        position: relative;
        padding-left: 2.5rem;
        padding-bottom: 1.5rem;
        border-left: 2px solid var(--border-color);
    }

    .timeline-item:last-child {
        border-left-color: transparent;
        padding-bottom: 0;
    }

    .timeline-icon {
        position: absolute;
        left: -13px;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    /* Quill Editor Customization */
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        border-color: #dee2e6;
    }

    .ql-container.ql-snow {
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
        border-color: #dee2e6;
        font-size: 15px;
    }

    .ql-editor {
        min-height: 250px;
    }

    .ql-snow .ql-stroke {
        stroke: #495057;
    }

    .ql-snow .ql-fill {
        fill: #495057;
    }

    .ql-snow .ql-picker-label {
        color: #495057;
    }

    .ql-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-label {
        border-color: #DC143C;
    }

    /* Follow-up content styling */
    .followup-content {
        line-height: 1.6;
    }

    .followup-content p {
        margin-bottom: 0.75rem;
    }

    .followup-content ul, .followup-content ol {
        padding-left: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .followup-content strong {
        font-weight: 600;
        color: #1a1a1a;
    }

    .followup-content a {
        color: #DC143C;
        text-decoration: underline;
    }

    /* Button hover text color fixes */
    .btn-primary-custom:hover {
        color: black !important;
    }
    .btn-success:hover {
        color: black !important;
    }
    .btn-danger:hover {
        color: white !important;
    }
    .btn-outline-primary:hover {
        color: black !important;
    }
    .btn-outline-secondary:hover {
        color: black !important;
    }
    .btn-outline-danger:hover {
        color: white !important;
    }
    .action-btn:hover {
        color: black !important;
    }
</style>
@endpush

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="page-title mb-2">{{ $student->name }}</h1>
            <p class="page-subtitle mb-0">{{ $student->email }} · {{ $student->phone }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('update', $student)
            <a href="{{ route('students.payments.index', $student) }}" class="btn btn-success">
                <i class="fas fa-money-bill-wave me-2"></i> Payments
            </a>
            <a href="{{ route('students.edit', $student) }}" class="btn btn-primary-custom">
                <i class="fas fa-edit me-2"></i> Edit Student
            </a>
            @endcan
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Application Status</div>
                        @php
                            $statusColors = [
                                'new' => 'info',
                                'contacted' => 'secondary',
                                'processing' => 'warning',
                                'applied' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger'
                            ];
                            $color = $statusColors[$student->status] ?? 'secondary';
                        @endphp
                        <span class="badge-custom badge-{{ $color }}-custom mt-2">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                    <div class="stat-icon {{ $color }}">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Account Status</div>
                        @php
                            $accountColors = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger'
                            ];
                            $accountColor = $accountColors[$student->account_status] ?? 'secondary';
                        @endphp
                        <span class="badge-custom badge-{{ $accountColor }}-custom mt-2">
                            {{ ucfirst($student->account_status) }}
                        </span>
                    </div>
                    <div class="stat-icon {{ $accountColor }}">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Checklist Progress</div>
                        <div class="stat-value" style="font-size: 1.5rem;">
                            {{ $student->checklist_progress['approved'] ?? 0 }} of {{ $student->checklist_progress['total'] ?? 0 }}
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">submitted</small>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Documents</div>
                        <div class="stat-value" style="font-size: 1.5rem;">
                            {{ $student->documents->count() }}
                        </div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Actions -->
    @can('approve', $student)
    @if($student->account_status === 'pending')
    <div class="alert alert-warning alert-custom d-flex justify-content-between align-items-center mb-4">
        <div>
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>This account is pending approval.</strong> Review the student information and take action.
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('students.approve.form', $student) }}" class="btn btn-success">
                <i class="fas fa-check me-2"></i> Approve & Enroll
            </a>
            <form action="{{ route('students.reject', $student) }}" method="POST" class="d-inline" id="reject-student-form">
                @csrf
                <button type="button" class="btn btn-danger"
                        onclick="confirmRejectStudent()">
                    <i class="fas fa-times me-2"></i> Reject
                </button>
            </form>
        </div>
    </div>
    @endif
    @endcan

    <!-- Tabs -->
    <div class="card-custom">
        <ul class="nav nav-tabs nav-tabs-custom" id="studentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab"
                        data-bs-target="#profile" type="button" role="tab">
                    <i class="fas fa-user me-2"></i> Profile
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="checklist-tab" data-bs-toggle="tab"
                        data-bs-target="#checklist" type="button" role="tab">
                    <i class="fas fa-tasks me-2"></i> Checklist & Documents
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="followups-tab" data-bs-toggle="tab"
                        data-bs-target="#followups" type="button" role="tab">
                    <i class="fas fa-comments me-2"></i> Follow-ups
                </button>
            </li>
        </ul>

        <div class="tab-content p-4">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Personal Information</h5>
                        <table class="table">
                            <tr>
                                <th width="40%">Full Name</th>
                                <td>{{ $student->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $student->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $student->phone }}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>{{ $student->country }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Academic & System Information</h5>
                        <table class="table">
                            <tr>
                                <th width="40%">Course/Program</th>
                                <td>{{ $student->course ?: ($student->targetProgram->name ?? 'Not specified') }}</td>
                            </tr>
                            <tr>
                                <th>Target University</th>
                                <td>{{ $student->targetUniversity->name ?? 'Not selected' }}</td>
                            </tr>
                            <tr>
                                <th>Assigned Counselor</th>
                                <td>{{ $student->assignedUser->name ?? 'Not assigned' }}</td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td>{{ $student->creator->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <th>Registration Date</th>
                                <td>{{ $student->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        </table>
                    </div>

                    @if($student->notes)
                    <div class="col-12 mt-3">
                        <h5 class="mb-3">Additional Notes</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $student->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Checklist Tab -->
            <div class="tab-pane fade" id="checklist" role="tabpanel">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-dark fw-bold mb-0">Checklist Progress</h5>
                        @php
                            // Calculate document statistics for merge button
                            $totalDocs = $student->documents()->whereNotNull('student_checklist_id')->count();
                            $approvedDocs = $student->documents()->whereNotNull('student_checklist_id')->where('status', 'approved')->count();
                            $allDocsApproved = $totalDocs > 0 && $totalDocs === $approvedDocs;
                        @endphp

                        @if($allDocsApproved)
                        <div>
                            <a href="{{ route('students.documents.mergeAll', $student) }}"
                               class="btn btn-success btn-sm"
                               title="Download all approved documents as single PDF">
                                <i class="fas fa-file-pdf me-2"></i>
                                Download All Documents (Merged PDF)
                            </a>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                All {{ $approvedDocs }} documents approved
                            </small>
                        </div>
                        @elseif($totalDocs > 0)
                        <div class="text-end">
                            <small class="text-muted d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ $approvedDocs }}/{{ $totalDocs }} documents approved
                            </small>
                            <small class="text-muted d-block mt-1">
                                Merge feature available when all documents are approved
                            </small>
                        </div>
                        @endif
                    </div>
                    @php
                        $total = $student->checklist_progress['total'] ?? 0;
                        $approved = $student->checklist_progress['approved'] ?? 0;
                        $percentage = $total > 0 ? (int)(($approved / $total) * 100) : 0;
                        $progressColor = $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="progress" style="height: 24px;">
                        <div class="progress-bar bg-{{ $progressColor }}" role="progressbar"
                             style="width: {{ $percentage }}%;"
                             aria-valuenow="{{ $percentage }}"
                             aria-valuemin="0" aria-valuemax="100">
                            <span class="fw-bold">{{ $approved }} of {{ $total }} completed ({{ $percentage }}%)</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-success me-2">{{ $student->checklist_progress['approved'] ?? 0 }} approved</span>
                        <span class="badge bg-warning me-2">{{ $student->checklist_progress['submitted'] ?? 0 }} under review</span>
                        <span class="badge bg-secondary">{{ $student->checklist_progress['pending'] ?? 0 }} pending</span>
                    </div>
                </div>

                <div class="list-group">
                    @forelse($student->checklists as $checklist)
                    <div class="list-group-item mb-3 border rounded">
                        <div class="row align-items-start">
                            <div class="col-auto">
                                @if($checklist->status === 'approved')
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; font-size: 20px;">
                                        <i class="fas fa-check"></i>
                                    </div>
                                @elseif($checklist->status === 'submitted')
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; font-size: 20px;">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                @elseif($checklist->status === 'rejected')
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; font-size: 20px;">
                                        <i class="fas fa-times"></i>
                                    </div>
                                @else
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; font-size: 20px;">
                                        <i class="fas fa-circle"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">
                                            {{ $checklist->checklistItem->title }}
                                            @if($checklist->checklistItem->is_required)
                                                <span class="badge bg-danger ms-2">Required</span>
                                            @else
                                                <span class="badge bg-secondary ms-2">Optional</span>
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="ms-3">
                                        @php
                                            $checklistColors = [
                                                'pending' => 'secondary',
                                                'submitted' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $checklistColor = $checklistColors[$checklist->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $checklistColor }}">
                                            {{ ucfirst($checklist->status) }}
                                        </span>
                                    </div>
                                </div>

                                @if($checklist->submitted_at)
                                    <p class="small text-muted mb-2">
                                        <i class="fas fa-calendar me-1"></i>
                                        Submitted: {{ $checklist->submitted_at->format('M d, Y g:i A') }}
                                    </p>
                                @endif

                                @if($checklist->documents->count() > 0)
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="small">Documents:</strong>
                                            @if($checklist->documents->count() > 1)
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="toggleDocumentSelection({{ $checklist->id }})"
                                                    id="select-btn-{{ $checklist->id }}">
                                                <i class="fas fa-check-square me-1"></i> Select for Merge
                                            </button>
                                            @endif
                                        </div>
                                        <div class="list-group mt-2" id="doc-list-{{ $checklist->id }}">
                                            @foreach($checklist->documents as $document)
                                            <div class="list-group-item document-item"
                                                 data-checklist="{{ $checklist->id }}"
                                                 data-document="{{ $document->id }}">
                                                <div class="d-flex align-items-start gap-3">
                                                    <input type="checkbox" class="form-check-input mt-1 doc-checkbox d-none"
                                                           data-checklist="{{ $checklist->id }}"
                                                           data-document="{{ $document->id }}"
                                                           value="{{ $document->id }}">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                                            <strong>{{ $document->original_name ?? $document->file_name }}</strong>
                                                            <small class="text-muted ms-2">({{ number_format($document->file_size / 1024, 2) }} KB)</small>
                                                            @php
                                                                $docStatusColors = [
                                                                    'pending' => 'secondary',
                                                                    'submitted' => 'warning',
                                                                    'approved' => 'success',
                                                                    'rejected' => 'danger'
                                                                ];
                                                                $docColor = $docStatusColors[$document->status] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge bg-{{ $docColor }} ms-2">{{ ucfirst($document->status) }}</span>
                                                        </div>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-user me-1"></i>
                                                            Uploaded by {{ $document->uploader->name ?? 'Unknown' }} on
                                                            {{ $document->created_at->format('M d, Y g:i A') }}
                                                        </small>
                                                        @if($document->reviewed_by && $document->reviewed_at)
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            Reviewed by {{ $document->reviewer->name ?? 'Unknown' }} on
                                                            {{ $document->reviewed_at->format('M d, Y g:i A') }}
                                                        </small>
                                                        @endif
                                                    </div>
                                                    <div class="btn-group btn-group-sm ms-auto">
                                                        @if($document->file_data || ($document->file_path && \Storage::disk('public')->exists($document->file_path)))
                                                            <button type="button"
                                                                    class="btn btn-outline-primary"
                                                                    onclick="viewDocument({{ $document->id }}, '{{ addslashes($document->filename ?? 'Document') }}')"
                                                                    title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <a href="{{ route('students.documents.download', ['student' => $student, 'document' => $document]) }}"
                                                               class="btn btn-outline-success"
                                                               title="Download">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        @else
                                                            <span class="badge bg-danger">File Missing</span>
                                                        @endif
                                                        @can('update', $student)
                                                            @if($document->status !== 'approved')
                                                            <form action="{{ route('students.documents.destroy', ['student' => $student, 'document' => $document]) }}"
                                                                  method="POST"
                                                                  id="delete-doc-form-{{ $document->id }}"
                                                                  class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                        class="btn btn-outline-danger"
                                                                        title="Delete"
                                                                        onclick="confirmDeleteDocument({{ $document->id }})">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @if($checklist->documents->count() > 1)
                                        <div class="mt-3 d-none" id="merge-actions-{{ $checklist->id }}">
                                            <button type="button" class="btn btn-sm btn-success me-2"
                                                    onclick="mergeSelectedDocuments({{ $checklist->id }}, {{ $student->id }})">
                                                <i class="fas fa-file-pdf me-1"></i> Merge Selected PDFs
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                    onclick="cancelDocumentSelection({{ $checklist->id }})">
                                                Cancel
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-info alert-sm mt-2 mb-0">
                                        <small><i class="fas fa-info-circle me-1"></i> No documents uploaded yet for this item</small>
                                    </div>
                                @endif

                                @if($checklist->status === 'submitted')
                                    @can('update', $student)
                                    <div class="mt-3">
                                        <form action="{{ route('student.checklist.approve', $checklist) }}" method="POST" class="d-inline" id="approve-form-{{ $checklist->id }}">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-success me-2" onclick="confirmApproveDocument({{ $checklist->id }})">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $checklist->id }}">
                                            <i class="fas fa-times me-1"></i> Reject
                                        </button>
                                    </div>
                                    @endcan
                                @endif

                                @if($checklist->feedback)
                                    <div class="alert alert-warning alert-sm mt-2 mb-0">
                                        <strong>Feedback:</strong> {{ $checklist->feedback }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No checklist items found</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Follow-ups Tab -->
            <div class="tab-pane fade" id="followups" role="tabpanel">
                <div class="mb-4">
                    @can('update', $student)
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#followupModal">
                        <i class="fas fa-plus me-2"></i> Add Follow-up Note
                    </button>
                    @endcan
                </div>

                <div class="timeline">
                    @forelse($student->followUps as $followUp)
                    <div class="timeline-item mb-4">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 48px; height: 48px; font-size: 20px;">
                                    <i class="fas fa-comment"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-light border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="text-dark">{{ $followUp->creator->name ?? 'Unknown' }}</strong>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $followUp->created_at->format('M d, Y g:i A') }}
                                                </small>
                                                @if($followUp->next_follow_up_date)
                                                <span class="badge bg-warning text-dark mt-2">
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                    Next: {{ $followUp->next_follow_up_date->format('M d, Y') }}
                                                </span>
                                                @endif
                                            </div>
                                            @can('update', $student)
                                            <div class="btn-group btn-group-sm">
                                                <button type="button"
                                                        class="btn btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editFollowupModal{{ $followUp->id }}"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        onclick="confirmDeleteFollowUp({{ $followUp->id }})"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="followup-content">
                                            {!! $followUp->note !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Follow-up Modal -->
                    @can('update', $student)
                    <div class="modal fade" id="editFollowupModal{{ $followUp->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit me-2"></i>Edit Follow-up Note
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('follow-ups.update', $followUp) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Follow-up Note <span class="text-danger">*</span></label>
                                            <div id="edit-quill-editor-{{ $followUp->id }}" style="height: 250px;"></div>
                                            <textarea name="note" id="edit-note-{{ $followUp->id }}" style="display:none;" required>{{ $followUp->note }}</textarea>
                                            <small class="text-muted">Document student interactions, concerns, and action items</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_next_follow_up_date_{{ $followUp->id }}" class="form-label fw-semibold">
                                                <i class="fas fa-calendar-alt text-danger me-1"></i>Next Follow-up Date
                                            </label>
                                            <input type="date"
                                                   class="form-control"
                                                   id="edit_next_follow_up_date_{{ $followUp->id }}"
                                                   name="next_follow_up_date"
                                                   value="{{ $followUp->next_follow_up_date?->format('Y-m-d') }}"
                                                   min="{{ date('Y-m-d') }}">
                                            <small class="text-muted">Optional: Set a reminder for the next follow-up</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </button>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-save me-1"></i>Update Follow-up
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Form -->
                    <form id="delete-followup-form-{{ $followUp->id }}"
                          action="{{ route('follow-ups.destroy', $followUp) }}"
                          method="POST"
                          style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endcan
                    @empty
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-comments fa-4x text-muted"></i>
                        </div>
                        <h5 class="text-muted">No Follow-ups Yet</h5>
                        <p class="text-muted mb-3">Start tracking your interactions with this student</p>
                        @can('update', $student)
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#followupModal">
                            <i class="fas fa-plus me-2"></i> Add First Follow-up
                        </button>
                        @endcan
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Add Follow-up Modal -->
    @can('update', $student)
    <div class="modal fade" id="followupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Add Follow-up Note
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('follow-ups.store') }}" method="POST" id="followupForm">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <div class="modal-body">
                        <div class="alert alert-info border-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Track student interactions:</strong> Record calls, meetings, emails, and important updates about {{ $student->user->name ?? $student->name }}'s application progress.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Follow-up Note <span class="text-danger">*</span></label>
                            <div id="quill-followup-editor" style="height: 250px;"></div>
                            <textarea name="note" id="followup-note" style="display:none;" required></textarea>
                            <small class="text-muted">Document student interactions, concerns, and action items</small>
                        </div>
                        <div class="mb-3">
                            <label for="next_follow_up_date" class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt text-danger me-1"></i>Next Follow-up Date
                            </label>
                            <input type="date"
                                   class="form-control"
                                   id="next_follow_up_date"
                                   name="next_follow_up_date"
                                   min="{{ date('Y-m-d') }}">
                            <small class="text-muted">Optional: Set a reminder for the next follow-up</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-1"></i>Save Follow-up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    <!-- Reject Modals -->
    @foreach($student->checklists as $checklist)
    <div class="modal fade" id="rejectModal{{ $checklist->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Document: {{ $checklist->checklistItem->title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('student.checklist.reject', $checklist) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="feedback{{ $checklist->id }}" class="form-label">Feedback/Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="feedback{{ $checklist->id }}" name="feedback" rows="4" required placeholder="Please provide specific feedback about why this document is being rejected..."></textarea>
                            <small class="text-muted">This feedback will be shown to the student so they can resubmit correctly.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Document Viewer Modal -->
    <div class="modal fade" id="documentViewerModal" tabindex="-1" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentViewerModalLabel">
                        <i class="fas fa-file-pdf me-2"></i>
                        <span id="documentTitle">Document Viewer</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="min-height: 70vh;">
                    <div id="documentLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading document...</p>
                    </div>
                    <div id="documentError" class="alert alert-danger m-4" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="errorMessage">Error loading document</span>
                    </div>
                    <div id="documentContent" style="display: none; height: 70vh; overflow: hidden;">
                        <!-- Container for document display -->
                        <div id="documentViewer" style="width: 100%; height: 100%;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                    <a id="documentDownloadBtn" href="#" class="btn btn-primary" download>
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Initialize Quill Editor for Add Follow-up
        document.addEventListener('DOMContentLoaded', function() {
            const followupModal = document.getElementById('followupModal');
            if (followupModal) {
                let quillFollowup = null;

                followupModal.addEventListener('shown.bs.modal', function() {
                    if (!quillFollowup) {
                        quillFollowup = new Quill('#quill-followup-editor', {
                            theme: 'snow',
                            placeholder: 'Document your interaction with the student...',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                                    [{ 'color': [] }, { 'background': [] }],
                                    ['link'],
                                    ['clean']
                                ]
                            }
                        });

                        // Sync Quill content with hidden textarea
                        quillFollowup.on('text-change', function() {
                            document.getElementById('followup-note').value = quillFollowup.root.innerHTML;
                        });
                    }
                });

                // Reset form on modal hide
                followupModal.addEventListener('hidden.bs.modal', function() {
                    if (quillFollowup) {
                        quillFollowup.setContents([]);
                        document.getElementById('followup-note').value = '';
                        document.getElementById('next_follow_up_date').value = '';
                    }
                });
            }

            // Initialize Quill Editors for Edit Follow-up modals
            @foreach($student->followUps as $followUp)
            (function() {
                const editModal{{ $followUp->id }} = document.getElementById('editFollowupModal{{ $followUp->id }}');
                if (editModal{{ $followUp->id }}) {
                    let quillEdit{{ $followUp->id }} = null;

                    editModal{{ $followUp->id }}.addEventListener('shown.bs.modal', function() {
                        if (!quillEdit{{ $followUp->id }}) {
                            quillEdit{{ $followUp->id }} = new Quill('#edit-quill-editor-{{ $followUp->id }}', {
                                theme: 'snow',
                                modules: {
                                    toolbar: [
                                        [{ 'header': [1, 2, 3, false] }],
                                        ['bold', 'italic', 'underline', 'strike'],
                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                                        [{ 'color': [] }, { 'background': [] }],
                                        ['link'],
                                        ['clean']
                                    ]
                                }
                            });

                            // Set initial content
                            const initialContent = document.getElementById('edit-note-{{ $followUp->id }}').value;
                            if (initialContent) {
                                quillEdit{{ $followUp->id }}.root.innerHTML = initialContent;
                            }

                            // Sync Quill content with hidden textarea
                            quillEdit{{ $followUp->id }}.on('text-change', function() {
                                document.getElementById('edit-note-{{ $followUp->id }}').value = quillEdit{{ $followUp->id }}.root.innerHTML;
                            });
                        }
                    });
                }
            })();
            @endforeach
        });

        function confirmDeleteFollowUp(followUpId) {
            Swal.fire({
                title: 'Delete Follow-up?',
                text: 'Are you sure you want to delete this follow-up note? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC143C',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-followup-form-' + followUpId).submit();
                }
            });
        }

        function confirmRejectStudent() {
            Swal.fire({
                title: 'Reject Student?',
                text: 'Are you sure you want to reject this student?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC143C',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, reject it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-student-form').submit();
                }
            });
        }

        function confirmDeleteDocument(documentId) {
            Swal.fire({
                title: 'Delete Document?',
                text: 'Are you sure you want to delete this document?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC143C',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-doc-form-' + documentId).submit();
                }
            });
        }

        function confirmApproveDocument(checklistId) {
            Swal.fire({
                title: 'Approve Document?',
                text: 'Are you sure you want to approve this document?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#DC143C',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + checklistId).submit();
                }
            });
        }

        function viewDocument(documentId, documentName) {
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('documentViewerModal'));
            modal.show();

            // Set document title
            document.getElementById('documentTitle').textContent = documentName;

            // Reset states
            document.getElementById('documentLoading').style.display = 'block';
            document.getElementById('documentError').style.display = 'none';
            document.getElementById('documentContent').style.display = 'none';

            // Clear viewer content immediately to prevent showing old content
            const viewer = document.getElementById('documentViewer');
            viewer.innerHTML = '';

            // Fetch document data
            fetch(`/api/documents/${documentId}/data`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load document');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('documentLoading').style.display = 'none';
                document.getElementById('documentContent').style.display = 'block';

                // Set download button
                const downloadBtn = document.getElementById('documentDownloadBtn');
                downloadBtn.href = `/students/{{ $student->id }}/documents/${documentId}/download`;
                downloadBtn.download = data.filename;

                // Display document based on mime type
                const viewer = document.getElementById('documentViewer');
                viewer.innerHTML = ''; // Clear previous content
                const timestamp = new Date().getTime(); // Unique timestamp to prevent caching

                if (data.mime_type === 'application/pdf') {
                    // For PDF, use object tag with embed fallback for better compatibility
                    viewer.innerHTML = `
                        <object
                            data="data:application/pdf;base64,${data.file_data}#toolbar=1&navpanes=0&scrollbar=1&view=FitH"
                            type="application/pdf"
                            style="width: 100%; height: 100%; border: none;">
                            <embed
                                src="data:application/pdf;base64,${data.file_data}#toolbar=1&navpanes=0&scrollbar=1&view=FitH"
                                type="application/pdf"
                                style="width: 100%; height: 100%; border: none;" />
                            <div style="padding: 40px; text-align: center; background: #f8f9fa;">
                                <div style="font-size: 48px; color: #6c757d; margin-bottom: 20px;">📄</div>
                                <h4 style="color: #495057; margin-bottom: 10px;">${data.filename}</h4>
                                <p style="color: #6c757d; margin-bottom: 20px;">Your browser cannot display PDF files.</p>
                                <a href="${downloadBtn.href}" class="btn btn-primary" download="${data.filename}">
                                    <i class="fas fa-download me-1"></i> Download PDF
                                </a>
                            </div>
                        </object>
                    `;
                } else if (data.mime_type.startsWith('image/')) {
                    // For images, display directly with proper styling
                    viewer.innerHTML = `
                        <div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; background: #f5f5f5; overflow: auto; padding: 20px;">
                            <img
                                src="data:${data.mime_type};base64,${data.file_data}"
                                alt="${data.filename}"
                                style="max-width: 100%; max-height: 100%; object-fit: contain; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: white;"
                                onerror="this.parentElement.innerHTML='<div style=\\'text-align: center; color: #dc3545;\\'><i class=\\'fas fa-exclamation-triangle\\' style=\\'font-size: 48px; margin-bottom: 20px;\\'></i><p>Failed to load image</p></div>';"
                            />
                        </div>
                    `;
                } else if (data.mime_type === 'application/msword' || data.mime_type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    // For Word documents
                    viewer.innerHTML = `
                        <div style="padding: 40px; text-align: center; background: #f8f9fa; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                            <div style="font-size: 64px; color: #2b579a; margin-bottom: 20px;">📝</div>
                            <h4 style="color: #495057; margin-bottom: 10px;">${data.filename}</h4>
                            <p style="color: #6c757d; margin-bottom: 10px;">Microsoft Word Document</p>
                            <p style="color: #6c757d; margin-bottom: 20px;">Preview not available for Word documents.</p>
                            <a href="${downloadBtn.href}" class="btn btn-primary" download="${data.filename}">
                                <i class="fas fa-download me-1"></i> Download Document
                            </a>
                        </div>
                    `;
                } else {
                    // For other/unknown types
                    const fileIcon = '📄';
                    viewer.innerHTML = `
                        <div style="padding: 40px; text-align: center; background: #f8f9fa; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                            <div style="font-size: 64px; color: #6c757d; margin-bottom: 20px;">${fileIcon}</div>
                            <h4 style="color: #495057; margin-bottom: 10px;">${data.filename}</h4>
                            <p style="color: #6c757d; margin-bottom: 10px;">File Type: ${data.mime_type}</p>
                            <p style="color: #6c757d; margin-bottom: 20px;">Preview not available for this file type.</p>
                            <a href="${downloadBtn.href}" class="btn btn-primary" download="${data.filename}">
                                <i class="fas fa-download me-1"></i> Download File
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading document:', error);
                document.getElementById('documentLoading').style.display = 'none';
                document.getElementById('documentError').style.display = 'block';
                document.getElementById('errorMessage').textContent = error.message || 'Failed to load document. Please try again.';

                // Also show SweetAlert for better visibility
                Swal.fire({
                    icon: 'error',
                    title: 'Document Load Failed',
                    text: error.message || 'Failed to load document. Please try again.',
                    confirmButtonColor: '#DC143C'
                });
            });
        }

        // Clean up modal when it's hidden to prevent content bleeding between views
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('documentViewerModal');
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function () {
                    // Clear viewer content when modal is closed
                    const viewer = document.getElementById('documentViewer');
                    viewer.innerHTML = '';

                    // Reset to loading state
                    document.getElementById('documentLoading').style.display = 'block';
                    document.getElementById('documentError').style.display = 'none';
                    document.getElementById('documentContent').style.display = 'none';
                });
            }
        });

        // Document merge functionality
        function toggleDocumentSelection(checklistId) {
            const docList = document.getElementById('doc-list-' + checklistId);
            const checkboxes = docList.querySelectorAll('.doc-checkbox[data-checklist="' + checklistId + '"]');
            const selectBtn = document.getElementById('select-btn-' + checklistId);
            const mergeActions = document.getElementById('merge-actions-' + checklistId);

            checkboxes.forEach(cb => {
                cb.classList.remove('d-none');
            });
            selectBtn.classList.add('d-none');
            mergeActions.classList.remove('d-none');
        }

        function cancelDocumentSelection(checklistId) {
            const docList = document.getElementById('doc-list-' + checklistId);
            const checkboxes = docList.querySelectorAll('.doc-checkbox[data-checklist="' + checklistId + '"]');
            const selectBtn = document.getElementById('select-btn-' + checklistId);
            const mergeActions = document.getElementById('merge-actions-' + checklistId);

            checkboxes.forEach(cb => {
                cb.classList.add('d-none');
                cb.checked = false;
            });
            selectBtn.classList.remove('d-none');
            mergeActions.classList.add('d-none');
        }

        function mergeSelectedDocuments(checklistId, studentId) {
            const checkboxes = document.querySelectorAll('.doc-checkbox[data-checklist="' + checklistId + '"]:checked');
            const documentIds = Array.from(checkboxes).map(cb => cb.value);

            if (documentIds.length < 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select at least 2 documents to merge.',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            Swal.fire({
                title: 'Merge Documents',
                text: `Merge ${documentIds.length} selected documents into a single PDF?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Merge',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Merging Documents',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make AJAX request to merge documents
                    fetch(`/students/${studentId}/documents/merge`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ document_ids: documentIds })
                    })
                    .then(response => response.blob())
                    .then(blob => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `merged_documents_${Date.now()}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Documents merged successfully!',
                            confirmButtonColor: '#dc3545'
                        });

                        cancelDocumentSelection(checklistId);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to merge documents. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        }

        // Display success messages with SweetAlert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#DC143C',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#DC143C'
            });
        @endif
    </script>
    @endpush
@endsection
