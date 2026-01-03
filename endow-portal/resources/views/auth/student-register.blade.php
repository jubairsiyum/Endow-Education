@extends('layouts.app')

@section('title', 'Student Registration - Endow Global Education')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
    }

    /* Professional Header */
    .professional-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        padding: 20px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .brand {
        display: flex;
        align-items: center;
        gap: 15px;
        color: white;
    }
    
    .brand-logo {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #DC143C 0%, #ff6b6b 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
        color: white;
    }
    
    .brand-text h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    
    .brand-text p {
        font-size: 0.75rem;
        color: #a8b2d1;
        margin: 0;
    }
    
    .header-contact {
        display: flex;
        gap: 25px;
        align-items: center;
    }
    
    .contact-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .contact-item:hover {
        color: #DC143C;
    }
    
    .contact-item i {
        color: #DC143C;
        font-size: 1.1rem;
    }

    /* Main Content */
    .register-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: calc(100vh - 160px);
        padding: 60px 20px;
    }
    
    .register-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .register-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        animation: fadeInUp 0.6s ease;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .register-header {
        background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
        padding: 50px 40px;
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
        opacity: 0.1;
    }
    
    .register-header-content {
        position: relative;
        z-index: 1;
    }
    
    .register-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        animation: bounceIn 0.8s ease;
    }
    
    @keyframes bounceIn {
        0%, 20%, 40%, 60%, 80%, 100% {
            animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
        }
        0% {
            opacity: 0;
            transform: scale3d(.3, .3, .3);
        }
        20% {
            transform: scale3d(1.1, 1.1, 1.1);
        }
        40% {
            transform: scale3d(.9, .9, .9);
        }
        60% {
            opacity: 1;
            transform: scale3d(1.03, 1.03, 1.03);
        }
        80% {
            transform: scale3d(.97, .97, .97);
        }
        100% {
            opacity: 1;
            transform: scale3d(1, 1, 1);
        }
    }
    
    .register-icon i {
        font-size: 40px;
        color: white;
    }
    
    .register-header h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: white;
        margin-bottom: 10px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .register-header p {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
    }
    
    .register-body {
        padding: 50px 40px;
    }
    
    .welcome-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 25px 30px;
        border-radius: 16px;
        color: white;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .welcome-banner i {
        font-size: 3rem;
        opacity: 0.9;
    }
    
    .welcome-text h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .welcome-text p {
        margin: 0;
        opacity: 0.95;
        font-size: 0.95rem;
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
    
    .required {
        color: #DC143C;
        margin-left: 3px;
    }
    
    .form-control, .form-select {
        width: 100%;
        padding: 14px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
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
        margin-top: 6px;
        display: block;
    }
    
    .divider {
        display: flex;
        align-items: center;
        margin: 35px 0;
    }
    
    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }
    
    .divider span {
        padding: 0 20px;
        color: #718096;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .info-box {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #2196F3;
        padding: 20px 25px;
        border-radius: 12px;
        margin: 30px 0;
    }
    
    .info-box i {
        color: #1976d2;
        font-size: 1.2rem;
        margin-right: 10px;
    }
    
    .info-box strong {
        color: #1565c0;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 40px;
    }
    
    .btn-back {
        flex: 1;
        padding: 16px 30px;
        border: 2px solid #cbd5e0;
        background: white;
        color: #4a5568;
        border-radius: 12px;
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
    }
    
    .btn-register {
        flex: 2;
        padding: 16px 40px;
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
    
    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
    }
    
    .btn-register i {
        margin-right: 8px;
    }

    /* Professional Footer */
    .professional-footer {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: white;
        padding: 40px 0 20px;
        margin-top: auto;
    }
    
    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 30px;
    }
    
    .footer-section h4 {
        color: white;
        margin-bottom: 20px;
        font-size: 1.1rem;
        font-weight: 700;
    }
    
    .footer-section p {
        color: #a8b2d1;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
    }
    
    .footer-links li {
        margin-bottom: 12px;
    }
    
    .footer-links a {
        color: #a8b2d1;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-block;
    }
    
    .footer-links a:hover {
        color: #DC143C;
        transform: translateX(5px);
    }
    
    .footer-contact-item {
        display: flex;
        align-items: start;
        gap: 10px;
        margin-bottom: 15px;
        color: #a8b2d1;
    }
    
    .footer-contact-item i {
        color: #DC143C;
        margin-top: 4px;
    }
    
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 20px;
        text-align: center;
        color: #a8b2d1;
        font-size: 0.9rem;
    }
    
    .footer-bottom a {
        color: #DC143C;
        text-decoration: none;
    }
    
    .footer-bottom a:hover {
        text-decoration: underline;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 15px;
        }
        
        .header-contact {
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }
        
        .contact-item {
            font-size: 0.9rem;
        }
        
        .register-wrapper {
            padding: 30px 15px;
        }
        
        .register-header {
            padding: 40px 20px;
        }
        
        .register-header h2 {
            font-size: 1.8rem;
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
        
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
</style>
@endpush

@section('content')
<!-- Professional Header -->
<div class="professional-header">
    <div class="header-content">
        <div class="brand">
            <div class="brand-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="brand-text">
                <h1>Endow Global Education</h1>
                <p>Global Vision, Guided Path</p>
            </div>
        </div>
        <div class="header-contact">
            <a href="tel:+8801901463204" class="contact-item">
                <i class="fas fa-phone"></i>
                <span>+880 19014 63204</span>
            </a>
            <a href="mailto:contact@endowglobaledu.com" class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>contact@endowglobaledu.com</span>
            </a>
        </div>
    </div>
</div>

<!-- Main Registration Content -->
<div class="register-wrapper">
    <div class="register-container">
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
                        <p>Join thousands of students achieving their dreams of studying abroad. Complete your registration to get started.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('student.register') }}" id="studentRegisterForm">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i>Full Name<span class="required">*</span>
                                </label>
                                <input id="name" type="text" class="form-control" name="name" 
                                       value="{{ old('name') }}" required placeholder="Enter your complete name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i>Email Address<span class="required">*</span>
                                </label>
                                <input id="email" type="email" class="form-control" name="email" 
                                       value="{{ old('email') }}" required placeholder="your.email@example.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i>Phone Number<span class="required">*</span>
                                </label>
                                <input id="phone" type="tel" class="form-control" name="phone" 
                                       value="{{ old('phone') }}" required placeholder="+880 1234 567890">
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
                                <input id="date_of_birth" type="date" class="form-control" name="date_of_birth" 
                                       value="{{ old('date_of_birth') }}" required max="{{ date('Y-m-d', strtotime('-15 years')) }}">
                                <small class="form-helper">You must be at least 15 years old</small>
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
                                <input id="password" type="password" class="form-control" name="password" 
                                       required minlength="8" placeholder="Minimum 8 characters">
                                <small class="form-helper">Use a strong password with letters, numbers, and symbols</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle"></i>Confirm Password<span class="required">*</span>
                                </label>
                                <input id="password_confirmation" type="password" class="form-control" 
                                       name="password_confirmation" required minlength="8" placeholder="Re-enter your password">
                            </div>
                        </div>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <strong>What's Next?</strong> After registration, you can complete additional details like family information, address, and passport details in your profile dashboard once approved.
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
    </div>
