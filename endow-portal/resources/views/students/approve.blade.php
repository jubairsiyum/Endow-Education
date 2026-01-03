@extends('layouts.admin')

@section('page-title', 'Approve Student - ' . $student->name)
@section('breadcrumb', 'Home / Students / Approve')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Approve Student & Enroll in Program
                </h2>
                <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Student Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i> Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Name:</strong> {{ $student->name }}</p>
                            <p class="mb-2"><strong>Email:</strong> {{ $student->email }}</p>
                            <p class="mb-2"><strong>Phone:</strong> {{ $student->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Country:</strong> {{ $student->country }}</p>
                            <p class="mb-2"><strong>Nationality:</strong> {{ $student->nationality }}</p>
                            <p class="mb-2"><strong>Current Status:</strong> 
                                <span class="badge bg-warning">{{ ucfirst($student->account_status) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Form -->
    <div class="row">
        <div class="col-12">
            <form action="{{ route('students.approve', $student) }}" method="POST">
                @csrf

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i> Program Enrollment & Academic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong> Please select the university and program for this student. 
                            The student will see checklist items specific to the selected program.
                        </div>

                        <!-- Program Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="target_university_id" class="form-label">
                                    Target University <span class="text-danger">*</span>
                                </label>
                                <select name="target_university_id" id="target_university_id" 
                                        class="form-select @error('target_university_id') is-invalid @enderror" 
                                        required onchange="loadPrograms(this.value)">
                                    <option value="">-- Select University --</option>
                                    @foreach($universities as $university)
                                        <option value="{{ $university->id }}" 
                                                {{ old('target_university_id', $student->target_university_id) == $university->id ? 'selected' : '' }}>
                                            {{ $university->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('target_university_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="target_program_id" class="form-label">
                                    Program <span class="text-danger">*</span>
                                </label>
                                <select name="target_program_id" id="target_program_id" 
                                        class="form-select @error('target_program_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Select Program --</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" 
                                                data-university="{{ $program->university_id }}"
                                                {{ old('target_program_id', $student->target_program_id) == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }} - {{ $program->level }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('target_program_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Checklist items will be assigned based on this program</small>
                            </div>
                        </div>

                        <!-- Academic Information (Optional) -->
                        <div class="border-top pt-4">
                            <h6 class="mb-3">Additional Academic Information (Optional)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="applying_program" class="form-label">Applying Program</label>
                                    <input type="text" name="applying_program" id="applying_program" 
                                           class="form-control @error('applying_program') is-invalid @enderror"
                                           value="{{ old('applying_program', $student->applying_program) }}"
                                           placeholder="e.g., Bachelor of Science">
                                    @error('applying_program')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">Course/Field of Study</label>
                                    <input type="text" name="course" id="course" 
                                           class="form-control @error('course') is-invalid @enderror"
                                           value="{{ old('course', $student->course) }}"
                                           placeholder="e.g., Computer Science">
                                    @error('course')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="highest_education" class="form-label">Highest Education Level</label>
                                    <select name="highest_education" id="highest_education" 
                                            class="form-select @error('highest_education') is-invalid @enderror">
                                        <option value="">-- Select Education Level --</option>
                                        <option value="High School" {{ old('highest_education', $student->highest_education) == 'High School' ? 'selected' : '' }}>
                                            High School
                                        </option>
                                        <option value="Associate Degree" {{ old('highest_education', $student->highest_education) == 'Associate Degree' ? 'selected' : '' }}>
                                            Associate Degree
                                        </option>
                                        <option value="Bachelor's Degree" {{ old('highest_education', $student->highest_education) == "Bachelor's Degree" ? 'selected' : '' }}>
                                            Bachelor's Degree
                                        </option>
                                        <option value="Master's Degree" {{ old('highest_education', $student->highest_education) == "Master's Degree" ? 'selected' : '' }}>
                                            Master's Degree
                                        </option>
                                        <option value="Doctorate" {{ old('highest_education', $student->highest_education) == 'Doctorate' ? 'selected' : '' }}>
                                            Doctorate
                                        </option>
                                    </select>
                                    @error('highest_education')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="button" class="btn btn-success btn-lg" onclick="confirmApproval()">
                                <i class="fas fa-check-circle me-2"></i> Approve & Enroll Student
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function loadPrograms(universityId) {
        const programSelect = document.getElementById('target_program_id');
        const options = programSelect.querySelectorAll('option[data-university]');
        
        // Show all programs if no university selected
        if (!universityId) {
            options.forEach(option => option.style.display = 'block');
            return;
        }
        
        // Filter programs by university
        options.forEach(option => {
            if (option.dataset.university == universityId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
                if (option.selected) {
                    option.selected = false;
                }
            }
        });
        
        // Reset program selection
        programSelect.value = '';
    }
    
    function confirmApproval() {
        // Validate form first
        const form = document.querySelector('form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const universityName = document.getElementById('target_university_id').selectedOptions[0]?.text || 'N/A';
        const programName = document.getElementById('target_program_id').selectedOptions[0]?.text || 'N/A';
        
        Swal.fire({
            title: 'Approve Student?',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Student:</strong> {{ $student->name }}</p>
                    <p><strong>University:</strong> ${universityName}</p>
                    <p><strong>Program:</strong> ${programName}</p>
                    <hr>
                    <p class="text-muted">This will:</p>
                    <ul class="text-muted">
                        <li>Approve the student's account</li>
                        <li>Send a welcome email</li>
                        <li>Assign program-specific checklists</li>
                    </ul>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check"></i> Yes, Approve',
            cancelButtonText: '<i class="fas fa-times"></i> Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const universityId = document.getElementById('target_university_id').value;
        if (universityId) {
            loadPrograms(universityId);
        }
    });
</script>
@endpush

@endsection
