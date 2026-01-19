@extends('layouts.student')

@section('page-title', 'My Profile')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">My Profile</h1>
                    <p class="text-muted">Manage your personal information and profile photo</p>
                </div>
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

                            @if(!Schema::hasTable('student_profiles'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Setup Required</h6>
                    <p class="mb-2">The student profile tables need to be fixed. Please run these commands in your terminal:</p>
                    <code class="bg-dark text-white p-2 d-block mb-2">php artisan migrate:fresh --force</code>
                    <p class="text-danger mb-0"><strong>Note:</strong> This will reset your database. Use only in development.</p>
                    <p class="mb-0 mt-2">After running the command, refresh this page.</p>
                </div>
            @elseif(Schema::hasTable('student_profile_photos') && !Schema::hasColumn('student_profile_photos', 'student_id'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Database Issue Detected</h6>
                    <p class="mb-2">The profile photos table has incorrect structure. Please run:</p>
                    <code class="bg-dark text-white p-2 d-block mb-2">php artisan migrate:rollback --step=1</code>
                    <code class="bg-dark text-white p-2 d-block mb-2">php artisan migrate</code>
                    <p class="mb-0 mt-2">Then refresh this page.</p>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Profile Photo Section -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-4">Profile Photo</h5>

                            <div class="profile-photo-container mb-3" id="photoContainer">
                                @php
                                    $activePhoto = null;
                                    try {
                                        // Ensure the relationship is fresh
                                        $student->load('activeProfilePhoto');
                                        $activePhoto = $student->activeProfilePhoto;
                                    } catch (\Exception $e) {
                                        // Photo table structure issue - ignore
                                    }
                                @endphp
                                @if($activePhoto && $activePhoto->photo_path)
                                  <img src="{{ $activePhoto->photo_url }}?t={{ time() }}"
                                         alt="Profile Photo"
                                         class="profile-photo"
                                         id="profilePhotoPreview"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'profile-photo-placeholder\'><i class=\'fas fa-user\'></i></div>'; console.error('Failed to load image:', this.src);">
                                @else
                                    <div class="profile-photo-placeholder" id="profilePhotoPreview">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>

                            <form action="{{ route('student.profile.photo.upload') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  id="photoUploadForm">
                                @csrf
                                <div class="mb-3">
                                    <input type="file"
                                           class="form-control"
                                           name="photo"
                                           id="photoInput"
                                           accept="image/jpeg,image/jpg,image/png"
                                           onchange="previewPhoto(event)">
                                    <small class="text-muted d-block mt-2">
                                        JPG, JPEG, or PNG. Max 2MB.<br>
                                        Minimum 200x200 pixels.
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 mb-2" id="photoUploadBtn" style="display: none;">
                                    <i class="fas fa-upload me-2"></i>Confirm Upload
                                </button>
                            </form>

                            @php
                                $hasActivePhoto = false;
                                try {
                                    $hasActivePhoto = $student->activeProfilePhoto && Schema::hasColumn('student_profile_photos', 'student_id');
                                } catch (\Exception $e) {
                                    // Photo table structure issue
                                }
                            @endphp
                            @if($hasActivePhoto)
                                <form action="{{ route('student.profile.photo.delete') }}"
                                      method="POST"
                                      id="delete-photo-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger w-100"
                                            onclick="confirmDeletePhoto()">
                                        <i class="fas fa-trash me-2"></i>Remove Photo
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Profile Completion Card -->
                    @if($student->profile ?? false)
                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Profile Completion</h5>
                            @php
                                $completion = $student->profile->getCompletionPercentage();
                            @endphp
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-success"
                                     role="progressbar"
                                     style="width: {{ $completion }}%"
                                     aria-valuenow="{{ $completion }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    {{ $completion }}%
                                </div>
                            </div>
                            <small class="text-muted">Complete your profile to help us serve you better</small>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Profile Information Form -->
                <div class="col-lg-8">
                    <form action="{{ route('student.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <!-- Name, Email, Phone in 2 rows -->
                                    <div class="col-12">
                                        <label for="name" class="form-label mb-1">Full Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               id="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               name="name"
                                               value="{{ old('name', $student->name) }}"
                                               placeholder="Enter your full name"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label mb-1">Email Address <span class="text-danger">*</span></label>
                                        <input type="email"
                                               id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               name="email"
                                               value="{{ old('email', $student->email) }}"
                                               placeholder="your.email@example.com"
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label mb-1">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text"
                                               id="phone"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               name="phone"
                                               value="{{ old('phone', $student->phone) }}"
                                               placeholder="+1234567890"
                                               required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4 col-md-6">
                                        <label for="date_of_birth" class="form-label mb-1">Date of Birth</label>
                                        <input type="date"
                                               id="date_of_birth"
                                               class="form-control @error('date_of_birth') is-invalid @enderror"
                                               name="date_of_birth"
                                               value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}">
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4 col-md-6">
                                        <label for="gender" class="form-label mb-1">Gender</label>
                                        <select id="gender" class="form-select @error('gender') is-invalid @enderror" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4 col-md-12">
                                        <label for="nationality" class="form-label mb-1">Nationality</label>
                                        <input type="text"
                                               id="nationality"
                                               class="form-control @error('nationality') is-invalid @enderror"
                                               name="nationality"
                                               value="{{ old('nationality', $student->nationality) }}"
                                               placeholder="e.g., Bangladeshi">
                                        @error('nationality')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="father_name" class="form-label mb-1">Father's Name</label>
                                        <input type="text"
                                               id="father_name"
                                               class="form-control @error('father_name') is-invalid @enderror"
                                               name="father_name"
                                               value="{{ old('father_name', $student->father_name) }}"
                                               placeholder="Father's full name">
                                        @error('father_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="mother_name" class="form-label mb-1">Mother's Name</label>
                                        <input type="text"
                                               id="mother_name"
                                               class="form-control @error('mother_name') is-invalid @enderror"
                                               name="mother_name"
                                               value="{{ old('mother_name', $student->mother_name) }}"
                                               placeholder="Mother's full name">
                                        @error('mother_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-success"></i>Address Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="address" class="form-label mb-1">Street Address</label>
                                        <textarea id="address"
                                                  class="form-control @error('address') is-invalid @enderror"
                                                  name="address"
                                                  rows="2"
                                                  placeholder="Enter your complete address">{{ old('address', $student->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4 col-md-6">
                                        <label for="city" class="form-label mb-1">City</label>
                                        <input type="text"
                                               id="city"
                                               class="form-control @error('city') is-invalid @enderror"
                                               name="city"
                                               value="{{ old('city', $student->city) }}"
                                               placeholder="e.g., Dhaka">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4 col-md-6">
                                        <label for="country" class="form-label mb-1">Country <span class="text-danger">*</span></label>
                                        <input type="text"
                                               id="country"
                                               class="form-control @error('country') is-invalid @enderror"
                                               name="country"
                                               value="{{ old('country', $student->country) }}"
                                               placeholder="e.g., Bangladesh"
                                               required>
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4 col-md-12">
                                        <label for="postal_code" class="form-label mb-1">Postal Code</label>
                                        <input type="text"
                                               id="postal_code"
                                               class="form-control @error('postal_code') is-invalid @enderror"
                                               name="postal_code"
                                               value="{{ old('postal_code', $student->postal_code) }}"
                                               placeholder="e.g., 1200">
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Passport Information -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-passport me-2 text-info"></i>Passport Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="passport_number" class="form-label mb-1">Passport Number</label>
                                        <input type="text"
                                               id="passport_number"
                                               class="form-control @error('passport_number') is-invalid @enderror"
                                               name="passport_number"
                                               value="{{ old('passport_number', $student->passport_number) }}"
                                               placeholder="e.g., A12345678">
                                        @error('passport_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="passport_expiry_date" class="form-label mb-1">Passport Expiry Date</label>
                                        <input type="date"
                                               id="passport_expiry_date"
                                               class="form-control @error('passport_expiry_date') is-invalid @enderror"
                                               name="passport_expiry_date"
                                               value="{{ old('passport_expiry_date', $student->passport_expiry_date?->format('Y-m-d')) }}">
                                        @error('passport_expiry_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Educational Background -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2 text-warning"></i>Educational Background</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <!-- SSC Information -->
                                    <div class="col-12">
                                        <h6 class="text-muted mb-2 fw-semibold"><i class="fas fa-certificate me-2"></i>SSC (Secondary School Certificate)</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ssc_year" class="form-label mb-1">SSC Year</label>
                                        <input type="text"
                                               id="ssc_year"
                                               class="form-control @error('ssc_year') is-invalid @enderror"
                                               name="ssc_year"
                                               value="{{ old('ssc_year', $student->ssc_year) }}"
                                               placeholder="e.g., 2018"
                                               maxlength="4"
                                               pattern="[0-9]{4}">
                                        <small class="form-text text-muted">4-digit year format</small>
                                        @error('ssc_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ssc_result" class="form-label mb-1">SSC Result</label>
                                        <input type="text"
                                               id="ssc_result"
                                               class="form-control @error('ssc_result') is-invalid @enderror"
                                               name="ssc_result"
                                               value="{{ old('ssc_result', $student->ssc_result) }}"
                                               placeholder="e.g., 5.00, A+, 85%">
                                        <small class="form-text text-muted">GPA or grade</small>
                                        @error('ssc_result')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- HSC Information -->
                                    <div class="col-12 mt-2">
                                        <h6 class="text-muted mb-2 fw-semibold"><i class="fas fa-certificate me-2"></i>HSC (Higher Secondary Certificate)</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="hsc_year" class="form-label mb-1">HSC Year</label>
                                        <input type="text"
                                               id="hsc_year"
                                               class="form-control @error('hsc_year') is-invalid @enderror"
                                               name="hsc_year"
                                               value="{{ old('hsc_year', $student->hsc_year) }}"
                                               placeholder="e.g., 2020"
                                               maxlength="4"
                                               pattern="[0-9]{4}">
                                        <small class="form-text text-muted">4-digit year format</small>
                                        @error('hsc_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="hsc_result" class="form-label mb-1">HSC Result</label>
                                        <input type="text"
                                               id="hsc_result"
                                               class="form-control @error('hsc_result') is-invalid @enderror"
                                               name="hsc_result"
                                               value="{{ old('hsc_result', $student->hsc_result) }}"
                                               placeholder="e.g., 4.95, A, 80%">
                                        <small class="form-text text-muted">GPA or grade</small>
                                        @error('hsc_result')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- IELTS Information -->
                                    <div class="col-12 mt-2">
                                        <h6 class="text-muted mb-2 fw-semibold"><i class="fas fa-language me-2"></i>IELTS (English Proficiency)</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="has_ielts" class="form-label mb-1">Do you have IELTS?</label>
                                        <select id="has_ielts"
                                                class="form-select @error('has_ielts') is-invalid @enderror"
                                                name="has_ielts">
                                            <option value="0" {{ old('has_ielts', $student->has_ielts) == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('has_ielts', $student->has_ielts) == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('has_ielts')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ielts_score" class="form-label mb-1">IELTS Score</label>
                                        <input type="text"
                                               id="ielts_score"
                                               class="form-control @error('ielts_score') is-invalid @enderror"
                                               name="ielts_score"
                                               value="{{ old('ielts_score', $student->ielts_score) }}"
                                               placeholder="e.g., 7.0, 6.5"
                                               {{ old('has_ielts', $student->has_ielts) == '0' ? 'disabled' : '' }}>
                                        <small class="form-text text-muted">Band score (0.0 - 9.0)</small>
                                        @error('ielts_score')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Note:</strong> Providing accurate educational details helps us better assess your eligibility for various programs and scholarships.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Profile -->
                        @if($student->profile ?? false)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-user-graduate me-2 text-purple"></i>Academic Profile</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="academic_level" class="form-label mb-1">Academic Level</label>
                                        <input type="text"
                                               id="academic_level"
                                               class="form-control @error('profile.academic_level') is-invalid @enderror"
                                               name="profile[academic_level]"
                                               value="{{ old('profile.academic_level', $student->profile->academic_level) }}"
                                               placeholder="e.g., Undergraduate, Graduate">
                                        @error('profile.academic_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="major" class="form-label mb-1">Major</label>
                                        <input type="text"
                                               id="major"
                                               class="form-control @error('profile.major') is-invalid @enderror"
                                               name="profile[major]"
                                               value="{{ old('profile.major', $student->profile->major) }}"
                                               placeholder="e.g., Computer Science">
                                        @error('profile.major')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="minor" class="form-label mb-1">Minor</label>
                                        <input type="text"
                                               id="minor"
                                               class="form-control @error('profile.minor') is-invalid @enderror"
                                               name="profile[minor]"
                                               value="{{ old('profile.minor', $student->profile->minor) }}"
                                               placeholder="e.g., Mathematics">
                                        @error('profile.minor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="gpa" class="form-label mb-1">GPA</label>
                                        <input type="number"
                                               id="gpa"
                                               step="0.01"
                                               min="0"
                                               max="4"
                                               class="form-control @error('profile.gpa') is-invalid @enderror"
                                               name="profile[gpa]"
                                               value="{{ old('profile.gpa', $student->profile->gpa) }}"
                                               placeholder="e.g., 3.75">
                                        @error('profile.gpa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="bio" class="form-label mb-1">Bio</label>
                                        <textarea id="bio"
                                                  class="form-control @error('profile.bio') is-invalid @enderror"
                                                  name="profile[bio]"
                                                  rows="3"
                                                  maxlength="1000"
                                                  placeholder="Brief description about yourself">{{ old('profile.bio', $student->profile->bio) }}</textarea>
                                        <small class="text-muted">Max 1000 characters</small>
                                        @error('profile.bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="interests" class="form-label mb-1">Interests</label>
                                        <textarea id="interests"
                                                  class="form-control @error('profile.interests') is-invalid @enderror"
                                                  name="profile[interests]"
                                                  rows="2"
                                                  placeholder="e.g., Reading, Sports, Music">{{ old('profile.interests', $student->profile->interests) }}</textarea>
                                        @error('profile.interests')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="skills" class="form-label mb-1">Skills</label>
                                        <textarea id="skills"
                                                  class="form-control @error('profile.skills') is-invalid @enderror"
                                                  name="profile[skills]"
                                                  rows="2"
                                                  placeholder="e.g., Python, Java, Leadership">{{ old('profile.skills', $student->profile->skills) }}</textarea>
                                        @error('profile.skills')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Emergency Contact -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-phone-alt me-2 text-danger"></i>Emergency Contact</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-lg-4 col-md-6">
                                        <label for="emergency_contact_name" class="form-label mb-1">Contact Name</label>
                                        <input type="text"
                                               id="emergency_contact_name"
                                               class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                               name="emergency_contact_name"
                                               value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}"
                                               placeholder="Emergency contact name">
                                        @error('emergency_contact_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4 col-md-6">
                                        <label for="emergency_contact_phone" class="form-label mb-1">Contact Phone</label>
                                        <input type="text"
                                               id="emergency_contact_phone"
                                               class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                               name="emergency_contact_phone"
                                               value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}"
                                               placeholder="+1234567890">
                                        @error('emergency_contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4 col-md-12">
                                        <label for="emergency_contact_relationship" class="form-label mb-1">Relationship</label>
                                        <input type="text"
                                               id="emergency_contact_relationship"
                                               class="form-control @error('emergency_contact_relationship') is-invalid @enderror"
                                               name="emergency_contact_relationship"
                                               value="{{ old('emergency_contact_relationship', $student->emergency_contact_relationship) }}"
                                               placeholder="e.g., Parent, Sibling">
                                        @error('emergency_contact_relationship')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #dc3545;
        --primary-hover: #c82333;
        --success-color: #28a745;
        --info-color: #17a2b8;
        --warning-color: #ffc107;
    }

    .profile-photo-container {
        width: 200px;
        height: 200px;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #f0f0f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile-photo-container:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    .profile-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-photo-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        color: white;
        font-weight: 600;
    }

    .card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
    }

    .card-header {
        border-bottom: 2px solid #f0f0f0;
        padding: 1.25rem 1.5rem;
        background: #fafafa !important;
        border-radius: 12px 12px 0 0 !important;
    }

    .card-header h5 {
        font-weight: 600;
        color: #2c3e50;
    }

    .card-header i {
        font-size: 1.1rem;
    }

    .card-body {
        padding: 1.75rem 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.925rem;
    }

    .form-control, .form-select {
        border: 1.5px solid #dee2e6;
        border-radius: 8px;
        padding: 0.65rem 0.95rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        transform: scale(1.005);
    }

    .form-control:hover:not(:focus),
    .form-select:hover:not(:focus) {
        border-color: #adb5bd;
    }

    .form-control::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--primary-color);
        border-color: var(--primary-color);
        padding: 0.7rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .btn-outline-secondary {
        border-width: 1.5px;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        transform: translateY(-2px);
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.25rem 1rem;
        }

        .card-header {
            padding: 1rem 1.25rem;
        }

        .profile-photo-container {
            width: 150px;
            height: 150px;
        }

        .profile-photo-placeholder {
            font-size: 60px;
        }

        .form-control, .form-select {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }

    @media (max-width: 576px) {
        .col-form-label {
            padding-top: 0.5rem;
        }

        .btn-primary, .btn-outline-secondary {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }

    /* Section headers in Educational Background */
    h6.text-muted {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem !important;
    }

    h6.fw-semibold i {
        color: var(--warning-color);
    }

    /* Form text helpers */
    .form-text {
        font-size: 0.825rem;
        margin-top: 0.25rem;
    }

    /* Alert improvements */
    .alert {
        border-radius: 10px;
        border-left: 4px solid;
    }

    .alert-success {
        border-left-color: var(--success-color);
    }

    .alert-danger {
        border-left-color: var(--primary-color);
    }

    .alert-info {
        border-left-color: var(--info-color);
    }

    /* Gap utility for better spacing */
    .g-3 {
        gap: 1rem !important;
    }

    /* Invalid feedback styling */
    .invalid-feedback {
        font-size: 0.85rem;
        margin-top: 0.35rem;
    }

    /* Required asterisk */
    .text-danger {
        color: var(--primary-color) !important;
        font-weight: 700;
    }

    /* Input group styling */
    .row.g-3 > * {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
</style>

<script>
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showError('File size must not exceed 2MB');
            event.target.value = '';
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            showError('Please select a JPG, JPEG, or PNG image');
            event.target.value = '';
            return;
        }

        // Preview the image
        const reader = new FileReader();
        reader.onload = function(e) {
            updatePhotoPreview(e.target.result);
            // Show the upload button
            const uploadBtn = document.getElementById('photoUploadBtn');
            if (uploadBtn) uploadBtn.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }

    function updatePhotoPreview(imageSrc) {
        const container = document.getElementById('photoContainer');
        const preview = document.getElementById('profilePhotoPreview');

        if (preview.tagName === 'IMG') {
            preview.src = imageSrc;
        } else {
            // Replace placeholder with image
            container.innerHTML = `<img src="${imageSrc}" alt="Profile Photo" class="profile-photo" id="profilePhotoPreview">`;
        }
    }

    function showSuccess(message) {
        showAlert(message, 'success');
    }

    function showError(message) {
        showAlert(message, 'danger');
    }

    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        const alertContainer = document.querySelector('.container-fluid.py-4 .row .col-12');
        const firstCard = alertContainer.querySelector('.d-flex');
        firstCard.insertAdjacentHTML('afterend', alertHtml);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }

    // Handle form submission with AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const uploadForm = document.getElementById('photoUploadForm');
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const fileInput = document.getElementById('photoInput');
                if (!fileInput.files.length) {
                    showError('Please select a photo to upload');
                    return false;
                }

                // Show loading indicator
                const submitBtn = uploadForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

                // Prepare form data
                const formData = new FormData(uploadForm);

                // Send AJAX request
                fetch(uploadForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the photo preview with the new image
                        updatePhotoPreview(data.photo.url);
                        showSuccess(data.message);

                        // Clear the file input
                        fileInput.value = '';

                        // Reload page after 1 second to show the delete button
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showError(data.message || 'Failed to upload photo');
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    showError('An error occurred while uploading the photo. Please try again.');
                })
                .finally(() => {
                    // Re-enable button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });

    function confirmDeletePhoto() {
        Swal.fire({
            title: 'Delete Profile Photo?',
            text: 'Are you sure you want to delete your profile photo?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC143C',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-photo-form').submit();
            }
        });
    }

    // Show SweetAlert for success/error messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#DC143C',
            timer: 3000,
            showConfirmButton: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#DC143C'
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            html: '<ul style="text-align: left;">' +
                @foreach($errors->all() as $error)
                    '<li>{{ $error }}</li>' +
                @endforeach
                '</ul>',
            confirmButtonColor: '#DC143C'
        });
    @endif

    // Educational Background - IELTS Score field enable/disable
    document.addEventListener('DOMContentLoaded', function() {
        const hasIeltsSelect = document.getElementById('has_ielts');
        const ieltsScoreInput = document.getElementById('ielts_score');

        if (hasIeltsSelect && ieltsScoreInput) {
            hasIeltsSelect.addEventListener('change', function() {
                if (this.value === '1') {
                    ieltsScoreInput.disabled = false;
                    ieltsScoreInput.focus();
                } else {
                    ieltsScoreInput.disabled = true;
                    ieltsScoreInput.value = '';
                }
            });
        }
    });
</script>
@endsection
