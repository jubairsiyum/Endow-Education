@extends('layouts.admin')

@section('title', 'Financial Dashboard - Endow Corporation')

@section('content')
<style>
    /* Modern Professional Accounting Dashboard */
    :root {
        --color-profit: #10b981; --color-loss: #ef4444; --color-income: #3b82f6; --color-expense: #f59e0b;
        --color-cash: #8b5cf6; --color-bank: #06b6d4; --color-neutral: #6b7280;
    }
    body { background: #f3f4f6; }
    .stat-card {
        border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
        transition: all 0.3s; background: #ffffff; position: relative;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
    .stat-card.profit::before { background: linear-gradient(90deg, var(--color-profit), #059669); }
    .stat-card.loss::before { background: linear-gradient(90deg, var(--color-loss), #dc2626); }
    .stat-card.income::before { background: linear-gradient(90deg, var(--color-income), #2563eb); }
    .stat-card.expense::before { background: linear-gradient(90deg, var(--color-expense), #d97706); }
    .stat-card.cash::before { background: linear-gradient(90deg, var(--color-cash), #7c3aed); }
    .stat-card.bank::before { background: linear-gradient(90deg, var(--color-bank), #0891b2); }
    .stat-card.neutral::before { background: linear-gradient(90deg, var(--color-neutral), #4b5563); }
    .stat-value { font-size: 1.875rem; font-weight: 700; margin: 0; line-height: 1.2; color: #111827; }
    .stat-label { font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.025em; color: #6b7280; font-weight: 600; }
    .stat-icon { font-size: 2.25rem; opacity: 0.15; }
    .stat-meta { font-size: 0.8125rem; color: #6b7280; padding-top: 0.75rem; margin-top: 0.75rem; border-top: 1px solid #e5e7eb; }
    .stat-badge { padding: 0.25rem 0.625rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem; }
    .badge-profit { background: #d1fae5; color: #065f46; }
    .badge-loss { background: #fee2e2; color: #991b1b; }
    .badge-income { background: #dbeafe; color: #1e40af; }
    .badge-expense { background: #fef3c7; color: #92400e; }
    .badge-cash { background: #ede9fe; color: #5b21b6; }
    .badge-bank { background: #cffafe; color: #155e75; }
    .modern-table { font-size: 0.875rem; }
    .modern-table thead th { padding: 0.875rem 1rem; background: #f9fafb; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.025em; }
    .modern-table tbody td { padding: 0.875rem 1rem; vertical-align: middle; border-bottom: 1px solid #f3f4f6; color: #374151; }
    .modern-table tbody tr:hover { background-color: #f9fafb; }
    .progress-modern { height: 8px; border-radius: 4px; background: #e5e7eb; }
    .progress-bar-profit { background: linear-gradient(90deg, var(--color-profit), #059669); }
    .progress-bar-income { background: linear-gradient(90deg, var(--color-income), #2563eb); }
    .progress-bar-expense { background: linear-gradient(90deg, var(--color-expense), #d97706); }
    .period-selector { display: inline-flex; gap: 0.5rem; flex-wrap: wrap; }
    .period-btn { padding: 0.5rem 1.125rem; border-radius: 8px; border: 1.5px solid #e5e7eb; background: #ffffff; color: #374151; cursor: pointer; transition: all 0.2s; font-weight: 500; font-size: 0.875rem; }
    .period-btn:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
    .period-btn.active { background: #3b82f6; color: #ffffff; border-color: #3b82f6; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); }
    .quick-action-btn { padding: 0.625rem 1.125rem; border-radius: 8px; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; border: 1.5px solid; display: inline-flex; align-items-center; gap: 0.5rem; }
    .quick-action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .empty-state { padding: 3rem 1.5rem; text-align: center; color: #9ca3af; }
    @media (max-width: 768px) { .stat-value { font-size: 1.5rem; } .period-btn { flex: 1; padding: 0.5rem; } }
    @media print { .period-selector, .quick-action-btn, .btn { display: none !important; } .stat-card { box-shadow: none; border: 1px solid #dee2e6; } }
</style>

<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark"><i class="fas fa-chart-line me-2" style="color: #3b82f6;"></i>Financial Dashboard</h2>
            <p class="text-muted mb-0 fs-6">Endow Corporation • {{ date('F Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('office.accounting.transactions.create') }}" class="quick-action-btn btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add Transaction
            </a>
            <a href="{{ route('office.accounting.transactions.export', array_merge(request()->query(), ['currency' => $selectedCurrency])) }}" class="quick-action-btn btn btn-success">
                <i class="fas fa-file-excel"></i> Export {{ $selectedCurrency ? "($selectedCurrency)" : '' }}
            </a>
            <button class="quick-action-btn btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="period-selector">
                    <button class="period-btn {{ request('period') == 'month' ? 'active' : '' }}" onclick="setPeriod('month')">
                        <i class="fas fa-calendar-day"></i> This Month
                    </button>
                    <button class="period-btn {{ request('period') == 'week' ? 'active' : '' }}" onclick="setPeriod('week')">
                        <i class="fas fa-calendar-week"></i> This Week
                    </button>
                    <button class="period-btn {{ request('period') == 'quarter' ? 'active' : '' }}" onclick="setPeriod('quarter')">
                        <i class="fas fa-calendar-check"></i> Quarter
                    </button>
                    <button class="period-btn {{ !request()->has('period') && !request()->has('start_date') ? 'active' : (request('period') == 'year' ? 'active' : '') }}" onclick="setPeriod('year')">
                        <i class="fas fa-calendar-alt"></i> Year (Default)
                    </button>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <select id="currency_filter" class="form-select form-select-sm" style="width:150px; border-radius: 8px;">
                        <option value="">All Currencies</option>
                        @foreach($currencies as $curr)
                            <option value="{{ $curr }}" {{ $selectedCurrency == $curr ? 'selected' : '' }}>
                                {{ $curr }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" id="custom_start" class="form-control form-control-sm" style="width:150px; border-radius: 8px;" value="{{ $startDate }}">
                    <span class="text-muted">to</span>
                    <input type="date" id="custom_end" class="form-control form-control-sm" style="width:150px; border-radius: 8px;" value="{{ $endDate }}">
                    <button class="btn btn-sm btn-primary" style="border-radius: 8px;" onclick="applyCustomPeriod()">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Note about Non-Financial Transactions and Currency Filter -->
    @if($selectedCurrency)
    <div class="alert alert-info mb-4" style="border-radius: 12px;">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Currency Filter Active:</strong> Showing only transactions in {{ $selectedCurrency }}.
        Non-financial transactions are excluded from financial calculations.
    </div>
    @else
    <div class="alert alert-light mb-4" style="border-radius: 12px; border-left: 4px solid #3b82f6;">
        <i class="fas fa-calendar-alt me-2"></i>
        <strong>Date Range:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
        <span class="ms-3"><i class="fas fa-chart-bar me-2"></i>Income/Expense for this period. Cash/Bank balances as of {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}.</span>
        @if($totalIncome == 0 && $totalExpense == 0)
        <div class="mt-2 pt-2 border-top">
            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
            <strong>No transactions found.</strong> 
            Make sure: 
            (1) Transactions are <strong>approved</strong> (check <a href="{{ route('office.accounting.transactions.pending') }}" class="alert-link">Pending Transactions</a>)
            (2) Transaction <strong>entry_date</strong> falls within the date range above
            (3) Transactions are marked as <strong>income</strong> or <strong>expense</strong> type
        </div>
        @endif
    </div>
    @endif

    <!-- Row 1: Primary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label">Net Profit/Loss</div>
                            <h2 class="stat-value mt-2" style="color: {{ $netProfit >= 0 ? '#10b981' : '#ef4444' }}">{{ $currencySymbol }}{{ number_format(abs($netProfit), 2) }}</h2>
                            <span class="stat-badge {{ $netProfit >= 0 ? 'badge-profit' : 'badge-loss' }} mt-2">
                                <i class="fas fa-{{ $netProfit >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ $netProfit >= 0 ? 'Profitable' : 'Loss' }}
                            </span>
                        </div>
                        <div class="stat-icon" style="color: {{ $netProfit >= 0 ? '#10b981' : '#ef4444' }}"><i class="fas fa-chart-line"></i></div>
                    </div>
                    @if($totalIncome > 0)
                    <div class="stat-meta">
                        <div class="d-flex justify-content-between"><span>Margin:</span><strong>{{ number_format(($netProfit / $totalIncome) * 100, 1) }}%</strong></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stat-card income">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label">Total Income</div>
                            <h2 class="stat-value mt-2" style="color: #3b82f6">{{ $currencySymbol }}{{ number_format($totalIncome, 2) }}</h2>
                            <span class="stat-badge badge-income mt-2"><i class="fas fa-arrow-up"></i> Revenue</span>
                        </div>
                        <div class="stat-icon" style="color: #3b82f6"><i class="fas fa-hand-holding-usd"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div class="d-flex justify-content-between"><span>Transactions:</span><strong>{{ $totalIncomeCount ?? 0 }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card expense">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label">Total Expenses</div>
                            <h2 class="stat-value mt-2" style="color: #f59e0b">{{ $currencySymbol }}{{ number_format($totalExpense, 2) }}</h2>
                            <span class="stat-badge badge-expense mt-2"><i class="fas fa-arrow-down"></i> Outflow</span>
                        </div>
                        <div class="stat-icon" style="color: #f59e0b"><i class="fas fa-receipt"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div class="d-flex justify-content-between"><span>Transactions:</span><strong>{{ $totalExpenseCount ?? 0 }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card neutral">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="stat-label">Cash Flow Ratio</div>
                            <h2 class="stat-value mt-2" style="color: #6b7280">{{ $totalExpense > 0 ? number_format(($totalIncome / $totalExpense), 2) : '∞' }}</h2>
                            <span class="stat-badge {{ ($totalExpense == 0 || $totalIncome / $totalExpense >= 1) ? 'badge-profit' : 'badge-loss' }} mt-2">
                                <i class="fas fa-{{ ($totalExpense == 0 || $totalIncome / $totalExpense >= 1) ? 'check-circle' : 'exclamation-triangle' }}"></i>
                                {{ ($totalExpense == 0 || $totalIncome / $totalExpense >= 1) ? 'Healthy' : 'Critical' }}
                            </span>
                        </div>
                        <div class="stat-icon" style="color: #6b7280"><i class="fas fa-exchange-alt"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div class="d-flex justify-content-between"><span>Status:</span><strong>{{ $totalIncome >= $totalExpense ? 'Positive' : 'Negative' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Cash Management (Cumulative Balances) -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stat-card cash">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div class="flex-grow-1">
                            <div class="stat-label"><i class="fas fa-money-bill-wave me-1"></i> Cash on Hand</div>
                            <h2 class="stat-value mt-2" style="color: #8b5cf6">{{ $currencySymbol }}{{ number_format($totalCash, 2) }}</h2>
                            <small class="text-muted">Current balance (as of {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</small>
                        </div>
                        <div class="stat-icon" style="color: #8b5cf6"><i class="fas fa-wallet"></i></div>
                    </div>
                    @if(isset($cashIncome) && isset($cashExpense))
                    <div class="stat-meta">
                        <div class="d-flex justify-content-between mb-2">
                            <small><i class="fas fa-arrow-down text-success" style="font-size:10px"></i> Total In: {{ $currencySymbol }}{{ number_format($cashIncome, 2) }}</small>
                            <small><i class="fas fa-arrow-up text-danger" style="font-size:10px"></i> Total Out: {{ $currencySymbol }}{{ number_format($cashExpense, 2) }}</small>
                        </div>
                        <div class="progress progress-modern">
                            <div class="progress-bar-profit" style="width: {{ $cashIncome > 0 ? (($cashIncome / ($cashIncome + $cashExpense)) * 100) : 0 }}%"></div>
                            <div class="progress-bar-expense" style="width: {{ $cashExpense > 0 ? (($cashExpense / ($cashIncome + $cashExpense)) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stat-card bank">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div class="flex-grow-1">
                            <div class="stat-label"><i class="fas fa-university me-1"></i> Bank Deposits</div>
                            <h2 class="stat-value mt-2" style="color: #06b6d4">{{ $currencySymbol }}{{ number_format($totalDepositedToBank, 2) }}</h2>
                            <small class="text-muted">Total deposited (as of {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</small>
                        </div>
                        <div class="stat-icon" style="color: #06b6d4"><i class="fas fa-landmark"></i></div>
                    </div>
                    <div class="stat-meta">
                        <a href="{{ route('office.accounting.bank-deposits.index') }}" class="btn btn-sm btn-outline-primary w-100" style="border-radius: 6px;">
                            <i class="fas fa-eye"></i> View Deposits
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stat-card neutral">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div class="flex-grow-1">
                            <div class="stat-label"><i class="fas fa-piggy-bank me-1"></i> Liquid Assets</div>
                            <h2 class="stat-value mt-2" style="color: #6b7280">{{ $currencySymbol }}{{ number_format($totalCash + $totalDepositedToBank, 2) }}</h2>
                            <small class="text-muted">Total available funds</small>
                        </div>
                        <div class="stat-icon" style="color: #6b7280"><i class="fas fa-coins"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div class="row text-center g-2">
                            <div class="col-6"><small class="d-block text-muted">Cash</small><strong>{{ ($totalCash + $totalDepositedToBank) > 0 ? number_format(($totalCash / ($totalCash + $totalDepositedToBank)) * 100, 0) : 0 }}%</strong></div>
                            <div class="col-6"><small class="d-block text-muted">Bank</small><strong>{{ ($totalCash + $totalDepositedToBank) > 0 ? number_format(($totalDepositedToBank / ($totalCash + $totalDepositedToBank)) * 100, 0) : 0 }}%</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Breakdowns -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-arrow-up text-success me-2"></i>Income Breakdown</h6>
                        <span class="badge bg-success">{{ $currencySymbol }}{{ number_format($totalIncome, 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($incomeByCategory->count() > 0)
                    <table class="table modern-table mb-0">
                        <thead><tr><th>Category</th><th class="text-end">Amount</th><th>Share</th></tr></thead>
                        <tbody>
                            @foreach($incomeByCategory as $item)
                            <tr>
                                <td><i class="fas fa-circle text-success" style="font-size:6px"></i> {{ $item->category->name }}</td>
                                <td class="text-end fw-semibold">{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
                                <td>
                                    @php $pct = $totalIncome > 0 ? ($item->total / $totalIncome) * 100 : 0; @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress progress-modern flex-grow-1" style="max-width: 100px;">
                                            <div class="progress-bar-income" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <small class="text-muted" style="min-width:40px">{{ number_format($pct, 1) }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="empty-state"><i class="fas fa-chart-pie fa-3x mb-3"></i><p class="mb-0">No income data</p></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-arrow-down text-danger me-2"></i>Expense Breakdown</h6>
                        <span class="badge bg-danger">{{ $currencySymbol }}{{ number_format($totalExpense, 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($expenseByCategory->count() > 0)
                    <table class="table modern-table mb-0">
                        <thead><tr><th>Category</th><th class="text-end">Amount</th><th>Share</th></tr></thead>
                        <tbody>
                            @foreach($expenseByCategory as $item)
                            <tr>
                                <td><i class="fas fa-circle text-danger" style="font-size:6px"></i> {{ $item->category->name }}</td>
                                <td class="text-end fw-semibold">{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
                                <td>
                                    @php $pct = $totalExpense > 0 ? ($item->total / $totalExpense) * 100 : 0; @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress progress-modern flex-grow-1" style="max-width: 100px;">
                                            <div class="progress-bar-expense" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <small class="text-muted" style="min-width:40px">{{ number_format($pct, 1) }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="empty-state"><i class="fas fa-chart-pie fa-3x mb-3"></i><p class="mb-0">No expense data</p></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Currency-Specific Reporting Section -->
    @if(!$selectedCurrency && isset($currencySummaries) && count($currencySummaries) > 1)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-coins me-2"></i>Currency-Wise Financial Summary</h6>
                        <span class="badge bg-primary">{{ count($currencySummaries) }} Currencies</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th class="text-end">Income</th>
                                    <th class="text-end">Expense</th>
                                    <th class="text-end">Net Profit/Loss</th>
                                    <th class="text-center">Transactions</th>
                                    <th class="text-center">Margin</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currencySummaries as $curr => $summary)
                                <tr>
                                    <td>
                                        <strong>{{ $curr }}</strong>
                                        <small class="text-muted d-block">
                                            @if($curr == 'BDT') Bangladeshi Taka
                                            @elseif($curr == 'USD') US Dollar
                                            @elseif($curr == 'KRW') Korean Won
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-success fw-semibold">
                                            @if($curr == 'BDT') ৳
                                            @elseif($curr == 'USD') $
                                            @elseif($curr == 'KRW') ₩
                                            @endif
                                            {{ number_format($summary['income'], 2) }}
                                        </span>
                                        <small class="text-muted d-block">{{ $summary['income_count'] }} txns</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-danger fw-semibold">
                                            @if($curr == 'BDT') ৳
                                            @elseif($curr == 'USD') $
                                            @elseif($curr == 'KRW') ₩
                                            @endif
                                            {{ number_format($summary['expense'], 2) }}
                                        </span>
                                        <small class="text-muted d-block">{{ $summary['expense_count'] }} txns</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold" style="color: {{ $summary['profit'] >= 0 ? '#10b981' : '#ef4444' }}">
                                            {{ $summary['profit'] >= 0 ? '+' : '' }}
                                            @if($curr == 'BDT') ৳
                                            @elseif($curr == 'USD') $
                                            @elseif($curr == 'KRW') ₩
                                            @endif
                                            {{ number_format($summary['profit'], 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $summary['total_transactions'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($summary['income'] > 0)
                                            <span class="stat-badge {{ ($summary['profit'] / $summary['income']) >= 0 ? 'badge-profit' : 'badge-loss' }}">
                                                {{ number_format(($summary['profit'] / $summary['income']) * 100, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="stat-badge {{ $summary['profit'] >= 0 ? 'badge-profit' : 'badge-loss' }}">
                                            <i class="fas fa-{{ $summary['profit'] >= 0 ? 'check-circle' : 'exclamation-triangle' }}"></i>
                                            {{ $summary['profit'] >= 0 ? 'Profit' : 'Loss' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('office.accounting.summary', array_merge(request()->query(), ['currency' => $curr])) }}" 
                                           class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
                                            <i class="fas fa-chart-line"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #f9fafb; font-weight: 600;">
                                <tr>
                                    <td colspan="8" class="text-center py-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Amounts shown in their original currencies. Click "View Details" for currency-specific analysis.
                                        </small>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Row 4: Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Recent Transactions</h6>
                        <a href="{{ route('office.accounting.transactions.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th><th>Type</th><th>Category</th><th>Description</th><th>Student</th><th>Currency</th><th class="text-end">Amount</th><th>Method</th><th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $t)
                                <tr>
                                    <td><small>{{ $t->entry_date->format('M d, Y') }}</small></td>
                                    <td><span class="stat-badge {{ $t->type == 'income' ? 'badge-income' : 'badge-expense' }}">{{ ucfirst($t->type) }}</span></td>
                                    <td><small>{{ $t->category->name ?? 'N/A' }}</small></td>
                                    <td><small class="text-muted">{{ Str::limit($t->headline ?? '-', 25) }}</small></td>
                                    <td><small>{{ $t->student_name ?? '-' }}</small></td>
                                    <td><span class="badge bg-light text-dark">{{ $t->currency }}</span></td>
                                    <td class="text-end fw-semibold">
                                        <small class="{{ $t->type == 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ $t->type == 'income' ? '+' : '-' }}
                                            @if($t->currency == 'BDT') ৳
                                            @elseif($t->currency == 'USD') $
                                            @elseif($t->currency == 'KRW') ₩
                                            @endif
                                            {{ number_format($t->currency != 'BDT' && $t->original_amount ? $t->original_amount : $t->amount, 2) }}
                                        </small>
                                    </td>
                                    <td><span class="stat-badge" style="background: #f3f4f6; color: #374151;"><i class="fas fa-{{ $t->payment_method == 'cash' ? 'money-bill' : ($t->payment_method == 'bank' ? 'university' : 'credit-card') }}"></i> {{ ucfirst($t->payment_method ?? 'N/A') }}</span></td>
                                    <td><small class="text-muted">{{ Str::limit($t->creator->name ?? 'N/A', 10) }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state"><i class="fas fa-receipt fa-3x mb-3"></i><p class="mb-0">No transactions for this period</p></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setPeriod(period) { 
    const currency = document.getElementById('currency_filter').value;
    let url = `{{ route('office.accounting.summary') }}?period=${period}`;
    if (currency) {
        url += `&currency=${currency}`;
    }
    window.location.href = url;
}
function applyCustomPeriod() {
    const start = document.getElementById('custom_start').value;
    const end = document.getElementById('custom_end').value;
    const currency = document.getElementById('currency_filter').value;
    if (start && end) {
        let url = `{{ route('office.accounting.summary') }}?start_date=${start}&end_date=${end}`;
        if (currency) {
            url += `&currency=${currency}`;
        }
        window.location.href = url;
    }
}

// Add event listener for currency filter change
document.addEventListener('DOMContentLoaded', function() {
    const currencyFilter = document.getElementById('currency_filter');
    if (currencyFilter) {
        currencyFilter.addEventListener('change', function() {
            const start = document.getElementById('custom_start').value;
            const end = document.getElementById('custom_end').value;
            const currency = this.value;
            
            let url = `{{ route('office.accounting.summary') }}`;
            const params = new URLSearchParams();
            
            if (start) params.append('start_date', start);
            if (end) params.append('end_date', end);
            if (currency) params.append('currency', currency);
            
            const queryString = params.toString();
            if (queryString) {
                url += '?' + queryString;
            }
            
            window.location.href = url;
        });
    }
});
</script>
@endsection
