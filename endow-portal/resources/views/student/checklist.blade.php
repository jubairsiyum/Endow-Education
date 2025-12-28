@extends('layouts.admin')

@section('page-title', 'My Checklist')
@section('breadcrumb', 'Home / My Checklist')

@section('content')
<div style="width: 100%; max-width: 100%;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="fas fa-clipboard-list text-danger"></i> My Document Checklist
            </h4>
            <small class="text-muted">Upload required documents for your {{ $student->targetProgram ? $student->targetProgram->name : 'application' }}</small>
        </div>
    </div>

    @if($student->targetProgram)
    <div class="alert alert-info border-0 shadow-sm mb-3">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-info-circle fa-2x text-info"></i>
            <div>
                <h6 class="mb-1 fw-bold">Target Program</h6>
                <p class="mb-0 small">
                    <strong>{{ $student->targetProgram->name }}</strong> at {{ $student->targetUniversity->name }}
                    <br>Level: {{ ucfirst($student->targetProgram->level) }}
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning border-0 shadow-sm mb-3">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
            <div>
                <h6 class="mb-1 fw-bold">No Target Program Selected</h6>
                <p class="mb-0 small">Please update your profile to select a target university and program to see customized checklist items.</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Progress Card -->
    @php
        $totalItems = $checklistItems->count();
        $completedItems = $studentChecklists->where('status', 'approved')->count();
        $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
    @endphp

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold">Overall Progress</span>
                <span class="badge bg-{{ $progress == 100 ? 'success' : 'primary' }}">{{ $completedItems }}/{{ $totalItems }} Completed</span>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-{{ $progress == 100 ? 'success' : 'danger' }}"
                     role="progressbar"
                     style="width: {{ $progress }}%;"
                     aria-valuenow="{{ $progress }}"
                     aria-valuemin="0"
                     aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <!-- Checklist Items -->
    <div class="row g-3">
        @forelse($checklistItems as $item)
            @php
                $checklistStatus = $studentChecklists->get($item->id);
                $itemDocuments = $documents->get($item->id, collect());
                $status = $checklistStatus->status ?? 'pending';

                $statusConfig = [
                    'pending' => ['icon' => 'clock', 'color' => 'secondary', 'text' => 'Not Started'],
                    'submitted' => ['icon' => 'hourglass-half', 'color' => 'warning', 'text' => 'Under Review'],
                    'approved' => ['icon' => 'check-circle', 'color' => 'success', 'text' => 'Approved'],
                    'rejected' => ['icon' => 'times-circle', 'color' => 'danger', 'text' => 'Rejected'],
                ];
                $config = $statusConfig[$status];
            @endphp

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">
                                    <i class="fas fa-file-alt text-danger me-2"></i>{{ $item->title }}
                                    @if($item->is_required)
                                        <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">Required</span>
                                    @endif
                                </h6>
                                @if($item->description)
                                    <p class="text-muted small mb-2">{{ $item->description }}</p>
                                @endif
                            </div>
                            <span class="badge bg-{{ $config['color'] }}">
                                <i class="fas fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                            </span>
                        </div>

                        @if($itemDocuments->isNotEmpty())
                            <div class="border-top pt-2 mt-2">
                                <p class="text-muted small mb-2"><strong>Uploaded Documents:</strong></p>
                                @foreach($itemDocuments as $doc)
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-file-{{ $doc->file_type == 'pdf' ? 'pdf' : 'image' }} text-danger"></i>
                                            <small class="text-truncate" style="max-width: 200px;">{{ basename($doc->file_path) }}</small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('student.document.delete', $doc) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this document?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($checklistStatus && $checklistStatus->remarks)
                            <div class="alert alert-info alert-sm mt-2 mb-0">
                                <small><strong>Admin Note:</strong> {{ $checklistStatus->remarks }}</small>
                            </div>
                        @endif

                        @if($status !== 'approved')
                            <button class="btn btn-danger btn-sm w-100 mt-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#uploadModal{{ $item->id }}">
                                <i class="fas fa-upload me-1"></i> Upload Document
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upload Modal -->
            <div class="modal fade" id="uploadModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('student.checklist.upload', $item) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Document</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h6 class="mb-3">{{ $item->title }}</h6>

                                <div class="mb-3">
                                    <label class="form-label">Select Document <span class="text-danger">*</span></label>
                                    <input type="file" name="document" class="form-control" required
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted">Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 10MB)</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Remarks (Optional)</label>
                                    <textarea name="remarks" class="form-control" rows="3" placeholder="Add any notes or comments..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-upload me-1"></i> Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No checklist items available yet.</p>
                        @if(!$student->targetProgram)
                            <p class="text-muted small">Please select a target program in your profile to view checklist items.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
