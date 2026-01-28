@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add New Transaction</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('office.accounting.transactions.store') }}" method="POST" id="transactionForm">
                        @csrf

                        <div class="row">
                            <!-- Transaction Type -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                                    <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Entry Date -->
                            <div class="col-md-6 mb-3">
                                <label for="entry_date" class="form-label">Entry Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       name="entry_date" 
                                       id="entry_date" 
                                       class="form-control @error('entry_date') is-invalid @enderror"
                                       value="{{ old('entry_date', date('Y-m-d')) }}"
                                       required>
                                @error('entry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Transaction Headline -->
                        <div class="mb-3">
                            <label for="headline" class="form-label">Transaction Headline <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="headline" 
                                   id="headline" 
                                   class="form-control @error('headline') is-invalid @enderror"
                                   value="{{ old('headline') }}"
                                   placeholder="Brief description of transaction"
                                   required>
                            @error('headline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Employee Selection -->
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Responsible Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                <optgroup label="Income Categories" id="income-categories" style="display: none;">
                                    @foreach($categories->where('type', 'income') as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Expense Categories" id="expense-categories" style="display: none;">
                                    @foreach($categories->where('type', 'expense') as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('category_id')
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
                                <small class="text-muted" id="conversion-note" style="display: none;">
                                    This will be automatically converted to BDT using today's exchange rate.
                                </small>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Income-specific fields -->
                        <div id="income-fields" style="display: none;">
                            <div class="row">
                                <!-- Student Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="student_name" class="form-label">Student Name</label>
                                    <select name="student_name" 
                                            id="student_name" 
                                            class="form-control @error('student_name') is-invalid @enderror"
                                            style="width: 100%;">
                                        @if(old('student_name'))
                                            <option value="{{ old('student_name') }}" selected>{{ old('student_name') }}</option>
                                        @endif
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Search by name, email, or ID. You can also type a custom name.
                                    </small>
                                    @error('student_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Payment Method -->
                                <div class="col-md-6 mb-3">
                                    <label for="payment_method" class="form-label">Payment Method <span class="text-danger income-required">*</span></label>
                                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                        <option value="">Select Payment Method</option>
                                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="Check" {{ old('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
                                        <option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="Debit Card" {{ old('payment_method') == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                                        <option value="Mobile Payment" {{ old('payment_method') == 'Mobile Payment' ? 'selected' : '' }}>Mobile Payment</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" 
                                      id="remarks" 
                                      class="form-control @error('remarks') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Enter any additional notes or remarks">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('office.accounting.transactions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-results__option {
        padding: 8px 12px;
    }
    .select2-results__option .student-info {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }
    .select2-results__option .student-name {
        font-weight: 600;
        color: #212529;
    }
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const categorySelect = document.getElementById('category_id');
        const currencySelect = document.getElementById('currency');
        const conversionNote = document.getElementById('conversion-note');
        const incomeFields = document.getElementById('income-fields');
        const incomeCategories = document.getElementById('income-categories');
        const expenseCategories = document.getElementById('expense-categories');
        const paymentMethodInput = document.getElementById('payment_method');

        // Initialize Select2 for student name with search functionality
        const studentNameSelect = $('#student_name').select2({
            theme: 'bootstrap-5',
            placeholder: 'Search student by name, email, or ID',
            allowClear: true,
            tags: true, // Allow custom input
            ajax: {
                url: '{{ route("office.accounting.transactions.searchStudents") }}',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(student) {
                            return {
                                id: student.text, // Use name as value
                                text: student.text,
                                email: student.email,
                                university: student.university,
                                program: student.program,
                                registration_id: student.registration_id
                            };
                        })
                    };
                },
                cache: true
            },
            templateResult: function(student) {
                if (student.loading) {
                    return student.text;
                }
                
                if (!student.email) {
                    return student.text; // For custom input
                }

                var $container = $(
                    '<div class="student-option">' +
                        '<div class="student-name">' + student.text + '</div>' +
                        '<div class="student-info">' +
                            '<i class="fas fa-envelope"></i> ' + student.email + ' | ' +
                            '<i class="fas fa-university"></i> ' + student.university + ' | ' +
                            '<i class="fas fa-graduation-cap"></i> ' + student.program +
                        '</div>' +
                    '</div>'
                );

                return $container;
            },
            templateSelection: function(student) {
                return student.text;
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term + ' (Custom)',
                    isCustom: true
                };
            }
        });

        // Function to toggle fields based on transaction type
        function toggleFieldsByType() {
            const type = typeSelect.value;
            
            // Reset category selection
            categorySelect.value = '';
            
            if (type === 'income') {
                // Show income-specific fields
                incomeFields.style.display = 'block';
                incomeCategories.style.display = 'block';
                expenseCategories.style.display = 'none';
                
                // Make income fields required
                paymentMethodInput.required = true;
            } else if (type === 'expense') {
                // Hide income-specific fields
                incomeFields.style.display = 'none';
                incomeCategories.style.display = 'none';
                expenseCategories.style.display = 'block';
                
                // Remove required attribute and clear values
                paymentMethodInput.required = false;
                paymentMethodInput.value = '';
                studentNameSelect.val(null).trigger('change'); // Clear student name
            } else {
                // No type selected
                incomeFields.style.display = 'none';
                incomeCategories.style.display = 'none';
                expenseCategories.style.display = 'none';
                
                paymentMethodInput.required = false;
            }
        }

        // Function to toggle currency conversion note
        function toggleCurrencyNote() {
            const currency = currencySelect.value;
            if (currency !== 'BDT') {
                conversionNote.style.display = 'block';
            } else {
                conversionNote.style.display = 'none';
            }
        }

        // Listen for type changes
        typeSelect.addEventListener('change', toggleFieldsByType);
        
        // Listen for currency changes
        currencySelect.addEventListener('change', toggleCurrencyNote);

        // Initialize on page load (for old() values)
        toggleFieldsByType();
        toggleCurrencyNote();
    });
</script>
@endpush
@endsection
