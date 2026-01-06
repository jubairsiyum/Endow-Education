<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Portal - Endow Connect</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .admin-login-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #ffffff;
        }

        /* Left Panel - Dark Theme */
        .left-panel {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #2d2d2d 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(220, 20, 60, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -200px;
            right: -200px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(220, 20, 60, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
        }

        .brand-section {
            text-align: center;
            z-index: 2;
            max-width: 500px;
        }

        .brand-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 40px;
            box-shadow: 0 20px 60px rgba(220, 20, 60, 0.4);
            position: relative;
        }

        .brand-icon::before {
            content: '';
            position: absolute;
            inset: -3px;
            background: linear-gradient(135deg, #DC143C, #8B0000);
            border-radius: 24px;
            z-index: -1;
            opacity: 0.3;
            filter: blur(20px);
        }

        .brand-icon i {
            font-size: 48px;
            color: white;
        }

        .brand-title {
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .features-list {
            text-align: left;
            margin-top: 60px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }

        .feature-item i {
            color: #DC143C;
            font-size: 1.2rem;
            margin-right: 16px;
            width: 24px;
            text-align: center;
        }

        /* Right Panel - Login Form */
        .right-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            background: #ffffff;
        }

        .login-container {
            width: 100%;
            max-width: 460px;
        }

        .login-header {
            margin-bottom: 48px;
        }

        .login-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #6b7280;
            font-size: 1rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
            display: block;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: #f9fafb;
            font-weight: 400;
        }

        .form-control:focus {
            outline: none;
            border-color: #DC143C;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.08);
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #DC143C;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 24px 0 32px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #DC143C;
        }

        .remember-me label {
            margin: 0;
            cursor: pointer;
            font-size: 0.9rem;
            color: #4b5563;
            font-weight: 500;
        }

        .forgot-link {
            color: #DC143C;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #B0102F;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(220, 20, 60, 0.25);
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 20, 60, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-login i {
            margin-right: 8px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 36px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            padding: 0 16px;
            color: #9ca3af;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .student-portal-link {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .student-portal-link p {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .btn-student {
            display: inline-flex;
            align-items: center;
            padding: 10px 24px;
            background: white;
            color: #1a1a1a;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-student:hover {
            background: #1a1a1a;
            color: white;
            border-color: #1a1a1a;
        }

        .btn-student i {
            margin-right: 8px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            border: none;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 3px solid #DC143C;
        }

        .footer-note {
            text-align: center;
            color: #9ca3af;
            font-size: 0.85rem;
            margin-top: 40px;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .admin-login-page {
                grid-template-columns: 1fr;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                padding: 40px 20px;
            }
        }

        @media (max-width: 640px) {
            .login-header h1 {
                font-size: 1.75rem;
            }

            .brand-title {
                font-size: 2rem;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }

        /* Loading State */
        .btn-login.loading {
            position: relative;
            color: transparent;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="admin-login-page">
        <!-- Left Panel - Branding -->
        <div class="left-panel">
            <div class="brand-section">
                <div class="brand-icon">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h1 class="brand-title">Admin Portal</h1>
                <p class="brand-subtitle">Secure access to Endow Connect Management System</p>

                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span>Enterprise-grade security</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Real-time analytics & reporting</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <span>Comprehensive user management</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-cog"></i>
                        <span>Advanced system configuration</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="right-panel">
            <div class="login-container">
                <div class="login-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to access your administrator account</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Authentication Failed:</strong> {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">EMAIL ADDRESS</label>
                        <input id="email"
                               type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus
                               placeholder="admin@endowconnect.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">PASSWORD</label>
                        <div class="password-wrapper">
                            <input id="password"
                                   type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Enter your password">
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Keep me signed in</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-login" id="loginButton">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>

                <div class="divider">
                    <span>OR</span>
                </div>

                <div class="student-portal-link">
                    <p>Looking for the student portal?</p>
                    <a href="{{ route('student.login') }}" class="btn-student">
                        <i class="fas fa-graduation-cap"></i> Student Login
                    </a>
                </div>

                <div class="footer-note">
                    <p>&copy; {{ date('Y') }} Endow Connect. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput && toggleIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'password') {
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    } else {
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    }
                });
            }

            // Form submission with loading state
            const form = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');

            if (form && loginButton) {
                form.addEventListener('submit', function() {
                    loginButton.classList.add('loading');
                    loginButton.disabled = true;
                });
            }

            // Auto-focus on first input with error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.focus();
            }
        });
    </script>
</body>
</html>
