@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-university me-2"></i>Record Deposit to Bank</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle"></i> <strong>Note:</strong> This feature tracks cash deposits to the bank. It does not affect profit/loss calculations.
                            </div>
                        </div>
                    </div>

                    <!-- Available Cash Alert -->
                    <div class="alert alert-success mb-3">
                        <div class="mb-2">
                            <i class="fas fa-money-bill-wave"></i> <strong>Available Cash on Hand:</strong>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white">
                                    <small class="text-muted d-block">BDT</small>
                                    <h5 class="mb-0 text-success">৳ {{ number_format($availableCashBDT ?? 0, 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white">
                                    <small class="text-muted d-block">USD</small>
                                    <h5 class="mb-0 text-success">$ {{ number_format($availableCashUSD ?? 0, 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white">
                                    <small class="text-muted d-block">KRW</small>
                                    <h5 class="mb-0 text-success">₩ {{ number_format($availableCashKRW ?? 0, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Maximum amount you can deposit to bank per currency</small>
                    </div>

                    <form action="{{ route('office.accounting.bank-deposits.store') }}" method="POST">
                        @csrf

                        <!-- Deposit Date -->
                        <div class="mb-3">
                            <label for="deposit_date" class="form-label">Deposit Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="deposit_date" 
                                   id="deposit_date" 
                                   class="form-control @error('deposit_date') is-invalid @enderror"
                                   value="{{ old('deposit_date', date('Y-m-d')) }}"
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
                                   value="{{ old('bank_name') }}"
                                   placeholder="e.g., Dutch Bangla Bank, HSBC, etc."
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
                                   value="{{ old('account_number') }}"
                                   placeholder="Optional">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Currency and Amount -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                <select name="currency" id="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                    <option value="BDT" {{ old('currency', 'BDT') == 'BDT' ? 'selected' : '' }}>BDT (৳)</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="KRW" {{ old('currency') == 'KRW' ? 'selected' : '' }}>KRW (₩)</option>
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
                                       value="{{ old('amount') }}"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="0.00"
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
                                   value="{{ old('reference_number') }}"
                                   placeholder="Deposit slip or reference number">
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
                                      rows="3"
                                      placeholder="Any additional notes...">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('office.accounting.bank-deposits.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Record Deposit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
