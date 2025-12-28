@extends('layouts.admin')

@section('page-title', $university->name)
@section('breadcrumb', 'Home / Configuration / Universities / View')

@section('content')
<div style="width: 100%; max-width: 100%;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('universities.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Universities
        </a>
        <div class="btn-group">
            <a href="{{ route('universities.edit', $university) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form action="{{ route('universities.destroy', $university) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this university?');">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <!-- University Details -->
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-university text-danger me-2"></i>University Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h4 class="mb-2 fw-bold text-dark">{{ $university->name }}</h4>
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-secondary">{{ $university->code }}</span>
                                @if($university->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Country</label>
                            <div class="fw-semibold text-dark">
                                <i class="fas fa-flag text-danger me-2"></i>{{ $university->country }}
                            </div>
                        </div>

                        @if($university->city)
                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">City</label>
                            <div class="fw-semibold text-dark">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $university->city }}
                            </div>
                        </div>
                        @endif

                        @if($university->website)
                        <div class="col-12">
                            <label class="text-muted mb-1 small">Website</label>
                            <div class="fw-semibold">
                                <a href="{{ $university->website }}" target="_blank" class="text-danger">
                                    <i class="fas fa-external-link-alt me-2"></i>{{ $university->website }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($university->description)
                        <div class="col-12">
                            <label class="text-muted mb-1 small">Description</label>
                            <div class="text-dark">{{ $university->description }}</div>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Created By</label>
                            <div class="fw-semibold text-dark">{{ $university->creator->name ?? 'System' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted mb-1 small">Created At</label>
                            <div class="fw-semibold text-dark">{{ $university->created_at->format('M d, Y') }}</div>
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
                            <div class="text-muted small">Programs</div>
                            <h4 class="mb-0 fw-bold text-danger">{{ $university->programs->count() }}</h4>
                        </div>
                        <i class="fas fa-graduation-cap fa-2x text-danger opacity-25"></i>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                        <div>
                            <div class="text-muted small">Students</div>
                            <h4 class="mb-0 fw-bold text-primary">{{ $university->students->count() }}</h4>
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
                        <a href="{{ route('programs.create') }}?university_id={{ $university->id }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Program
                        </a>
                        <a href="{{ route('programs.index') }}?university_id={{ $university->id }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i> View Programs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs List -->
    @if($university->programs->isNotEmpty())
    <div class="card shadow-sm border-0 mt-3" style="width: 100%;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-graduation-cap text-danger me-2"></i>Programs ({{ $university->programs->count() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem;">
                        <th>Program Name</th>
                        <th>Code</th>
                        <th>Level</th>
                        <th>Duration</th>
                        <th>Students</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($university->programs as $program)
                    <tr>
                        <td><strong>{{ $program->name }}</strong></td>
                        <td><code class="small">{{ $program->code }}</code></td>
                        <td><span class="badge bg-info">{{ ucfirst($program->level) }}</span></td>
                        <td>{{ $program->duration ?? 'N/A' }}</td>
                        <td><span class="badge bg-primary">{{ $program->students_count }}</span></td>
                        <td>
                            @if($program->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('programs.show', $program) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('programs.edit', $program) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
