@extends('layouts.admin')

@section('page-title', 'Reports & Analytics')
@section('breadcrumb', 'Home / Reports')

@section('content')
    <div class="card-custom mb-4">
        <div class="card-body-custom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-1 fw-bold"><i class="fas fa-chart-line text-danger"></i> Reports & Analytics</h4>
                    <p class="text-muted mb-0">Comprehensive insights and performance metrics</p>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="row g-2">
                        <div class="col"><input type="date" name="start_date" class="form-control" value="{{ $startDate }}"></div>
                        <div class="col"><input type="date" name="end_date" class="form-control" value="{{ $endDate }}"></div>
                        <div class="col-auto"><button type="submit" class="btn btn-primary-custom"><i class="fas fa-filter"></i> Filter</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6"><div class="stat-card"><div class="stat-card-header"><div><div class="stat-label mb-2">Total Students</div><div class="stat-value">{{ $totalStudents }}</div></div><div class="stat-icon primary"><i class="fas fa-users"></i></div></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="stat-card"><div class="stat-card-header"><div><div class="stat-label mb-2">New Applications</div><div class="stat-value">{{ $newStudents }}</div></div><div class="stat-icon success"><i class="fas fa-user-plus"></i></div></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="stat-card"><div class="stat-card-header"><div><div class="stat-label mb-2">Active Processing</div><div class="stat-value">{{ $activeStudents }}</div></div><div class="stat-icon warning"><i class="fas fa-spinner"></i></div></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="stat-card"><div class="stat-card-header"><div><div class="stat-label mb-2">Conversion Rate</div><div class="stat-value">{{ $conversionRate }}%</div></div><div class="stat-icon info"><i class="fas fa-chart-pie"></i></div></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card-custom"><div class="card-header-custom"><h5 class="mb-0">Application Status</h5></div>
                <div class="card-body-custom">
                    @foreach($statusBreakdown as $status => $count)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="badge-custom badge-primary-custom">{{ ucfirst($status) }}</span>
                        <div><strong style="font-size: 1.25rem;">{{ $count }}</strong><small class="text-muted"> students</small></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-custom"><div class="card-header-custom"><h5 class="mb-0">Top Universities</h5></div>
                <div class="card-body-custom">
                    @foreach($topUniversities as $uni)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="flex-grow-1"><div class="fw-bold">{{ $uni['name'] }}</div>
                            <div class="progress mt-2" style="height: 6px;"><div class="progress-bar" style="width: {{ ($uni['total'] / max($totalStudents, 1)) * 100 }}%"></div></div>
                        </div><div class="ms-3"><strong>{{ $uni['total'] }}</strong></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
