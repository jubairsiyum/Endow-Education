@extends('layouts.admin')

@section('page-title', 'Payment History - ' . $student->name)
@section('breadcrumb', 'Home / Students / Payments')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-money-bill-wave text-success me-2"></i>
                        Payment History
                    </h2>
                    <p class="text-muted mb-0">{{ $student->name }} - {{ $student->email }}</p>
                </div>
                <div>
                    <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i> Back to Student
                    </a>
                    <a href="{{ route('students.payments.create', $student) }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i> Add Payment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-check-circle text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Paid</h6>
                            <h3 class="mb-0">৳ {{ number_format($totalPaid, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-receipt text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Payments</h6>
                            <h3 class="mb-0">{{ $payments->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-clock text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3 class="mb-0">{{ $student->payments()->pending()->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-calendar text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Last Payment</h6>
                            <h3 class="mb-0 small">{{ $payments->first()?->payment_date?->format('M d, Y') ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Payment Records</h5>
                </div>
                <div class="card-body p-0">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Payment Type</th>
                                        <th>Payment Method</th>
                                        <th>Transaction ID</th>
                                        <th>Status</th>
                                        <th>Received By</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <i class="fas fa-calendar me-2 text-muted"></i>
                                                {{ $payment->payment_date->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <strong class="text-success">৳ {{ number_format($payment->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $payment->payment_type }}</span>
                                            </td>
                                            <td>
                                                <i class="fas fa-wallet me-2 text-muted"></i>
                                                {{ $payment->payment_method }}
                                            </td>
                                            <td>
                                                <code class="text-muted">{{ $payment->transaction_id ?? 'N/A' }}</code>
                                            </td>
                                            <td>
                                                @if($payment->status === 'Confirmed')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Confirmed
                                                    </span>
                                                @elseif($payment->status === 'Pending')
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Cancelled
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <i class="fas fa-user me-2 text-muted"></i>
                                                {{ $payment->receivedBy->name }}
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('students.payments.edit', [$student, $payment]) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('students.payments.destroy', [$student, $payment]) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirmDelete(event, '{{ $payment->amount }}', '{{ $payment->payment_date->format('M d, Y') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @if($payment->notes)
                                            <tr class="bg-light">
                                                <td colspan="8" class="py-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-sticky-note me-2"></i>
                                                        <strong>Notes:</strong> {{ $payment->notes }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No payment records found for this student.</p>
                            <a href="{{ route('students.payments.create', $student) }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i> Add First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(event, amount, date) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Delete Payment?',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Amount:</strong> ৳${amount}</p>
                    <p><strong>Date:</strong> ${date}</p>
                    <hr>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete',
            cancelButtonText: '<i class="fas fa-times"></i> Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
        
        return false;
    }

    // SweetAlert for success/error messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#28a745'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#dc3545'
        });
    @endif
</script>
@endpush

@endsection
