@extends('layouts.student')

@section('page-title', 'Consultant Evaluation')
@section('breadcrumb', 'Home / Consultant Evaluation')

@section('content')
    <!-- Modern Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-warning-light">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box-large bg-white text-warning">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Rate your consultant's performance...</h4>
                                    <p class="mb-0 text-muted">Rate your consultant's performance and provide valuable feedback</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            @if(isset($hasConsultant) && $hasConsultant && isset($existingEvaluations))
                                @if($existingEvaluations->count() > 0)
                                    <div class="d-inline-flex align-items-center gap-2 px-4 py-2 bg-white rounded-pill shadow-sm">
                                        <i class="fas fa-check-circle text-success fa-lg"></i>
                                        <span class="fw-semibold">Evaluation Submitted</span>
                                    </div>
                                @else
                                    <div class="d-inline-flex align-items-center gap-2 px-4 py-2 bg-white rounded-pill shadow-sm">
                                        <i class="fas fa-clock text-warning fa-lg"></i>
                                        <span class="fw-semibold">Evaluation Pending</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Quick Info Pills -->
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-user-tie text-primary me-1"></i> Professional Feedback
                        </span>
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-shield-alt text-success me-1"></i> Confidential
                        </span>
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-edit text-info me-1"></i> Editable Anytime
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!isset($isApproved) || !$isApproved)
    <!-- Application Not Approved -->
    <div class="alert alert-warning border-0 shadow-sm">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-clock fa-2x text-warning"></i>
            <div>
                <h5 class="mb-1 fw-bold">Application Under Review</h5>
                <p class="mb-2">Your application is currently being reviewed by our team. The Consultant Evaluation feature will be available once your application is approved.</p>
                <p class="mb-0 text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    <small>You'll be notified via email once your application status is updated. Thank you for your patience!</small>
                </p>
            </div>
        </div>
    </div>
    @elseif(!$hasConsultant)
    <!-- No Consultant Assigned -->
    <div class="alert alert-info border-0 shadow-sm">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-info-circle fa-2x text-info"></i>
            <div>
                <h5 class="mb-1 fw-bold">No Consultant Assigned</h5>
                <p class="mb-0">You don't have a consultant assigned yet. Once a consultant is assigned to you, you'll be able to submit an evaluation here.</p>
            </div>
        </div>
    </div>
    @else
    <!-- Consultant Info Card -->
    <div class="card-custom mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="consultant-avatar">
                            {{ strtoupper(substr($consultant->name, 0, 2)) }}
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">{{ $consultant->name }}</h5>
                            <p class="text-muted mb-1 small">
                                <i class="fas fa-envelope me-1"></i>{{ $consultant->email }}
                            </p>
                            @if($consultant->phone)
                            <p class="text-muted mb-0 small">
                                <i class="fas fa-phone me-1"></i>{{ $consultant->phone }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @if($existingEvaluations->count() > 0)
                    <div class="evaluation-status-badge success">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>
                            <div class="status-title">Completed</div>
                            <div class="status-subtitle">You can update anytime</div>
                        </div>
                    </div>
                    @else
                    <div class="evaluation-status-badge pending">
                        <i class="fas fa-clock me-2"></i>
                        <div>
                            <div class="status-title">Pending</div>
                            <div class="status-subtitle">Please submit your feedback</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($questions->count() === 0)
    <!-- No Questions Available -->
    <div class="card-custom border-0 bg-warning bg-opacity-10">
        <div class="card-body p-4">
            <div class="d-flex align-items-start gap-3">
                <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                <div>
                    <h5 class="mb-1 fw-bold">No Evaluation Questions Available</h5>
                    <p class="mb-0 text-muted">There are currently no evaluation questions set up by the administration. Please check back later.</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Evaluation Form -->
    <form action="{{ route('student.consultant-evaluation.store') }}" method="POST">
        @csrf

        <div class="card-custom">
            <div class="card-body p-4">
                <!-- Instructions Banner -->
                <div class="instructions-banner mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong>Instructions:</strong> Please rate your consultant on each of the following criteria. Your honest feedback helps us improve our services.
                    </div>
                </div>

                @foreach($questions as $index => $question)
                <div class="evaluation-question-card @if(!$loop->last) mb-4 @endif">
                    <div class="question-header">
                        <span class="question-number">{{ $index + 1 }}</span>
                        <h6 class="question-text mb-0">{{ $question->question }}</h6>
                    </div>

                    <input type="hidden" name="evaluations[{{ $index }}][question_id]" value="{{ $question->id }}">

                    <!-- Rating Options -->
                    <div class="rating-options mt-3">
                        @php
                            $ratings = [
                                'below_average' => ['label' => 'Below Average', 'color' => 'danger', 'icon' => 'fa-times-circle'],
                                'average' => ['label' => 'Average', 'color' => 'warning', 'icon' => 'fa-minus-circle'],
                                'neutral' => ['label' => 'Neutral', 'color' => 'secondary', 'icon' => 'fa-circle'],
                                'good' => ['label' => 'Good', 'color' => 'info', 'icon' => 'fa-check-circle'],
                                'excellent' => ['label' => 'Excellent', 'color' => 'success', 'icon' => 'fa-star'],
                            ];
                            $existingRating = $existingEvaluations->get($question->id);
                        @endphp

                        <div class="row g-2">
                            @foreach($ratings as $value => $ratingInfo)
                            <div class="col-md">
                                <input type="radio"
                                       class="btn-check"
                                       name="evaluations[{{ $index }}][rating]"
                                       id="rating_{{ $question->id }}_{{ $value }}"
                                       value="{{ $value }}"
                                       {{ $existingRating && $existingRating->rating === $value ? 'checked' : '' }}
                                       required>
                                <label class="rating-btn rating-btn-{{ $ratingInfo['color'] }}"
                                       for="rating_{{ $question->id }}_{{ $value }}">
                                    <i class="fas {{ $ratingInfo['icon'] }}"></i>
                                    <span>{{ $ratingInfo['label'] }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Optional Comment -->
                    <div class="mt-3">
                        <label for="comment_{{ $question->id }}" class="form-label small text-muted">
                            <i class="fas fa-comment-dots me-1"></i>Additional Comments (Optional)
                        </label>
                        <textarea class="form-control comment-textarea"
                                  id="comment_{{ $question->id }}"
                                  name="evaluations[{{ $index }}][comment]"
                                  rows="2"
                                  placeholder="Share your thoughts...">{{ $existingRating ? $existingRating->comment : '' }}</textarea>
                    </div>
                </div>
                @if(!$loop->last)
                <hr class="my-4">
                @endif
                @endforeach

                @error('evaluations')
                <div class="alert alert-danger mt-4 mb-0">{{ $message }}</div>
                @enderror
            </div>

            <!-- Form Footer -->
            <div class="card-footer bg-light p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-paper-plane me-2"></i>
                        {{ $existingEvaluations->count() > 0 ? 'Update Evaluation' : 'Submit Evaluation' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
    @endif

<style>
/* Gradient Header */
.bg-gradient-warning-light {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
    border-left: 4px solid #ffc107;
}

.icon-box-large {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Card Custom Styling */
.card-custom {
    background: #fff;
    border-radius: 15px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.card-custom:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

/* Consultant Avatar */
.consultant-avatar {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Evaluation Status Badge */
.evaluation-status-badge {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    border-radius: 12px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
}

.evaluation-status-badge.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-color: #28a745;
}

.evaluation-status-badge.pending {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
    border-color: #ffc107;
}

.evaluation-status-badge i {
    font-size: 1.5rem;
}

.status-title {
    font-weight: 600;
    font-size: 15px;
    color: #1a1a1a;
}

.status-subtitle {
    font-size: 12px;
    color: #6c757d;
}

/* Instructions Banner */
.instructions-banner {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left: 4px solid #17a2b8;
    padding: 16px 20px;
    border-radius: 10px;
    display: flex;
    align-items: start;
    gap: 10px;
    font-size: 14px;
    color: #0c5460;
}

.instructions-banner i {
    font-size: 18px;
    margin-top: 2px;
}

/* Evaluation Question Card */
.evaluation-question-card {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.question-header {
    display: flex;
    align-items: start;
    gap: 12px;
    margin-bottom: 15px;
}

.question-number {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.question-text {
    flex: 1;
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.5;
    padding-top: 4px;
}

/* Rating Options */
.rating-options {
    margin: 0;
}

.rating-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 15px 10px;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
    text-align: center;
}

.rating-btn i {
    font-size: 1.5rem;
    transition: all 0.2s ease;
}

.rating-btn span {
    font-size: 13px;
    font-weight: 500;
}

.rating-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Rating Color Variants */
.rating-btn-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.rating-btn-danger:hover {
    background: #dc3545;
    color: white;
}

.rating-btn-warning {
    border-color: #ffc107;
    color: #856404;
}

.rating-btn-warning:hover {
    background: #ffc107;
    color: #856404;
}

.rating-btn-secondary {
    border-color: #6c757d;
    color: #6c757d;
}

.rating-btn-secondary:hover {
    background: #6c757d;
    color: white;
}

.rating-btn-info {
    border-color: #17a2b8;
    color: #17a2b8;
}

.rating-btn-info:hover {
    background: #17a2b8;
    color: white;
}

.rating-btn-success {
    border-color: #28a745;
    color: #28a745;
}

.rating-btn-success:hover {
    background: #28a745;
    color: white;
}

/* Selected Rating */
.btn-check:checked + .rating-btn {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    font-weight: 600;
}

.btn-check:checked + .rating-btn-danger {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.btn-check:checked + .rating-btn-warning {
    background: #ffc107;
    color: #856404;
    border-color: #ffc107;
}

.btn-check:checked + .rating-btn-secondary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.btn-check:checked + .rating-btn-info {
    background: #17a2b8;
    color: white;
    border-color: #17a2b8;
}

.btn-check:checked + .rating-btn-success {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

/* Comment Textarea */
.comment-textarea {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.comment-textarea:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    background: white;
}

/* Card Footer */
.card-footer {
    border-top: 1px solid #e9ecef;
    background: #f8f9fa !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .icon-box-large {
        width: 50px;
        height: 50px;
    }

    .consultant-avatar {
        width: 50px;
        height: 50px;
        font-size: 18px;
    }

    .evaluation-status-badge {
        padding: 10px 15px;
    }

    .rating-btn {
        padding: 12px 8px;
    }

    .rating-btn i {
        font-size: 1.2rem;
    }

    .rating-btn span {
        font-size: 11px;
    }
}
</style>
@endsection
