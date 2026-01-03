@extends('layouts.admin')

@section('page-title', 'Add Payment - ' . $student->name)
@section('breadcrumb', 'Home / Students / Payments / Add')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h3 mb-0">
                    <i class="fas fa-plus-circle text-success me-2"></i>
                    Add New Payment
                </h2>
                <a href="{{ route('students.payments.index', $student) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Payments
                </a>
            </div>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-0">
                                <i class="fas fa-user me-2 text-primary"></i>
                                <strong>Student:</strong> {{ $student->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                <strong>Email:</strong> {{ $student->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('students.payments.store', $student) }}" method="POST">
                @csrf

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Payment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" 
                                           name="amount" 
                                           id="amount" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount') }}" 
                                           step="0.01" 
                                           min="0" 
                                           required
                                           placeholder="0.00">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="payment_date" class="form-label">
                                    Payment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="payment_date" 
                                       id="payment_date" 
                                       class="form-control @error('payment_date') is-invalid @enderror" 
                                       value="{{ old('payment_date', date('Y-m-d')) }}" 
                                       required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="payment_type" class="form-label">
                                    Payment Type <span class="text-danger">*</span>
                                </label>
                                <select name="payment_type" 
                                        id="payment_type" 
                                        class="form-select @error('payment_type') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Select Payment Type --</option>
                                    <option value="Tuition Fee" {{ old('payment_type') == 'Tuition Fee' ? 'selected' : '' }}>Tuition Fee</option>
                                    <option value="Service Fee" {{ old('payment_type') == 'Service Fee' ? 'selected' : '' }}>Service Fee</option>
                                    <option value="Processing Fee" {{ old('payment_type') == 'Processing Fee' ? 'selected' : '' }}>Processing Fee</option>
                                    <option value="Consultation Fee" {{ old('payment_type') == 'Consultation Fee' ? 'selected' : '' }}>Consultation Fee</option>
                                    <option value="Document Fee" {{ old('payment_type') == 'Document Fee' ? 'selected' : '' }}>Document Fee</option>
                                    <option value="Other" {{ old('payment_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" 
                                        id="payment_method" 
                                        class="form-select @error('payment_method') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Select Payment Method --</option>
                                    <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="bKash" {{ old('payment_method') == 'bKash' ? 'selected' : '' }}>bKash</option>
                                    <option value="Rocket" {{ old('payment_method') == 'Rocket' ? 'selected' : '' }}>Rocket</option>
                                    <option value="Nagad" {{ old('payment_method') == 'Nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="transaction_id" class="form-label">
                                    Transaction ID
                                </label>
                                <input type="text" 
                                       name="transaction_id" 
                                       id="transaction_id" 
                                       class="form-control @error('transaction_id') is-invalid @enderror" 
                                       value="{{ old('transaction_id') }}" 
                                       placeholder="Enter transaction reference">
                                @error('transaction_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional - For bank transfer, mobile banking, etc.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" 
                                        id="status" 
                                        class="form-select @error('status') is-invalid @enderror" 
                                        required>
                                    <option value="Confirmed" {{ old('status', 'Confirmed') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">
                                    Notes
                                </label>
                                <textarea name="notes" 
                                          id="notes" 
                                          class="form-control @error('notes') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Add any additional notes or comments...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('students.payments.index', $student) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i> Save Payment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Helper Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Important Information</h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Payment Recording Guidelines:</h6>
                    <ul class="small mb-0">
                        <li class="mb-2">You will be recorded as the employee who received this payment.</li>
                        <li class="mb-2">Always verify the payment amount before recording.</li>
                        <li class="mb-2">For mobile banking, include the transaction ID for verification.</li>
                        <li class="mb-2">Use "Confirmed" status only after verifying the payment.</li>
                        <li class="mb-2">Add notes for any special circumstances or payment plans.</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i> Recent Payments</h6>
                </div>
                <div class="card-body">
                    @php
                        $recentPayments = $student->payments()->confirmed()->latest('payment_date')->take(3)->get();
                    @endphp
                    @if($recentPayments->count() > 0)
                        @foreach($recentPayments as $payment)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <small class="text-muted">{{ $payment->payment_date->format('M d, Y') }}</small><br>
                                    <strong class="text-success">৳ {{ number_format($payment->amount, 2) }}</strong>
                                </div>
                                <span class="badge bg-primary">{{ $payment->payment_type }}</span>
                            </div>
                        @endforeach
                        <div class="text-center mt-3">
                            <strong>Total Paid:</strong> 
                            <span class="text-success">৳ {{ number_format($student->payments()->confirmed()->sum('amount'), 2) }}</span>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No previous payments</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            html: '<ul style="text-align: left; padding-left: 20px;">' +
                @foreach($errors->all() as $error)
                    '<li>{{ $error }}</li>' +
                @endforeach
                '</ul>',
            confirmButtonColor: '#dc3545'
        });
    @endif
</script>
@endpush

@endsection
