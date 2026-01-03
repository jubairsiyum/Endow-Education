@extends('layouts.app')

@section('title', 'Student Login - Endow Global Education')

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
    .login-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: calc(100vh - 160px);
        padding: 60px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .login-container {
        max-width: 500px;
        width: 100%;
    }
    
    .login-card {
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
    
    .login-header {
        background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
        padding: 50px 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .login-header::before {
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
    
    .login-header-content {
        position: relative;
        z-index: 1;
    }
    
    .login-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .login-icon i {
        font-size: 40px;
        color: white;
    }
    
    .login-header h2 {
        font-size: 2.2rem;
        font-weight: 800;
        color: white;
        margin-bottom: 10px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .login-header p {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
    }
    
    .login-body {
        padding: 40px;
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
        margin-bottom: 25px;
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
    }
    
    .remember-me label {
        margin: 0;
        cursor: pointer;
        font-size: 0.9rem;
        color: #4a5568;
    }
    
    .forgot-password {
        color: #DC143C;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .forgot-password:hover {
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
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .action-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 25px;
    }
    
    .option-card {
        padding: 20px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s;
        background: white;
    }
    
    .option-card:hover {
        border-color: #DC143C;
        background: #fff5f5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 20, 60, 0.15);
    }
    
    .option-card i {
        font-size: 2rem;
        margin-bottom: 10px;
        display: block;
    }
    
    .option-card.register i {
        color: #667eea;
    }
    
    .option-card.admin i {
        color: #48bb78;
    }
    
    .option-card h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }
    
    .option-card p {
        font-size: 0.85rem;
        color: #718096;
        margin: 0;
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
        
        .login-wrapper {
            padding: 30px 15px;
        }
        
        .login-header {
            padding: 40px 20px;
        }
        
        .login-header h2 {
            font-size: 1.8rem;
        }
        
        .login-body {
            padding: 30px 20px;
        }
        
        .action-options {
            grid-template-columns: 1fr;
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

<!-- Main Login Content -->
<div class="login-wrapper">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-header-content">
                    <div class="login-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h2>Welcome Back</h2>
                    <p>Login to access your student portal</p>
                </div>
            </div>

            <div class="login-body">
                <form method="POST" action="{{ route('student.login') }}" id="studentLoginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>Email Address
                        </label>
                        <input id="email" type="email" class="form-control" name="email" 
                               value="{{ old('email') }}" required placeholder="your.email@example.com">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>Password
                        </label>
                        <input id="password" type="password" class="form-control" name="password" 
                               required placeholder="Enter your password">
                    </div>

                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Remember me</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>

                <div class="divider">
                    <span>Other Options</span>
                </div>

                <div class="action-options">
                    <a href="{{ route('student.register') }}" class="option-card register">
                        <i class="fas fa-user-plus"></i>
                        <h4>New Student</h4>
                        <p>Create Account</p>
                    </a>
                    <a href="{{ route('admin.login') }}" class="option-card admin">
                        <i class="fas fa-user-shield"></i>
                        <h4>Staff Login</h4>
                        <p>Admin Portal</p>
                    </a>
                </div>
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
            <a href="{{ route('student.register') }}">Student Registration</a></p>
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
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#DC143C'
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
            html: '<ul style="text-align: left; padding-left: 20px;">' +
                @foreach($errors->all() as $error)
                    '<li>{{ $error }}</li>' +
                @endforeach
                '</ul>',
            confirmButtonColor: '#DC143C'
        });
    @endif
</script>
@endpush
