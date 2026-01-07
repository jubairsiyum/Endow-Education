<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Student Portal</title>

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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .reset-page {
            min-height: 100vh;
            background: linear-gradient(rgba(26, 26, 46, 0.85), rgba(26, 26, 46, 0.85)),
                        url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-container {
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

        .reset-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .reset-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .reset-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .reset-header p {
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
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #DC143C;
            background: white;
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.1);
        }

        .input-group .form-control {
            padding-right: 45px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #718096;
            cursor: pointer;
            transition: color 0.3s;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #DC143C;
        }

        .invalid-feedback, .text-danger {
            color: #e53e3e !important;
            font-size: 0.875rem;
            margin-top: 8px;
            display: block;
        }

        .btn-reset {
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

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
        }

        .password-requirements {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
            font-size: 0.875rem;
            color: #0c4a6e;
        }

        .password-requirements ul {
            margin: 10px 0 0 0;
            padding-left: 20px;
        }

        .password-requirements li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="reset-page">
        <div class="reset-container">
            <div class="brand-header">
                <div class="brand-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="brand-title">Student Portal</h1>
            </div>

            <div class="reset-card">
                <div class="reset-header">
                    <h2><i class="fas fa-shield-alt" style="color: #DC143C; margin-right: 10px;"></i>Reset Password</h2>
                    <p>Enter your new password below.</p>
                </div>

                <form method="POST" action="{{ route('student.password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <input id="email"
                               type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ $email ?? old('email') }}"
                               required
                               autocomplete="email"
                               autofocus
                               placeholder="student@example.com">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            New Password
                        </label>
                        <div class="input-group">
                            <input id="password"
                                   type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Enter new password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="password-requirements">
                            <i class="fas fa-info-circle"></i> <strong>Password must contain:</strong>
                            <ul>
                                <li>At least 8 characters</li>
                                <li>Mix of letters and numbers recommended</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="form-label">
                            <i class="fas fa-lock"></i>
                            Confirm New Password
                        </label>
                        <div class="input-group">
                            <input id="password-confirm"
                                   type="password"
                                   class="form-control"
                                   name="password_confirmation"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Confirm new password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password-confirm')">
                                <i class="fas fa-eye" id="password-confirm-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-reset">
                        <i class="fas fa-check"></i>
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
