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
            <!-- Display Errors -->
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('students.approve', $student) }}" method="POST" id="approvalForm">
                @csrf

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i> Program Enrollment & Academic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong> Select the university and program the student will be enrolled in, and assign a counselor to manage their application.
                        </div>

                        <!-- University and Program Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="target_university_id" class="form-label">
                                    <i class="fas fa-university me-2"></i>Target University <span class="text-danger">*</span>
                                </label>
                                <select name="target_university_id" id="target_university_id" 
                                        class="form-select @error('target_university_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Select University --</option>
                                    @foreach($universities as $university)
                                        <option value="{{ $university->id }}" 
                                                {{ old('target_university_id', $student->target_university_id) == $university->id ? 'selected' : '' }}>
                                            {{ $university->name }} ({{ $university->country }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('target_university_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="target_program_id" class="form-label">
                                    <i class="fas fa-book me-2"></i>Target Program <span class="text-danger">*</span>
                                </label>
                                <select name="target_program_id" id="target_program_id" 
                                        class="form-select @error('target_program_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Select Program --</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" 
                                                data-university-id="{{ $program->university_id }}"
                                                {{ old('target_program_id', $student->target_program_id) == $program->id ? 'selected' : '' }}
                                                style="display: none;">
                                            {{ $program->name }} ({{ $program->level }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('target_program_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Counselor Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label for="assigned_to" class="form-label">
                                    <i class="fas fa-user-tie me-2"></i>Assign Counselor <span class="text-danger">*</span>
                                </label>
                                <select name="assigned_to" id="assigned_to" 
                                        class="form-select @error('assigned_to') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Select Counselor --</option>
                                    @foreach($counselors as $counselor)
                                        <option value="{{ $counselor->id }}" 
                                                {{ old('assigned_to', $student->assigned_to ?? Auth::id()) == $counselor->id ? 'selected' : '' }}>
                                            {{ $counselor->name }} 
                                            @if($counselor->roles->first())
                                                ({{ $counselor->roles->first()->name }})
                                            @endif
                                            @if($counselor->id == Auth::id())
                                                - You
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    The selected counselor will manage this student's application process.
                                </small>
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
    // Load programs based on selected university
    function loadPrograms() {
        const universitySelect = document.getElementById('target_university_id');
        const programSelect = document.getElementById('target_program_id');
        const selectedUniversityId = universitySelect.value;
        
        // Reset program dropdown
        programSelect.value = '';
        
        // Hide all programs first
        const allOptions = programSelect.querySelectorAll('option:not([value=""])');
        allOptions.forEach(option => {
            option.style.display = 'none';
        });
        
        if (selectedUniversityId) {
            // Show only programs for selected university
            const matchingOptions = programSelect.querySelectorAll(`option[data-university-id="${selectedUniversityId}"]`);
            matchingOptions.forEach(option => {
                option.style.display = '';
            });
            
            // Auto-select if there's a previously selected program
            const previouslySelected = "{{ old('target_program_id', $student->target_program_id) }}";
            if (previouslySelected) {
                const previousOption = programSelect.querySelector(`option[value="${previouslySelected}"]`);
                if (previousOption && previousOption.getAttribute('data-university-id') === selectedUniversityId) {
                    programSelect.value = previouslySelected;
                }
            }
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const universitySelect = document.getElementById('target_university_id');
        universitySelect.addEventListener('change', loadPrograms);
        
        // Load programs if university is pre-selected
        if (universitySelect.value) {
            loadPrograms();
        }
    });

    function confirmApproval() {
        // Validate form first
        const form = document.getElementById('approvalForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const universitySelect = document.getElementById('target_university_id');
        const programSelect = document.getElementById('target_program_id');
        const counselorSelect = document.getElementById('assigned_to');
        
        const universityName = universitySelect.selectedOptions[0]?.text || 'N/A';
        const programName = programSelect.selectedOptions[0]?.text || 'N/A';
        const counselorName = counselorSelect.selectedOptions[0]?.text || 'N/A';
        
        Swal.fire({
            title: 'Approve Student?',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Student:</strong> {{ $student->name }}</p>
                    <p><strong>University:</strong> ${universityName}</p>
                    <p><strong>Program:</strong> ${programName}</p>
                    <p><strong>Assigned Counselor:</strong> ${counselorName}</p>
                    <hr>
                    <p class="text-muted">This will:</p>
                    <ul class="text-muted">
                        <li>Approve the student's account</li>
                        <li>Enroll the student in the selected program</li>
                        <li>Assign the student to the selected counselor</li>
                        <li>Initialize program-specific checklists</li>
                        <li>Send a welcome email to the student</li>
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
                console.log('Form submission confirmed');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                form.submit();
            }
        });
    }
</script>
@endpush

@endsection
