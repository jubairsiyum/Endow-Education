@extends('layouts.admin')

@section('page-title', 'Contact Submissions')
@section('breadcrumb', 'Home / Contact Submissions')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-envelope text-danger me-2"></i>Contact Submissions</h4>
            <small class="text-muted">View and manage student contact form submissions</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('contact-submissions.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label small">Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="priority" class="form-label small">Priority</label>
                    <select name="priority" id="priority" class="form-select form-select-sm">
                        <option value="all" {{ request('priority') === 'all' ? 'selected' : '' }}>All Priorities</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label small">Search</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm" 
                           placeholder="Search by student name or message..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('contact-submissions.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Submitted</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                                <tr>
                                    <td>
                                        <div>
                                            <a href="{{ route('students.show', $submission->student) }}" 
                                               class="text-decoration-none fw-medium">
                                                {{ $submission->student->user->name ?? 'N/A' }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $submission->student->user->email ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $submission->subject_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $submission->priority_color }}">
                                            {{ ucfirst($submission->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $submission->status_color }}">
                                            {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($submission->assignedUser)
                                            <div>
                                                <i class="fas fa-user-circle me-1"></i>
                                                {{ $submission->assignedUser->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $submission->created_at->format('M d, Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $submission->created_at->format('g:i A') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('contact-submissions.show', $submission) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top">
                    {{ $submissions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No contact submissions found.</p>
                    @if(request()->hasAny(['status', 'priority', 'search']))
                        <a href="{{ route('contact-submissions.index') }}" class="btn btn-sm btn-outline-primary">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
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
