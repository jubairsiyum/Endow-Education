<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@hasSection('page-title')Endow Connect - @yield('page-title')@else Endow Connect - Student Portal @endif</title>

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

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .menu-text,
        .sidebar.collapsed .menu-label {
            display: none;
        }

        .sidebar.collapsed .sidebar-brand span {
            display: none;
        }

        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 12px;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-header {
            padding: 24px 20px;
            background: var(--primary);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .sidebar-brand i {
            font-size: 28px;
            color: white;
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
            display: flex;
            flex-direction: column;
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: 70px;
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

        .sidebar-toggle {
            background: var(--primary);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 15px;
        }

        .sidebar-toggle:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        .page-info h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary);
            line-height: 1.3;
        }

        .breadcrumb-text {
            font-size: 13px;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        @media (max-width: 576px) {
            .topbar-actions {
                gap: 10px;
            }
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

        @media (max-width: 768px) {
            .user-menu {
                padding: 6px 10px;
                gap: 8px;
            }
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
            flex: 1;
        }

        /* Footer Styles */
        .student-footer {
            background: #ffffff;
            color: #6c757d;
            padding: 25px 30px;
            margin-top: auto;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-left p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #495057;
        }

        .footer-left p:first-child {
            font-weight: 600;
            margin-bottom: 5px;
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
            line-height: 1.4;
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

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .content-area {
                padding: 15px;
            }
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

            .page-info h5 {
                font-size: 16px;
                line-height: 1.3;
            }

            .breadcrumb-text {
                font-size: 11px;
            }

            .content-area {
                padding: 15px 12px;
            }

            .card-custom {
                margin-bottom: 16px;
            }

            .card-header-custom {
                padding: 15px 16px;
            }

            .card-header-custom h5 {
                font-size: 14px;
            }

            .card-body-custom {
                padding: 16px;
            }

            .card-footer-custom {
                padding: 15px 16px;
            }

            /* Alert headings */
            .alert h5 {
                font-size: 15px !important;
            }

            .alert h4 {
                font-size: 16px !important;
            }

            .alert p {
                font-size: 13px;
            }

            /* Button adjustments */
            .btn {
                font-size: 13px;
                padding: 8px 14px;
            }

            .btn-lg {
                font-size: 14px;
                padding: 10px 18px;
            }

            .btn-sm {
                font-size: 12px;
                padding: 6px 10px;
            }

            /* User menu adjustments */
            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .user-name {
                font-size: 13px;
            }

            .user-role {
                font-size: 11px;
            }

            /* Table responsive improvements */
            .table {
                font-size: 13px;
            }

            .table thead th {
                font-size: 11px;
                padding: 10px 8px;
            }

            .table tbody td {
                padding: 10px 8px;
                font-size: 13px;
            }

            /* Form controls */
            .form-control, .form-select {
                font-size: 13px;
                padding: 8px 12px;
            }

            .form-label {
                font-size: 13px;
            }

            /* Badge adjustments */
            .badge {
                font-size: 11px;
                padding: 4px 8px;
            }

            /* Hide user info text on very small screens */
            .user-info {
                display: none;
            }

            .sidebar-toggle {
                padding: 10px;
            }

            /* Footer adjustments */
            .student-footer {
                padding: 20px 15px;
            }

            .footer-left p {
                font-size: 12px;
            }
        }

        /* Extra small devices (phones in portrait, less than 576px) */
        @media (max-width: 576px) {
            .page-info h5 {
                font-size: 14px;
            }

            .breadcrumb-text {
                font-size: 10px;
            }

            .content-area {
                padding: 12px 10px;
            }

            .card-header-custom h5 {
                font-size: 13px;
            }

            .alert h5 {
                font-size: 14px !important;
            }

            .alert h4 {
                font-size: 15px !important;
            }

            .alert p, .alert small {
                font-size: 12px;
            }

            .alert .fa-2x {
                font-size: 1.5em !important;
            }

            /* Stat cards in dashboard */
            .stat-card h2, .stat-card h3 {
                font-size: 1.5rem !important;
            }

            .stat-card h4 {
                font-size: 1.1rem !important;
            }

            .stat-card p, .stat-card small {
                font-size: 11px;
            }
        }

        /* Medium devices (tablets, 768px to 991px) */
        @media (min-width: 768px) and (max-width: 991px) {
            .page-info h5 {
                font-size: 18px;
            }

            .content-area {
                padding: 20px;
            }

            .card-body-custom {
                padding: 20px;
            }
        }

        /* Large devices improvements */
        @media (min-width: 1200px) {
            .content-area {
                padding: 35px;
            }

            .card-custom {
                margin-bottom: 28px;
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

        /* Mobile-specific utilities */
        .mobile-text-sm {
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .mobile-text-sm {
                font-size: 12px;
            }

            .mobile-text-xs {
                font-size: 11px;
            }

            /* Improve readability on mobile */
            h1 { font-size: 1.75rem !important; }
            h2 { font-size: 1.5rem !important; }
            h3 { font-size: 1.25rem !important; }
            h4 { font-size: 1.1rem !important; }
            h5 { font-size: 1rem !important; }
            h6 { font-size: 0.9rem !important; }
        }

        @media (max-width: 576px) {
            h1 { font-size: 1.5rem !important; }
            h2 { font-size: 1.3rem !important; }
            h3 { font-size: 1.15rem !important; }
            h4 { font-size: 1rem !important; }
            h5 { font-size: 0.95rem !important; }
            h6 { font-size: 0.85rem !important; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('student.dashboard') }}" class="sidebar-brand">
                <i class="fas fa-graduation-cap" style="font-size: 28px;"></i>
                <span>Endow Connect</span>
            </a>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('student.dashboard') }}" class="menu-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>

            <a href="{{ route('student.profile.edit') }}" class="menu-item {{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span class="menu-text">My Profile</span>
            </a>

            <a href="{{ route('student.documents') }}" class="menu-item {{ request()->routeIs('student.documents') ? 'active' : '' }}">
                <i class="fas fa-file-upload"></i>
                <span class="menu-text">Submit Documents</span>
            </a>

            <a href="{{ route('student.program') }}" class="menu-item {{ request()->routeIs('student.program') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i>
                <span class="menu-text">My Program</span>
            </a>

            <a href="{{ route('student.universities') }}" class="menu-item {{ request()->routeIs('student.universities') ? 'active' : '' }}">
                <i class="fas fa-university"></i>
                <span class="menu-text">University Info</span>
            </a>

            <a href="{{ route('student.faq') }}" class="menu-item {{ request()->routeIs('student.faq') ? 'active' : '' }}">
                <i class="fas fa-question-circle"></i>
                <span class="menu-text">Help & FAQ</span>
            </a>

            <a href="{{ route('student.emergency-contact') }}" class="menu-item {{ request()->routeIs('student.emergency-contact') ? 'active' : '' }}">
                <i class="fas fa-phone-alt"></i>
                <span class="menu-text">Support</span>
            </a>

            <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 12px;"></div>

            <a href="{{ route('student.settings') }}" class="menu-item {{ request()->routeIs('student.settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span class="menu-text">Settings</span>
            </a>

            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-text">Logout</span>
            </a>

            <form id="logout-form" action="{{ route('student.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-wrapper" id="mainWrapper">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle" id="sidebarToggle" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="page-info">
                    <h5>@yield('page-title', 'Dashboard')</h5>
                    <p class="breadcrumb-text">@yield('breadcrumb', 'Home')</p>
                </div>
            </div>

            <div class="topbar-actions">
                <div class="user-menu">
                    @php
                        $currentStudent = Auth::user()->student;
                        $profilePhoto = null;
                        if ($currentStudent) {
                            try {
                                $profilePhoto = $currentStudent->activeProfilePhoto;
                            } catch (\Exception $e) {
                                // Ignore if table doesn't exist
                            }
                        }
                    @endphp
                    <div class="user-avatar" style="overflow: hidden;">
                        @if($profilePhoto && $profilePhoto->photo_path)
                            <img src="{{ $profilePhoto->photo_url }}?t={{ time() }}"
                                 alt="{{ Auth::user()->name }}"
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.style.display='none'; this.parentElement.innerHTML='{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}'">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
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

        <!-- Footer -->
        <footer class="student-footer">
            <div class="footer-content">
                <div class="footer-left">
                    <p>&copy; {{ date('Y') }} <strong>Endow Global Education</strong>. All rights reserved.</p>
                    <p><small>Empowering students to achieve their educational dreams worldwide</small></p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Load saved sidebar state from localStorage
            const sidebarCollapsed = localStorage.getItem('studentSidebarCollapsed') === 'true';
            if (sidebarCollapsed && window.innerWidth > 768) {
                sidebar.classList.add('collapsed');
                mainWrapper.classList.add('sidebar-collapsed');
            }

            // Toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    // Mobile: slide sidebar in/out
                    sidebar.classList.toggle('mobile-open');
                    sidebarOverlay.classList.toggle('active');
                } else {
                    // Desktop: collapse/expand
                    sidebar.classList.toggle('collapsed');
                    mainWrapper.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('studentSidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            });

            // Close sidebar when clicking overlay (mobile)
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            });

            // Close mobile sidebar when clicking a menu item
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('mobile-open');
                        sidebarOverlay.classList.remove('active');
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
