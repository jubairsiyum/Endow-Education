@extends('layouts.student')

@section('page-title', 'Submit Documents')
@section('breadcrumb', 'Home / Submit Documents')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex align-items-start gap-3">
                    <i class="fas fa-info-circle fa-2x text-info"></i>
                    <div>
                        <h5 class="mb-1 fw-bold">Document Submission Guidelines</h5>
                        <p class="mb-1">Please submit the required documents in the order listed below. Each document must be reviewed and approved before proceeding to the next.</p>
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max size: 5MB)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-tasks text-danger me-2"></i>Required Documents Checklist</h5>
                        <span class="badge bg-danger">{{ $completedCount }} of {{ $totalCount }} Completed</span>
                    </div>
                </div>
                <div class="card-body-custom p-0">
                    @if($checklistItems->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Documents Required Yet</h5>
                            <p class="text-muted">Your counselor will assign required documents based on your target program.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($checklistItems as $index => $item)
                                @php
                                    $studentChecklist = $item->studentChecklists->firstWhere('student_id', $student->id);
                                    $status = $studentChecklist->status ?? 'pending';
                                    $isCompleted = $status === 'completed';
                                    $isRejected = $status === 'rejected';
                                    $isPending = $status === 'pending';
                                    $isSubmitted = $status === 'submitted';

                                    // Determine if this item can be edited
                                    $canEdit = $isPending || $isRejected;

                                    // Status badge config
                                    $statusConfig = [
                                        'completed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Approved'],
                                        'submitted' => ['class' => 'info', 'icon' => 'clock', 'text' => 'Under Review'],
                                        'rejected' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Needs Revision'],
                                        'pending' => ['class' => 'warning', 'icon' => 'exclamation-circle', 'text' => 'Not Submitted'],
                                    ];
                                    $config = $statusConfig[$status] ?? $statusConfig['pending'];
                                @endphp

                                <div class="list-group-item border-start-0 border-end-0 p-4 {{ $isCompleted ? 'bg-light bg-opacity-50' : '' }}">
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Step Number -->
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold {{ $isCompleted ? 'bg-success text-white' : 'bg-danger bg-opacity-10 text-danger' }}"
                                                 style="width: 45px; height: 45px; font-size: 18px;">
                                                @if($isCompleted)
                                                    <i class="fas fa-check"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Document Info -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1 fw-bold">{{ $item->name }}</h6>
                                                    @if($item->description)
                                                        <p class="text-muted mb-2 small">{{ $item->description }}</p>
                                                    @endif
                                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                                        <span class="badge bg-{{ $config['class'] }}">
                                                            <i class="fas fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                                        </span>
                                                        @if($item->is_required)
                                                            <span class="badge bg-danger">Required</span>
                                                        @else
                                                            <span class="badge bg-secondary">Optional</span>
                                                        @endif
                                                        @if($item->targetProgram)
                                                            <small class="text-muted">
                                                                <i class="fas fa-graduation-cap me-1"></i>{{ $item->targetProgram->name }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Document Details & Actions -->
                                            @if($studentChecklist && $studentChecklist->document_path)
                                                <div class="mt-3 p-3 bg-light rounded">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                                            <div>
                                                                <div class="fw-semibold small">{{ basename($studentChecklist->document_path) }}</div>
                                                                <small class="text-muted">Uploaded: {{ $studentChecklist->updated_at->format('M d, Y h:i A') }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ Storage::url($studentChecklist->document_path) }}"
                                                               class="btn btn-sm btn-outline-primary"
                                                               target="_blank">
                                                                <i class="fas fa-eye me-1"></i>View
                                                            </a>
                                                            @if($canEdit)
                                                                <button type="button"
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        onclick="deleteDocument({{ $studentChecklist->id }})">
                                                                    <i class="fas fa-trash me-1"></i>Remove
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if($isRejected && $studentChecklist->feedback)
                                                        <div class="alert alert-danger mt-3 mb-0 small">
                                                            <strong><i class="fas fa-exclamation-triangle me-1"></i>Revision Required:</strong><br>
                                                            {{ $studentChecklist->feedback }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Upload Form -->
                                            @if($canEdit)
                                                <div class="mt-3">
                                                    <form action="{{ route('student.checklist.upload', $item->id) }}"
                                                          method="POST"
                                                          enctype="multipart/form-data"
                                                          class="upload-form">
                                                        @csrf
                                                        <div class="d-flex gap-2">
                                                            <input type="file"
                                                                   name="document"
                                                                   class="form-control form-control-sm"
                                                                   accept=".pdf,.jpg,.jpeg,.png"
                                                                   required>
                                                            <button type="submit" class="btn btn-sm btn-primary-custom">
                                                                <i class="fas fa-upload me-1"></i>
                                                                {{ $studentChecklist && $studentChecklist->document_path ? 'Re-upload' : 'Upload' }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
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
                                Keep file sizes under 5MB
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to remove this document? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Document</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function deleteDocument(checklistId) {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const form = document.getElementById('deleteForm');
        form.action = `/student/checklist/${checklistId}`;
        modal.show();
    }

    // Form submission handling with loading state
    document.querySelectorAll('.upload-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';

            // Re-enable after 5 seconds (in case of error)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 5000);
        });
    });
</script>
@endpush
