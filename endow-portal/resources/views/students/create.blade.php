@extends('layouts.admin')

@section('page-title', 'Add New Student')
@section('breadcrumb', 'Home / Students / Create')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Add New Student</h1>
        <p class="page-subtitle">Fill in the details to register a new student</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('students.store') }}" method="POST">
                @csrf

                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5>Personal Information</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                       id="country" name="country" value="{{ old('country') }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-graduation-cap me-2 text-danger"></i>Academic Information</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="target_university_id" class="form-label">Target University</label>
                                <select class="form-select @error('target_university_id') is-invalid @enderror"
                                        id="target_university_id" name="target_university_id">
                                    <option value="">Select University</option>
                                    @foreach($universities as $university)
                                        <option value="{{ $university->id }}" {{ old('target_university_id') == $university->id ? 'selected' : '' }}>
                                            {{ $university->name }} ({{ $university->country }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('target_university_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Student's target university for admission</small>
                            </div>

                            <div class="col-md-6">
                                <label for="target_program_id" class="form-label">Target Program</label>
                                <select class="form-select @error('target_program_id') is-invalid @enderror"
                                        id="target_program_id" name="target_program_id">
                                    <option value="">Select Program</option>
                                    <!-- Programs will be loaded dynamically based on university -->
                                </select>
                                @error('target_program_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Specific program the student is applying for</small>
                            </div>

                            <div class="col-12">
                                <label for="course" class="form-label">Course/Program Notes</label>
                                <input type="text" class="form-control @error('course') is-invalid @enderror"
                                       id="course" name="course" value="{{ old('course') }}"
                                       placeholder="e.g., Additional course preferences or notes">
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="4"
                                          placeholder="Any additional information about the student...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-save me-2"></i> Create Student
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5><i class="fas fa-info-circle me-2 text-info"></i>Quick Tips</h5>
                </div>
                <div class="card-body-custom">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Select target university first</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Programs will load based on university</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Student will see program-specific checklists</li>
                        <li class="mb-0"><i class="fas fa-check text-success me-2"></i>All activities will be logged</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Dynamic program loading based on university selection
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

        // Trigger on page load if university is already selected (for old() values)
        window.addEventListener('DOMContentLoaded', function() {
            const universityId = document.getElementById('target_university_id').value;
            if (universityId) {
                document.getElementById('target_university_id').dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
@endsection

        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5><i class="fas fa-info-circle me-2"></i> Information</h5>
                </div>
                <div class="card-body-custom">
                    <p><strong>What happens next?</strong></p>
                    <ul class="ps-3">
                        <li class="mb-2">Student will be created with "Pending" account status</li>
                        <li class="mb-2">A checklist will be automatically initialized</li>
                        <li class="mb-2">You will be assigned as the counselor</li>
                        <li class="mb-2">Student can be approved/rejected by admins</li>
                    </ul>

                    <hr>

                    <p class="mb-2"><strong>Required Fields:</strong></p>
                    <ul class="ps-3 mb-0">
                        <li>Full Name</li>
                        <li>Email Address</li>
                        <li>Phone Number</li>
                        <li>Country</li>
                        <li>Course/Program</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
