@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Transactions</h4>
                    <div>
                        @can('create-transaction')
                            <a href="{{ route('office.accounting.transactions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Transaction
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('office.accounting.transactions.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All</option>
                                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('office.accounting.transactions.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Transactions Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Student Name</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->entry_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->type == 'income' ? 'success' : 'danger' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->category->name }}</td>
                                        <td>{{ $transaction->student_name ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ $transaction->payment_method ?? '-' }}</td>
                                        <td>
                                            @if($transaction->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($transaction->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->creator->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('office.accounting.transactions.show', $transaction) }}" 
                                                   class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($transaction->isPending())
                                                    @can('update-transaction')
                                                        <a href="{{ route('office.accounting.transactions.edit', $transaction) }}" 
                                                           class="btn btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete-transaction')
                                                        <form action="{{ route('office.accounting.transactions.destroy', $transaction) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
