@extends('layouts.admin')

@section('page-title', 'Edit Evaluation Question')
@section('breadcrumb', 'Home / System / Evaluation Questions / Edit')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="page-title mb-2">
            <i class="fas fa-edit text-primary me-2"></i>
            Edit Evaluation Question
        </h2>
        <p class="text-muted mb-0">Update evaluation question #{{ $evaluationQuestion->id }}</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Question Details</h5>
                </div>
                <div class="card-body-custom">
                    <form action="{{ route('admin.evaluation-questions.update', $evaluationQuestion) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Question -->
                        <div class="mb-4">
                            <label for="question" class="form-label fw-semibold">
                                Question <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('question') is-invalid @enderror"
                                      id="question"
                                      name="question"
                                      rows="3"
                                      required>{{ old('question', $evaluationQuestion->question) }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Order -->
                        <div class="mb-4">
                            <label for="order" class="form-label fw-semibold">
                                Display Order
                            </label>
                            <input type="number"
                                   class="form-control @error('order') is-invalid @enderror"
                                   id="order"
                                   name="order"
                                   min="0"
                                   value="{{ old('order', $evaluationQuestion->order) }}">
                            <small class="form-text text-muted">
                                Lower numbers appear first
                            </small>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $evaluationQuestion->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active (visible to students)
                                </label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Question
                            </button>
                            <a href="{{ route('admin.evaluation-questions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card-custom mt-4 border-danger">
                <div class="card-header-custom bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
                </div>
                <div class="card-body-custom">
                    <p class="text-muted mb-3">
                        Deleting this question will permanently remove it and all associated evaluation responses from students.
                    </p>
                    <form action="{{ route('admin.evaluation-questions.destroy', $evaluationQuestion) }}"
                          method="POST"
                          onsubmit="return confirm('Are you absolutely sure? This cannot be undone and will delete all evaluation responses for this question.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Question
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Question Info</h5>
                </div>
                <div class="card-body-custom">
                    <div class="mb-3">
                        <label class="small text-muted">Created</label>
                        <p class="mb-0">{{ $evaluationQuestion->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">Last Updated</label>
                        <p class="mb-0">{{ $evaluationQuestion->updated_at->format('M d, Y g:i A') }}</p>
                    </div>
                    @if($evaluationQuestion->creator)
                    <div>
                        <label class="small text-muted">Created By</label>
                        <p class="mb-0">{{ $evaluationQuestion->creator->name }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
