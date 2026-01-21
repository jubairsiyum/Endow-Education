@extends('layouts.admin')

@section('page-title', 'Department Management')
@section('breadcrumb', 'Home / Office / Departments')

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header with Gradient -->
    <div class="modern-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="display-6 fw-bold mb-2">
                    <i class="fas fa-building text-white me-3"></i>
                    Department Management
                </h1>
                <p class="text-white-50 mb-0">Organize teams and manage organizational structure</p>
            </div>
            <a href="{{ route('office.departments.create') }}" class="btn btn-light btn-lg shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Create Department
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card-modern bg-gradient-primary">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $departments->count() }}</div>
                    <div class="stat-label">Total Departments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-modern bg-gradient-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $departments->where('is_active', true)->count() }}</div>
                    <div class="stat-label">Active Departments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-modern bg-gradient-info">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $departments->sum('users_count') }}</div>
                    <div class="stat-label">Total Team Members</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-modern bg-gradient-warning">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $departments->whereNotNull('manager_id')->count() }}</div>
                    <div class="stat-label">Assigned Managers</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Grid with Modern Design -->
    <div class="row g-4">
        @forelse($departments as $department)
        <div class="col-xl-4 col-lg-6">
            <div class="department-card">
                <!-- Department Header with Color -->
                <div class="department-header" style="background: linear-gradient(135deg, {{ $department->color }} 0%, {{ $department->color }}dd 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="department-icon-wrapper">
                                <i class="{{ $department->icon }}"></i>
                            </div>
                            <h4 class="department-title">{{ $department->name }}</h4>
                            <span class="department-code">{{ $department->code }}</span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('office.departments.show', $department) }}">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('office.departments.edit', $department) }}">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('office.departments.destroy', $department) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure? This will unassign all users from this department.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Department Body -->
                <div class="department-body">
                    <p class="department-description">{{ Str::limit($department->description, 100) }}</p>

                    <!-- Stats Row -->
                    <div class="department-stats">
                        <div class="stat-item">
                            <div class="stat-icon-sm" style="background: {{ $department->color }}20; color: {{ $department->color }};">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="stat-value-sm">{{ $department->users_count }}</div>
                                <div class="stat-label-sm">Members</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon-sm" style="background: {{ $department->color }}20; color: {{ $department->color }};">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <div class="stat-value-sm">{{ $department->manager ? '1' : '0' }}</div>
                                <div class="stat-label-sm">Manager</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon-sm" style="background: {{ $department->color }}20; color: {{ $department->color }};">
                                <i class="fas fa-{{ $department->is_active ? 'check-circle' : 'times-circle' }}"></i>
                            </div>
                            <div>
                                <div class="stat-value-sm">{{ $department->is_active ? 'Active' : 'Inactive' }}</div>
                                <div class="stat-label-sm">Status</div>
                            </div>
                        </div>
                    </div>

                    @if($department->manager)
                    <div class="manager-info">
                        <div class="manager-avatar" style="background: {{ $department->color }};">
                            {{ strtoupper(substr($department->manager->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="manager-label">Department Manager</div>
                            <div class="manager-name">{{ $department->manager->name }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Department Footer -->
                <div class="department-footer">
                    <a href="{{ route('office.departments.show', $department) }}" class="btn btn-sm w-100" style="background: {{ $department->color }}15; color: {{ $department->color }}; border: 1px solid {{ $department->color }}30;">
                        <i class="fas fa-cog me-2"></i>Manage Department
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-building"></i>
                <h3>No Departments Yet</h3>
                <p>Create your first department to start organizing your team</p>
                <a href="{{ route('office.departments.create') }}" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-plus-circle me-2"></i>Create First Department
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    .modern-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 20px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .stat-card-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1.5rem;
        border-radius: 15px;
        color: white;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
    .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; }
    .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
    .bg-gradient-warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important; }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .stat-content .stat-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }

    .stat-content .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    .department-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .department-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .department-header {
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .department-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-30px, -30px); }
    }

    .department-icon-wrapper {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
        backdrop-filter: blur(10px);
    }

    .department-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .department-code {
        background: rgba(255, 255, 255, 0.25);
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .department-body {
        padding: 1.5rem;
        flex-grow: 1;
    }

    .department-description {
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .department-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .stat-icon-sm {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
    }

    .stat-value-sm {
        font-size: 1.125rem;
        font-weight: 700;
        color: #212529;
        line-height: 1;
    }

    .stat-label-sm {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.125rem;
    }

    .manager-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .manager-avatar {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .manager-label {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .manager-name {
        font-weight: 600;
        color: #212529;
    }

    .department-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 5rem;
        color: #e9ecef;
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        color: #212529;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 0;
    }
</style>
@endsection
