@extends('layouts.admin')

@section('page-title', 'My Dashboard')
@section('breadcrumb', 'Home / Student Dashboard')

@section('content')
    <!-- Account Status Alert -->
    @if($student->account_status === 'pending')
    <div class="alert alert-warning alert-custom mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-clock fa-2x"></i>
            <div>
                <h5 class="mb-1">Account Pending Approval</h5>
                <p class="mb-0">Your account is currently under review. You'll be notified once it's been approved.</p>
            </div>
        </div>
    </div>
    @elseif($student->account_status === 'rejected')
    <div class="alert alert-danger alert-custom mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-times-circle fa-2x"></i>
            <div>
                <h5 class="mb-1">Account Not Approved</h5>
                <p class="mb-0">Unfortunately, your account was not approved. Please contact support for more information.</p>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-success alert-custom mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-check-circle fa-2x"></i>
            <div>
                <h5 class="mb-1">Account Approved</h5>
                <p class="mb-0">Your account has been approved. You can now track your application progress.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Profile Overview -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5>My Profile</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Full Name</label>
                            <div class="fw-semibold">{{ $student->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Email</label>
                            <div class="fw-semibold">{{ $student->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Phone</label>
                            <div class="fw-semibold">{{ $student->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Country</label>
                            <div class="fw-semibold">{{ $student->country }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Course/Program</label>
                            <div class="fw-semibold">{{ $student->course }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Application Status</label>
                            <div>
                                @php
                                    $statusColors = [
                                        'new' => 'info',
                                        'contacted' => 'secondary',
                                        'processing' => 'warning',
                                        'applied' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    $color = $statusColors[$student->status] ?? 'secondary';
                                @endphp
                                <span class="badge-custom badge-{{ $color }}-custom">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.875rem;">Assigned Counselor</label>
                            <div class="fw-semibold">{{ $student->assignedUser->name ?? 'Not assigned yet' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label mb-2">Checklist Progress</div>
                        <div class="stat-value">{{ $student->checklist_progress['percentage'] ?? 0 }}%</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 12px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ $student->checklist_progress['percentage'] ?? 0 }}%;"
                         aria-valuenow="{{ $student->checklist_progress['percentage'] ?? 0 }}" 
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <div class="mt-3 text-muted" style="font-size: 0.875rem;">
                    {{ $student->checklist_progress['approved'] ?? 0 }} approved · 
                    {{ $student->checklist_progress['pending'] ?? 0 }} pending
                </div>
            </div>
        </div>
    </div>

    <!-- Checklist Items -->
    <div class="card-custom">
        <div class="card-header-custom">
            <h5>My Checklist</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Required</th>
                        <th>Status</th>
                        <th>Submitted Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($student->checklists as $checklist)
                    <tr>
                        <td>
                            <strong>{{ $checklist->checklistItem->name }}</strong>
                            @if($checklist->checklistItem->description)
                            <br><small class="text-muted">{{ $checklist->checklistItem->description }}</small>
                            @endif
                        </td>
                        <td>
                            @if($checklist->checklistItem->is_required)
                                <span class="badge-custom badge-danger-custom">Required</span>
                            @else
                                <span class="badge-custom badge-secondary-custom">Optional</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $checklistColors = [
                                    'pending' => 'secondary',
                                    'submitted' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $checklistColor = $checklistColors[$checklist->status] ?? 'secondary';
                            @endphp
                            <span class="badge-custom badge-{{ $checklistColor }}-custom">
                                {{ ucfirst($checklist->status) }}
                            </span>
                        </td>
                        <td>
                            {{ $checklist->submitted_at ? $checklist->submitted_at->format('M d, Y') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                            <p class="text-muted mb-0">No checklist items assigned yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
