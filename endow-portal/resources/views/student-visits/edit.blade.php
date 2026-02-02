@extends('layouts.admin')

@section('page-title', 'Edit Student Visit')
@section('breadcrumb', 'Home / Student Visits / Edit')

@section('content')
    <div class="mb-3">
        <a href="{{ route('student-visits.index') }}?{{ http_build_query(request()->except(['_token', '_method'])) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Visits
        </a>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-edit text-danger me-2"></i>Edit Student Visit Record
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('student-visits.update', $studentVisit) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Hidden fields to preserve filters -->
                        @if(isset($filters))
                            @foreach($filters as $key => $value)
                                @if($value !== null && $value !== '')
                                    <input type="hidden" name="filter_{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                        @endif

                        <div class="row g-3">
                            <!-- Student Name -->
                            <div class="col-md-12">
                                <label for="student_name" class="form-label fw-semibold">
                                    Student Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('student_name') is-invalid @enderror"
                                       id="student_name"
                                       name="student_name"
                                       value="{{ old('student_name', $studentVisit->student_name) }}"
                                       placeholder="Enter student's full name"
                                       required>
                                @error('student_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Phone Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $studentVisit->phone) }}"
                                           placeholder="+1234567890"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $studentVisit->email) }}"
                                           placeholder="student@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Assigned Employee (Only for Admins) -->
                            @if(Auth::user()->isAdmin() && $employees->count() > 0)
                            <div class="col-md-12">
                                <label for="employee_id" class="form-label fw-semibold">
                                    Assign to Employee
                                </label>
                                <select class="form-select @error('employee_id') is-invalid @enderror"
                                        id="employee_id"
                                        name="employee_id">
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                            {{ old('employee_id', $studentVisit->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <!-- Prospective Status -->
                            <div class="col-md-12">
                                <label for="prospective_status" class="form-label fw-semibold">
                                    Prospective Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('prospective_status') is-invalid @enderror"
                                        id="prospective_status"
                                        name="prospective_status"
                                        required>
                                    <option value="">Select Status</option>
                                    @foreach(\App\Models\StudentVisit::getStatuses() as $status)
                                    <option value="{{ $status }}" {{ old('prospective_status', $studentVisit->prospective_status ?? '') == $status ? 'selected' : '' }}>
                                        {{ \App\Models\StudentVisit::getStatusLabel($status) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('prospective_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Classify the prospective student based on their interest level</small>
                            </div>

                            <!-- Visit Notes -->
                            <div class="col-md-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Visit Notes
                                </label>
                                <div id="quill-editor" style="height: 300px;"></div>
                                <textarea name="notes" id="notes" style="display:none;">{{ old('notes', $studentVisit->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Document the visit details, student queries, requirements, and follow-up actions</small>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fas fa-save me-2"></i>Update Visit Record
                            </button>
                            <a href="{{ route('student-visits.index') }}?{{ http_build_query(request()->except(['_token', '_method'])) }}" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-info-circle text-danger me-2"></i>Visit Information
                    </h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <strong>Created:</strong><br>
                            <span class="text-muted">{{ $studentVisit->created_at->format('M d, Y h:i A') }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Last Updated:</strong><br>
                            <span class="text-muted">{{ $studentVisit->updated_at->format('M d, Y h:i A') }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Assigned To:</strong><br>
                            <span class="text-muted">{{ $studentVisit->employee ? $studentVisit->employee->name : 'Not Assigned' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 300px;
        font-size: 14px;
    }
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: 'Document the visit details, student queries, requirements, and follow-up actions...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Sync Quill content with hidden textarea
        quill.on('text-change', function() {
            document.getElementById('notes').value = quill.root.innerHTML;
        });

        // Set initial content if exists
        var initialContent = document.getElementById('notes').value;
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }
    });
</script>
@endsection

@push('scripts')
<script>
    // Success message will be handled by admin layout's SweetAlert integration
    // Additional form-specific scripts can be added here if needed
</script>
@endpush
