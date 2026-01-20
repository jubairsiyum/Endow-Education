@extends('layouts.student')

@section('page-title', 'Consultant Evaluation')
@section('breadcrumb', 'Home / Consultant Evaluation')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="page-title mb-2">
            <i class="fas fa-star text-warning me-2"></i>
            Consultant Evaluation
        </h2>
        <p class="text-muted mb-0">Rate your consultant's performance and provide feedback</p>
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
        <div class="card-header-custom bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Your Consultant</h5>
        </div>
        <div class="card-body-custom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 60px; height: 60px; font-size: 24px;">
                            {{ strtoupper(substr($consultant->name, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $consultant->name }}</h4>
                            <p class="text-muted mb-0">
                                <i class="fas fa-envelope me-2"></i>{{ $consultant->email }}
                            </p>
                            @if($consultant->phone)
                            <p class="text-muted mb-0">
                                <i class="fas fa-phone me-2"></i>{{ $consultant->phone }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    @if($existingEvaluations->count() > 0)
                    <span class="badge bg-success px-3 py-2 fs-6">
                        <i class="fas fa-check-circle me-1"></i>
                        Evaluation Submitted
                    </span>
                    <p class="text-muted small mt-2 mb-0">You can update your evaluation anytime</p>
                    @else
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                        <i class="fas fa-clock me-1"></i>
                        Evaluation Pending
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($questions->count() === 0)
    <!-- No Questions Available -->
    <div class="alert alert-warning border-0 shadow-sm">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
            <div>
                <h5 class="mb-1 fw-bold">No Evaluation Questions Available</h5>
                <p class="mb-0">There are currently no evaluation questions set up by the administration. Please check back later.</p>
            </div>
        </div>
    </div>
    @else
    <!-- Evaluation Form -->
    <form action="{{ route('student.consultant-evaluation.store') }}" method="POST">
        @csrf

        <div class="card-custom">
            <div class="card-header-custom bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Evaluation Questions
                </h5>
            </div>
            <div class="card-body-custom">
                <div class="alert alert-info border-0 mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Instructions:</strong> Please rate your consultant on each of the following criteria. Your honest feedback helps us improve our services.
                </div>

                @foreach($questions as $index => $question)
                <div class="evaluation-question mb-4 pb-4 @if(!$loop->last) border-bottom @endif">
                    <h6 class="fw-bold mb-3">
                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                        {{ $question->question }}
                    </h6>

                    <input type="hidden" name="evaluations[{{ $index }}][question_id]" value="{{ $question->id }}">

                    <!-- Rating Options -->
                    <div class="rating-options mb-3">
                        <div class="row g-2">
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

                            @foreach($ratings as $value => $ratingInfo)
                            <div class="col-md">
                                <input type="radio"
                                       class="btn-check"
                                       name="evaluations[{{ $index }}][rating]"
                                       id="rating_{{ $question->id }}_{{ $value }}"
                                       value="{{ $value }}"
                                       {{ $existingRating && $existingRating->rating === $value ? 'checked' : '' }}
                                       required>
                                <label class="btn btn-outline-{{ $ratingInfo['color'] }} w-100"
                                       for="rating_{{ $question->id }}_{{ $value }}">
                                    <i class="fas {{ $ratingInfo['icon'] }} me-1"></i>
                                    <div class="small">{{ $ratingInfo['label'] }}</div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Optional Comment -->
                    <div class="mt-3">
                        <label for="comment_{{ $question->id }}" class="form-label text-muted small">
                            <i class="fas fa-comment-dots me-1"></i>Additional Comments (Optional)
                        </label>
                        <textarea class="form-control"
                                  id="comment_{{ $question->id }}"
                                  name="evaluations[{{ $index }}][comment]"
                                  rows="2"
                                  placeholder="Share your thoughts...">{{ $existingRating ? $existingRating->comment : '' }}</textarea>
                    </div>
                </div>
                @endforeach

                @error('evaluations')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="card-footer-custom bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>
                        {{ $existingEvaluations->count() > 0 ? 'Update Evaluation' : 'Submit Evaluation' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
    @endif
</div>

<style>
.evaluation-question {
    transition: all 0.3s ease;
}

.btn-check:checked + label {
    transform: scale(1.05);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.rating-options label {
    transition: all 0.2s ease;
    cursor: pointer;
}

.rating-options label:hover {
    transform: translateY(-2px);
}

.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
</style>
@endsection
