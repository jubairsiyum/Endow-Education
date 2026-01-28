@extends('layouts.admin')

@section('title', 'Account Categories')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-tags text-primary me-2"></i>
                        Account Categories
                    </h2>
                    <p class="text-muted mb-0">Manage income and expense categories for transactions</p>
                </div>
                <div>
                    @can('create-transaction')
                    <a href="{{ route('office.accounting.categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add Category
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total Categories</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm border-start border-success border-3">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-success">{{ $stats['active'] }}</h3>
                    <small class="text-muted">Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm border-start border-danger border-3">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-danger">{{ $stats['inactive'] }}</h3>
                    <small class="text-muted">Inactive</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-3">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-primary">{{ $stats['income'] }}</h3>
                    <small class="text-muted">Income Categories</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-3">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-warning">{{ $stats['expense'] }}</h3>
                    <small class="text-muted">Expense Categories</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Tables -->
    <div class="row">
        <!-- Income Categories -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-arrow-up me-2"></i> Income Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%">Name</th>
                                    <th style="width: 15%" class="text-center">Status</th>
                                    <th style="width: 20%" class="text-center">Transactions</th>
                                    <th style="width: 20%" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories->where('type', 'income') as $category)
                                <tr>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                        @if($category->description)
                                        <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($category->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $category->transactions()->count() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('edit-transaction')
                                            <a href="{{ route('office.accounting.categories.edit', $category) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('office.accounting.categories.toggle-status', $category) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $category->is_active ? 'ban' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            @endcan
                                            @can('delete-transaction')
                                            @if($category->transactions()->count() == 0)
                                            <form action="{{ route('office.accounting.categories.destroy', $category) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this category?');"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No income categories found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Categories -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-arrow-down me-2"></i> Expense Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%">Name</th>
                                    <th style="width: 15%" class="text-center">Status</th>
                                    <th style="width: 20%" class="text-center">Transactions</th>
                                    <th style="width: 20%" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories->where('type', 'expense') as $category)
                                <tr>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                        @if($category->description)
                                        <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($category->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $category->transactions()->count() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('edit-transaction')
                                            <a href="{{ route('office.accounting.categories.edit', $category) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('office.accounting.categories.toggle-status', $category) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $category->is_active ? 'ban' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            @endcan
                                            @can('delete-transaction')
                                            @if($category->transactions()->count() == 0)
                                            <form action="{{ route('office.accounting.categories.destroy', $category) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this category?');"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No expense categories found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