</div>

<!-- Professional Footer -->
<div class="professional-footer">
    <div class="footer-content">
        <div class="footer-grid">
            <div class="footer-section">
                <h4>About Endow Global</h4>
                <p>Endow Global Education is dedicated to guiding students toward international academic success with personalized support and experienced consultants.</p>
                <p style="color: #DC143C; font-weight: 600; font-style: italic;">"Global Vision, Guided Path"</p>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="https://endowglobaledu.com/about-us/"><i class="fas fa-angle-right"></i> About Us</a></li>
                    <li><a href="https://endowglobaledu.com/why-endow-global/"><i class="fas fa-angle-right"></i> Why Endow Global?</a></li>
                    <li><a href="https://endowglobaledu.com/stories/"><i class="fas fa-angle-right"></i> Success Stories</a></li>
                    <li><a href="https://endowglobaledu.com/apply-now/"><i class="fas fa-angle-right"></i> Apply Now</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact Information</h4>
                <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>3rd floor, House-17, Road-01, Mohammadia Housing Society, Mohammadpur, Dhaka-1207</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone"></i>
                    <span>+880 19014 63204</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>contact@endowglobaledu.com</span>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Endow Global Education. All rights reserved. | 
            <a href="https://endowglobaledu.com/privacy-policy/">Privacy Policy</a> | 
            <a href="{{ route('admin.login') }}">Staff Login</a></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
            html: '<ul style="text-align: left; padding-left: 20px;">' +
                @foreach($errors->all() as $error)
                    '<li>{{ $error }}</li>' +
                @endforeach
                '</ul>',
            confirmButtonColor: '#DC143C'
        });
    @endif

    // Form validation
    document.getElementById('studentRegisterForm').addEventListener('submit', function(e) {
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
@endpush
