@extends('layouts.admin')

@section('title', 'Add Account Category')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h3 mb-0">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    Add New Category
                </h2>
                <a href="{{ route('office.accounting.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('office.accounting.categories.store') }}" method="POST">
                @csrf

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Category Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
                                   required 
                                   placeholder="e.g., Office Supplies">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Optional description of this category">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       class="form-check-input" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (allow this category to be used in transactions)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('office.accounting.categories.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Create Category
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Guidelines</h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Category Naming Tips:</h6>
                    <ul class="small mb-0">
                        <li class="mb-2">Use clear, descriptive names</li>
                        <li class="mb-2">Be consistent with naming conventions</li>
                        <li class="mb-2">Avoid duplicate or similar names</li>
                        <li class="mb-2">Use title case (e.g., "Office Equipment")</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="mb-3">Income vs Expense:</h6>
                    <div class="small">
                        <p><strong>Income:</strong> Money received (tuition fees, service charges, etc.)</p>
                        <p><strong>Expense:</strong> Money spent (salaries, rent, utilities, etc.)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
