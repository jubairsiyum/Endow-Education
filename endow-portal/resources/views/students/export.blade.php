@extends('layouts.admin')

@section('title', 'Export Students')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Export Students to CSV</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
        <li class="breadcrumb-item active">Export</li>
    </ol>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-export me-1"></i>
                    Configure Export
                </div>
                <div class="card-body">
                    <form action="{{ route('students.export') }}" method="POST" id="exportForm">
                        @csrf

                        <!-- Hidden field for student IDs -->
                        @if(!empty($studentIds) && count($studentIds) > 0)
                            @foreach($studentIds as $id)
                                <input type="hidden" name="student_ids[]" value="{{ $id }}">
                            @endforeach
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Export Summary:</strong>
                            @if(!empty($studentIds) && count($studentIds) > 0)
                                You are exporting <strong>{{ $exportCount }}</strong> selected student(s).
                            @else
                                You are exporting <strong>all {{ $exportCount }}</strong> students.
                            @endif
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Select Columns to Export</h5>
                            <p class="text-muted small mb-3">Choose which fields you want to include in the CSV export.</p>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-bold" for="selectAll">
                                    Select All Columns
                                </label>
                            </div>

                            <hr class="my-3">

                            <div class="row">
                                @foreach($availableColumns as $key => $label)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox"
                                                   type="checkbox"
                                                   name="columns[]"
                                                   value="{{ $key }}"
                                                   id="col_{{ $key }}"
                                                   {{ in_array($key, ['id', 'name', 'email', 'phone', 'status']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="col_{{ $key }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-download me-1"></i> Export to CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-lightbulb me-1"></i>
                    Export Tips
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li class="mb-2">The CSV file will be encoded in UTF-8 with BOM for proper Excel compatibility.</li>
                        <li class="mb-2">Large exports are handled efficiently using streaming to avoid memory issues.</li>
                        <li class="mb-2">Select only the columns you need to keep the file size manageable.</li>
                        <li class="mb-2">The exported file will include a timestamp in its name for easy identification.</li>
                        <li class="mb-0">You can open the CSV file in Excel, Google Sheets, or any spreadsheet application.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const columnCheckboxes = document.querySelectorAll('.column-checkbox');

    // Select/Deselect all functionality
    selectAllCheckbox.addEventListener('change', function() {
        columnCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    // Update "Select All" based on individual checkboxes
    columnCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(columnCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(columnCheckboxes).some(cb => cb.checked);

            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });

    // Initialize Select All state
    const allChecked = Array.from(columnCheckboxes).every(cb => cb.checked);
    const someChecked = Array.from(columnCheckboxes).some(cb => cb.checked);
    selectAllCheckbox.checked = allChecked;
    selectAllCheckbox.indeterminate = someChecked && !allChecked;

    // Form validation
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        const checkedColumns = Array.from(columnCheckboxes).filter(cb => cb.checked);

        if (checkedColumns.length === 0) {
            e.preventDefault();
            alert('Please select at least one column to export.');
            return false;
        }
    });
});
</script>
@endpush
@endsection
