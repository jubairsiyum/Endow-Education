@extends('layouts.admin')

@section('page-title', 'Universities')
@section('breadcrumb', 'Home / Configuration / Universities')

@section('content')
<div style="width: 100%; max-width: 100%;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-university text-danger"></i> Universities Management</h4>
            <small class="text-muted">Manage partner universities and institutions</small>
        </div>
        <a href="{{ route('universities.create') }}" class="btn btn-danger">
            <i class="fas fa-plus me-1"></i> Add University
        </a>
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

    <!-- Universities Table -->
    <div class="card shadow-sm border-0" style="width: 100%;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem;">
                        <th style="width: 60px;">Order</th>
                        <th style="width: 25%;">University Name</th>
                        <th style="width: 10%;">Code</th>
                        <th style="width: 15%;">Country</th>
                        <th style="width: 10%;">Programs</th>
                        <th style="width: 10%;">Students</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 20%;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($universities as $university)
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $university->order }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($university->logo)
                                    <img src="{{ asset('storage/' . $university->logo) }}" alt="{{ $university->name }}"
                                         class="rounded" style="width: 32px; height: 32px; object-fit: cover;">
                                @else
                                    <div class="bg-danger bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-university text-danger"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong class="text-dark">{{ $university->name }}</strong>
                                    @if($university->city)
                                        <br><small class="text-muted">{{ $university->city }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="small">{{ $university->code }}</code>
                        </td>
                        <td>
                            <i class="fas fa-flag text-danger me-1"></i>{{ $university->country }}
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $university->programs_count }}</span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $university->students_count }}</span>
                        </td>
                        <td>
                            @if($university->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('universities.show', $university) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('universities.edit', $university) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('universities.destroy', $university) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete"
                                            onclick="return confirm('Are you sure? This will affect {{ $university->students_count }} students.');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="my-4">
                                <i class="fas fa-university fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No universities found</p>
                                <a href="{{ route('universities.create') }}" class="btn btn-danger mt-3">
                                    <i class="fas fa-plus me-1"></i> Add Your First University
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
    @if($universities->hasPages())
    <div class="mt-3">
        {{ $universities->links() }}
    </div>
    @endif
</div>
@endsection
