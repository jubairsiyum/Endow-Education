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
                                    <option value="">-- Select Template to Test --</option>
                                    <option value="basic" {{ old('type') == 'basic' ? 'selected' : '' }}>
                                        🔹 Basic Test Email
                                    </option>
                                    <option value="registration" {{ old('type') == 'registration' ? 'selected' : '' }}>
                                        📝 New Student Registration Notification
                                    </option>
                                    <option value="approval" {{ old('type') == 'approval' ? 'selected' : '' }}>
                                        ✅ Student Approval Welcome Email
                                    </option>
                                    <option value="rejection" {{ old('type') == 'rejection' ? 'selected' : '' }}>
                                        ❌ Student Rejection Notification
                                    </option>
                                    <option value="assignment" {{ old('type') == 'assignment' ? 'selected' : '' }}>
                                        👤 Student Assignment to Counselor
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Select the email template you want to test</small>
                            </div>

                            <!-- Template Descriptions -->
                            <div class="col-12">
                                <div class="alert alert-info border-0 mb-0" id="templateDescription" style="display: none;">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-info-circle me-2 mt-1"></i>
                                        <div>
                                            <strong class="d-block mb-2">What this email contains:</strong>
                                            <div id="descriptionText"></div>
                                        </div>
                                    </div>
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
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-flask text-info me-2"></i>Template Testing Guide</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-3"><strong>How to test:</strong></p>
                    <ol class="mb-3">
                        <li class="mb-2">Enter your email address or any test recipient</li>
                        <li class="mb-2">Select the template type you want to test</li>
                        <li class="mb-2">Review the template description below</li>
                        <li class="mb-2">Click "Send Test Email"</li>
                        <li>Check your inbox (and spam folder)</li>
                    </ol>
                    
                    <div class="alert alert-warning border-0 mb-3">
                        <small>
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Note:</strong> Templates using student data require at least one student in the database.
                        </small>
                    </div>

                    <div class="alert alert-success border-0 mb-0">
                        <small>
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Tip:</strong> Test with different email providers (Gmail, Outlook, Yahoo) to ensure compatibility.
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
            basic: `
                <p class="mb-2">A simple connectivity test email with:</p>
                <ul class="mb-0">
                    <li>Plain text message</li>
                    <li>Timestamp of sending</li>
                    <li>No special formatting</li>
                </ul>
                <p class="mt-2 mb-0"><strong>Best for:</strong> Quick SMTP connection verification</p>
            `,
            registration: `
                <p class="mb-2">Sent to all Super Admins and Admins when a student registers:</p>
                <ul class="mb-0">
                    <li>Student's full name and email</li>
                    <li>Target university and program</li>
                    <li>Registration date and time</li>
                    <li>Quick action links (Approve/Reject)</li>
                    <li>Direct link to student profile</li>
                </ul>
                <p class="mt-2 mb-0"><strong>Recipient:</strong> All admins with review permissions</p>
            `,
            approval: `
                <p class="mb-2">Sent to student when their account is approved:</p>
                <ul class="mb-0">
                    <li>Welcome message and congratulations</li>
                    <li>Login credentials (email + generated password)</li>
                    <li>Target university and program details</li>
                    <li>Assigned counselor information</li>
                    <li>Login link and getting started guide</li>
                    <li>Important dates and next steps</li>
                </ul>
                <p class="mt-2 mb-0"><strong>Recipient:</strong> Approved student's email address</p>
            `,
            rejection: `
                <p class="mb-2">Professional notification sent when application is rejected:</p>
                <ul class="mb-0">
                    <li>Polite and professional tone</li>
                    <li>Reason for rejection (if provided)</li>
                    <li>Encouragement to reapply</li>
                    <li>Contact information for questions</li>
                    <li>Alternative next steps</li>
                </ul>
                <p class="mt-2 mb-0"><strong>Recipient:</strong> Rejected student's email address</p>
            `,
            assignment: `
                <p class="mb-2">Sent to counselor when a student is assigned to them:</p>
                <ul class="mb-0">
                    <li>Student's complete profile information</li>
                    <li>Target university and program</li>
                    <li>Assignment date and assigned by whom</li>
                    <li>Direct link to student management page</li>
                    <li>Quick actions (Documents, Visits, Payments)</li>
                </ul>
                <p class="mt-2 mb-0"><strong>Recipient:</strong> Assigned counselor's email address</p>
            `
        };

        document.getElementById('type').addEventListener('change', function() {
            const descriptionDiv = document.getElementById('templateDescription');
            const descriptionText = document.getElementById('descriptionText');
            
            if (this.value && descriptions[this.value]) {
                descriptionText.innerHTML = descriptions[this.value];
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
