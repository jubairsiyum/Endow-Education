@extends('layouts.admin')

@section('page-title', 'Edit Student')
@section('breadcrumb', 'Home / Students / Edit')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="fas fa-user-edit text-danger"></i> Edit Student
            </h4>
            <small class="text-muted">Update student information</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('students.show', $student) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-eye me-1"></i> View Profile
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('students.update', $student) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card shadow-sm border-0 mb-4 card-hover">
                    <div class="card-header bg-gradient border-0">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fas fa-user-circle text-danger me-2"></i>Personal Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Full Name - Full Width for Prominence -->
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="fas fa-id-card text-muted me-2"></i>Full Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $student->name) }}"
                                       placeholder="Enter student's full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Information Row -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">
                                            <i class="fas fa-envelope text-muted me-2"></i>Email Address
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email', $student->email) }}"
                                               placeholder="student@example.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-semibold">
                                            <i class="fas fa-phone text-muted me-2"></i>Phone Number
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone', $student->phone) }}"
                                               placeholder="+880 1XXX-XXXXXX" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="col-12">
                                <label for="country" class="form-label fw-semibold">
                                    <i class="fas fa-globe text-muted me-2"></i>Country of Origin
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                       id="country" name="country" value="{{ old('country', $student->country) }}"
                                       placeholder="e.g., Bangladesh, India, Pakistan" required>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Student's country of citizenship
                                </small>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Passport Information -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="passport_number" class="form-label fw-semibold">
                                            <i class="fas fa-passport text-muted me-2"></i>Passport Number
                                        </label>
                                        <input type="text" class="form-control @error('passport_number') is-invalid @enderror"
                                               id="passport_number" name="passport_number" value="{{ old('passport_number', $student->passport_number) }}"
                                               placeholder="e.g., A12345678">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Student's passport number
                                        </small>
                                        @error('passport_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="passport_expiry_date" class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt text-muted me-2"></i>Passport Expiry Date
                                        </label>
                                        <input type="date" class="form-control @error('passport_expiry_date') is-invalid @enderror"
                                               id="passport_expiry_date" name="passport_expiry_date" value="{{ old('passport_expiry_date', $student->passport_expiry_date?->format('Y-m-d')) }}">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Passport expiration date
                                        </small>
                                        @error('passport_expiry_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-university me-2 text-danger"></i>Academic Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong> Changing the program will update the student's required document checklist. Any pending (not submitted) checklist items will be removed and replaced with the new program's requirements.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="target_university_id" class="form-label">Target University</label>
                                <select class="form-select @error('target_university_id') is-invalid @enderror"
                                        id="target_university_id" name="target_university_id">
                                    <option value="">Select University</option>
                                    @foreach($universities as $university)
                                    <option value="{{ $university->id }}"
                                            {{ old('target_university_id', $student->target_university_id) == $university->id ? 'selected' : '' }}>
                                        {{ $university->name }} ({{ $university->country }})
                                    </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Select the university this student is targeting</small>
                                @error('target_university_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="target_program_id" class="form-label">Target Program</label>
                                <select class="form-select @error('target_program_id') is-invalid @enderror"
                                        id="target_program_id" name="target_program_id">
                                    <option value="">Select University First</option>
                                    <!-- Programs will be loaded dynamically based on university -->
                                </select>
                                <small class="form-text text-muted">Select the program this student is targeting</small>
                                @error('target_program_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="course" class="form-label">Additional Course Notes</label>
                                <input type="text" class="form-control @error('course') is-invalid @enderror"
                                       id="course" name="course" value="{{ old('course', $student->course) }}"
                                       placeholder="Any additional course/program information">
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Educational Background Section -->
                            <div class="col-12"><hr class="my-3"></div>
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3"><i class="fas fa-graduation-cap me-2 text-primary"></i>Educational Background</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="ssc_year" class="form-label">SSC Year</label>
                                <input type="text" class="form-control @error('ssc_year') is-invalid @enderror"
                                       id="ssc_year" name="ssc_year" value="{{ old('ssc_year', $student->ssc_year) }}"
                                       placeholder="e.g., 2018" maxlength="4">
                                <small class="form-text text-muted">4-digit year</small>
                                @error('ssc_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="ssc_result" class="form-label">SSC Result</label>
                                <input type="text" class="form-control @error('ssc_result') is-invalid @enderror"
                                       id="ssc_result" name="ssc_result" value="{{ old('ssc_result', $student->ssc_result) }}"
                                       placeholder="e.g., 5.00, A+">
                                @error('ssc_result')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hsc_year" class="form-label">HSC Year</label>
                                <input type="text" class="form-control @error('hsc_year') is-invalid @enderror"
                                       id="hsc_year" name="hsc_year" value="{{ old('hsc_year', $student->hsc_year) }}"
                                       placeholder="e.g., 2020" maxlength="4">
                                <small class="form-text text-muted">4-digit year</small>
                                @error('hsc_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hsc_result" class="form-label">HSC Result</label>
                                <input type="text" class="form-control @error('hsc_result') is-invalid @enderror"
                                       id="hsc_result" name="hsc_result" value="{{ old('hsc_result', $student->hsc_result) }}"
                                       placeholder="e.g., 5.00, A+">
                                @error('hsc_result')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- IELTS Section -->
                            <div class="col-12"><hr class="my-3"></div>
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3"><i class="fas fa-language me-2 text-success"></i>IELTS Information</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="has_ielts" class="form-label">Has IELTS?</label>
                                <select class="form-select @error('has_ielts') is-invalid @enderror"
                                        id="has_ielts" name="has_ielts">
                                    <option value="0" {{ old('has_ielts', $student->has_ielts) == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('has_ielts', $student->has_ielts) == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('has_ielts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="ielts_score" class="form-label">IELTS Score</label>
                                <input type="text" class="form-control @error('ielts_score') is-invalid @enderror"
                                       id="ielts_score" name="ielts_score" value="{{ old('ielts_score', $student->ielts_score) }}"
                                       placeholder="e.g., 7.0, 6.5" {{ old('has_ielts', $student->has_ielts) == '0' ? 'disabled' : '' }}>
                                <small class="form-text text-muted">Enter score if IELTS taken</small>
                                @error('ielts_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12"><hr class="my-3"></div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Application Status</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status">
                                    <option value="new" {{ old('status', $student->status) == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ old('status', $student->status) == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="processing" {{ old('status', $student->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="applied" {{ old('status', $student->status) == 'applied' ? 'selected' : '' }}>Applied</option>
                                    <option value="approved" {{ old('status', $student->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status', $student->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label">Assign Counselor</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror"
                                        id="assigned_to" name="assigned_to">
                                    <option value="">-- Select Counselor --</option>
                                    @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}"
                                            {{ old('assigned_to', $student->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="4">{{ old('notes', $student->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-2"></i> Update Student
                    </button>
                    <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    @can('delete', $student)
                    <button type="button" class="btn btn-outline-danger ms-auto"
                            onclick="confirmDeleteStudentEdit()">
                        <i class="fas fa-trash me-2"></i> Delete Student
                    </button>
                    @endcan
                </div>
            </form>

            @can('delete', $student)
            <form id="delete-form" action="{{ route('students.destroy', $student) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
            @endcan
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-info-circle me-2 text-info"></i>Student Status</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Account Status</small>
                        @php
                            $accountColors = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger'
                            ];
                            $accountColor = $accountColors[$student->account_status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $accountColor }}">
                            {{ ucfirst($student->account_status) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Created</small>
                        <strong>{{ $student->created_at->format('M d, Y g:i A') }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Last Updated</small>
                        <strong>{{ $student->updated_at->format('M d, Y g:i A') }}</strong>
                    </div>

                    @if($student->assignedUser)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Assigned Counselor</small>
                        <strong>{{ $student->assignedUser->name }}</strong>
                    </div>
                    @endif

                    <div>
                        <small class="text-muted d-block mb-1">Created By</small>
                        <strong>{{ $student->creator->name ?? 'System' }}</strong>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-lightbulb me-2 text-warning"></i>Quick Tips</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Select target university first</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Programs will load based on university</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Student will see program-specific checklists</li>
                        <li class="mb-0"><i class="fas fa-check text-success me-2"></i>All activities are logged</li>
                    </ul>
                </div>
            </div>

            @if($student->account_status === 'pending')
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-warning">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-2 fw-semibold">Account Pending Approval</h6>
                            <p class="text-muted mb-2 small">This student account requires approval before access is granted.</p>
                            @can('approve', $student)
                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> View & Approve
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        /* Professional Card Styling */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        /* Enhanced Form Labels */
        .form-label {
            color: #495057;
            font-size: 0.925rem;
            margin-bottom: 0.6rem;
        }

        .form-label i.text-muted {
            opacity: 0.7;
            font-size: 0.9rem;
        }

        /* Form Control Enhancements */
        .form-control, .form-select {
            border: 1.5px solid #dee2e6;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: all 0.25s ease;
        }

        .form-control-lg {
            padding: 0.85rem 1.2rem;
            font-size: 1.05rem;
            font-weight: 500;
        }

        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
            transform: translateY(-1px);
        }

        .form-control:hover:not(:focus):not(:disabled),
        .form-select:hover:not(:focus):not(:disabled) {
            border-color: #adb5bd;
        }

        .form-control::placeholder {
            color: #adb5bd;
            font-style: italic;
        }

        /* Card Header Styling */
        .card-header h5 {
            color: #2c3e50;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .card-header h5 i {
            font-size: 1.15rem;
        }

        /* Enhanced Spacing */
        .g-4 > * {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }

        /* Form Text Helper */
        .form-text {
            font-size: 0.825rem;
            color: #6c757d;
            margin-top: 0.35rem;
        }

        .form-text i {
            font-size: 0.75rem;
        }

        /* Required Asterisk */
        .text-danger {
            font-weight: 700;
            font-size: 1rem;
        }

        /* Invalid Feedback */
        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.4rem;
            display: block;
        }

        /* Button Enhancements */
        .btn {
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
            border-color: #bd2130;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        /* Responsive Improvements */
        @media (max-width: 992px) {
            .card-body {
                padding: 1.5rem !important;
            }
        }

        @media (max-width: 768px) {
            .form-control, .form-select {
                font-size: 16px; /* Prevents iOS zoom */
            }

            .form-control-lg {
                font-size: 16px;
            }

            .card-body {
                padding: 1.25rem !important;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn.ms-auto {
                margin-left: 0 !important;
            }
        }

        /* Card Border Radius */
        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        /* Smooth Transitions */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Dynamic program loading based on university selection
        const selectedProgramId = {{ old('target_program_id', $student->target_program_id ?? 'null') }};

        document.getElementById('target_university_id').addEventListener('change', function() {
            const universityId = this.value;
            const programSelect = document.getElementById('target_program_id');

            // Clear existing options
            programSelect.innerHTML = '<option value="">Loading programs...</option>';
            programSelect.disabled = true;

            if (universityId) {
                // Fetch programs for the selected university
                fetch(`/universities/${universityId}/programs`)
                    .then(response => response.json())
                    .then(programs => {
                        programSelect.innerHTML = '<option value="">Select Program</option>';
                        programs.forEach(program => {
                            const option = document.createElement('option');
                            option.value = program.id;
                            option.textContent = `${program.name} (${program.level})`;
                            // Preserve selection after form validation error
                            if (program.id == selectedProgramId) {
                                option.selected = true;
                            }
                            programSelect.appendChild(option);
                        });
                        programSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading programs:', error);
                        programSelect.innerHTML = '<option value="">Error loading programs</option>';
                        programSelect.disabled = false;
                    });
            } else {
                programSelect.innerHTML = '<option value="">Select University First</option>';
                programSelect.disabled = false;
            }
        });

        // Load programs on page load if university is selected
        window.addEventListener('DOMContentLoaded', function() {
            const universityId = document.getElementById('target_university_id').value;
            if (universityId) {
                document.getElementById('target_university_id').dispatchEvent(new Event('change'));
            }
        });

        // IELTS Score field enable/disable based on has_ielts selection
        document.getElementById('has_ielts').addEventListener('change', function() {
            const ieltsScoreField = document.getElementById('ielts_score');
            if (this.value === '1') {
                ieltsScoreField.disabled = false;
                ieltsScoreField.focus();
            } else {
                ieltsScoreField.disabled = true;
                ieltsScoreField.value = '';
            }
        });

        function confirmDeleteStudentEdit() {
            Swal.fire({
                title: 'Delete Student?',
                text: 'Are you sure you want to delete this student? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC143C',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
    @endpush
@endsection
