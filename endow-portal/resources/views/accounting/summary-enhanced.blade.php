@extends('layouts.admin')

@section('title', 'Accounting Dashboard - Endow Corporation')

@section('content')
<style>
    /* Professional Compact Styling for Accounting Module */
    .stat-card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
        font-weight: 600;
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }
    .stat-change {
        font-size: 0.75rem;
        font-weight: 600;
    }
    .compact-table {
        font-size: 0.875rem;
    }
    .compact-table th {
        padding: 0.5rem 0.75rem;
        background: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    .compact-table td {
        padding: 0.5rem 0.75rem;
        vertical-align: middle;
    }
    .progress-thin {
        height: 6px;
    }
    .metric-card {
        border-left: 4px solid;
        padding: 1rem;
        background: #fff;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .summary-badge {
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 4px;
    }
    .action-btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.25rem;
    }
    .period-selector {
        display: inline-flex;
        border-radius: 6px;
        overflow: hidden;
    }
    .period-btn {
        padding: 0.4rem 1rem;
        border: 1px solid #dee2e6;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }
    .period-btn:hover {
        background: #e9ecef;
    }
    .period-btn.active {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><i class="fas fa-chart-line me-2 text-primary"></i>Accounting Dashboard</h3>
            <p class="text-muted mb-0">Endow Corporation Financial Overview</p>
        </div>
        <div>
            <button class="btn btn-outline-primary btn-sm me-2" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('office.accounting.transactions.export', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Quick Period Selector -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="period-selector">
                    <button class="period-btn {{ !request()->has('period') || request('period') == 'today' ? 'active' : '' }}" 
                            onclick="setPeriod('today')">Today</button>
                    <button class="period-btn {{ request('period') == 'week' ? 'active' : '' }}" 
                            onclick="setPeriod('week')">This Week</button>
                    <button class="period-btn {{ request('period') == 'month' ? 'active' : '' }}" 
                            onclick="setPeriod('month')">This Month</button>
                    <button class="period-btn {{ request('period') == 'quarter' ? 'active' : '' }}" 
                            onclick="setPeriod('quarter')">This Quarter</button>
                    <button class="period-btn {{ request('period') == 'year' ? 'active' : '' }}" 
                            onclick="setPeriod('year')">This Year</button>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <input type="date" id="custom_start" class="form-control form-control-sm" style="width:140px" 
                           value="{{ $startDate }}" placeholder="Start">
                    <span>to</span>
                    <input type="date" id="custom_end" class="form-control form-control-sm" style="width:140px" 
                           value="{{ $endDate }}" placeholder="End">
                    <button class="btn btn-sm btn-primary" onclick="applyCustom Period()">
                        <i class="fas fa-search"></i> Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Financial Metrics - Row 1 -->
    <div class="row g-3 mb-3">
        <!-- Net Profit/Loss -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} text-white border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label">Net Profit/Loss</div>
                            <h2 class="stat-value mt-2 mb-1">৳{{ number_format(abs($netProfit), 2) }}</h2>
                            <div class="stat-change">
                                <i class="fas fa-{{ $netProfit >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                    </div>
                    @if($totalIncome > 0)
                        <div class="mt-2 pt-2 border-top border-white-50">
                            <small>Margin: {{ number_format(($netProfit / $totalIncome) * 100, 1) }}%</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Total Income -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-primary text-white border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label">Total Income</div>
                            <h2 class="stat-value mt-2 mb-1">৳{{ number_format($totalIncome, 2) }}</h2>
                            <div class="stat-change text-white-50">
                                <i class="fas fa-arrow-up"></i> Revenue Generated
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top border-white-50">
                        <small>From {{ $totalIncomeCount ?? 0 }} transaction(s)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Expense -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning text-white border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label">Total Expense</div>
                            <h2 class="stat-value mt-2 mb-1">৳{{ number_format($totalExpense, 2) }}</h2>
                            <div class="stat-change text-white-50">
                                <i class="fas fa-arrow-down"></i> Total Spent
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top border-white-50">
                        <small>From {{ $totalExpenseCount ?? 0 }} transaction(s)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Flow Status -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-info text-white border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label">Cash Flow Ratio</div>
                            <h2 class="stat-value mt-2 mb-1">
                                {{ $totalExpense > 0 ? number_format(($totalIncome / $totalExpense), 2) : '∞' }}
                            </h2>
                            <div class="stat-change text-white-50">
                                <i class="fas fa-chart-line"></i> Income/Expense Ratio
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-stream"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top border-white-50">
                        <small>{{ $totalIncome > $totalExpense ? 'Positive' : 'Negative' }} Cash Flow</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Management - Row 2 -->
    <div class="row g-3 mb-3">
        <!-- Cash on Hand -->
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted">
                                <i class="fas fa-money-bill-wave me-1"></i> Cash on Hand
                            </div>
                            <h2 class="stat-value mt-2 mb-1 text-success">৳{{ number_format($totalCash, 2) }}</h2>
                            <small class="text-muted">Available for operations</small>
                        </div>
                        <div class="stat-icon text-success">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                    @if(isset($cashIncome) && isset($cashExpense))
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Cash In: ৳{{ number_format($cashIncome, 2) }}</small>
                            <small class="text-muted">Cash Out: ৳{{ number_format($cashExpense, 2) }}</small>
                        </div>
                        <div class="progress progress-thin">
                            <div class="progress-bar bg-success" style="width: {{ $cashIncome > 0 ? (($cashIncome / ($cashIncome + $cashExpense)) * 100) : 0 }}%"></div>
                            <div class="progress-bar bg-danger" style="width: {{ $cashExpense > 0 ? (($cashExpense / ($cashIncome + $cashExpense)) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bank Deposits -->
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted">
                                <i class="fas fa-university me-1"></i> Deposited to Bank
                            </div>
                            <h2 class="stat-value mt-2 mb-1 text-primary">৳{{ number_format($totalDepositedToBank, 2) }}</h2>
                            <small class="text-muted">Banked amount</small>
                        </div>
                        <div class="stat-icon text-primary">
                            <i class="fas fa-landmark"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('office.accounting.bank-deposits.index') }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-eye"></i> View Bank Deposits
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Liquid Assets -->
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted">
                                <i class="fas fa-piggy-bank me-1"></i> Total Liquid Assets
                            </div>
                            <h2 class="stat-value mt-2 mb-1 text-info">৳{{ number_format($totalCash + $totalDepositedToBank, 2) }}</h2>
                            <small class="text-muted">Cash + Bank</small>
                        </div>
                        <div class="stat-icon text-info">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <small class="d-block text-muted">Cash</small>
                                <strong>{{ $totalCash > 0 ? number_format(($totalCash / ($totalCash + $totalDepositedToBank)) * 100, 1) : 0 }}%</strong>
                            </div>
                            <div class="col-6">
                                <small class="d-block text-muted">Bank</small>
                                <strong>{{ $totalDepositedToBank > 0 ? number_format(($totalDepositedToBank / ($totalCash + $totalDepositedToBank)) * 100, 1) : 0 }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Income & Expense Breakdown -->
    <div class="row g-3 mb-3">
        <!-- Income by Category -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-arrow-up text-success me-2"></i>Income Breakdown
                        </h6>
                        <span class="badge bg-success">৳{{ number_format($totalIncome, 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($incomeByCategory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover compact-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:50%">Category</th>
                                        <th class="text-end" style="width:25%">Amount</th>
                                        <th style="width:25%">Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomeByCategory as $item)
                                        <tr>
                                            <td>
                                                <i class="fas fa-circle text-success" style="font-size:8px"></i>
                                                {{ $item->category->name }}
                                            </td>
                                            <td class="text-end fw-semibold">৳{{ number_format($item->total, 2) }}</td>
                                            <td>
                                                @php $percentage = $totalIncome > 0 ? ($item->total / $totalIncome) * 100 : 0; @endphp
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2 progress-thin">
                                                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                    <small class="text-muted" style="min-width:45px">{{ number_format($percentage, 1) }}%</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-chart-pie fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No income data for this period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Expense by Category -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-arrow-down text-danger me-2"></i>Expense Breakdown
                        </h6>
                        <span class="badge bg-danger">৳{{ number_format($totalExpense, 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($expenseByCategory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover compact-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:50%">Category</th>
                                        <th class="text-end" style="width:25%">Amount</th>
                                        <th style="width:25%">Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseByCategory as $item)
                                        <tr>
                                            <td>
                                                <i class="fas fa-circle text-danger" style="font-size:8px"></i>
                                                {{ $item->category->name }}
                                            </td>
                                            <td class="text-end fw-semibold">৳{{ number_format($item->total, 2) }}</td>
                                            <td>
                                                @php $percentage = $totalExpense > 0 ? ($item->total / $totalExpense) * 100 : 0; @endphp
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2 progress-thin">
                                                        <div class="progress-bar bg-danger" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                    <small class="text-muted" style="min-width:45px">{{ number_format($percentage, 1) }}%</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-chart-pie fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No expense data for this period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2"></i>Recent Transactions
                        </h6>
                        <a href="{{ route('office.accounting.transactions.index') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover compact-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:10%">Date</th>
                                        <th style="width:8%">Type</th>
                                        <th style="width:18%">Category</th>
                                        <th style="width:20%">Description</th>
                                        <th style="width:15%">Student</th>
                                        <th class="text-end" style="width:12%">Amount</th>
                                        <th style="width:10%">Method</th>
                                        <th style="width:7%">By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td><small>{{ $transaction->entry_date->format('M d, Y') }}</small></td>
                                            <td>
                                                <span class="summary-badge {{ $transaction->type == 'income' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td><small>{{ $transaction->category->name ?? 'N/A' }}</small></td>
                                            <td><small class="text-muted">{{ Str::limit($transaction->headline ?? '-', 30) }}</small></td>
                                            <td><small>{{ $transaction->student_name ?? '-' }}</small></td>
                                            <td class="text-end fw-semibold">
                                                <small class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type == 'income' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="summary-badge bg-light text-dark">
                                                    <i class="fas fa-{{ $transaction->payment_method == 'cash' ? 'money-bill' : ($transaction->payment_method == 'bank' ? 'university' : 'credit-card') }}"></i>
                                                    {{ ucfirst($transaction->payment_method ?? 'N/A') }}
                                                </span>
                                            </td>
                                            <td><small class="text-muted">{{ Str::limit($transaction->creator->name ?? 'N/A', 10) }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No transactions for this period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setPeriod(period) {
    window.location.href = `{{ route('office.accounting.summary') }}?period=${period}`;
}

function applyCustomPeriod() {
    const start = document.getElementById('custom_start').value;
    const end = document.getElementById('custom_end').value;
    if (start && end) {
        window.location.href = `{{ route('office.accounting.summary') }}?start_date=${start}&end_date=${end}`;
    }
}
</script>
@endsection
