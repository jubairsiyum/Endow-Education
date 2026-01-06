@extends('layouts.admin')

@section('page-title', 'Checklist Items')
@section('breadcrumb', 'Home / Configuration / Checklist Items')

@section('content')
<div style="width: 100%; max-width: 100%;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-clipboard-check text-danger"></i> Document Checklist Items</h4>
            <small class="text-muted">Manage required documents for student applications</small>
        </div>
        @can('create checklists')
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-1"></i> Add Item
        </button>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Checklist Items Table -->
    <div class="card shadow-sm border-0" style="width: 100%;">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="width: 100%;">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem;">
                        <th style="width: 80px;" class="text-center">Order</th>
                        <th style="width: 25%;">Document Name</th>
                        <th style="width: 40%;">Description</th>
                        <th style="width: 100px;">Required</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 180px;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-checklist">
                    @forelse($checklistItems ?? [] as $item)
                    <tr data-id="{{ $item->id }}">
                        <td class="text-center">
                            <i class="fas fa-grip-vertical text-muted" style="cursor: move;"></i>
                            <span class="ms-2 badge bg-secondary">{{ $item->order }}</span>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $item->title }}</strong>
                        </td>
                        <td>
                            <small class="text-muted">{{ $item->description ?? 'No description' }}</small>
                        </td>
                        <td>
                            @if($item->is_required)
                                <span class="badge bg-danger">Required</span>
                            @else
                                <span class="badge bg-secondary">Optional</span>
                            @endif
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                @can('update checklists')
                                <button class="btn btn-outline-primary" title="Edit"
                                        onclick="editItem({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ addslashes($item->description ?? '') }}', {{ $item->is_required ? 'true' : 'false' }}, {{ $item->is_active ? 'true' : 'false' }})">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('checklist-items.update', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="title" value="{{ $item->title }}">
                                    <input type="hidden" name="description" value="{{ $item->description }}">
                                    <input type="hidden" name="is_required" value="{{ $item->is_required ? '1' : '0' }}">
                                    <input type="hidden" name="is_active" value="{{ $item->is_active ? '0' : '1' }}">
                                    <button type="submit" class="btn btn-outline-{{ $item->is_active ? 'warning' : 'success' }}"
                                            title="{{ $item->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $item->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                @endcan

                                @can('delete checklists')
                                <form action="{{ route('checklist-items.destroy', $item) }}" method="POST" class="d-inline" id="delete-checklist-form-{{ $item->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                            onclick="confirmDeleteChecklistItem({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="my-4">
                                <i class="fas fa-clipboard-check fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No checklist items found</p>
                                @can('create checklists')
                                <button class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#createModal">
                                    <i class="fas fa-plus me-1"></i> Add Your First Item
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('checklist-items.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add Checklist Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Document Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title') }}" required
                                   placeholder="e.g., Passport Copy, Transcripts">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter the name of the document students need to upload</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Provide detailed instructions or requirements...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: Add instructions for students</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required"
                                       value="1" {{ old('is_required') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_required">
                                    Required Document
                                </label>
                            </div>
                            <small class="text-muted d-block ms-4">Students must upload this to complete their application</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active
                                </label>
                            </div>
                            <small class="text-muted d-block ms-4">Only active items appear in student checklists</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-1"></i> Create Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
                        {{-- <button type="submit" class="btn btn-primary-custom">Create Item</button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Checklist Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label fw-semibold">Document Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_required" name="is_required" value="1">
                                <label class="form-check-label fw-semibold" for="edit_is_required">
                                    Required Document
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                                <label class="form-check-label fw-semibold" for="edit_is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function editItem(id, title, description, isRequired, isActive) {
        const form = document.getElementById('editForm');
        form.action = `/checklist-items/${id}`;

        document.getElementById('edit_title').value = title;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_is_required').checked = isRequired;
        document.getElementById('edit_is_active').checked = isActive;

        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    }

    // Reopen modal if there are validation errors
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            const createModal = new bootstrap.Modal(document.getElementById('createModal'));
            createModal.show();
        });
    @endif

    function confirmDeleteChecklistItem(itemId) {
        Swal.fire({
            title: 'Delete Checklist Item?',
            text: 'Are you sure? This will affect all students.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC143C',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-checklist-form-' + itemId).submit();
            }
        });
    }
    </script>
</div>
@endsection
