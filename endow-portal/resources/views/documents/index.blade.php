@extends('layouts.admin')

@section('page-title', 'Documents')
@section('breadcrumb', 'Home / Documents / All Documents')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Documents</h1>
            <p class="page-subtitle">View and manage all student documents</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Documents Table -->
    <div class="card-custom">
        <div class="card-body-custom">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Document Type</th>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Status</th>
                                <th>Uploaded By</th>
                                <th>Uploaded At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <a href="{{ route('students.show', $document->student) }}" class="text-decoration-none">
                                            {{ $document->student->user->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($document->checklistItem)
                                            {{ $document->checklistItem->title }}
                                        @else
                                            {{ $document->document_type ?? 'Other' }}
                                        @endif
                                    </td>
                                    <td>{{ $document->filename }}</td>
                                    <td>{{ $document->file_size_human }}</td>
                                    <td>
                                        @if($document->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($document->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($document->status == 'submitted')
                                            <span class="badge bg-info">Submitted</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $document->uploader->name ?? 'N/A' }}</td>
                                    <td>{{ $document->created_at->format('M d, Y H:i') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @can('view', $document->student)
                                                <a href="{{ route('documents.view', $document) }}"
                                                   class="btn btn-sm btn-info"
                                                   title="View"
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('documents.download', $document) }}"
                                                   class="btn btn-sm btn-primary"
                                                   title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endcan

                                            @can('update', $document->student)
                                                @if($document->status == 'pending' || $document->status == 'submitted')
                                                    <form action="{{ route('documents.approve', $document) }}"
                                                          method="POST"
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-sm btn-success"
                                                                title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button"
                                                            class="btn btn-sm btn-warning"
                                                            title="Reject"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $document->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            @endcan

                                            @can('delete', $document->student)
                                                <form action="{{ route('documents.destroy', $document) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>

                                <!-- Reject Modal -->
                                @can('update', $document->student)
                                <div class="modal fade" id="rejectModal{{ $document->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('documents.reject', $document) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Document</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to reject this document?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Reason for Rejection (Optional)</label>
                                                        <textarea name="rejection_reason"
                                                                  class="form-control"
                                                                  rows="3"
                                                                  placeholder="Enter reason for rejection..."></textarea>
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
                                @endcan
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $documents->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No documents found.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
