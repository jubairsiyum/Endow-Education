@extends('layouts.admin')

@section('page-title', 'Evaluation Questions')
@section('breadcrumb', 'Home / System / Evaluation Questions')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="fas fa-question-circle text-primary me-2"></i>
                Evaluation Questions
            </h2>
            <p class="text-muted mb-0">Manage evaluation questions for student consultant ratings</p>
        </div>
        <a href="{{ route('admin.evaluation-questions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Question
        </a>
    </div>

    <!-- Questions List -->
    <div class="card-custom">
        <div class="card-header-custom bg-light">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Questions ({{ $questions->total() }})</h5>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="5%">Order</th>
                            <th width="50%">Question</th>
                            <th width="10%">Status</th>
                            <th width="15%">Created By</th>
                            <th width="15%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                        <tr>
                            <td class="fw-semibold">#{{ $question->id }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $question->order }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $question->question }}</div>
                                <small class="text-muted">Created: {{ $question->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                @if($question->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times-circle me-1"></i>Inactive
                                </span>
                                @endif
                            </td>
                            <td>
                                @if($question->creator)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($question->creator->name, 0, 2)) }}
                                    </div>
                                    <span class="small">{{ $question->creator->name }}</span>
                                </div>
                                @else
                                <span class="text-muted small">System</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.evaluation-questions.edit', $question) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.evaluation-questions.toggle-status', $question) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-{{ $question->is_active ? 'warning' : 'success' }}"
                                                title="{{ $question->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $question->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.evaluation-questions.destroy', $question) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this question? This will also remove all evaluations associated with it.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No evaluation questions found.</p>
                                <a href="{{ route('admin.evaluation-questions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create First Question
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($questions->hasPages())
        <div class="card-footer-custom bg-light">
            {{ $questions->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
</style>
@endsection
