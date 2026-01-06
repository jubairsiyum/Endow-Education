@extends('layouts.admin')

@section('page-title', 'User Details')
@section('breadcrumb', 'Home / Users / Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-2">
                <i class="fas fa-user-circle text-primary me-2"></i>
                {{ $user->name }}
            </h2>
            <p class="text-muted mb-0">User account details and activity</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit User
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-lg-4">
            <div class="card-custom text-center">
                <div class="card-body-custom py-5">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3" 
                         style="width: 120px; height: 120px; font-size: 48px;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    @php
                        $roleColors = [
                            'Super Admin' => 'danger',
                            'Admin' => 'warning',
                            'Employee' => 'success',
                            'Student' => 'info',
                        ];
                        $roleColor = $roleColors[$user->roles->first()->name ?? ''] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $roleColor }} px-3 py-2 fs-6">
                        <i class="fas fa-shield-alt me-1"></i>
                        {{ $user->roles->first()->name ?? 'No Role' }}
                    </span>
                    
                    <div class="mt-3">
                        @if($user->status === 'active')
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i>Active Account
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">
                                <i class="fas fa-ban me-1"></i>Inactive Account
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card-custom mt-3">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                </div>
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <i class="fas fa-users text-primary me-2"></i>
                            <span class="text-muted">Assigned Students</span>
                        </div>
                        <span class="badge bg-primary">{{ $user->assignedStudents->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <i class="fas fa-calendar text-success me-2"></i>
                            <span class="text-muted">Member Since</span>
                        </div>
                        <span class="fw-semibold">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-clock text-info me-2"></i>
                            <span class="text-muted">Last Update</span>
                        </div>
                        <span class="fw-semibold">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Contact Information -->
            <div class="card-custom mb-3">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Email Address</label>
                            <div class="fw-semibold">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                {{ $user->email }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Phone Number</label>
                            <div class="fw-semibold">
                                <i class="fas fa-phone text-success me-2"></i>
                                {{ $user->phone ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Card -->
            <div class="card-custom mb-3">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Permissions</h5>
                </div>
                <div class="card-body-custom">
                    @if($user->getAllPermissions()->count() > 0)
                        <div class="row g-2">
                            @foreach($user->getAllPermissions() as $permission)
                                <div class="col-md-6 col-lg-4">
                                    <span class="badge bg-light text-dark border w-100 text-start py-2">
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        {{ $permission->name }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            No specific permissions assigned
                        </p>
                    @endif
                </div>
            </div>

            <!-- Assigned Students -->
            @if($user->assignedStudents->count() > 0)
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>
                        Assigned Students ({{ $user->assignedStudents->count() }})
                    </h5>
                </div>
                <div class="card-body-custom p-0">
                    <div class="table-responsive">
                        <table class="table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->assignedStudents as $student)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $student->name }}</div>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $student->targetProgram->name ?? 'Not selected' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $student->account_status == 'approved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($student->account_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $approved = $student->checklist_progress['approved'] ?? 0;
                                            $total = $student->checklist_progress['total'] ?? 0;
                                        @endphp
                                        <small class="fw-semibold">{{ $approved }} of {{ $total }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('students.show', $student) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-circle {
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .card-custom {
            margin-bottom: 1rem;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            width: 100%;
        }
        
        .d-flex.gap-2 .btn {
            width: 100%;
        }
    }
</style>
@endpush
@endsection
