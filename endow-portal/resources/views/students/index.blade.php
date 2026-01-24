@extends('layouts.admin')

@section('page-title', 'Students')
@section('breadcrumb', 'Home / Students / All Students')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-users text-danger"></i> Students Management</h4>
            <small class="text-muted">Manage and track all student applications</small>
        </div>
        <div class="d-flex gap-2">
            @can('create students')
            <a href="{{ route('students.create') }}" class="btn btn-danger">
                <i class="fas fa-plus me-1"></i> Add Student
            </a>
            @endcan
            <button type="button" class="btn btn-success" id="exportSelectedBtn" style="display: none;">
                <i class="fas fa-file-export me-1"></i> Export Selected
            </button>
            <a href="{{ route('students.export.form') }}" class="btn btn-outline-success">
                <i class="fas fa-download me-1"></i> Export All
            </a>
        </div>
    </div>

    <!-- Compact Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('students.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3 col-12">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, email, phone..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2 col-6">
                    <select name="university_id" class="form-select form-select-sm">
                        <option value="">All Universities</option>
                        @foreach($universities ?? [] as $university)
                        <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                            {{ Str::limit($university->name, 30) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-6">
                    <select name="program_id" class="form-select form-select-sm" id="program_filter">
                        <option value="">All Programs</option>
                        @forelse($programs ?? [] as $program)
                            @if($program->university || request('university_id'))
                                <option value="{{ $program->id }}" 
                                        data-university-id="{{ $program->university_id ?? '' }}"
                                        {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ Str::limit($program->name, 20) }}
                                    @if($program->university && !request('university_id'))
                                        ({{ Str::limit($program->university->name, 15) }})
                                    @endif
                                </option>
                            @else
                                <option value="{{ $program->id }}" 
                                        data-university-id="{{ $program->university_id ?? '' }}"
                                        style="display: none;"
                                        {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ Str::limit($program->name, 20) }}
                                </option>
                            @endif
                        @empty
                            <option value="" disabled>No programs available</option>
                        @endforelse
                    </select>
                </div>

                <div class="col-md-2 col-6">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Applied</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2 col-6">
                    <select name="account_status" class="form-select form-select-sm">
                        <option value="">Account Status</option>
                        <option value="pending" {{ request('account_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('account_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('account_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                <div class="col-md-1 col-12 d-flex gap-1">
                    <button type="submit" class="btn btn-danger btn-sm flex-grow-1">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
                @else
                <div class="col-md-2 col-12 d-flex gap-1">
                    <button type="submit" class="btn btn-danger btn-sm flex-grow-1">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted student-table-header">
                        <th style="width: 40px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllStudents">
                            </div>
                        </th>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Name</th>
                        <th class="d-none d-md-table-cell">Contact</th>
                        <th class="d-none d-lg-table-cell">University</th>
                        <th class="d-none d-lg-table-cell">Program</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">Account</th>
                        <th class="d-none d-md-table-cell">Progress</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input student-checkbox" type="checkbox" value="{{ $student->id }}">
                            </div>
                        </td>
                        <td class="text-center fw-bold text-muted" style="font-size: 0.85rem;">
                            {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($student->activeProfilePhoto)
                                    <img src="{{ $student->activeProfilePhoto->photo_url }}"
                                         alt="{{ $student->name }}"
                                         class="student-avatar rounded-circle me-2"
                                         style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e9ecef;">
                                @else
                                    <div class="student-avatar-placeholder bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 40px; height: 40px; font-size: 1.2rem; flex-shrink: 0;">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                @endif
                                <div class="student-info">
                                    <strong class="d-block text-dark student-name">{{ $student->name }}</strong>
                                    <small class="text-muted student-date">
                                        <i class="fas fa-calendar-alt me-1"></i>{{ $student->created_at->format('M d, Y') }}
                                    </small>
                                    <!-- Mobile only: Show email -->
                                    <small class="d-md-none text-muted d-block">
                                        <i class="fas fa-envelope me-1"></i>{{ Str::limit($student->email, 25) }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell student-contact">
                            <div><i class="fas fa-envelope text-muted me-1"></i>{{ Str::limit($student->email, 25) }}</div>
                            <div><i class="fas fa-phone text-muted me-1"></i>{{ $student->phone }}</div>
                        </td>
                        <td class="d-none d-lg-table-cell student-university">
                            {{ $student->targetUniversity->name ?? 'N/A' }}
                        </td>
                        <td class="d-none d-lg-table-cell student-program">
                            {{ Str::limit($student->targetProgram->name ?? 'N/A', 25) }}
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'new' => 'primary',
                                    'contacted' => 'info',
                                    'processing' => 'warning',
                                    'applied' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $color = $statusColors[$student->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} student-badge">
                                {{ ucfirst($student->status) }}
                            </span>
                            <!-- Mobile only: Show account status -->
                            @php
                                $accountColors = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $accountColor = $accountColors[$student->account_status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $accountColor }} student-badge d-md-none d-block mt-1">
                                {{ ucfirst($student->account_status) }}
                            </span>
                        </td>
                        <td class="d-none d-md-table-cell">
                            @php
                                $accountColors = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $accountColor = $accountColors[$student->account_status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $accountColor }} student-badge">
                                {{ ucfirst($student->account_status) }}
                            </span>
                        </td>
                        <td class="d-none d-md-table-cell">
                            @php
                                $inProgress = $student->checklist_progress['in_progress'] ?? 0;
                                $approved = $student->checklist_progress['approved'] ?? 0;
                                $submitted = $student->checklist_progress['submitted'] ?? 0;
                                $total = $student->checklist_progress['total'] ?? 0;
                                $progress = $total > 0 ? (int)(($inProgress / $total) * 100) : 0;
                                $progressColor = $progress >= 75 ? 'success' : ($progress >= 50 ? 'warning' : 'danger');
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress student-progress" style="width: 70px; height: 6px;">
                                    <div class="progress-bar bg-{{ $progressColor }}" role="progressbar"
                                         style="width: {{ $progress }}%;"
                                         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"
                                         title="{{ $approved }} approved, {{ $submitted }} pending review">
                                    </div>
                                </div>
                                <small class="text-muted student-progress-text" title="{{ $approved }} approved, {{ $submitted }} pending review">
                                    {{ $inProgress }}/{{ $total }}
                                </small>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary btn-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $student)
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-primary btn-sm d-none d-sm-inline-block" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete', $student)
                                <form action="{{ route('students.destroy', $student) }}" method="POST"
                                      class="d-inline" id="delete-student-form-{{ $student->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm d-none d-sm-inline-block" title="Delete"
                                            onclick="confirmDeleteStudent({{ $student->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="my-4">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No students found</p>
                                @can('create students')
                                <a href="{{ route('students.create') }}" class="btn btn-danger mt-3">
                                    <i class="fas fa-plus me-1"></i> Add Your First Student
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">
                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                </span>
                <form method="GET" action="{{ route('students.index') }}" class="d-flex align-items-center gap-2">
                    @foreach(request()->except(['page', 'per_page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label class="text-muted small mb-0">Per page:</label>
                    <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
        @endif
    </div>

    @push('styles')
    <style>
        /* Modern Professional Styling for Students Table */
        .student-table-header th {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.75rem 0.5rem;
        }

        .student-name {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .student-date {
            font-size: 0.75rem;
        }

        .student-contact,
        .student-country,
        .student-program,
        .student-assigned {
            font-size: 0.8rem;
        }

        .student-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            font-weight: 500;
        }

        .student-progress-text {
            font-size: 0.75rem;
        }

        .student-avatar,
        .student-avatar-placeholder {
            transition: transform 0.2s ease;
        }

        .student-avatar:hover,
        .student-avatar-placeholder:hover {
            transform: scale(1.1);
        }

        /* Mobile Responsive Styles */
        @media (max-width: 767.98px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .student-table-header th {
                font-size: 0.7rem;
                padding: 0.5rem 0.25rem;
            }

            .student-name {
                font-size: 0.8rem;
            }

            .student-date {
                font-size: 0.7rem;
            }

            .student-badge {
                font-size: 0.65rem;
                padding: 0.2rem 0.4rem;
            }

            .student-info {
                max-width: 150px;
            }

            .btn-group-sm > .btn {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
            }

            .card-body.p-3 {
                padding: 1rem !important;
            }
        }

        @media (max-width: 575.98px) {
            .student-avatar,
            .student-avatar-placeholder {
                width: 35px !important;
                height: 35px !important;
                font-size: 1rem !important;
            }

            .student-info {
                max-width: 120px;
            }

            .table td,
            .table th {
                padding: 0.5rem 0.25rem;
            }
        }

        /* Table Hover Effect */
        .table-hover tbody tr:hover {
            background-color: rgba(220, 20, 60, 0.03);
            transition: background-color 0.2s ease;
        }

        /* Improved Card Shadow */
        .card.shadow-sm {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075);
        }

        /* Better Progress Bar */
        .student-progress {
            border-radius: 10px;
            overflow: hidden;
        }

        .student-progress .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function confirmDeleteStudent(studentId) {
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
                    document.getElementById('delete-student-form-' + studentId).submit();
                }
            });
        }

        // Bulk Selection and Export functionality
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAllStudents');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');
            const exportSelectedBtn = document.getElementById('exportSelectedBtn');

            // Select/Deselect all students
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    studentCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                    updateExportButtonVisibility();
                });
            }

            // Update export button visibility when individual checkboxes change
            studentCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateExportButtonVisibility();
                    updateSelectAllState();
                });
            });

            // Export selected students
            if (exportSelectedBtn) {
                exportSelectedBtn.addEventListener('click', function() {
                    const selectedIds = Array.from(studentCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);

                    if (selectedIds.length === 0) {
                        Swal.fire({
                            title: 'No Students Selected',
                            text: 'Please select at least one student to export.',
                            icon: 'warning',
                            confirmButtonColor: '#DC143C'
                        });
                        return;
                    }

                    // Redirect to export form with selected IDs
                    const params = new URLSearchParams();
                    selectedIds.forEach(id => params.append('student_ids[]', id));
                    window.location.href = '{{ route("students.export.form") }}?' + params.toString();
                });
            }

            function updateExportButtonVisibility() {
                const checkedCount = Array.from(studentCheckboxes).filter(cb => cb.checked).length;
                if (exportSelectedBtn) {
                    exportSelectedBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';

                    // Update button text with count
                    const icon = '<i class="fas fa-file-export me-1"></i>';
                    const text = checkedCount > 0 ? `Export Selected (${checkedCount})` : 'Export Selected';
                    exportSelectedBtn.innerHTML = icon + text;
                }
            }

            function updateSelectAllState() {
                if (selectAllCheckbox) {
                    const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(studentCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked && studentCheckboxes.length > 0;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }
            }

            // Dynamic program filtering based on university selection
            const universityFilter = document.querySelector('select[name="university_id"]');
            const programFilter = document.getElementById('program_filter');
            
            if (universityFilter && programFilter) {
                universityFilter.addEventListener('change', function() {
                    const selectedUniversityId = this.value;
                    const options = programFilter.querySelectorAll('option');
                    
                    // Reset program filter
                    programFilter.value = '';
                    
                    options.forEach(option => {
                        if (option.value === '') {
                            // Always show "All Programs" option
                            option.style.display = 'block';
                        } else {
                            const optionUniversityId = option.getAttribute('data-university-id');
                            if (selectedUniversityId === '') {
                                // Show all programs with university context
                                option.style.display = 'block';
                            } else if (optionUniversityId === selectedUniversityId) {
                                // Show only programs from selected university
                                option.style.display = 'block';
                            } else {
                                // Hide programs from other universities
                                option.style.display = 'none';
                            }
                        }
                    });
                });
            }
        });
    </script>
    @endpush
@endsection
