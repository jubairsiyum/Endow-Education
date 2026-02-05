@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Pending Transactions - Awaiting Approval</h4>
                </div>

                <div class="card-body">
                    @if($transactions->count() > 0)
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
                                        <th>Remarks</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
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
                                            <td>{{ $transaction->payment_method ?? '-' }}</td>
                                            <td>
                                                @if($transaction->remarks)
                                                    <span data-bs-toggle="tooltip" title="{{ $transaction->remarks }}">
                                                        {{ Str::limit($transaction->remarks, 30) }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $transaction->creator->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('office.accounting.transactions.show', $transaction) }}" 
                                                       class="btn btn-info" 
                                                       title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @can('approve-transaction')
                                                        <form action="{{ route('office.accounting.transactions.approve', $transaction) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to approve this transaction?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success" title="Approve">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <button type="button" 
                                                                class="btn btn-danger" 
                                                                title="Reject"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#rejectModal{{ $transaction->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>

                                                        <!-- Reject Modal -->
                                                        <div class="modal fade" id="rejectModal{{ $transaction->id }}" tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form action="{{ route('office.accounting.transactions.reject', $transaction) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Reject Transaction</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                                                                <textarea name="rejection_reason" 
                                                                                          class="form-control" 
                                                                                          rows="3" 
                                                                                          required 
                                                                                          placeholder="Please provide a reason for rejection"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit" class="btn btn-danger">Reject Transaction</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>No Pending Transactions</h5>
                            <p class="mb-0">There are currently no transactions awaiting approval.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection
