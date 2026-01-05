@extends('layouts.admin')

@section('page-title', 'Programs')
@section('breadcrumb', 'Home / Configuration / Programs')

@section('content')
<div style="width: 100%; max-width: 100%;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-graduation-cap text-danger"></i> Programs Management</h4>
            <small class="text-muted">Manage academic programs and associated checklists</small>
        </div>
        @can('create users')
        <a href="{{ route('programs.create') }}" class="btn btn-danger">
            <i class="fas fa-plus me-1"></i> Add Program
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('programs.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Filter by University</label>
                    <select name="university_id" class="form-select form-select-sm">
                        <option value="">All Universities</option>
                        @foreach($universities as $uni)
                            <option value="{{ $uni->id }}" {{ request('university_id') == $uni->id ? 'selected' : '' }}>
                                {{ $uni->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Filter by Level</label>
                    <select name="level" class="form-select form-select-sm">
                        <option value="">All Levels</option>
                        <option value="undergraduate" {{ request('level') == 'undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                        <option value="postgraduate" {{ request('level') == 'postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                        <option value="phd" {{ request('level') == 'phd' ? 'selected' : '' }}>PhD</option>
                        <option value="diploma" {{ request('level') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                        <option value="certificate" {{ request('level') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Programs Table -->
    <div class="card shadow-sm border-0" style="width: 100%;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem;">
                        <th style="width: 25%;">Program Name</th>
                        <th style="width: 20%;">University</th>
                        <th style="width: 10%;">Code</th>
                        <th style="width: 12%;">Level</th>
                        <th style="width: 8%;">Checklists</th>
                        <th style="width: 8%;">Students</th>
                        <th style="width: 8%;">Status</th>
                        <th style="width: 9%;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $program)
                    <tr>
                        <td>
                            <strong class="text-dark">{{ $program->name }}</strong>
                            @if($program->duration)
                                <br><small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $program->duration }}</small>
                            @endif
                        </td>
                        <td>
                            <i class="fas fa-university text-danger me-1"></i>{{ $program->university->name }}
                        </td>
                        <td>
                            <code class="small">{{ $program->code }}</code>
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <span class="badge bg-danger">{{ $program->checklist_items_count }}</span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $program->students_count }}</span>
                        </td>
                        <td>
                            @if($program->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('programs.show', $program) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit users')
                                <a href="{{ route('programs.edit', $program) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete users')
                                <form action="{{ route('programs.destroy', $program) }}" method="POST" class="d-inline" id="delete-program-form-{{ $program->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                            onclick="confirmDeleteProgramIndex({{ $program->id }}, {{ $program->students_count }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="my-4">
                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No programs found</p>
                                <a href="{{ route('programs.create') }}" class="btn btn-danger mt-3">
                                    <i class="fas fa-plus me-1"></i> Add Your First Program
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($programs->hasPages())
    <div class="mt-3">
        {{ $programs->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    function confirmDeleteProgramIndex(programId, studentCount) {
        Swal.fire({
            title: 'Delete Program?',
            text: `Are you sure? This will affect ${studentCount} students.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC143C',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-program-form-' + programId).submit();
            }
        });
    }
</script>
@endpush
@endsection
