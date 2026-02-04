@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-university me-2"></i>Bank Deposits</h4>
                    <div>
                        <a href="{{ route('office.accounting.bank-deposits.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Record Deposit to Bank
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Summary Card -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-info-circle"></i> About Bank Deposits</h5>
                                <p class="mb-0">Track cash deposits to bank. This feature only records the transfer and does not affect profit/loss calculations.</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5>Total Deposited (Approved)</h5>
                                <h3 class="text-primary mb-0">৳ {{ number_format($totalDeposited, 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('office.accounting.bank-deposits.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ request('bank_name') }}" placeholder="Search by bank name">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('office.accounting.bank-deposits.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bank Deposits Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Deposit Date</th>
                                    <th>Bank Name</th>
                                    <th>Amount</th>
                                    <th>Reference No</th>
                                    <th>Status</th>
                                    <th>Deposited By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>{{ $deposit->deposit_date->format('M d, Y') }}</td>
                                        <td>{{ $deposit->bank_name }}</td>
                                        <td class="text-end fw-bold">{{ $deposit->formatted_amount }}</td>
                                        <td>{{ $deposit->reference_number ?? '-' }}</td>
                                        <td>
                                            @if($deposit->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($deposit->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $deposit->depositor->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('office.accounting.bank-deposits.show', $deposit) }}" 
                                                   class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($deposit->isPending())
                                                    <a href="{{ route('office.accounting.bank-deposits.edit', $deposit) }}" 
                                                       class="btn btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('office.accounting.bank-deposits.destroy', $deposit) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this deposit record?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No bank deposits found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $deposits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
