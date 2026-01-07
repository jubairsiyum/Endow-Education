<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Login - Endow Global Education</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-page {
            min-height: 100vh;
            background: linear-gradient(rgba(26, 26, 46, 0.85), rgba(26, 26, 46, 0.85)),
                        url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #DC143C 0%, #ff6b6b 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 30px rgba(220, 20, 60, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .brand-logo i {
            font-size: 40px;
            color: white;
        }

        .brand-title {
            color: white;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 8px;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
        }

        .brand-tagline {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            font-weight: 500;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #718096;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
            display: block;
            font-size: 0.95rem;
        }

        .form-label i {
            color: #DC143C;
            margin-right: 8px;
        }

        .input-group {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #DC143C;
            background: white;
            box-shadow: 0 0 0 4px rgba(220, 20, 60, 0.1);
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0 30px;
            flex-wrap: wrap;
            gap: 10px;
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
            font-size: 0.95rem;
            color: #4a5568;
        }

        .forgot-link {
            color: #DC143C;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            margin-right: 8px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            padding: 0 15px;
            color: #718096;
            font-size: 0.9rem;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
        }

        .register-link p {
            color: #4a5568;
            font-size: 0.95rem;
        }

        .register-link a {
            color: #DC143C;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .footer-text {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            margin-top: 30px;
        }

        .footer-text a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        /* Mobile Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 40px 25px;
            }

            .brand-title {
                font-size: 1.5rem;
            }

            .login-header h2 {
                font-size: 1.5rem;
            }

            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="brand-header">
                <div class="brand-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="brand-title">Endow Global Education</h1>
                <p class="brand-tagline">Global Vision, Guided Path</p>
            </div>

            <div class="login-card">
                <div class="login-header">
                    <h2>Welcome Back!</h2>
                    <p>Sign in to access your student portal</p>
                </div>

                <form method="POST" action="{{ route('student.login') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            class="form-control"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="your.email@example.com"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            class="form-control"
                            name="password"
                            required
                            placeholder="Enter your password"
                        >
                    </div>

                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('student.password.request') }}" class="forgot-link">
                            Forgot Password?
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>

                <div class="divider">
                    <span>New to Endow?</span>
                </div>

                <div class="register-link">
                    <p>Don't have an account? <a href="{{ route('student.register') }}">Create Account</a></p>
                </div>
            </div>

            <div class="footer-text">
                <p>&copy; {{ date('Y') }} Endow Global Education. All rights reserved.<br>
                <a href="https://endowglobaledu.com">Visit our website</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Registration Success notification
        @if(session('registration_success'))
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                html: `
                    <div style="text-align: center; padding: 20px;">
                        <p style="font-size: 1.1rem; margin-bottom: 15px;">{{ session('registration_success') }}</p>
                        <div style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin-top: 20px; text-align: left; border-radius: 8px;">
                            <p style="margin: 0; color: #1e40af; font-size: 0.95rem;">
                                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                                <strong>What happens next?</strong>
                            </p>
                            <ul style="margin: 10px 0 0 25px; color: #475569; font-size: 0.9rem;">
                                <li>Our team will review your registration</li>
                                <li>You'll receive an email notification once approved</li>
                                <li>After approval, you can login with your credentials</li>
                            </ul>
                        </div>
                    </div>
                `,
                confirmButtonColor: '#10B981',
                confirmButtonText: '<i class="fas fa-check"></i> Got it!',
                width: '600px',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                }
            });
        @endif

        // SweetAlert notifications
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#DC143C',
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                html: '<p>{{ session('error') }}</p>',
                confirmButtonColor: '#DC143C'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Login Error',
                html: '<ul style="text-align: left; padding-left: 20px; list-style: none;">' +
                    @foreach($errors->all() as $error)
                        '<li><i class="fas fa-times-circle" style="color: #DC143C; margin-right: 8px;"></i>{{ $error }}</li>' +
                    @endforeach
                    '</ul>',
                confirmButtonColor: '#DC143C'
            });
        @endif
    </script>
</body>
</html>
