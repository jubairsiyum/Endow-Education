<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Student Portal') - Endow Education</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #DC143C;
            --primary-dark: #B01030;
            --secondary: #1a1a1a;
            --light-bg: #f8f9fa;
            --sidebar-width: 260px;
            --topbar-height: 65px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-bg);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--secondary);
            color: #fff;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-header {
            padding: 20px;
            background: #000;
            border-bottom: 2px solid var(--primary);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
        }

        .brand-text h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }

        .brand-text p {
            margin: 0;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            margin: 2px 12px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .menu-item i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(2px);
        }

        .menu-item.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.3);
        }

        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Topbar */
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-info h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary);
        }

        .breadcrumb-text {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .user-menu:hover {
            background: var(--light-bg);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--secondary);
            display: block;
            line-height: 1.2;
        }

        .user-role {
            font-size: 12px;
            color: #666;
        }

        /* Content Area */
        .content-area {
            padding: 30px;
        }

        /* Cards */
        .card-custom {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }

        .card-header-custom {
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fff;
            border-radius: 12px 12px 0 0;
        }

        .card-header-custom h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--secondary);
        }

        .card-body-custom {
            padding: 24px;
        }

        .card-footer-custom {
            padding: 20px 24px;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
            border-radius: 0 0 12px 12px;
        }

        /* Buttons */
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.3);
        }

        /* Badges */
        .badge-custom {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 12px;
        }

        .badge-primary-custom {
            background: var(--primary);
            color: white;
        }

        .badge-success-custom {
            background: #28a745;
            color: white;
        }

        .badge-warning-custom {
            background: #ffc107;
            color: #000;
        }

        .badge-danger-custom {
            background: #dc3545;
            color: white;
        }

        .badge-info-custom {
            background: #17a2b8;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .topbar {
                padding: 0 15px;
            }

            .content-area {
                padding: 20px 15px;
            }
        }

        /* Table Styles */
        .table {
            margin: 0;
        }

        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: var(--secondary);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px;
        }

        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            font-size: 14px;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 10px 14px;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 14px;
            color: var(--secondary);
            margin-bottom: 8px;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <div class="brand-icon">E</div>
                <div class="brand-text">
                    <h4>Endow Education</h4>
                    <p>Student Portal</p>
                </div>
            </a>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('student.dashboard') }}" class="menu-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('student.documents') }}" class="menu-item {{ request()->routeIs('student.documents') ? 'active' : '' }}">
                <i class="fas fa-file-upload"></i>
                <span>Submit Documents</span>
            </a>

            <a href="{{ route('student.profile') }}" class="menu-item {{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>

            <a href="{{ route('student.faq') }}" class="menu-item {{ request()->routeIs('student.faq') ? 'active' : '' }}">
                <i class="fas fa-question-circle"></i>
                <span>FAQ</span>
            </a>

            <a href="{{ route('student.emergency-contact') }}" class="menu-item {{ request()->routeIs('student.emergency-contact') ? 'active' : '' }}">
                <i class="fas fa-phone-alt"></i>
                <span>Emergency Contact</span>
            </a>

            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>

            <form id="logout-form" action="{{ route('student.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <div class="topbar">
            <div class="page-info">
                <h5>@yield('page-title', 'Dashboard')</h5>
                <p class="breadcrumb-text">@yield('breadcrumb', 'Home')</p>
            </div>

            <div class="topbar-actions">
                <div class="user-menu">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-role">Student</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
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

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
