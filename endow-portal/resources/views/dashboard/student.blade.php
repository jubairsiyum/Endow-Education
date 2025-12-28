@extends('layouts.admin')

@section('page-title', 'My Dashboard')
@section('breadcrumb', 'Home / Student Dashboard')

@section('content')
    <!-- Account Status Alert -->
    @if($student->account_status === 'pending')
    <div class="alert alert-warning alert-custom mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-clock fa-2x"></i>
            <div>
                <h5 class="mb-1">Account Pending Approval</h5>
                <p class="mb-0">Your account is currently under review. You'll be notified once it's been approved.</p>
            </div>
        </div>
    </div>
    @elseif($student->account_status === 'rejected')
    <div class="alert alert-danger alert-custom mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-times-circle fa-2x"></i>
            <div>
                <h5 class="mb-1">Account Not Approved</h5>
                <p class="mb-0">Unfortunately, your account was not approved. Please contact support for more information.</p>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-success alert-custom mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-check-circle fa-2x"></i>
            <div>
                <h5 class="mb-1">Account Approved</h5>
                <p class="mb-0">Your account has been approved. You can now track your application progress.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Profile Overview -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5>My Profile</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Full Name</label>
                            <div class="fw-semibold">{{ $student->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Email</label>
                            <div class="fw-semibold">{{ $student->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Phone</label>
                            <div class="fw-semibold">{{ $student->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Country</label>
                            <div class="fw-semibold">{{ $student->country }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Course/Program</label>
                            <div class="fw-semibold">{{ $student->course }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Application Status</label>
                            <div>
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
                                <span class="badge-custom badge-{{ $color }}-custom">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Assigned Counselor</label>
                            <div class="fw-semibold">{{ $student->assignedUser->name ?? 'Not assigned yet' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label mb-2">Checklist Progress</div>
                        <div class="stat-value">{{ $student->checklist_progress['percentage'] ?? 0 }}%</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 12px;">
                    <div class="progress-bar" role="progressbar"
                         style="width: {{ $student->checklist_progress['percentage'] ?? 0 }}%;"
                         aria-valuenow="{{ $student->checklist_progress['percentage'] ?? 0 }}"
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <div class="mt-3 text-muted" style="font-size: 0.875rem;">
                    {{ $student->checklist_progress['approved'] ?? 0 }} approved ·
                    {{ $student->checklist_progress['pending'] ?? 0 }} pending
                </div>
            </div>
        </div>
    </div>

    <!-- Checklist Items -->
    <div class="card-custom">
        <div class="card-header-custom">
            <h5>My Document Checklist</h5>
            <small class="text-muted">Upload required documents for your application</small>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Required</th>
                        <th>Status</th>
                        <th>Uploaded</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($student->checklists as $checklist)
                    <tr>
                        <td>
                            <strong>{{ $checklist->checklistItem->title }}</strong>
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
                            @if($checklist->remarks)
                                <br><small class="text-muted">{{ $checklist->remarks }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $document = $student->documents->where('checklist_item_id', $checklist->checklist_item_id)->first();
                            @endphp
                            @if($document)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                    <div>
                                        <small class="d-block">{{ $document->file_name }}</small>
                                        <small class="text-muted">{{ $document->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($document && $checklist->status !== 'approved')
                                <div class="d-flex gap-2">
                                    <a href="{{ route('documents.download', $document) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $document->id }})"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $document->id }}"
                                          action="{{ route('documents.destroy', $document) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            @elseif($checklist->status === 'approved')
                                <span class="badge-custom badge-success-custom">
                                    <i class="fas fa-check"></i> Approved
                                </span>
                            @else
                                <button type="button"
                                        class="btn btn-sm btn-primary-custom"
                                        onclick="openUploadModal({{ $checklist->id }}, {{ $checklist->checklist_item_id }}, '{{ $checklist->checklistItem->title }}')">
                                    <i class="fas fa-upload me-1"></i> Upload
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                            <p class="text-muted mb-0">No checklist items assigned yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('documents.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <input type="hidden" name="student_checklist_id" id="student_checklist_id">
                        <input type="hidden" name="checklist_item_id" id="checklist_item_id">

                        <div class="mb-3">
                            <label class="form-label fw-bold" id="documentTitle"></label>
                        </div>

                        <div class="mb-3">
                            <label for="document" class="form-label">Select File <span class="text-danger">*</span></label>
                            <input type="file"
                                   class="form-control @error('document') is-invalid @enderror"
                                   id="document"
                                   name="document"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   required>
                            <small class="form-text text-muted">Allowed: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</small>
                            @error('document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control"
                                      id="notes"
                                      name="notes"
                                      rows="3"
                                      placeholder="Any additional information about this document"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-upload me-1"></i> Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function openUploadModal(checklistId, checklistItemId, title) {
    document.getElementById('student_checklist_id').value = checklistId;
    document.getElementById('checklist_item_id').value = checklistItemId;
    document.getElementById('documentTitle').textContent = title;

    const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
    modal.show();
}

function confirmDelete(documentId) {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        document.getElementById('delete-form-' + documentId).submit();
    }
}
</script>
@endpush
