@extends('layouts.admin')

@section('page-title', $student->name)
@section('breadcrumb', 'Home / Students / ' . $student->name)

@push('styles')
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
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="page-title mb-2">{{ $student->name }}</h1>
            <p class="page-subtitle mb-0">{{ $student->email }} · {{ $student->phone }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('update', $student)
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
                            {{ $student->checklist_progress['percentage'] ?? 0 }}%
                        </div>
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
            <form action="{{ route('students.approve', $student) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check me-2"></i> Approve
                </button>
            </form>
            <form action="{{ route('students.reject', $student) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to reject this student?');">
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
                    <i class="fas fa-tasks me-2"></i> Checklist
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab"
                        data-bs-target="#documents" type="button" role="tab">
                    <i class="fas fa-file-pdf me-2"></i> Documents
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
                                <td>{{ $student->course }}</td>
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
                    <h5>Checklist Progress</h5>
                    <div class="progress" style="height: 24px;">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $student->checklist_progress['percentage'] ?? 0 }}%; background: var(--primary-color);"
                             aria-valuenow="{{ $student->checklist_progress['percentage'] ?? 0 }}"
                             aria-valuemin="0" aria-valuemax="100">
                            {{ $student->checklist_progress['percentage'] ?? 0 }}%
                        </div>
                    </div>
                    <div class="mt-2 text-muted">
                        {{ $student->checklist_progress['approved'] ?? 0 }} approved ·
                        {{ $student->checklist_progress['submitted'] ?? 0 }} submitted ·
                        {{ $student->checklist_progress['pending'] ?? 0 }} pending
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Required</th>
                                <th>Status</th>
                                <th>Submitted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->checklists as $checklist)
                            <tr>
                                <td>
                                    <strong>{{ $checklist->checklistItem->name }}</strong>
                                    @if($checklist->checklistItem->description)
                                    <br><small class="text-muted">{{ $checklist->checklistItem->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($checklist->checklistItem->is_required)
                                        <span class="badge-custom badge-danger-custom">Required</span>
                                    @else
                                        <span class="badge-custom badge-secondary-custom">Optional</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $checklistColors = [
                                            'pending' => 'secondary',
                                            'submitted' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        $checklistColor = $checklistColors[$checklist->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge-custom badge-{{ $checklistColor }}-custom">
                                        {{ ucfirst($checklist->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $checklist->submitted_at ? $checklist->submitted_at->format('M d, Y') : '-' }}
                                </td>
                                <td>
                                    @if($checklist->status === 'submitted')
                                        @can('update', $student)
                                        <button class="btn btn-sm btn-success">Approve</button>
                                        <button class="btn btn-sm btn-danger">Reject</button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    No checklist items found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="mb-4">
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-upload me-2"></i> Upload Document
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Document</th>
                                <th>Checklist Item</th>
                                <th>Size</th>
                                <th>Uploaded</th>
                                <th>Uploaded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->documents as $document)
                            <tr>
                                <td>
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <strong>{{ $document->original_filename }}</strong>
                                </td>
                                <td>{{ $document->checklistItem->name ?? 'General' }}</td>
                                <td>{{ $document->file_size_human }}</td>
                                <td>{{ $document->created_at->format('M d, Y g:i A') }}</td>
                                <td>{{ $document->uploadedBy->name ?? 'Unknown' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="#" class="action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="action-btn info" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @can('update', $student)
                                        <form action="#" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Delete"
                                                    onclick="return confirm('Are you sure?');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-file-pdf fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No documents uploaded yet</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Follow-ups Tab -->
            <div class="tab-pane fade" id="followups" role="tabpanel">
                <div class="mb-4">
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#followupModal">
                        <i class="fas fa-plus me-2"></i> Add Follow-up
                    </button>
                </div>

                <div class="timeline">
                    @forelse($student->followUps()->orderBy('created_at', 'desc')->get() as $followUp)
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $followUp->creator->name ?? 'Unknown' }}</strong>
                                    <small class="text-muted ms-2">
                                        {{ $followUp->created_at->format('M d, Y g:i A') }}
                                    </small>
                                    @if($followUp->next_follow_up_date)
                                        <br><small class="text-muted">Next follow-up: {{ $followUp->next_follow_up_date->format('M d, Y') }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($followUp->note)) !!}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No follow-ups yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
