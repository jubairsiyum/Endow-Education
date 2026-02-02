@extends('layouts.admin')

@section('page-title', 'Export Daily Reports')
@section('breadcrumb', 'Home / Office / Daily Reports / Export')

@section('content')
<link rel="stylesheet" href="{{ asset('css/daily-reports-compact.css') }}">
<div class="container-fluid daily-reports-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <h2 class="fw-bold mb-2" style="color: #000000;">
                    <i class="fas fa-file-export me-2" style="color: #DC143C;"></i>
                    Export Daily Reports to PDF
                </h2>
                <p class="text-muted mb-0">Select filters to export specific reports as PDF document</p>
            </div>

            <!-- Export Form Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-header border-0 p-4" style="background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%); border-radius: 1rem 1rem 0 0;">
                    <h5 class="mb-0 fw-bold" style="color: #FFFFFF;">
                        <i class="fas fa-filter me-2"></i>
                        Export Filters
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('office.daily-reports.export-pdf') }}" method="POST">
                        @csrf

                        <!-- User Selection -->
                        <div class="mb-4">
                            <label for="user_id" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-user me-2" style="color: #DC143C;"></i>
                                Select Specific User <span class="text-muted">(Optional)</span>
                            </label>
                            <select name="user_id" id="user_id" class="form-select" style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave empty to export all users' reports</small>
                        </div>

                        <!-- Department Selection -->
                        <div class="mb-4">
                            <label for="department_id" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-building me-2" style="color: #DC143C;"></i>
                                Select Department <span class="text-muted">(Optional)</span>
                            </label>
                            <select name="department_id" id="department_id" class="form-select" style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Filter by specific department</small>
                        </div>

                        <!-- Date Range -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-semibold" style="color: #000000;">
                                    <i class="fas fa-calendar-alt me-2" style="color: #DC143C;"></i>
                                    Start Date <span class="text-muted">(Optional)</span>
                                </label>
                                <input type="date" 
                                       name="start_date" 
                                       id="start_date" 
                                       class="form-control" 
                                       style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-semibold" style="color: #000000;">
                                    <i class="fas fa-calendar-check me-2" style="color: #DC143C;"></i>
                                    End Date <span class="text-muted">(Optional)</span>
                                </label>
                                <input type="date" 
                                       name="end_date" 
                                       id="end_date" 
                                       class="form-control" 
                                       style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-tasks me-2" style="color: #DC143C;"></i>
                                Status <span class="text-muted">(Optional)</span>
                            </label>
                            <select name="status" id="status" class="form-select" style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                                <option value="">All Statuses</option>
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="pending_review">Pending Review</option>
                                <option value="in_progress">In Progress</option>
                                <option value="review">Under Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <!-- Priority Filter -->
                        <div class="mb-4">
                            <label for="priority" class="form-label fw-semibold" style="color: #000000;">
                                <i class="fas fa-flag me-2" style="color: #DC143C;"></i>
                                Priority <span class="text-muted">(Optional)</span>
                            </label>
                            <select name="priority" id="priority" class="form-select" style="border: 2px solid #E0E0E0; border-radius: 0.5rem; padding: 0.75rem;">
                                <option value="">All Priorities</option>
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="normal">Normal</option>
                                <option value="low">Low</option>
                            </select>
                        </div>

                        <!-- Info Box -->
                        <div class="alert border-0 mb-4" style="background-color: rgba(220, 20, 60, 0.1); border-left: 4px solid #DC143C !important;">
                            <h6 class="fw-bold mb-2" style="color: #000000;">
                                <i class="fas fa-info-circle me-2" style="color: #DC143C;"></i>
                                Export Information
                            </h6>
                            <ul class="mb-0 small" style="color: #000000;">
                                <li>PDF will include all filtered reports with full details</li>
                                <li>Reports are ordered by date (newest first)</li>
                                <li>Export includes report description, status, priority, and submitter info</li>
                                <li>Large exports may take a few moments to generate</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-end pt-3" style="border-top: 2px solid #E0E0E0;">
                            <a href="{{ route('office.daily-reports.index') }}" class="btn btn-lg" style="background-color: #F8F9FA; color: #000000; border: 2px solid #E0E0E0; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-lg" style="background-color: #DC143C; color: #FFFFFF; border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600;">
                                <i class="fas fa-file-pdf me-2"></i>Generate PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
