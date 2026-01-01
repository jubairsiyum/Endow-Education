@extends('layouts.admin')

@section('page-title', 'Edit Student')
@section('breadcrumb', 'Home / Students / Edit')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Edit Student</h1>
        <p class="page-subtitle">Update student information</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('students.update', $student) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5>Personal Information</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $student->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $student->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $student->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                       id="country" name="country" value="{{ old('country', $student->country) }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-university me-2"></i>Academic Information</h5>
                    </div>
                    <div class="card-body-custom">
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
                                    @foreach($employees ?? [] as $employee)
                                    <option value="{{ $employee->id }}"
                                            {{ old('assigned_to', $student->assigned_to) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
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
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-save me-2"></i> Update Student
                    </button>
                    <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    @can('delete', $student)
                    <button type="button" class="btn btn-outline-danger ms-auto"
                            onclick="if(confirm('Are you sure you want to delete this student?')) { document.getElementById('delete-form').submit(); }">
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
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5><i class="fas fa-info-circle me-2"></i> Student Status</h5>
                </div>
                <div class="card-body-custom">
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
                        <span class="badge-custom badge-{{ $accountColor }}-custom">
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

            <div class="card-custom">
                <div class="card-header-custom">
                    <h5><i class="fas fa-question-circle me-2 text-info"></i>Quick Tips</h5>
                </div>
                <div class="card-body-custom">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Select target university first</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Programs will load based on university</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Student will see program-specific checklists</li>
                        <li class="mb-0"><i class="fas fa-check text-success me-2"></i>All activities are logged</li>
                    </ul>
                </div>
            </div>

            @can('approve', $student)
            @if($student->account_status === 'pending')
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5><i class="fas fa-shield-alt me-2"></i> Account Actions</h5>
                </div>
                <div class="card-body-custom">
                    <form action="{{ route('students.approve', $student) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i> Approve Account
                        </button>
                    </form>

                    <form action="{{ route('students.reject', $student) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Are you sure you want to reject this student?');">
                            <i class="fas fa-times me-2"></i> Reject Account
                        </button>
                    </form>
                </div>
            </div>
            @endif
            @endcan
        </div>
    </div>

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
    </script>
    @endpush
@endsection
