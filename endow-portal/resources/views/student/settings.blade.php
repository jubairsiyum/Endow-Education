@extends('layouts.student')

@section('page-title', 'Settings')
@section('breadcrumb', 'Home / Settings')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-header-custom">
                        <h4 class="mb-1 fw-bold text-dark">
                            <i class="fas fa-cog text-primary me-2"></i>Settings
                        </h4>
                        <p class="text-muted mb-0 small">Manage your account preferences and security settings</p>
                    </div>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-4 mb-0" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-4 mb-0" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                <div class="card-body-custom p-0">
                    <div class="row g-0">
                        <!-- Settings Navigation -->
                        <div class="col-md-3 border-end">
                            <div class="p-4">
                                <div class="list-group list-group-flush">
                                    <a href="#account" class="list-group-item list-group-item-action border-0 rounded settings-tab active" data-bs-toggle="list">
                                        <i class="fas fa-user-circle me-2"></i>Account Settings
                                    </a>
                                    <a href="#security" class="list-group-item list-group-item-action border-0 rounded settings-tab" data-bs-toggle="list">
                                        <i class="fas fa-lock me-2"></i>Security
                                    </a>
                                    <a href="#notifications" class="list-group-item list-group-item-action border-0 rounded settings-tab" data-bs-toggle="list">
                                        <i class="fas fa-bell me-2"></i>Notifications
                                    </a>
                                    <a href="#privacy" class="list-group-item list-group-item-action border-0 rounded settings-tab" data-bs-toggle="list">
                                        <i class="fas fa-shield-alt me-2"></i>Privacy
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Content -->
                        <div class="col-md-9">
                            <div class="tab-content p-4">
                                <!-- Account Settings -->
                                <div class="tab-pane fade show active" id="account">
                                    <h5 class="fw-bold mb-3">Account Settings</h5>
                                    <p class="text-muted small mb-4">Manage your account information and preferences</p>

                                    <form action="{{ route('student.settings.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="section" value="account">

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Full Name</label>
                                            <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Email Address</label>
                                            <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
                                            <small class="text-muted">Your primary contact email</small>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Language Preference</label>
                                            <select class="form-select" name="language">
                                                <option value="en" selected>English</option>
                                                <option value="es">Spanish</option>
                                                <option value="fr">French</option>
                                                <option value="de">German</option>
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Time Zone</label>
                                            <select class="form-select" name="timezone">
                                                <option value="UTC" selected>UTC (Coordinated Universal Time)</option>
                                                <option value="America/New_York">Eastern Time (US & Canada)</option>
                                                <option value="America/Chicago">Central Time (US & Canada)</option>
                                                <option value="America/Denver">Mountain Time (US & Canada)</option>
                                                <option value="America/Los_Angeles">Pacific Time (US & Canada)</option>
                                                <option value="Europe/London">London</option>
                                                <option value="Europe/Paris">Paris</option>
                                                <option value="Asia/Dubai">Dubai</option>
                                                <option value="Asia/Kolkata">India</option>
                                                <option value="Asia/Shanghai">China</option>
                                                <option value="Asia/Tokyo">Tokyo</option>
                                            </select>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary-custom">
                                                <i class="fas fa-save me-2"></i>Save Changes
                                            </button>
                                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Security Settings -->
                                <div class="tab-pane fade" id="security">
                                    <h5 class="fw-bold mb-3">Security Settings</h5>
                                    <p class="text-muted small mb-4">Keep your account secure by updating your password</p>

                                    <form action="{{ route('student.settings.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="section" value="security">

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Current Password</label>
                                            <input type="password" class="form-control" name="current_password" required>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">New Password</label>
                                            <input type="password" class="form-control" name="new_password" required>
                                            <small class="text-muted">Minimum 8 characters, include uppercase, lowercase, and numbers</small>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Confirm New Password</label>
                                            <input type="password" class="form-control" name="new_password_confirmation" required>
                                        </div>

                                        <div class="alert alert-info" role="alert">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Password Tips:</strong>
                                            <ul class="mb-0 mt-2 small">
                                                <li>Use a combination of letters, numbers, and symbols</li>
                                                <li>Avoid using personal information</li>
                                                <li>Don't reuse passwords from other sites</li>
                                            </ul>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary-custom">
                                                <i class="fas fa-lock me-2"></i>Update Password
                                            </button>
                                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                        </div>
                                    </form>

                                    <hr class="my-4">

                                    <div class="mb-4">
                                        <h6 class="fw-semibold mb-3">Two-Factor Authentication</h6>
                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                            <div>
                                                <p class="mb-1 fw-semibold">Enable 2FA</p>
                                                <p class="text-muted small mb-0">Add an extra layer of security to your account</p>
                                            </div>
                                            <span class="badge bg-secondary">Coming Soon</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notification Settings -->
                                <div class="tab-pane fade" id="notifications">
                                    <h5 class="fw-bold mb-3">Notification Preferences</h5>
                                    <p class="text-muted small mb-4">Choose how you want to be notified</p>

                                    <form action="{{ route('student.settings.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="section" value="notifications">

                                        <div class="mb-4">
                                            <h6 class="fw-semibold mb-3">Email Notifications</h6>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="notify_document_status" id="notifyDocStatus" checked>
                                                <label class="form-check-label" for="notifyDocStatus">
                                                    <strong>Document Status Updates</strong>
                                                    <small class="d-block text-muted">Get notified when your documents are reviewed</small>
                                                </label>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="notify_application_updates" id="notifyAppUpdates" checked>
                                                <label class="form-check-label" for="notifyAppUpdates">
                                                    <strong>Application Updates</strong>
                                                    <small class="d-block text-muted">Important updates about your application</small>
                                                </label>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="notify_messages" id="notifyMessages" checked>
                                                <label class="form-check-label" for="notifyMessages">
                                                    <strong>New Messages</strong>
                                                    <small class="d-block text-muted">When you receive a new message from counselor</small>
                                                </label>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="notify_reminders" id="notifyReminders" checked>
                                                <label class="form-check-label" for="notifyReminders">
                                                    <strong>Reminders</strong>
                                                    <small class="d-block text-muted">Deadline reminders and important dates</small>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <h6 class="fw-semibold mb-3">Marketing Communications</h6>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="notify_newsletter" id="notifyNewsletter">
                                                <label class="form-check-label" for="notifyNewsletter">
                                                    <strong>Newsletter</strong>
                                                    <small class="d-block text-muted">Receive our monthly newsletter with tips and updates</small>
                                                </label>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="notify_promotions" id="notifyPromotions">
                                                <label class="form-check-label" for="notifyPromotions">
                                                    <strong>Promotional Emails</strong>
                                                    <small class="d-block text-muted">Special offers and program announcements</small>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary-custom">
                                                <i class="fas fa-save me-2"></i>Save Preferences
                                            </button>
                                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Privacy Settings -->
                                <div class="tab-pane fade" id="privacy">
                                    <h5 class="fw-bold mb-3">Privacy Settings</h5>
                                    <p class="text-muted small mb-4">Control your privacy and data sharing preferences</p>

                                    <div class="mb-4">
                                        <h6 class="fw-semibold mb-3">Data Visibility</h6>
                                        
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="profileVisibility" checked disabled>
                                            <label class="form-check-label" for="profileVisibility">
                                                <strong>Profile Visibility</strong>
                                                <small class="d-block text-muted">Allow counselors to view your profile</small>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="showProgress" checked>
                                            <label class="form-check-label" for="showProgress">
                                                <strong>Show Application Progress</strong>
                                                <small class="d-block text-muted">Display progress statistics on dashboard</small>
                                            </label>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="mb-4">
                                        <h6 class="fw-semibold mb-3 text-danger">Danger Zone</h6>
                                        
                                        <div class="alert alert-warning" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Account Actions</strong>
                                            <p class="mb-0 mt-2 small">These actions are permanent and cannot be undone. Please proceed with caution.</p>
                                        </div>

                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#downloadDataModal">
                                            <i class="fas fa-download me-2"></i>Download My Data
                                        </button>
                                        
                                        <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                            <i class="fas fa-trash me-2"></i>Delete Account
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Download Data Modal -->
<div class="modal fade" id="downloadDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Your Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Request a copy of your personal data. We'll prepare a file containing all your information and send it to your email address.</p>
                <p class="text-muted small mb-0">This process may take up to 24 hours to complete.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary-custom">Request Data Download</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> This action is permanent and cannot be undone.
                </div>
                <p>Deleting your account will:</p>
                <ul>
                    <li>Permanently delete all your data</li>
                    <li>Remove all uploaded documents</li>
                    <li>Cancel any pending applications</li>
                    <li>Remove your profile information</li>
                </ul>
                <p class="fw-bold">Are you absolutely sure you want to proceed?</p>
                <div class="mb-3">
                    <label class="form-label">Type <strong>DELETE</strong> to confirm:</label>
                    <input type="text" class="form-control" id="deleteConfirmation" placeholder="Type DELETE">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>Delete My Account</button>
            </div>
        </div>
    </div>
</div>@endsection

@push('styles')
<style>
    .settings-tab {
        padding: 12px 16px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 8px;
    }

    .settings-tab:hover {
        background-color: #f8f9fa !important;
    }

    .settings-tab.active {
        background-color: var(--primary) !important;
        color: white !important;
    }

    .settings-tab.active i {
        color: white !important;
    }

    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .form-check-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(220, 20, 60, 0.25);
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Delete account confirmation
    document.getElementById('deleteConfirmation')?.addEventListener('input', function(e) {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (e.target.value === 'DELETE') {
            confirmBtn.disabled = false;
        } else {
            confirmBtn.disabled = true;
        }
    });

    // Show toast on form submission
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            // You can add loading state here
        });
    });
</script>
@endpush
