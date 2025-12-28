@extends('layouts.student')

@section('page-title', 'Emergency Contact')
@section('breadcrumb', 'Home / Emergency Contact')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Emergency Alert -->
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <div class="d-flex align-items-start gap-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    <div>
                        <h5 class="mb-1 fw-bold">For Immediate Assistance</h5>
                        <p class="mb-0">If you're experiencing an urgent situation, please call our emergency hotline immediately.</p>
                    </div>
                </div>
            </div>

            <!-- Emergency Hotline -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card-custom border-danger border-2 h-100">
                        <div class="card-body-custom text-center py-5">
                            <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-phone-volume fa-2x"></i>
                            </div>
                            <h4 class="fw-bold text-danger mb-2">Emergency Hotline</h4>
                            <h2 class="fw-bold mb-3">+1 (800) 555-0100</h2>
                            <p class="text-muted mb-3">Available 24/7 for urgent matters</p>
                            <a href="tel:+18005550100" class="btn btn-danger btn-lg">
                                <i class="fas fa-phone-alt me-2"></i>Call Now
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-custom border-primary border-2 h-100">
                        <div class="card-body-custom text-center py-5">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <h4 class="fw-bold text-primary mb-2">Email Support</h4>
                            <h5 class="fw-bold mb-3">support@endoweducation.com</h5>
                            <p class="text-muted mb-3">Response within 24 hours</p>
                            <a href="mailto:support@endoweducation.com" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Your Counselor -->
            @if($student->assignedUser)
            <div class="card-custom mb-4">
                <div class="card-header-custom bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Your Assigned Counselor</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px; font-size: 32px; font-weight: 600;">
                                {{ strtoupper(substr($student->assignedUser->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="col-md-5">
                            <h4 class="fw-bold mb-1">{{ $student->assignedUser->name }}</h4>
                            <p class="text-muted mb-2">Education Counselor</p>
                            <div class="mb-1">
                                <i class="fas fa-envelope text-danger me-2"></i>
                                <a href="mailto:{{ $student->assignedUser->email }}">{{ $student->assignedUser->email }}</a>
                            </div>
                            @if($student->assignedUser->phone)
                            <div>
                                <i class="fas fa-phone text-danger me-2"></i>
                                <a href="tel:{{ $student->assignedUser->phone }}">{{ $student->assignedUser->phone }}</a>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <div class="d-grid gap-2">
                                <a href="mailto:{{ $student->assignedUser->email }}" class="btn btn-primary-custom">
                                    <i class="fas fa-envelope me-2"></i>Email Counselor
                                </a>
                                @if($student->assignedUser->phone)
                                <a href="tel:{{ $student->assignedUser->phone }}" class="btn btn-outline-danger">
                                    <i class="fas fa-phone me-2"></i>Call Counselor
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Office Information -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card-custom h-100">
                        <div class="card-body-custom text-center py-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-clock fa-2x text-success"></i>
                            </div>
                            <h6 class="fw-bold mb-2">Office Hours</h6>
                            <p class="mb-1 small"><strong>Monday - Friday</strong></p>
                            <p class="text-muted mb-1 small">9:00 AM - 6:00 PM</p>
                            <p class="mb-1 small"><strong>Saturday</strong></p>
                            <p class="text-muted mb-0 small">10:00 AM - 2:00 PM</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-custom h-100">
                        <div class="card-body-custom text-center py-4">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-map-marker-alt fa-2x text-info"></i>
                            </div>
                            <h6 class="fw-bold mb-2">Office Location</h6>
                            <p class="text-muted mb-0 small">
                                Endow Education<br>
                                123 Education Street<br>
                                Suite 456<br>
                                City, State 12345
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-custom h-100">
                        <div class="card-body-custom text-center py-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-phone-alt fa-2x text-warning"></i>
                            </div>
                            <h6 class="fw-bold mb-2">Main Office</h6>
                            <p class="mb-1 small">
                                <a href="tel:+18005550123">+1 (800) 555-0123</a>
                            </p>
                            <p class="text-muted mb-1 small">General Inquiries</p>
                            <p class="mb-0 small">
                                <a href="tel:+18005550124">+1 (800) 555-0124</a>
                            </p>
                            <p class="text-muted mb-0 small">Document Support</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Contact Information -->
            @if($student->targetUniversity)
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-university text-danger me-2"></i>Target University Contact</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">{{ $student->targetUniversity->name }}</h6>
                            @if($student->targetUniversity->address)
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <small>{{ $student->targetUniversity->address }}</small>
                            </div>
                            @endif
                            @if($student->targetUniversity->website)
                            <div class="mb-2">
                                <i class="fas fa-globe text-danger me-2"></i>
                                <a href="{{ $student->targetUniversity->website }}" target="_blank" class="small">
                                    Visit Website
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($student->targetUniversity->contact_email)
                            <div class="mb-2">
                                <i class="fas fa-envelope text-danger me-2"></i>
                                <a href="mailto:{{ $student->targetUniversity->contact_email }}" class="small">
                                    {{ $student->targetUniversity->contact_email }}
                                </a>
                            </div>
                            @endif
                            @if($student->targetUniversity->contact_phone)
                            <div class="mb-2">
                                <i class="fas fa-phone text-danger me-2"></i>
                                <a href="tel:{{ $student->targetUniversity->contact_phone }}" class="small">
                                    {{ $student->targetUniversity->contact_phone }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Common Contact Reasons -->
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-question-circle text-danger me-2"></i>When to Contact Us</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-1"></i>
                                <div>
                                    <strong class="d-block small">Document Issues</strong>
                                    <small class="text-muted">Problems uploading or questions about requirements</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-1"></i>
                                <div>
                                    <strong class="d-block small">Application Status</strong>
                                    <small class="text-muted">Questions about your application progress</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-1"></i>
                                <div>
                                    <strong class="d-block small">Technical Problems</strong>
                                    <small class="text-muted">Login issues or portal malfunctions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-1"></i>
                                <div>
                                    <strong class="d-block small">Program Changes</strong>
                                    <small class="text-muted">Request to change university or program</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-1"></i>
                                <div>
                                    <strong class="d-block small">Urgent Deadlines</strong>
                                    <small class="text-muted">Time-sensitive application matters</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-1"></i>
                                <div>
                                    <strong class="d-block small">General Guidance</strong>
                                    <small class="text-muted">Questions about the process or next steps</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Contact Form -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-paper-plane text-danger me-2"></i>Quick Contact Form</h5>
                </div>
                <div class="card-body-custom">
                    <form action="{{ route('student.contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <select class="form-select @error('subject') is-invalid @enderror"
                                        id="subject"
                                        name="subject"
                                        required>
                                    <option value="">Select a subject</option>
                                    <option value="document_issue">Document Issue</option>
                                    <option value="application_status">Application Status</option>
                                    <option value="technical_problem">Technical Problem</option>
                                    <option value="program_change">Program Change Request</option>
                                    <option value="general_inquiry">General Inquiry</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror"
                                        id="priority"
                                        name="priority"
                                        required>
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message"
                                          name="message"
                                          rows="5"
                                          placeholder="Please describe your issue or question in detail..."
                                          required></textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">For urgent matters, please call the emergency hotline above.</small>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
