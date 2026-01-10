@extends('layouts.admin')

@section('page-title', 'Create Evaluation Question')
@section('breadcrumb', 'Home / System / Evaluation Questions / Create')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="page-title mb-2">
            <i class="fas fa-plus-circle text-primary me-2"></i>
            Create Evaluation Question
        </h2>
        <p class="text-muted mb-0">Add a new evaluation question for students to rate consultants</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Question Details</h5>
                </div>
                <div class="card-body-custom">
                    <form action="{{ route('admin.evaluation-questions.store') }}" method="POST">
                        @csrf

                        <!-- Question -->
                        <div class="mb-4">
                            <label for="question" class="form-label fw-semibold">
                                Question <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('question') is-invalid @enderror"
                                      id="question"
                                      name="question"
                                      rows="3"
                                      placeholder="E.g., How satisfied are you with your consultant's communication?"
                                      required>{{ old('question') }}</textarea>
                            <small class="form-text text-muted">
                                Ask a clear, specific question about the consultant's performance
                            </small>
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
                                   value="{{ old('order', 0) }}"
                                   placeholder="0">
                            <small class="form-text text-muted">
                                Lower numbers appear first (0 = first)
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
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active (visible to students)
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Only active questions will be shown to students
                            </small>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Question
                            </button>
                            <a href="{{ route('admin.evaluation-questions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips</h5>
                </div>
                <div class="card-body-custom">
                    <h6 class="fw-bold text-primary">Question Guidelines:</h6>
                    <ul class="small mb-3">
                        <li>Be specific and clear</li>
                        <li>Focus on one aspect per question</li>
                        <li>Use neutral language</li>
                        <li>Avoid leading questions</li>
                    </ul>

                    <h6 class="fw-bold text-success">Example Questions:</h6>
                    <ul class="small mb-3">
                        <li>How would you rate the consultant's responsiveness?</li>
                        <li>How satisfied are you with the guidance provided?</li>
                        <li>How knowledgeable was the consultant about the process?</li>
                    </ul>

                    <h6 class="fw-bold text-warning">Rating Scale:</h6>
                    <p class="small mb-0">Students rate using:</p>
                    <ul class="small mb-0">
                        <li>Below Average</li>
                        <li>Average</li>
                        <li>Neutral</li>
                        <li>Good</li>
                        <li>Excellent</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
