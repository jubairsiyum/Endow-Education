@extends('layouts.admin')

@section('page-title', 'Department Management')
@section('breadcrumb', 'Home / Office / Departments')

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="modern-header mb-4" style="background: linear-gradient(135deg, #DC143C 0%, #A52A2A 100%); border-radius: 1rem; padding: 2rem; color: #FFFFFF; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="display-6 fw-bold mb-2">
                    <i class="fas fa-building me-3"></i>
                    Department Management
                </h1>
                <p class="mb-0 opacity-75">Organize teams and manage organizational structure</p>
            </div>
            <a href="{{ route('office.departments.create') }}" class="btn shadow-sm" style="background-color: #FFFFFF; color: #000000; border: 1px solid #E0E0E0;">
                <i class="fas fa-plus-circle me-2" style="color: #DC143C;"></i>Create Department
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="background-color: #FFFFFF; color: #000000; border: 1px solid #E0E0E0;">
        <i class="fas fa-check-circle me-2" style="color: #DC143C;"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="background-color: #FFFFFF; color: #000000; border: 1px solid #E0E0E0;">
        <i class="fas fa-exclamation-circle me-2" style="color: #DC143C;"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card-modern shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(220, 20, 60, 0.1); width: 50px; height: 50px; font-size: 1.5rem; color: #DC143C;">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold; color: #000000;">{{ $departments->count() }}</div>
                    <div class="stat-label text-uppercase fw-semibold" style="font-size: 0.875rem; color: #6C757D;">Total Departments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-modern shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(220, 20, 60, 0.1); width: 50px; height: 50px; font-size: 1.5rem; color: #DC143C;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold; color: #000000;">{{ $departments->where('is_active', true)->count() }}</div>
                    <div class="stat-label text-uppercase fw-semibold" style="font-size: 0.875rem; color: #6C757D;">Active Departments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-modern shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(220, 20, 60, 0.1); width: 50px; height: 50px; font-size: 1.5rem; color: #DC143C;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold; color: #000000;">{{ $departments->sum('users_count') }}</div>
                    <div class="stat-label text-uppercase fw-semibold" style="font-size: 0.875rem; color: #6C757D;">Total Team Members</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-modern shadow-sm rounded" style="background-color: #FFFFFF; border: 1px solid #E0E0E0; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(220, 20, 60, 0.1); width: 50px; height: 50px; font-size: 1.5rem; color: #DC143C;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold; color: #000000;">{{ $departments->whereNotNull('manager_id')->count() }}</div>
                    <div class="stat-label text-uppercase fw-semibold" style="font-size: 0.875rem; color: #6C757D;">Assigned Managers</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="row g-4">
        @forelse($departments as $department)
        <div class="col-xl-4 col-lg-6">
            <div class="department-card shadow-sm rounded overflow-hidden" style="background-color: #FFFFFF; transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                <!-- Department Header -->
                <div class="department-header p-4" style="background: linear-gradient(135deg, #DC143C 0%, #A52A2A 100%); color: #FFFFFF;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="department-icon-wrapper rounded d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(255, 255, 255, 0.2); font-size: 1.75rem;">
                                <i class="{{ $department->icon }}"></i>
                            </div>
                            <h4 class="department-title fw-bold mb-1" style="font-size: 1.5rem;">{{ $department->name }}</h4>
                            <span class="department-code rounded-pill px-3 py-1" style="background-color: rgba(255, 255, 255, 0.25); font-size: 0.75rem; font-weight: 600;">{{ $department->code }}</span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" style="background-color: transparent; border: none; color: #FFFFFF;">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu shadow-sm rounded border-0" style="background-color: #FFFFFF;">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('office.departments.show', $department) }}" style="color: #000000;">
                                        <i class="fas fa-eye me-2" style="color: #DC143C;"></i>View Details
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('office.departments.edit', $department) }}" style="color: #000000;">
                                        <i class="fas fa-edit me-2" style="color: #DC143C;"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider m-0"></li>
                                <li>
                                    <form action="{{ route('office.departments.destroy', $department) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure? This will unassign all users from this department.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Department Body -->
                <div class="department-body p-4 flex-grow-1">
                    <p class="department-description mb-4" style="color: #6C757D; font-size: 0.9rem; line-height: 1.6;">{{ Str::limit($department->description, 100) }}</p>

                    <!-- Stats Row -->
                    <div class="department-stats d-grid mb-4" style="grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div class="stat-item d-flex align-items-center gap-3">
                            <div class="stat-icon-sm rounded d-flex align-items-center justify-content-center" style="background-color: rgba(220, 20, 60, 0.1); color: #DC143C; width: 40px; height: 40px; font-size: 1.125rem;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="stat-value-sm fw-bold" style="font-size: 1.125rem; color: #000000;">{{ $department->users_count }}</div>
                                <div class="stat-label-sm text-uppercase" style="font-size: 0.75rem; color: #6C757D;">Members</div>
                            </div>
                        </div>
                        <div class="stat-item d-flex align-items-center gap-3">
                            <div class="stat-icon-sm rounded d-flex align-items-center justify-content-center" style="background-color: rgba(220, 20, 60, 0.1); color: #DC143C; width: 40px; height: 40px; font-size: 1.125rem;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <div class="stat-value-sm fw-bold" style="font-size: 1.125rem; color: #000000;">{{ $department->manager ? '1' : '0' }}</div>
                                <div class="stat-label-sm text-uppercase" style="font-size: 0.75rem; color: #6C757D;">Manager</div>
                            </div>
                        </div>
                        <div class="stat-item d-flex align-items-center gap-3">
                            <div class="stat-icon-sm rounded d-flex align-items-center justify-content-center" style="background-color: rgba(220, 20, 60, 0.1); color: #DC143C; width: 40px; height: 40px; font-size: 1.125rem;">
                                <i class="fas fa-{{ $department->is_active ? 'check-circle' : 'times-circle' }}"></i>
                            </div>
                            <div>
                                <div class="stat-value-sm fw-bold" style="font-size: 1.125rem; color: #000000;">{{ $department->is_active ? 'Active' : 'Inactive' }}</div>
                                <div class="stat-label-sm text-uppercase" style="font-size: 0.75rem; color: #6C757D;">Status</div>
                            </div>
                        </div>
                    </div>

                    @if($department->manager)
                    <div class="manager-info d-flex align-items-center gap-3 p-3 rounded" style="background-color: #F8F9FA; border: 1px solid #E0E0E0;">
                        <div class="manager-avatar rounded d-flex align-items-center justify-content-center fw-bold" style="background-color: #DC143C; color: #FFFFFF; width: 45px; height: 45px; font-size: 0.875rem;">
                            {{ strtoupper(substr($department->manager->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="manager-label text-uppercase fw-semibold" style="font-size: 0.75rem; color: #6C757D;">Department Manager</div>
                            <div class="manager-name fw-semibold" style="color: #000000;">{{ $department->manager->name }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Department Footer -->
                <div class="department-footer p-3 border-top" style="background-color: #F8F9FA; border-color: #E0E0E0;">
                    <a href="{{ route('office.departments.show', $department) }}" class="btn btn-sm w-100" style="background-color: rgba(220, 20, 60, 0.1); color: #DC143C; border: 1px solid rgba(220, 20, 60, 0.3);">
                        <i class="fas fa-cog me-2"></i>Manage Department
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state text-center p-5 rounded shadow-sm" style="background-color: #FFFFFF; border: 1px solid #E0E0E0;">
                <i class="fas fa-building" style="font-size: 5rem; color: #E0E0E0; margin-bottom: 1.5rem;"></i>
                <h3 style="color: #000000; margin-bottom: 0.5rem;">No Departments Yet</h3>
                <p style="color: #6C757D; margin-bottom: 0;">Create your first department to start organizing your team</p>
                <a href="{{ route('office.departments.create') }}" class="btn mt-3" style="background-color: #DC143C; color: #FFFFFF; border: none;">
                    <i class="fas fa-plus-circle me-2"></i>Create First Department
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    .stat-card-modern:hover,
    .department-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #F8F9FA;
        color: #DC143C !important;
    }

    .department-footer .btn:hover {
        background-color: #DC143C !important;
        color: #FFFFFF !important;
        border-color: #DC143C !important;
    }

    .modern-header a.btn:hover {
        background-color: #F8F9FA !important;
        color: #DC143C !important;
        border-color: #DC143C !important;
    }

    .empty-state .btn:hover {
        background-color: #A52A2A !important;
    }
</style>
@endsection
