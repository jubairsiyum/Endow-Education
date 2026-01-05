@extends('layouts.admin')

@section('page-title', 'Test Email Notifications')
@section('breadcrumb', 'Home / Settings / Email / Test')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="fas fa-paper-plane text-danger"></i> Test Email Notifications
            </h4>
            <small class="text-muted">Send test emails to verify your configuration</small>
        </div>
        <a href="{{ route('admin.email-settings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Settings
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
            <form action="{{ route('admin.email-settings.send-test') }}" method="POST">
                @csrf

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-envelope text-danger me-2"></i>Send Test Email</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="email" class="form-label">Recipient Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                       placeholder="recipient@example.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Enter the email address where you want to receive the test email</small>
                            </div>

                            <div class="col-12">
                                <label for="type" class="form-label">Email Template Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">-- Select Template --</option>
                                    <option value="basic" {{ old('type') == 'basic' ? 'selected' : '' }}>
                                        Basic Test Email (Plain text)
                                    </option>
                                    <option value="registration" {{ old('type') == 'registration' ? 'selected' : '' }}>
                                        New Student Registration (To Admins)
                                    </option>
                                    <option value="approval" {{ old('type') == 'approval' ? 'selected' : '' }}>
                                        Student Account Approved (To Student)
                                    </option>
                                    <option value="rejection" {{ old('type') == 'rejection' ? 'selected' : '' }}>
                                        Student Account Rejected (To Student)
                                    </option>
                                    <option value="assignment" {{ old('type') == 'assignment' ? 'selected' : '' }}>
                                        Student Assigned (To Counselor)
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Template Descriptions -->
                            <div class="col-12">
                                <div class="alert alert-info mb-0" id="templateDescription" style="display: none;">
                                    <strong>Template Preview:</strong>
                                    <p class="mb-0 mt-2" id="descriptionText"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                    <a href="{{ route('admin.email-settings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Email Templates Info -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-info-circle text-info me-2"></i>About Test Emails</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-3">Test emails help you verify:</p>
                    <ul class="mb-3">
                        <li>SMTP configuration is correct</li>
                        <li>Email templates display properly</li>
                        <li>Notifications reach recipients</li>
                        <li>Images and styling work</li>
                    </ul>
                    <div class="alert alert-warning mb-0">
                        <small>
                            <strong>Note:</strong> Some templates require student data. If no students exist, you'll need to create one first.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Current Configuration -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-cog text-secondary me-2"></i>Current Configuration</h5>
                </div>
                <div class="card-body p-4">
                    @if($settings)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Status</small>
                            @if($settings->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Disabled</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Driver</small>
                            <strong>{{ strtoupper($settings->mailer) }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">SMTP Host</small>
                            <strong>{{ $settings->host }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">From Address</small>
                            <strong>{{ $settings->from_address }}</strong>
                        </div>

                        <div>
                            <small class="text-muted d-block mb-1">From Name</small>
                            <strong>{{ $settings->from_name }}</strong>
                        </div>
                    @else
                        <p class="text-muted mb-0">No configuration found. Please set up email settings first.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const descriptions = {
            basic: 'A simple plain text email to verify basic connectivity. No template or styling.',
            registration: 'Notification sent to all admins when a new student registers. Includes student details and review link.',
            approval: 'Welcome email sent to student when their account is approved. Includes login credentials and getting started information.',
            rejection: 'Professional rejection notification sent to student. Includes reason and next steps.',
            assignment: 'Notification sent to counselor when a student is assigned to them. Includes student profile and management links.'
        };

        document.getElementById('type').addEventListener('change', function() {
            const descriptionDiv = document.getElementById('templateDescription');
            const descriptionText = document.getElementById('descriptionText');
            
            if (this.value && descriptions[this.value]) {
                descriptionText.textContent = descriptions[this.value];
                descriptionDiv.style.display = 'block';
            } else {
                descriptionDiv.style.display = 'none';
            }
        });

        // Trigger change on page load if value is selected
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
@endsection
