@extends('layouts.admin')

@section('page-title', 'Activity Log Details')
@section('breadcrumb', 'Home / Activity Logs / Details')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title"><i class="fas fa-file-alt me-2"></i>Activity Log Details</h1>
                <p class="page-subtitle">Detailed information about this activity</p>
            </div>
            <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Logs
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5><i class="fas fa-info-circle me-2"></i>Activity Information</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Activity Type</label>
                            @php
                                $typeColors = [
                                    'student' => 'primary',
                                    'document' => 'info',
                                    'authentication' => 'success',
                                    'checklist' => 'warning'
                                ];
                                $color = $typeColors[$activityLog->log_name] ?? 'secondary';
                            @endphp
                            <div>
                                <span class="badge-custom badge-{{ $color }}-custom">
                                    {{ ucfirst($activityLog->log_name) }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Date & Time</label>
                            <div><strong>{{ $activityLog->created_at->format('F d, Y g:i A') }}</strong></div>
                            <small class="text-muted">{{ $activityLog->created_at->diffForHumans() }}</small>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small mb-1">Description</label>
                            <div class="alert alert-light border mb-0">
                                <i class="fas fa-comment-dots me-2 text-primary"></i>
                                <strong>{{ $activityLog->description }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($activityLog->properties && count($activityLog->properties))
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5><i class="fas fa-database me-2"></i>Properties/Data</h5>
                </div>
                <div class="card-body-custom">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Property</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activityLog->properties as $key => $value)
                                <tr>
                                    <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                                    <td>
                                        @if(is_array($value))
                                        <pre class="mb-0 bg-light p-2 rounded"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                        @elseif(is_bool($value))
                                        <span class="badge bg-{{ $value ? 'success' : 'danger' }}">
                                            {{ $value ? 'Yes' : 'No' }}
                                        </span>
                                        @else
                                        {{ $value }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($activityLog->subject)
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5><i class="fas fa-cube me-2"></i>Subject Details</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Subject Type</label>
                            <div><code>{{ class_basename($activityLog->subject_type) }}</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Subject ID</label>
                            <div><strong>{{ $activityLog->subject_id }}</strong></div>
                        </div>

                        @if($activityLog->subject_type == 'App\\Models\\Student' && $activityLog->subject)
                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="text-muted small mb-1">Student Information</label>
                                    <div><strong>{{ $activityLog->subject->name }}</strong></div>
                                    <small class="text-muted">{{ $activityLog->subject->email }}</small>
                                </div>
                                <a href="{{ route('students.show', $activityLog->subject) }}" class="btn btn-sm btn-primary-custom">
                                    <i class="fas fa-eye me-1"></i> View Student
                                </a>
                            </div>
                        </div>
                        @elseif($activityLog->subject_type == 'App\\Models\\StudentDocument' && $activityLog->subject)
                        <div class="col-12">
                            <hr class="my-2">
                            <label class="text-muted small mb-1">Document Information</label>
                            <div><strong>{{ $activityLog->subject->original_name }}</strong></div>
                            <small class="text-muted">Filename: {{ $activityLog->subject->filename }}</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5><i class="fas fa-user me-2"></i>Performed By</h5>
                </div>
                <div class="card-body-custom">
                    @if($activityLog->causer)
                    <div class="text-center mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h5 class="mb-1">{{ $activityLog->causer->name }}</h5>
                        <p class="text-muted mb-2">{{ $activityLog->causer->email }}</p>
                        <span class="badge-custom badge-primary-custom">
                            {{ class_basename($activityLog->causer_type) }}
                        </span>
                    </div>
                    @else
                    <div class="text-center text-muted">
                        <i class="fas fa-robot fa-3x mb-3"></i>
                        <p class="mb-0">System Generated</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-custom">
                <div class="card-header-custom">
                    <h5><i class="fas fa-network-wired me-2"></i>Connection Details</h5>
                </div>
                <div class="card-body-custom">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">IP Address</label>
                        <div>
                            @if($activityLog->ip_address)
                            <code class="fs-6">{{ $activityLog->ip_address }}</code>
                            @else
                            <span class="text-muted">Not recorded</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-muted small mb-1">User Agent</label>
                        <div>
                            @if($activityLog->user_agent)
                            <small class="text-muted d-block" style="word-break: break-all;">
                                {{ $activityLog->user_agent }}
                            </small>
                            <div class="mt-2">
                                @if(str_contains($activityLog->user_agent, 'Chrome'))
                                <i class="fab fa-chrome text-warning me-1"></i>Chrome
                                @elseif(str_contains($activityLog->user_agent, 'Firefox'))
                                <i class="fab fa-firefox text-danger me-1"></i>Firefox
                                @elseif(str_contains($activityLog->user_agent, 'Safari'))
                                <i class="fab fa-safari text-info me-1"></i>Safari
                                @elseif(str_contains($activityLog->user_agent, 'Edge'))
                                <i class="fab fa-edge text-primary me-1"></i>Edge
                                @endif

                                @if(str_contains($activityLog->user_agent, 'Mobile') || str_contains($activityLog->user_agent, 'Android') || str_contains($activityLog->user_agent, 'iPhone'))
                                <i class="fas fa-mobile-alt text-success ms-2 me-1"></i>Mobile
                                @else
                                <i class="fas fa-desktop text-secondary ms-2 me-1"></i>Desktop
                                @endif
                            </div>
                            @else
                            <span class="text-muted">Not recorded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
