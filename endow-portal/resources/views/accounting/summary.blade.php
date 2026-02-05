@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Income</h6>
                            <h3 class="card-title mb-0">{{ $currencySymbol ?? '৳' }}{{ number_format($totalIncome, 2) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-arrow-up fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Expense</h6>
                            <h3 class="card-title mb-0">{{ $currencySymbol ?? '৳' }}{{ number_format($totalExpense, 2) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-arrow-down fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Cash (On Hand)</h6>
                            <h3 class="card-title mb-0">{{ $currencySymbol ?? '৳' }}{{ number_format($totalCash, 2) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Deposited to Bank</h6>
                            <h3 class="card-title mb-0">
                                {{ $currencySymbol ?? '৳' }}{{ number_format($totalDepositedToBank, 2) }}
                            </h3>
                        </div>
                        <div>
                            <i class="fas fa-university fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('office.accounting.summary') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                            <a href="{{ route('office.accounting.summary') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Income and Expense Breakdown -->
    <div class="row mb-4">
        <!-- Income Breakdown -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Income Breakdown by Category
                    </h5>
                </div>
                <div class="card-body">
                    @if($incomeByCategory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomeByCategory as $item)
                                        <tr>
                                            <td>{{ $item->category->name }}</td>
                                            <td class="text-end">{{ number_format($item->total, 2) }}</td>
                                            <td class="text-end">
                                                {{ $totalIncome > 0 ? number_format(($item->total / $totalIncome) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end">{{ number_format($totalIncome, 2) }}</th>
                                        <th class="text-end">100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0">No income data available for this period.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Expense Breakdown -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Expense Breakdown by Category
                    </h5>
                </div>
                <div class="card-body">
                    @if($expenseByCategory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseByCategory as $item)
                                        <tr>
                                            <td>{{ $item->category->name }}</td>
                                            <td class="text-end">{{ number_format($item->total, 2) }}</td>
                                            <td class="text-end">
                                                {{ $totalExpense > 0 ? number_format(($item->total / $totalExpense) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end">{{ number_format($totalExpense, 2) }}</th>
                                        <th class="text-end">100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0">No expense data available for this period.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Recent Transactions (Last 10)
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Student Name</th>
                                        <th class="text-end">Amount</th>
                                        <th>Created By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->entry_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($transaction->type == 'income')
                                                    <span class="badge bg-success">Income</span>
                                                @elseif($transaction->type == 'expense')
                                                    <span class="badge bg-danger">Expense</span>
                                                @else
                                                    <span class="badge bg-info">Non Financial</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->category ? $transaction->category->name : 'N/A' }}</td>
                                            <td>{{ $transaction->student_name ?? '-' }}</td>
                                            <td class="text-end">
                                                {{ $transaction->getCurrencySymbol() }} {{ number_format($transaction->currency != 'BDT' && $transaction->original_amount ? $transaction->original_amount : $transaction->amount, 2) }}
                                            </td>
                                            <td>{{ $transaction->creator->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0">No transactions available for this period.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
