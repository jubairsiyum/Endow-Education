@extends('layouts.admin')

@section('page-title', 'Checklist Items')
@section('breadcrumb', 'Home / Configuration / Checklist Items')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Checklist Items</h1>
            <p class="page-subtitle">Manage checklist templates for student applications</p>
        </div>
        @can('create checklists')
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-2"></i> Add Checklist Item
        </button>
        @endcan
    </div>

    <!-- Checklist Items Table -->
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th width="50px">Order</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Required</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-checklist">
                    @forelse($checklistItems ?? [] as $item)
                    <tr data-id="{{ $item->id }}">
                        <td class="text-center">
                            <i class="fas fa-grip-vertical text-muted" style="cursor: move;"></i>
                            <span class="ms-2">{{ $item->order }}</span>
                        </td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                        </td>
                        <td>
                            <small class="text-muted">{{ $item->description ?? 'No description' }}</small>
                        </td>
                        <td>
                            @if($item->is_required)
                                <span class="badge-custom badge-danger-custom">Required</span>
                            @else
                                <span class="badge-custom badge-secondary-custom">Optional</span>
                            @endif
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge-custom badge-success-custom">Active</span>
                            @else
                                <span class="badge-custom badge-secondary-custom">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @can('update checklists')
                                <button class="action-btn edit" title="Edit" 
                                        onclick="editItem({{ $item->id }}, '{{ $item->name }}', '{{ $item->description }}', {{ $item->is_required ? 'true' : 'false' }}, {{ $item->is_active ? 'true' : 'false' }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="{{ route('checklist-items.update', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_active" value="{{ $item->is_active ? '0' : '1' }}">
                                    <button type="submit" class="action-btn {{ $item->is_active ? 'warning' : 'success' }}" 
                                            title="{{ $item->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $item->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                @endcan

                                @can('delete checklists')
                                <form action="{{ route('checklist-items.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Delete"
                                            onclick="return confirm('Are you sure? This will affect all students.');">
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
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No checklist items found</p>
                            @can('create checklists')
                            <button class="btn btn-primary-custom mt-3" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="fas fa-plus me-2"></i> Add Your First Item
                            </button>
                            @endcan
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
                    <div class="modal-header">
                        <h5 class="modal-title">Add Checklist Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1">
                                <label class="form-check-label" for="is_required">
                                    Required Item
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Create Item</button>
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
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Checklist Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_required" name="is_required" value="1">
                                <label class="form-check-label" for="edit_is_required">
                                    Required Item
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function editItem(id, name, description, isRequired, isActive) {
        document.getElementById('editForm').action = `/checklist-items/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_is_required').checked = isRequired;
        document.getElementById('edit_is_active').checked = isActive;
        
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    }
</script>
@endpush
