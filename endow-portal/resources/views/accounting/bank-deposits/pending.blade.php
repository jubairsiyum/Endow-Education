@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-clock me-2"></i>Pending Bank Deposits</h4>
                    <a href="{{ route('office.accounting.bank-deposits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> View All Deposits
                    </a>
                </div>

                <div class="card-body">
                    @if($deposits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Deposit Date</th>
                                        <th>Bank Name</th>
                                        <th>Amount</th>
                                        <th>Reference No</th>
                                        <th>Deposited By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deposits as $deposit)
                                        <tr>
                                            <td>{{ $deposit->deposit_date->format('M d, Y') }}</td>
                                            <td>{{ $deposit->bank_name }}</td>
                                            <td class="text-end fw-bold">{{ $deposit->formatted_amount }}</td>
                                            <td>{{ $deposit->reference_number ?? '-' }}</td>
                                            <td>{{ $deposit->depositor->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('office.accounting.bank-deposits.show', $deposit) }}" 
                                                       class="btn btn-info" title="View & Approve/Reject">
                                                        <i class="fas fa-eye"></i> Review
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $deposits->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">No Pending Deposits</h4>
                            <p class="text-muted">All bank deposits have been processed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
