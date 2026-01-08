@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('breadcrumb', 'Home / Dashboard')

@section('content')
    <style>
        .btn-primary-custom:hover {
            color: black !important;
        }
    </style>
    <!-- Welcome Banner -->
    <div class="card-custom mb-4" style="background: linear-gradient(135deg, #B91C1C 0%, #DC143C 100%); border: none; overflow: hidden; position: relative;">
        <div style="position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%;"></div>
        <div class="card-body-custom" style="position: relative; z-index: 1;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2" style="color: white; font-weight: 800; font-size: 1.875rem;">
                        Welcome back, {{ Auth::user()->name }}! 👋
                    </h2>
                    <p class="mb-0" style="color: rgba(255,255,255,0.9); font-size: 1.0625rem;">
                        Here's what's happening with your students today.
                    </p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="fas fa-chart-line" style="font-size: 5rem; color: rgba(255,255,255,0.2);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label mb-2">Total Students</div>
                        <div class="stat-value">{{ $totalStudents ?? 0 }}</div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-custom badge-success-custom" style="font-size: 0.6875rem;">
                                <i class="fas fa-arrow-up"></i> 12.5%
                            </span>
                            <span style="font-size: 0.8125rem; color: var(--text-muted);">vs last month</span>
                        </div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label mb-2">Pending Approvals</div>
                        <div class="stat-value">{{ $pendingApprovals ?? 0 }}</div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-custom badge-warning-custom" style="font-size: 0.6875rem;">
                                <i class="fas fa-clock"></i> Needs attention
                            </span>
                        </div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label mb-2">New Applications</div>
                        <div class="stat-value">{{ $statusCounts['new'] ?? 0 }}</div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-custom badge-success-custom" style="font-size: 0.6875rem;">
                                <i class="fas fa-arrow-up"></i> 8.2%
                            </span>
                            <span style="font-size: 0.8125rem; color: var(--text-muted);">this week</span>
                        </div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label mb-2">In Processing</div>
                        <div class="stat-value">{{ $statusCounts['processing'] ?? 0 }}</div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-custom badge-info-custom" style="font-size: 0.6875rem;">
                                <i class="fas fa-spinner"></i> Active
                            </span>
                        </div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Students & Pending Approvals -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Recent Students</h5>
                        <p class="text-muted mb-0" style="font-size: 0.875rem;">Latest student applications and updates</p>
                    </div>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-sm">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead style="background: #1a1a1a;">
                            <tr>
                                <th style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">#</th>
                                <th style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Student</th>
                                <th style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Contact</th>
                                <th style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Country</th>
                                <th style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Status</th>
                                <th style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Account</th>
                                <th style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Progress</th>
                                <th style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents ?? [] as $index => $student)
                            <tr style="transition: all 0.2s ease;" onmouseover="this.style.background='rgba(220, 20, 60, 0.03)'" onmouseout="this.style.background='transparent'">
                                <td style="font-weight: 700; color: #64748B; text-align: center;">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($student->activeProfilePhoto)
                                            <img src="{{ $student->activeProfilePhoto->photo_url }}"
                                                 alt="{{ $student->name }}"
                                                 style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover; border: 2px solid #e9ecef;">
                                        @else
                                            <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.875rem;">
                                                {{ strtoupper(substr($student->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong style="display: block; color: var(--text-primary);">{{ $student->name }}</strong>
                                            <small class="text-muted">{{ $student->course }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div style="font-size: 0.875rem;">{{ $student->email }}</div>
                                        <small class="text-muted">{{ $student->phone }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                        <i class="fas fa-globe text-muted" style="font-size: 0.75rem;"></i>
                                        {{ $student->country }}
                                    </span>
                                </td>
                                <td>
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
                                </td>
                                <td>
                                    @php
                                        $accountColors = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        $accountColor = $accountColors[$student->account_status] ?? 'secondary';
                                    @endphp
                                    <span class="badge-custom badge-{{ $accountColor }}-custom">
                                        {{ ucfirst($student->account_status) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $approved = $student->checklist_progress['approved'] ?? 0;
                                        $total = $student->checklist_progress['total'] ?? 0;
                                        $progress = $total > 0 ? (int)(($approved / $total) * 100) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress" style="width: 80px; height: 8px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $progress }}%;"
                                                 aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted" style="font-weight: 600;">{{ $approved }}/{{ $total }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('students.show', $student) }}" class="action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $student)
                                        <a href="{{ route('students.edit', $student) }}" class="action-btn edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                                    <p class="text-muted mb-0">No students found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Pending Approvals -->
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-1">Pending Approvals</h5>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">Students awaiting review</p>
                </div>
                <div class="card-body-custom">
                    @forelse($pendingStudents ?? [] as $student)
                    <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: var(--border-color) !important;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem; flex-shrink: 0;">
                            {{ strtoupper(substr($student->name, 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <strong class="d-block mb-1" style="color: var(--text-primary);">{{ $student->name }}</strong>
                            <small class="text-muted d-block mb-2">{{ $student->course }}</small>
                            <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.8125rem;">
                                <i class="fas fa-clock" style="font-size: 0.75rem;"></i>
                                {{ $student->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <a href="{{ route('students.show', $student) }}" class="btn btn-primary-custom btn-sm" style="padding: 0.5rem 0.875rem; font-size: 0.8125rem; height: fit-content;">
                            Review
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div style="width: 64px; height: 64px; border-radius: 16px; background: var(--success-light); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="fas fa-check-circle fa-2x" style="color: var(--success);"></i>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 0.9375rem;">All caught up! No pending approvals.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-1">Quick Actions</h5>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">Common tasks</p>
                </div>
                <div class="card-body-custom">
                    @can('create students')
                    <a href="{{ route('students.create') }}" class="btn btn-primary-custom w-100 mb-3">
                        <i class="fas fa-plus me-2"></i> Add New Student
                    </a>
                    @endcan
                    <a href="{{ route('students.index') }}" class="btn btn-outline-primary w-100 mb-3">
                        <i class="fas fa-users me-2"></i> View All Students
                    </a>
                    @can('create checklists')
                    <a href="{{ route('checklist-items.index') }}" class="btn btn-outline-secondary w-100 mb-3">
                        <i class="fas fa-tasks me-2"></i> Manage Checklists
                    </a>
                    @endcan
                    <a href="{{ route('students.index', ['account_status' => 'pending']) }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-clock me-2"></i> Pending Approvals
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
