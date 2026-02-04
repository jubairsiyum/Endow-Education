@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-university me-2"></i>Bank Deposit Details</h4>
                    <div>
                        @if($bankDeposit->isPending())
                            <a href="{{ route('office.accounting.bank-deposits.edit', $bankDeposit) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('office.accounting.bank-deposits.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="mb-4">
                        @if($bankDeposit->status == 'pending')
                            <span class="badge bg-warning text-dark fs-5">Pending Approval</span>
                        @elseif($bankDeposit->status == 'approved')
                            <span class="badge bg-success fs-5">Approved</span>
                        @else
                            <span class="badge bg-danger fs-5">Rejected</span>
                        @endif
                    </div>

                    <!-- Deposit Information -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Deposit Date</label>
                            <p class="fs-5">{{ $bankDeposit->deposit_date->format('F d, Y') }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Amount</label>
                            <p class="fs-3 fw-bold text-primary">{{ $bankDeposit->formatted_amount }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Bank Name</label>
                            <p>{{ $bankDeposit->bank_name }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Account Number</label>
                            <p>{{ $bankDeposit->account_number ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Reference Number</label>
                            <p>{{ $bankDeposit->reference_number ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Deposited By</label>
                            <p>{{ $bankDeposit->depositor->name ?? 'N/A' }}</p>
                        </div>

                        @if($bankDeposit->remarks)
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold text-muted">Remarks</label>
                                <p>{{ $bankDeposit->remarks }}</p>
                            </div>
                        @endif

                        @if($bankDeposit->status == 'approved' || $bankDeposit->status == 'rejected')
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold text-muted">{{ $bankDeposit->status == 'approved' ? 'Approved' : 'Rejected' }} By</label>
                                <p>{{ $bankDeposit->approver->name ?? 'N/A' }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold text-muted">{{ $bankDeposit->status == 'approved' ? 'Approved' : 'Rejected' }} At</label>
                                <p>{{ $bankDeposit->approved_at?->format('F d, Y h:i A') }}</p>
                            </div>
                        @endif

                        @if($bankDeposit->status == 'rejected' && $bankDeposit->rejection_reason)
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold text-muted text-danger">Rejection Reason</label>
                                <p class="alert alert-danger">{{ $bankDeposit->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Approval Actions -->
                    @if($bankDeposit->isPending() && auth()->user()->can('approve-transaction'))
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ route('office.accounting.bank-deposits.approve', $bankDeposit) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to approve this deposit?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check-circle"></i> Approve Deposit
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="fas fa-times-circle"></i> Reject Deposit
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('office.accounting.bank-deposits.reject', $bankDeposit) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Reject Deposit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" 
                                  id="rejection_reason" 
                                  class="form-control" 
                                  rows="4" 
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
