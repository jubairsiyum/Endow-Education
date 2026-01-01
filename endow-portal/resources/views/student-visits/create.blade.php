@extends('layouts.admin')

@section('page-title', 'New Student Visit')
@section('breadcrumb', 'Home / Student Visits / Create')

@section('content')
    <div class="mb-3">
        <a href="{{ route('student-visits.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Visits
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-clipboard-list text-danger me-2"></i>New Student Visit Record
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('student-visits.store') }}" method="POST">
                        @csrf

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
                                       value="{{ old('student_name') }}"
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
                                           value="{{ old('phone') }}"
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
                                           value="{{ old('email') }}"
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
                                    <option value="">Select Employee (Default: You)</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to assign to yourself</small>
                            </div>
                            @endif

                            <!-- Visit Notes -->
                            <div class="col-md-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Visit Notes
                                </label>
                                <textarea class="form-control tinymce-editor @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="10">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Document the visit details, student queries, requirements, and follow-up actions</small>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fas fa-save me-2"></i>Save Visit Record
                            </button>
                            <a href="{{ route('student-visits.index') }}" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-info-circle text-danger me-2"></i>Quick Tips
                    </h6>
                    <ul class="small text-muted mb-0" style="line-height: 1.8;">
                        <li>Enter complete student name for easy identification</li>
                        <li>Phone number is mandatory for follow-up</li>
                        <li>Use notes field to document all visit details</li>
                        <li>Add bullet points, links, and formatting in notes</li>
                        <li>Record can be updated later if needed</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Best Practices
                    </h6>
                    <ul class="small text-muted mb-0" style="line-height: 1.8;">
                        <li>Document student's interests and goals</li>
                        <li>Note any specific university preferences</li>
                        <li>Record timeline expectations</li>
                        <li>Add follow-up reminders in notes</li>
                        <li>Track document requirements discussed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/4wswwg07jpmzsbi0dwn2j5tk4zky0ofs2539l59f7eolbl5l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '.tinymce-editor',
            height: 400,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic underline | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | link | removeformat | help',
            content_style: 'body { font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif; font-size: 14px; }',
            branding: false,
        });
    });
</script>
@endsection
