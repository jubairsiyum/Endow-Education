@extends('layouts.admin')

@section('page-title', 'Edit Payment - ' . $student->name)
@section('breadcrumb', 'Home / Students / Payments / Edit')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h3 mb-0">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Payment
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
            <form action="{{ route('students.payments.update', [$student, $payment]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
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
                                           value="{{ old('amount', $payment->amount) }}" 
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
                                       value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" 
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
                                    <option value="Tuition Fee" {{ old('payment_type', $payment->payment_type) == 'Tuition Fee' ? 'selected' : '' }}>Tuition Fee</option>
                                    <option value="Service Fee" {{ old('payment_type', $payment->payment_type) == 'Service Fee' ? 'selected' : '' }}>Service Fee</option>
                                    <option value="Processing Fee" {{ old('payment_type', $payment->payment_type) == 'Processing Fee' ? 'selected' : '' }}>Processing Fee</option>
                                    <option value="Consultation Fee" {{ old('payment_type', $payment->payment_type) == 'Consultation Fee' ? 'selected' : '' }}>Consultation Fee</option>
                                    <option value="Document Fee" {{ old('payment_type', $payment->payment_type) == 'Document Fee' ? 'selected' : '' }}>Document Fee</option>
                                    <option value="Other" {{ old('payment_type', $payment->payment_type) == 'Other' ? 'selected' : '' }}>Other</option>
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
                                    <option value="Cash" {{ old('payment_method', $payment->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Bank Transfer" {{ old('payment_method', $payment->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="bKash" {{ old('payment_method', $payment->payment_method) == 'bKash' ? 'selected' : '' }}>bKash</option>
                                    <option value="Rocket" {{ old('payment_method', $payment->payment_method) == 'Rocket' ? 'selected' : '' }}>Rocket</option>
                                    <option value="Nagad" {{ old('payment_method', $payment->payment_method) == 'Nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="Other" {{ old('payment_method', $payment->payment_method) == 'Other' ? 'selected' : '' }}>Other</option>
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
                                       value="{{ old('transaction_id', $payment->transaction_id) }}" 
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
                                    <option value="Confirmed" {{ old('status', $payment->status) == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="Pending" {{ old('status', $payment->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Cancelled" {{ old('status', $payment->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                          placeholder="Add any additional notes or comments...">{{ old('notes', $payment->notes) }}</textarea>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Payment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Payment Information</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Originally Recorded By:</strong><br>
                        <i class="fas fa-user me-2 text-muted"></i>{{ $payment->receivedBy->name }}
                    </p>
                    <p class="mb-2">
                        <strong>Created On:</strong><br>
                        <i class="fas fa-calendar me-2 text-muted"></i>{{ $payment->created_at->format('M d, Y h:i A') }}
                    </p>
                    @if($payment->updated_at != $payment->created_at)
                        <p class="mb-0">
                            <strong>Last Updated:</strong><br>
                            <i class="fas fa-clock me-2 text-muted"></i>{{ $payment->updated_at->format('M d, Y h:i A') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Important</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-0">
                        Please ensure all payment details are accurate before updating. Changes will be logged and tracked for accountability.
                    </p>
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
