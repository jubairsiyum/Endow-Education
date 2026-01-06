<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Registration - Endow Global Education</title>
    
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
        
        .register-page {
            min-height: 100vh;
            background: linear-gradient(rgba(26, 26, 46, 0.85), rgba(26, 26, 46, 0.85)),
                        url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat fixed;
            padding: 40px 20px;
        }
        
        .register-container {
            max-width: 900px;
            margin: 0 auto;
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
            font-size: 2.2rem;
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
        
        .register-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }
        
        .register-header {
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .register-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,106.7C1248,96,1344,96,1392,96L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
        }
        
        .register-header-content {
            position: relative;
            z-index: 1;
        }
        
        .register-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .register-icon i {
            font-size: 35px;
            color: white;
        }
        
        .register-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .register-header p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.95);
            margin: 0;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 25px;
            border-radius: 12px;
            color: white;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .welcome-banner i {
            font-size: 2.5rem;
        }
        
        .welcome-text h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .welcome-text p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.95;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
            font-size: 0.95rem;
        }
        
        .form-label i {
            color: #DC143C;
            margin-right: 8px;
        }
        
        .required {
            color: #DC143C;
            margin-left: 3px;
        }
        
        .form-control, .form-select {
            width: 100%;
            padding: 12px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #DC143C;
            background: white;
            box-shadow: 0 0 0 4px rgba(220, 20, 60, 0.1);
        }
        
        .form-control::placeholder {
            color: #a0aec0;
        }
        
        .form-helper {
            font-size: 0.85rem;
            color: #718096;
            margin-top: 5px;
            display: block;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
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
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .info-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-left: 4px solid #2196F3;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 25px 0;
            font-size: 0.9rem;
        }
        
        .info-box i {
            color: #1976d2;
            font-size: 1.1rem;
            margin-right: 8px;
        }
        
        .info-box strong {
            color: #1565c0;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-back {
            flex: 1;
            padding: 14px 25px;
            border: 2px solid #cbd5e0;
            background: white;
            color: #4a5568;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-back:hover {
            background: #f7fafc;
            border-color: #a0aec0;
            transform: translateY(-2px);
            color: #2d3748;
        }
        
        .btn-register {
            flex: 2;
            padding: 14px 30px;
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .btn-register i {
            margin-right: 8px;
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
        @media (max-width: 768px) {
            .brand-title {
                font-size: 1.6rem;
            }
            
            .register-header {
                padding: 30px 20px;
            }
            
            .register-header h2 {
                font-size: 1.6rem;
            }
            
            .register-body {
                padding: 30px 20px;
            }
            
            .welcome-banner {
                flex-direction: column;
                text-align: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-back, .btn-register {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .register-page {
                padding: 20px 15px;
            }
            
            .brand-title {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-page">
        <div class="register-container">
            <div class="brand-header">
                <div class="brand-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="brand-title">Endow Global Education</h1>
                <p class="brand-tagline">Global Vision, Guided Path</p>
            </div>
            
            <div class="register-card">
                <div class="register-header">
                    <div class="register-header-content">
                        <div class="register-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h2>Student Registration</h2>
                        <p>Begin Your Journey to Global Education</p>
                    </div>
                </div>
                
                <div class="register-body">
                    <div class="welcome-banner">
                        <i class="fas fa-globe-asia"></i>
                        <div class="welcome-text">
                            <h3>Welcome to Endow Global Education</h3>
                            <p>Join thousands of students achieving their dreams of studying abroad.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('student.register') }}" id="registerForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user"></i>Full Name<span class="required">*</span>
                                    </label>
                                    <input 
                                        id="name" 
                                        type="text" 
                                        class="form-control" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        required 
                                        placeholder="Enter your complete name"
                                    >
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i>Email Address<span class="required">*</span>
                                    </label>
                                    <input 
                                        id="email" 
                                        type="email" 
                                        class="form-control" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        placeholder="your.email@example.com"
                                    >
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone"></i>Phone Number<span class="required">*</span>
                                    </label>
                                    <input 
                                        id="phone" 
                                        type="tel" 
                                        class="form-control" 
                                        name="phone" 
                                        value="{{ old('phone') }}" 
                                        required 
                                        placeholder="+880 1234 567890"
                                    >
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars"></i>Gender<span class="required">*</span>
                                    </label>
                                    <select id="gender" class="form-select" name="gender" required>
                                        <option value="" selected disabled>Select your gender</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth" class="form-label">
                                        <i class="fas fa-calendar-alt"></i>Date of Birth<span class="required">*</span>
                                    </label>
                                    <input 
                                        id="date_of_birth" 
                                        type="date" 
                                        class="form-control" 
                                        name="date_of_birth" 
                                        value="{{ old('date_of_birth') }}" 
                                        required 
                                        max="{{ date('Y-m-d', strtotime('-15 years')) }}"
                                    >
                                    {{-- <small class="form-helper">You must be at least 15 years old</small> --}}
                                </div>
                            </div>
                        </div>
                        
                        <div class="divider">
                            <span><i class="fas fa-lock"></i> Create Your Password</span>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-key"></i>Password<span class="required">*</span>
                                    </label>
                                    <input 
                                        id="password" 
                                        type="password" 
                                        class="form-control" 
                                        name="password" 
                                        required 
                                        minlength="8" 
                                        placeholder="Minimum 8 characters"
                                    >
                                    <small class="form-helper">Use a strong password</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-check-circle"></i>Confirm Password<span class="required">*</span>
                                    </label>
                                    <input 
                                        id="password_confirmation" 
                                        type="password" 
                                        class="form-control" 
                                        name="password_confirmation" 
                                        required 
                                        minlength="8" 
                                        placeholder="Re-enter your password"
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <strong>What's Next?</strong> After registration, you can complete additional details in your profile once approved.
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ route('student.login') }}" class="btn-back">
                                <i class="fas fa-arrow-left"></i> Back to Login
                            </a>
                            <button type="submit" class="btn-register">
                                <i class="fas fa-check-circle"></i> Create My Account
                            </button>
                        </div>
                    </form>
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
        // SweetAlert notifications
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#DC143C',
                confirmButtonText: 'Go to Login'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('student.login') }}';
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                html: '<p>{{ session('error') }}</p>',
                confirmButtonColor: '#DC143C'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Errors',
                html: '<ul style="text-align: left; padding-left: 20px; list-style: none;">' +
                    @foreach($errors->all() as $error)
                        '<li><i class="fas fa-times-circle" style="color: #DC143C; margin-right: 8px;"></i>{{ $error }}</li>' +
                    @endforeach
                    '</ul>',
                confirmButtonColor: '#DC143C'
            });
        @endif

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Passwords do not match! Please check and try again.',
                    confirmButtonColor: '#DC143C'
                });
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters long!',
                    confirmButtonColor: '#DC143C'
                });
                return false;
            }
        });
    </script>
</body>
</html>
