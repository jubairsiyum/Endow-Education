@extends('layouts.admin')

@section('page-title', 'Email Settings')
@section('breadcrumb', 'Home / Settings / Email')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="fas fa-envelope-open-text text-danger"></i> Email Configuration
            </h4>
            <small class="text-muted">Configure SMTP settings and manage email notifications</small>
        </div>
        <a href="{{ route('admin.email-settings.test-form') }}" class="btn btn-outline-primary">
            <i class="fas fa-paper-plane me-1"></i> Test Emails
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
            <form action="{{ route('admin.email-settings.update') }}" method="POST" id="emailSettingsForm">
                @csrf
                @method('PUT')

                <!-- SMTP Configuration -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-server text-danger me-2"></i>SMTP Configuration</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="mailer" class="form-label">Mail Driver <span class="text-danger">*</span></label>
                                <select class="form-select @error('mailer') is-invalid @enderror" 
                                        id="mailer" name="mailer" required>
                                    <option value="smtp" {{ old('mailer', $settings->mailer) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ old('mailer', $settings->mailer) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    <option value="mailgun" {{ old('mailer', $settings->mailer) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ old('mailer', $settings->mailer) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="postmark" {{ old('mailer', $settings->mailer) == 'postmark' ? 'selected' : '' }}>Postmark</option>
                                    <option value="log" {{ old('mailer', $settings->mailer) == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                                </select>
                                @error('mailer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('host') is-invalid @enderror"
                                       id="host" name="host" value="{{ old('host', $settings->host) }}"
                                       placeholder="smtp.gmail.com" required>
                                @error('host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">e.g., smtp.gmail.com, smtp.mailgun.org</small>
                            </div>

                            <div class="col-md-6">
                                <label for="port" class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('port') is-invalid @enderror"
                                       id="port" name="port" value="{{ old('port', $settings->port) }}"
                                       placeholder="587" required>
                                @error('port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Common: 587 (TLS), 465 (SSL), 25</small>
                            </div>

                            <div class="col-md-6">
                                <label for="encryption" class="form-label">Encryption</label>
                                <select class="form-select @error('encryption') is-invalid @enderror" 
                                        id="encryption" name="encryption">
                                    <option value="tls" {{ old('encryption', $settings->encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('encryption', $settings->encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ old('encryption', $settings->encryption) == '' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('encryption')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="username" class="form-label">Username / Email</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       id="username" name="username" value="{{ old('username', $settings->username) }}"
                                       placeholder="your-email@example.com">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Password / App Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" 
                                           placeholder="Leave empty to keep current">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">For Gmail, use App Password (16 characters)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- From Address Configuration -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-at text-danger me-2"></i>From Address Configuration</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="from_address" class="form-label">From Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('from_address') is-invalid @enderror"
                                       id="from_address" name="from_address" 
                                       value="{{ old('from_address', $settings->from_address) }}"
                                       placeholder="noreply@endowconnect.com" required>
                                @error('from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">This will appear as the sender of all emails</small>
                            </div>

                            <div class="col-md-6">
                                <label for="from_name" class="form-label">From Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('from_name') is-invalid @enderror"
                                       id="from_name" name="from_name" 
                                       value="{{ old('from_name', $settings->from_name) }}"
                                       placeholder="Endow Connect" required>
                                @error('from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                           {{ old('is_active', $settings->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Enable Email Notifications
                                    </label>
                                    <small class="d-block text-muted">When disabled, no emails will be sent from the system</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-2"></i> Save Configuration
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="testConnection">
                        <i class="fas fa-plug me-2"></i> Test Connection
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Quick Setup Guides -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-book text-info me-2"></i>Quick Setup Guides</h5>
                </div>
                <div class="card-body p-4">
                    <div class="accordion" id="setupGuides">
                        <!-- Gmail -->
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gmailGuide">
                                    <i class="fab fa-google text-danger me-2"></i> Gmail Setup
                                </button>
                            </h2>
                            <div id="gmailGuide" class="accordion-collapse collapse" data-bs-parent="#setupGuides">
                                <div class="accordion-body">
                                    <ol class="small mb-0">
                                        <li>Enable 2-Factor Authentication</li>
                                        <li>Go to Security → App Passwords</li>
                                        <li>Select Mail → Other (Custom)</li>
                                        <li>Copy 16-character password</li>
                                        <li>Use settings:
                                            <ul>
                                                <li>Host: smtp.gmail.com</li>
                                                <li>Port: 587</li>
                                                <li>Encryption: TLS</li>
                                            </ul>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Mailgun -->
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mailgunGuide">
                                    <i class="fas fa-envelope text-warning me-2"></i> Mailgun Setup
                                </button>
                            </h2>
                            <div id="mailgunGuide" class="accordion-collapse collapse" data-bs-parent="#setupGuides">
                                <div class="accordion-body">
                                    <ol class="small mb-0">
                                        <li>Sign up at mailgun.com</li>
                                        <li>Get SMTP credentials</li>
                                        <li>Use settings:
                                            <ul>
                                                <li>Host: smtp.mailgun.org</li>
                                                <li>Port: 587</li>
                                                <li>Encryption: TLS</li>
                                            </ul>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- SendGrid -->
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sendgridGuide">
                                    <i class="fas fa-paper-plane text-primary me-2"></i> SendGrid Setup
                                </button>
                            </h2>
                            <div id="sendgridGuide" class="accordion-collapse collapse" data-bs-parent="#setupGuides">
                                <div class="accordion-body">
                                    <ol class="small mb-0">
                                        <li>Sign up at sendgrid.com</li>
                                        <li>Create API Key</li>
                                        <li>Use settings:
                                            <ul>
                                                <li>Host: smtp.sendgrid.net</li>
                                                <li>Port: 587</li>
                                                <li>Username: apikey</li>
                                                <li>Password: Your API Key</li>
                                            </ul>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-info-circle text-success me-2"></i>System Status</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Email Status</small>
                        @if($settings->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Disabled</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Current Driver</small>
                        <strong>{{ strtoupper($settings->mailer) }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Queue Status</small>
                        <strong>{{ config('queue.default') }}</strong>
                    </div>

                    <div>
                        <small class="text-muted d-block mb-1">Last Updated</small>
                        <strong>{{ $settings->updated_at->format('M d, Y g:i A') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Test connection
        document.getElementById('testConnection').addEventListener('click', function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Testing...';

            fetch('{{ route("admin.email-settings.test-connection") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Connection Successful!',
                        text: data.message,
                        confirmButtonColor: '#DC143C'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Failed',
                        text: data.message,
                        confirmButtonColor: '#DC143C'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to test connection. Please try again.',
                    confirmButtonColor: '#DC143C'
                });
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });

        // Toggle SMTP fields based on mailer selection
        document.getElementById('mailer').addEventListener('change', function() {
            const smtpFields = ['host', 'port', 'encryption', 'username', 'password'];
            const isSmtp = this.value === 'smtp';
            
            smtpFields.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    element.closest('.col-md-6').style.display = isSmtp || field === 'username' || field === 'password' ? '' : 'none';
                }
            });
        });
    </script>
    @endpush
@endsection
