@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Bank Deposit</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('office.accounting.bank-deposits.update', $bankDeposit) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Deposit Date -->
                        <div class="mb-3">
                            <label for="deposit_date" class="form-label">Deposit Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="deposit_date" 
                                   id="deposit_date" 
                                   class="form-control @error('deposit_date') is-invalid @enderror"
                                   value="{{ old('deposit_date', $bankDeposit->deposit_date->format('Y-m-d')) }}"
                                   required>
                            @error('deposit_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bank Name -->
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="bank_name" 
                                   id="bank_name" 
                                   class="form-control @error('bank_name') is-invalid @enderror"
                                   value="{{ old('bank_name', $bankDeposit->bank_name) }}"
                                   required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Account Number -->
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" 
                                   name="account_number" 
                                   id="account_number" 
                                   class="form-control @error('account_number') is-invalid @enderror"
                                   value="{{ old('account_number', $bankDeposit->account_number) }}">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Currency and Amount -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                <select name="currency" id="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                    <option value="BDT" {{ old('currency', $bankDeposit->currency) == 'BDT' ? 'selected' : '' }}>BDT (৳)</option>
                                    <option value="USD" {{ old('currency', $bankDeposit->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="KRW" {{ old('currency', $bankDeposit->currency) == 'KRW' ? 'selected' : '' }}>KRW (₩)</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" 
                                       name="amount" 
                                       id="amount" 
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', $bankDeposit->amount) }}"
                                       step="0.01"
                                       min="0.01"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Reference Number -->
                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number / Slip Number</label>
                            <input type="text" 
                                   name="reference_number" 
                                   id="reference_number" 
                                   class="form-control @error('reference_number') is-invalid @enderror"
                                   value="{{ old('reference_number', $bankDeposit->reference_number) }}">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remarks -->
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" 
                                      id="remarks" 
                                      class="form-control @error('remarks') is-invalid @enderror"
                                      rows="3">{{ old('remarks', $bankDeposit->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('office.accounting.bank-deposits.show', $bankDeposit) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Deposit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
