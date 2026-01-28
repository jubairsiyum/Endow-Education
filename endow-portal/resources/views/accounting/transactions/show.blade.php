@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Transaction Details</h4>
                    <div>
                        <span class="badge bg-{{ $transaction->type == 'income' ? 'success' : 'danger' }} fs-6">
                            {{ ucfirst($transaction->type) }}
                        </span>
                        @if($transaction->status == 'pending')
                            <span class="badge bg-warning text-dark fs-6">Pending</span>
                        @elseif($transaction->status == 'approved')
                            <span class="badge bg-success fs-6">Approved</span>
                        @else
                            <span class="badge bg-danger fs-6">Rejected</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Category</h6>
                            <p class="fs-5">{{ $transaction->category->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Amount</h6>
                            <p class="fs-5 fw-bold text-{{ $transaction->type == 'income' ? 'success' : 'danger' }}">
                                {{ number_format($transaction->amount, 2) }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Entry Date</h6>
                            <p>{{ $transaction->entry_date->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Created By</h6>
                            <p>{{ $transaction->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($transaction->type == 'income')
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Student Name</h6>
                                <p>{{ $transaction->student_name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Payment Method</h6>
                                <p>{{ $transaction->payment_method ?? '-' }}</p>
                            </div>
                        </div>
                    @endif

                    @if($transaction->remarks)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-1">Remarks</h6>
                                <p>{{ $transaction->remarks }}</p>
                            </div>
                        </div>
                    @endif

                    @if($transaction->isApproved() || $transaction->isRejected())
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">{{ $transaction->isApproved() ? 'Approved' : 'Rejected' }} By</h6>
                                <p>{{ $transaction->approver->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">{{ $transaction->isApproved() ? 'Approved' : 'Rejected' }} At</h6>
                                <p>{{ $transaction->approved_at ? $transaction->approved_at->format('F d, Y h:i A') : 'N/A' }}</p>
                            </div>
                        </div>
                    @endif

                    @if($transaction->isRejected() && $transaction->rejection_reason)
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">Rejection Reason</h6>
                            <p class="mb-0">{{ $transaction->rejection_reason }}</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('office.accounting.transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <div>
                            @if($transaction->isPending())
                                @can('update-transaction')
                                    <a href="{{ route('office.accounting.transactions.edit', $transaction) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endcan
                                @can('delete-transaction')
                                    <form action="{{ route('office.accounting.transactions.destroy', $transaction) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
