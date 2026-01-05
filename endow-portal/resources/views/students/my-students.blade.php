@extends('layouts.admin')

@section('page-title', 'My Students')
@section('breadcrumb', 'Home / My Students')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-user-graduate text-danger"></i> My Students</h4>
            <small class="text-muted">View and manage students assigned to you</small>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Total Assigned</div>
                        <div class="stat-value">{{ $students->total() }}</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Pending Approval</div>
                        <div class="stat-value">{{ \App\Models\Student::where('assigned_to', Auth::id())->where('account_status', 'pending')->count() }}</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Approved</div>
                        <div class="stat-value">{{ \App\Models\Student::where('assigned_to', Auth::id())->where('account_status', 'approved')->count() }}</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-label">Processing</div>
                        <div class="stat-value">{{ \App\Models\Student::where('assigned_to', Auth::id())->where('status', 'processing')->count() }}</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-spinner"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('students.my-students') }}" class="row g-2 align-items-end">
                <div class="col-md-4 col-12">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, email, phone..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2 col-6">
                    <select name="university_id" class="form-select form-select-sm">
                        <option value="">All Universities</option>
                        @foreach($universities ?? [] as $university)
                        <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                            {{ Str::limit($university->name, 30) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-6">
                    <select name="program_id" class="form-select form-select-sm">
                        <option value="">All Programs</option>
                        @foreach($programs ?? [] as $program)
                        <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                            {{ Str::limit($program->name, 30) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-6">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Applied</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2 col-6">
                    <select name="account_status" class="form-select form-select-sm">
                        <option value="">Account Status</option>
                        <option value="pending" {{ request('account_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('account_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('account_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2 col-12 d-flex gap-1">
                    <button type="submit" class="btn btn-danger btn-sm flex-grow-1">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('students.my-students') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Button -->
    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('students.my-students', array_merge(request()->except('page'), ['export' => 'csv'])) }}" 
           class="btn btn-success btn-sm">
            <i class="fas fa-file-csv me-1"></i> Export to CSV
        </a>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: #1e293b; position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="padding: 12px; font-size: 13px; font-weight: 600; color: #ffffff;">#</th>
                        <th style="padding: 12px; font-size: 13px; font-weight: 600; color: #ffffff;">Student</th>
                        <th style="padding: 12px; font-size: 13px; font-weight: 600; color: #ffffff;">Contact</th>
                        <th style="padding: 12px; font-size: 13px; font-weight: 600; color: #ffffff;">Program</th>
                        <th style="padding: 12px; font-size: 13px; font-weight: 600; color: #ffffff;">Status</th>
                        <th style="padding: 12px; font-size: 13px; font-weight: 600; color: #ffffff;">Account</th>
                        <th style="padding: 12px; font-size: 13px; font-weight: 600; color: #ffffff;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td style="padding: 12px; font-size: 14px; vertical-align: middle;">
                            {{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}
                        </td>
                        <td style="padding: 12px; vertical-align: middle;">
                            <div class="d-flex align-items-center gap-2">
                                @if($student->activeProfilePhoto)
                                    <img src="{{ $student->activeProfilePhoto->photo_url }}" 
                                         alt="{{ $student->name }}" 
                                         style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0;">
                                @else
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #DC143C 0%, #B8102C 100%); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; color: white;">
                                        {{ strtoupper(substr($student->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight: 600; font-size: 14px; color: #1e293b;">{{ $student->name }}</div>
                                    <div style="font-size: 12px; color: #64748b;">{{ $student->country }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 12px; vertical-align: middle;">
                            <div style="font-size: 13px; color: #475569;">
                                <div><i class="fas fa-envelope" style="width: 14px; color: #94a3b8;"></i> {{ $student->email }}</div>
                                <div><i class="fas fa-phone" style="width: 14px; color: #94a3b8;"></i> {{ $student->phone }}</div>
                            </div>
                        </td>
                        <td style="padding: 12px; vertical-align: middle;">
                            @if($student->targetUniversity)
                                <div style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ Str::limit($student->targetUniversity->name, 25) }}</div>
                                @if($student->targetProgram)
                                    <div style="font-size: 12px; color: #64748b;">{{ Str::limit($student->targetProgram->name, 30) }}</div>
                                @endif
                            @else
                                <span style="font-size: 12px; color: #94a3b8;">Not assigned</span>
                            @endif
                        </td>
                        <td style="padding: 12px; vertical-align: middle;">
                            @php
                                $statusColors = [
                                    'new' => 'info',
                                    'contacted' => 'primary',
                                    'processing' => 'warning',
                                    'applied' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $color = $statusColors[$student->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}" style="font-size: 11px; padding: 4px 10px;">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                        <td style="padding: 12px; vertical-align: middle;">
                            @if($student->account_status == 'approved')
                                <span class="badge bg-success" style="font-size: 11px; padding: 4px 10px;">
                                    <i class="fas fa-check-circle"></i> Approved
                                </span>
                            @elseif($student->account_status == 'pending')
                                <span class="badge bg-warning" style="font-size: 11px; padding: 4px 10px;">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @else
                                <span class="badge bg-danger" style="font-size: 11px; padding: 4px 10px;">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px; vertical-align: middle;">
                            <div class="btn-group btn-group-sm">
                                @can('view', $student)
                                <a href="{{ route('students.show', $student) }}" 
                                   class="btn btn-outline-primary btn-sm"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('update', $student)
                                <a href="{{ route('students.edit', $student) }}" 
                                   class="btn btn-outline-warning btn-sm"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @if($student->account_status == 'pending')
                                    @can('approve', $student)
                                    <a href="{{ route('students.approve.form', $student) }}" 
                                       class="btn btn-outline-success btn-sm"
                                       title="Approve">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 60px 20px; text-align: center;">
                            <div style="opacity: 0.5;">
                                <i class="fas fa-user-graduate" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                                <p style="font-size: 15px; color: #64748b; margin-bottom: 8px;">No students assigned to you yet</p>
                                <small style="font-size: 13px; color: #94a3b8;">Students will appear here when they are assigned to you by an administrator.</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">
                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                </span>
                <form method="GET" action="{{ route('students.my-students') }}" class="d-flex align-items-center gap-2">
                    @foreach(request()->except(['page', 'per_page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label class="text-muted small mb-0">Per page:</label>
                    <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection
