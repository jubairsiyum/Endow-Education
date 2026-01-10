@extends('layouts.admin')

@section('page-title', 'Consultant Details')
@section('breadcrumb', 'Home / System / Consultant Evaluations / Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="fas fa-user-tie text-primary me-2"></i>
                {{ $consultant->name }}
            </h2>
            <p class="text-muted mb-0">Detailed evaluation report</p>
        </div>
        <a href="{{ route('admin.consultant-evaluations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to All Evaluations
        </a>
    </div>

    <!-- Consultant Info Card -->
    <div class="card-custom mb-4">
        <div class="card-body-custom">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 80px; height: 80px; font-size: 32px;">
                            {{ strtoupper(substr($consultant->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">{{ $consultant->name }}</h3>
                            <p class="text-muted mb-0">
                                <i class="fas fa-envelope me-2"></i>{{ $consultant->email }}
                            </p>
                            @if($consultant->phone)
                            <p class="text-muted mb-0">
                                <i class="fas fa-phone me-2"></i>{{ $consultant->phone }}
                            </p>
                            @endif
                            <span class="badge bg-{{ $consultant->roles->first()->name === 'Admin' ? 'danger' : 'success' }} mt-2">
                                {{ $consultant->roles->first()->name ?? 'Employee' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="mb-2">
                        <span class="text-muted small">Total Evaluations</span>
                        <h2 class="mb-0 fw-bold text-primary">{{ $evaluations->total() }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Breakdown -->
    <div class="card-custom mb-4">
        <div class="card-header-custom bg-light">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Rating Breakdown</h5>
        </div>
        <div class="card-body-custom">
            <div class="row g-3">
                @php
                    $ratingInfo = [
                        'excellent' => ['label' => 'Excellent', 'color' => 'success', 'icon' => 'fa-star'],
                        'good' => ['label' => 'Good', 'color' => 'info', 'icon' => 'fa-check-circle'],
                        'neutral' => ['label' => 'Neutral', 'color' => 'secondary', 'icon' => 'fa-circle'],
                        'average' => ['label' => 'Average', 'color' => 'warning', 'icon' => 'fa-minus-circle'],
                        'below_average' => ['label' => 'Below Average', 'color' => 'danger', 'icon' => 'fa-times-circle'],
                    ];
                    $totalRatings = array_sum($ratingBreakdown);
                @endphp

                @foreach($ratingInfo as $key => $info)
                <div class="col-md">
                    <div class="text-center p-3 border rounded">
                        <i class="fas {{ $info['icon'] }} fa-2x text-{{ $info['color'] }} mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ $ratingBreakdown[$key] ?? 0 }}</h4>
                        <small class="text-muted">{{ $info['label'] }}</small>
                        @if($totalRatings > 0)
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-{{ $info['color'] }}"
                                 style="width: {{ round((($ratingBreakdown[$key] ?? 0) / $totalRatings) * 100) }}%"></div>
                        </div>
                        <small class="text-muted">{{ round((($ratingBreakdown[$key] ?? 0) / $totalRatings) * 100) }}%</small>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Question Statistics -->
    <div class="card-custom mb-4">
        <div class="card-header-custom bg-light">
            <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Performance by Question</h5>
        </div>
        <div class="card-body-custom">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th class="text-center">Responses</th>
                            <th class="text-center">Average Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questionStats as $stat)
                        <tr>
                            <td>{{ $stat['question'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $stat['count'] }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <strong class="text-{{ $stat['average'] >= 4 ? 'success' : ($stat['average'] >= 3 ? 'warning' : 'danger') }}">
                                        {{ number_format($stat['average'], 1) }} / 5.0
                                    </strong>
                                    <div class="progress" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-{{ $stat['average'] >= 4 ? 'success' : ($stat['average'] >= 3 ? 'warning' : 'danger') }}"
                                             style="width: {{ ($stat['average'] / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No question statistics available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- All Evaluations -->
    <div class="card-custom">
        <div class="card-header-custom bg-light">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Evaluations ({{ $evaluations->total() }})</h5>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Question</th>
                            <th>Rating</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluations as $evaluation)
                        <tr>
                            <td>
                                <small>{{ $evaluation->created_at->format('M d, Y') }}</small><br>
                                <small class="text-muted">{{ $evaluation->created_at->format('g:i A') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-info text-white" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($evaluation->student->name ?? 'N', 0, 2)) }}
                                    </div>
                                    <span class="fw-semibold small">{{ $evaluation->student->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ $evaluation->question->question ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $evaluation->rating_color }}">
                                    {{ $evaluation->rating_label }}
                                </span>
                            </td>
                            <td>
                                @if($evaluation->comment)
                                <div class="small">{{ $evaluation->comment }}</div>
                                @else
                                <span class="text-muted small">No comment</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No evaluations yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($evaluations->hasPages())
        <div class="card-footer-custom bg-light">
            {{ $evaluations->links() }}
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
    flex-shrink: 0;
}
</style>
@endsection
