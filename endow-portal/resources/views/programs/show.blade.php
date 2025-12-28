@extends('layouts.admin')

@section('page-title', $program->name)
@section('breadcrumb', 'Home / Configuration / Programs / View')

@section('content')
<div style="width: 100%; max-width: 100%;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Programs
        </a>
        <div class="btn-group">
            <a href="{{ route('programs.edit', $program) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form action="{{ route('programs.destroy', $program) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this program?');">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Program Details -->
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-graduation-cap text-danger me-2"></i>Program Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h4 class="mb-2 fw-bold text-dark">{{ $program->name }}</h4>
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-secondary">{{ $program->code }}</span>
                                @php
                                    $levelColors = [
                                        'undergraduate' => 'primary',
                                        'postgraduate' => 'success',
                                        'phd' => 'danger',
                                        'diploma' => 'warning',
                                        'certificate' => 'info'
                                    ];
                                    $color = $levelColors[$program->level] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ ucfirst($program->level) }}</span>
                                @if($program->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="text-muted mb-1 small">University</label>
                            <div class="fw-semibold text-dark">
                                <i class="fas fa-university text-danger me-2"></i>
                                <a href="{{ route('universities.show', $program->university) }}" class="text-dark text-decoration-none">
                                    {{ $program->university->name }}
                                </a>
                            </div>
                        </div>

                        @if($program->duration)
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Duration</label>
                            <div class="fw-semibold text-dark">
                                <i class="fas fa-clock text-danger me-2"></i>{{ $program->duration }}
                            </div>
                        </div>
                        @endif

                        @if($program->tuition_fee)
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Tuition Fee</label>
                            <div class="fw-semibold text-dark">
                                <i class="fas fa-dollar-sign text-danger me-2"></i>{{ $program->formatted_tuition_fee }}
                            </div>
                        </div>
                        @endif

                        @if($program->description)
                        <div class="col-12">
                            <label class="text-muted mb-1 small">Description</label>
                            <div class="text-dark">{{ $program->description }}</div>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Created By</label>
                            <div class="fw-semibold text-dark">{{ $program->creator->name ?? 'System' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Created At</label>
                            <div class="fw-semibold text-dark">{{ $program->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-dark mb-3">Statistics</h6>

                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                        <div>
                            <div class="text-muted small">Checklist Items</div>
                            <h4 class="mb-0 fw-bold text-danger">{{ $program->checklistItems->count() }}</h4>
                        </div>
                        <i class="fas fa-clipboard-list fa-2x text-danger opacity-25"></i>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                        <div>
                            <div class="text-muted small">Students</div>
                            <h4 class="mb-0 fw-bold text-primary">{{ $program->students_count }}</h4>
                        </div>
                        <i class="fas fa-users fa-2x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-dark mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('programs.edit', $program) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit Program
                        </a>
                        <a href="{{ route('universities.show', $program->university) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-university me-1"></i> View University
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Checklist Items -->
    <div class="card shadow-sm border-0 mt-3" style="width: 100%;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-clipboard-list text-danger me-2"></i>
                Required Checklist Items ({{ $program->checklistItems->count() }})
            </h5>
        </div>
        @if($program->checklistItems->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem;">
                        <th>Order</th>
                        <th>Document Name</th>
                        <th>Description</th>
                        <th>Required</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($program->checklistItems as $item)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $item->order }}</span></td>
                        <td><strong>{{ $item->title }}</strong></td>
                        <td><small class="text-muted">{{ $item->description ?? 'No description' }}</small></td>
                        <td>
                            @if($item->is_required)
                                <span class="badge bg-danger">Required</span>
                            @else
                                <span class="badge bg-secondary">Optional</span>
                            @endif
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="card-body text-center py-5">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">No checklist items assigned to this program</p>
            <a href="{{ route('programs.edit', $program) }}" class="btn btn-danger mt-3 btn-sm">
                <i class="fas fa-plus me-1"></i> Assign Checklist Items
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
