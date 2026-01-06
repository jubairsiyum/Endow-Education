@extends('layouts.admin')

@section('page-title', 'Contact Submission Details')
@section('breadcrumb', 'Home / Contact Submissions / Details')

@section('content')
    <div class="mb-4">
        <a href="{{ route('contact-submissions.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Submission Details -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-envelope text-danger me-2"></i>Contact Submission
                        </h5>
                        <div>
                            <span class="badge bg-{{ $contactSubmission->priority_color }} me-2">
                                {{ ucfirst($contactSubmission->priority) }} Priority
                            </span>
                            <span class="badge bg-{{ $contactSubmission->status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $contactSubmission->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Student Info -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Student Name</label>
                                <div>
                                    <a href="{{ route('students.show', $contactSubmission->student) }}" 
                                       class="text-decoration-none fw-medium">
                                        <i class="fas fa-user-circle me-1"></i>
                                        {{ $contactSubmission->student->user->name ?? 'N/A' }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Email</label>
                                <div>{{ $contactSubmission->student->user->email ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Phone</label>
                                <div>{{ $contactSubmission->student->phone ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Submitted On</label>
                                <div>{{ $contactSubmission->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="mb-4">
                        <label class="text-muted small mb-1">Subject</label>
                        <div>
                            <span class="badge bg-light text-dark border fs-6">
                                {{ $contactSubmission->subject_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="mb-4">
                        <label class="text-muted small mb-1">Message</label>
                        <div class="border rounded p-3 bg-light">
                            {{ $contactSubmission->message }}
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div class="mb-3">
                        <label class="text-muted small mb-2">
                            <i class="fas fa-sticky-note me-1"></i>Admin Notes / Response
                        </label>
                        @if($contactSubmission->admin_notes)
                            <div class="border rounded p-3 mb-3" style="background-color: #fffacd;">
                                {{ $contactSubmission->admin_notes }}
                                @if($contactSubmission->responder)
                                    <hr class="my-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        Responded by {{ $contactSubmission->responder->name }}
                                        on {{ $contactSubmission->responded_at->format('M d, Y g:i A') }}
                                    </small>
                                @endif
                            </div>
                        @endif

                        <!-- Add/Update Notes Form -->
                        <form action="{{ route('contact-submissions.add-notes', $contactSubmission) }}" method="POST">
                            @csrf
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror"
                                      name="admin_notes"
                                      rows="4"
                                      placeholder="Add your response or notes here...">{{ old('admin_notes', $contactSubmission->admin_notes) }}</textarea>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <button type="submit" class="btn btn-primary mt-2">
                                <i class="fas fa-save me-1"></i>Save Notes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Update -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('contact-submissions.update-status', $contactSubmission) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="new" {{ $contactSubmission->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="in_progress" {{ $contactSubmission->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $contactSubmission->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $contactSubmission->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">
                            <i class="fas fa-check me-1"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Assignment -->
            @can('viewAny', App\Models\User::class)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-user-check me-2"></i>Assign To</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('contact-submissions.assign', $contactSubmission) }}" method="POST">
                        @csrf
                        <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                            <option value="">Select User</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}" 
                                        {{ $contactSubmission->assigned_to === $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->roles->pluck('name')->first() }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-success btn-sm w-100 mt-2">
                            <i class="fas fa-user-plus me-1"></i>Assign
                        </button>
                    </form>
                    
                    @if($contactSubmission->assignedUser)
                        <div class="mt-3 p-2 bg-light rounded">
                            <small class="text-muted">Currently assigned to:</small>
                            <div class="fw-medium">{{ $contactSubmission->assignedUser->name }}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endcan

            <!-- Delete -->
            @can('viewAny', App\Models\User::class)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 text-danger"><i class="fas fa-trash me-2"></i>Danger Zone</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Permanently delete this contact submission.</p>
                    <form action="{{ route('contact-submissions.destroy', $contactSubmission) }}" 
                          method="POST" 
                          id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                class="btn btn-danger btn-sm w-100" 
                                onclick="confirmDelete()">
                            <i class="fas fa-trash me-1"></i>Delete Submission
                        </button>
                    </form>
                </div>
            </div>
            @endcan
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Delete Submission?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC143C',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
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
</script>
@endpush
