<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@hasSection('title'){{ config('app.name', 'Endow Connect') }} - @yield('title')@else{{ config('app.name', 'Endow Connect') }}@endif</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/endow-theme.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary: #DC143C;
            --primary-dark: #B8102C;
            --primary-light: #FF1744;
            --secondary: #1a1a1a;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #DC143C;
            --info: #3B82F6;
            --dark: #1a1a1a;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1E293B;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gray-50);
            color: var(--dark);
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--dark);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .sidebar-brand span,
        .sidebar.collapsed .menu-item span,
        .sidebar.collapsed .menu-section-title,
        .sidebar.collapsed .menu-badge {
            display: none;
        }

        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .sidebar-header {
            padding: 24px 20px;
            background: var(--primary);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .sidebar-brand i {
            font-size: 28px;
        }

        .sidebar-menu {
            padding: 16px 0;
        }

        .menu-section-title {
            color: var(--gray-400);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 24px 20px 8px 20px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            margin: 2px 12px;
            color: var(--gray-300);
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
            background: var(--gray-800);
            color: white;
            transform: translateX(2px);
        }

        .menu-item.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.3);
        }

        .menu-badge {
            margin-left: auto;
            background: var(--primary);
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 700;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        /* ========== TOPBAR ========== */
        .topbar {
            background: white;
            padding: 16px 32px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .topbar-left h4 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: var(--dark);
            letter-spacing: -0.02em;
        }

        .topbar-breadcrumb {
            font-size: 13px;
            color: var(--gray-500);
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: var(--gray-50);
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            border: none;
        }

        .icon-btn:hover {
            background: var(--gray-100);
            color: var(--primary);
        }

        .icon-btn .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 700;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-menu:hover {
            background: var(--gray-50);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
            line-height: 1.2;
        }

        .user-role {
            font-size: 12px;
            color: var(--gray-500);
            line-height: 1.2;
        }

        /* ========== CONTENT AREA ========== */
        .content-area {
            padding: 32px;
            width: 100%;
            max-width: 100%;
        }

        /* ========== CARDS ========== */
        .card-custom {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .card-header-custom {
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-200);
            background: white;
        }

        .card-header-custom h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
        }

        .card-body-custom {
            padding: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .stat-icon.primary { background: var(--primary); }
        .stat-icon.success { background: #10B981; }
        .stat-icon.warning { background: #F59E0B; }
        .stat-icon.danger { background: #EF4444; }
        .stat-icon.info { background: #3B82F6; }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-500);
            font-weight: 500;
        }

        /* ========== PAGE HEADER ========== */
        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: var(--gray-500);
            font-size: 15px;
        }

        /* ========== BUTTONS ========== */
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
            color: white;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-outline-primary {
            border: 1.5px solid var(--primary);
            color: var(--primary);
            background: transparent;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
            text-decoration: none;
        }

        .btn-outline-secondary {
            border: 1.5px solid var(--gray-300);
            color: var(--gray-700);
            background: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-secondary:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            color: var(--gray-900) !important;
            text-decoration: none;
        }

        /* ========== TABLES ========== */
        .table-custom {
            margin: 0;
        }

        .table-custom thead {
            background: var(--gray-50);
        }

        .table-custom thead th {
            border: none;
            padding: 16px 20px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-600);
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-200);
            font-size: 14px;
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .table-custom tbody tr:hover {
            background: var(--gray-50);
        }

        /* ========== FORMS ========== */
        .form-control {
            border: 1.5px solid var(--gray-300);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-select {
            border: 1.5px solid var(--gray-300);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: var(--dark);
            margin-bottom: 8px;
            display: block;
        }

        /* ========== BADGES ========== */
        .badge-custom {
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-success-custom { background: #D1FAE5; color: #065F46; }
        .badge-warning-custom { background: #FEF3C7; color: #92400E; }
        .badge-danger-custom { background: #FEE2E2; color: #991B1B; }
        .badge-info-custom { background: #DBEAFE; color: #1E40AF; }
        .badge-secondary-custom { background: var(--gray-200); color: var(--gray-700); }

        /* ========== ALERTS ========== */
        .alert-custom {
            border-radius: 10px;
            border: 1px solid;
            padding: 16px 20px;
        }

        .alert-success { background: #D1FAE5; border-color: #A7F3D0; color: #065F46; }
        .alert-warning { background: #FEF3C7; border-color: #FDE68A; color: #92400E; }
        .alert-danger { background: #FEE2E2; border-color: #FECACA; color: #991B1B; }
        .alert-info { background: #DBEAFE; border-color: #BFDBFE; color: #1E40AF; }

        /* ========== PROGRESS BAR ========== */
        .progress {
            background: var(--gray-200);
            border-radius: 8px;
            overflow: hidden;
            height: 8px;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            transition: width 0.6s ease;
        }

        /* ========== DROPDOWN ========== */
        .dropdown-menu {
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            padding: 8px;
            margin-top: 8px;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: var(--gray-50);
            color: var(--primary);
        }

        .dropdown-divider {
            margin: 8px 0;
            border-color: var(--gray-200);
        }

        /* ========== UTILITIES ========== */
        .mb-1 { margin-bottom: 8px; }
        .mb-2 { margin-bottom: 16px; }
        .mb-3 { margin-bottom: 24px; }
        .mb-4 { margin-bottom: 32px; }
        .mb-5 { margin-bottom: 48px; }

        .mt-1 { margin-top: 8px; }
        .mt-2 { margin-top: 16px; }
        .mt-3 { margin-top: 24px; }
        .mt-4 { margin-top: 32px; }

        /* ========== MOBILE RESPONSIVE ========== */
        .mobile-menu-toggle {
            display: none;
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s ease;
        }

        .mobile-menu-toggle:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        .mobile-menu-toggle:active {
            transform: scale(0.95);
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
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        .sidebar-toggle-desktop {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: white;
            border: 1px solid var(--gray-200);
            cursor: pointer;
            transition: all 0.2s ease;
            margin-right: 16px;
        }

        .sidebar-toggle-desktop:hover {
            background: var(--gray-50);
            border-color: var(--primary);
            color: var(--primary);
            transform: scale(1.05);
        }

        @media (max-width: 1024px) {
            .sidebar-toggle-desktop {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.active {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0,0,0,0.2);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .content-area {
                padding: 16px;
            }

            .topbar {
                padding: 12px 16px;
                height: 60px;
            }

            .mobile-menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .page-title {
                font-size: 20px;
            }

            .topbar h4 {
                font-size: 18px;
                font-weight: 600;
            }

            .topbar-breadcrumb {
                display: none;
            }

            .topbar-right {
                gap: 8px;
            }

            .user-info {
                display: none !important;
            }

            /* Stat Cards */
            .stat-value {
                font-size: 24px;
            }

            .stat-label {
                font-size: 12px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            /* Cards */
            .card-custom {
                border-radius: 12px;
                margin-bottom: 16px;
            }

            .card-header-custom {
                padding: 16px;
            }

            .card-body-custom {
                padding: 16px;
            }

            /* Tables */
            .table-custom {
                font-size: 13px;
            }

            .table-custom thead th {
                padding: 12px 8px;
                font-size: 12px;
            }

            .table-custom tbody td {
                padding: 12px 8px;
            }

            /* Make tables scrollable on mobile */
            .table-responsive {
                margin: 0 -16px;
                padding: 0 16px;
            }

            /* Buttons */
            .btn-primary-custom,
            .btn-outline-primary,
            .btn-outline-secondary {
                padding: 10px 16px;
                font-size: 14px;
            }

            .btn-sm {
                padding: 6px 12px;
                font-size: 12px;
            }

            /* Modal adjustments */
            .modal-dialog {
                margin: 8px;
            }

            /* Badge sizing */
            .badge {
                font-size: 11px;
                padding: 4px 8px;
            }
        }

        @media (max-width: 576px) {
            .topbar-right .icon-btn {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }

            .icon-btn .badge {
                width: 16px;
                height: 16px;
                font-size: 9px;
            }

            .user-menu {
                padding: 6px;
            }

            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .stat-value {
                font-size: 22px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .card-header-custom h5,
            .card-header-custom h6 {
                font-size: 16px;
            }

            .page-header h1 {
                font-size: 24px;
            }

            /* Reduce gap in flex layouts */
            .d-flex.gap-2 {
                gap: 8px !important;
            }

            .d-flex.gap-3 {
                gap: 12px !important;
            }

            /* Stack buttons vertically on very small screens */
            .btn-group-sm {
                flex-direction: column;
            }

            .btn-group-sm .btn {
                width: 100%;
                margin-bottom: 8px;
            }
        }

        /* Touch-friendly interactions */
        @media (hover: none) {
            .menu-item:active {
                background: rgba(220, 20, 60, 0.15);
            }

            .btn-primary-custom:active {
                transform: scale(0.97);
            }

            .action-btn:active {
                transform: scale(0.95);
            }
        }

        /* ========== ENHANCED ACTION BUTTONS ========== */
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            text-decoration: none;
        }

        .action-btn.view {
            background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
            color: #1E40AF;
            border-color: #93C5FD;
        }

        .action-btn.edit {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #92400E;
            border-color: #FCD34D;
        }

        .action-btn.delete {
            background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
            color: #991B1B;
            border-color: #FCA5A5;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }

        .action-btn.view:hover {
            background: #1E40AF;
            color: white !important;
            border-color: #1E40AF;
        }

        .action-btn.edit:hover {
            background: #F59E0B;
            color: white !important;
            border-color: #F59E0B;
        }

        .action-btn.delete:hover {
            background: #DC2626;
            color: white !important;
            border-color: #DC2626;
        }

        @media (max-width: 768px) {
            .action-btn {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Endow Connect</span>
                </a>
            </div>

            <div class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                {{-- OFFICE MANAGEMENT SECTION --}}
                @if(Auth::user()->hasAnyRole(['Super Admin', 'Admin', 'Employee', 'office_admin', 'department_manager', 'staff']))
                <div class="menu-section-title">Office Management</div>

                <a href="{{ route('office.daily-reports.index') }}" class="menu-item {{ request()->routeIs('office.daily-reports.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Daily Reports</span>
                    @php
                        $myPendingReports = \App\Models\DailyReport::where('status', 'pending')
                            ->where('submitted_by', Auth::id())
                            ->count();
                        // Show NEW badge to Super Admin and Admin for 10 days (until Feb 1, 2026)
                        $showNewBadge = Auth::user()->hasAnyRole(['Super Admin', 'Admin'])
                            && now()->lt(now()->parse('2026-02-01'));
                    @endphp
                    @if($showNewBadge)
                        <style>
                            @keyframes borderGradient {
                                0% {
                                    background: linear-gradient(90deg, #06B6D4 0%, #10B981 100%);
                                }
                                50% {
                                    background: linear-gradient(90deg, #0EA5E9 0%, #06B6D4 100%);
                                }
                                100% {
                                    background: linear-gradient(90deg, #06B6D4 0%, #10B981 100%);
                                }
                            }
                            .trending-badge {
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                padding: 0.3rem 0.65rem;
                                border-radius: 20px;
                                font-size: 0.65rem;
                                font-weight: 700;
                                letter-spacing: 0.5px;
                                color: #FFFFFF;
                                background: linear-gradient(135deg, #0EA5E9 0%, #10B981 100%);
                                position: relative;
                                margin-left: 0.5rem;
                                animation: borderGradient 3s ease-in-out infinite;
                                box-shadow: 0 0 10px rgba(16, 185, 129, 0.3), inset 0 0 6px rgba(255, 255, 255, 0.2);
                                border: 1px solid rgba(255, 255, 255, 0.4);
                            }
                        </style>
                        <span class="trending-badge">NEW</span>
                    @endif
                    @if($myPendingReports > 0)
                        <span class="menu-badge">{{ $myPendingReports }}</span>
                    @endif
                </a>

                @if(Auth::user()->hasRole('Super Admin'))
                <a href="{{ route('office.departments.index') }}" class="menu-item {{ request()->routeIs('office.departments.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>Departments</span>
                </a>
                @endif
                @endif

                @canany(['view students', 'create students', 'edit students', 'delete students'])
                <div class="menu-section-title">Student Management</div>

                <!-- My Students - For Employees -->
                @if(Auth::user()->hasRole(['Employee', 'Admin', 'Super Admin']))
                <a href="{{ route('students.my-students') }}" class="menu-item {{ request()->routeIs('students.my-students') ? 'active' : '' }}">
                    <i class="fas fa-user-friends"></i>
                    <span>My Students</span>
                    @php
                        $myStudentsCount = \App\Models\Student::where('assigned_to', Auth::id())->where('account_status', 'pending')->count();
                    @endphp
                    @if($myStudentsCount > 0)
                        <span class="menu-badge">{{ $myStudentsCount }}</span>
                    @endif
                </a>
                @endif

                <!-- All Students - For All Staff -->
                <a href="{{ route('students.index') }}" class="menu-item {{ request()->routeIs('students.*') && !request()->routeIs('students.my-students') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>All Students</span>
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="menu-badge">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('student-visits.index') }}" class="menu-item {{ request()->routeIs('student-visits.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Student Visits</span>
                </a>
                @endcanany

                @canany(['view checklists', 'create checklists'])
                <div class="menu-section-title">Configuration</div>

                <a href="{{ route('universities.index') }}" class="menu-item {{ request()->routeIs('universities.*') ? 'active' : '' }}">
                    <i class="fas fa-university"></i>
                    <span>Universities</span>
                </a>

                <a href="{{ route('programs.index') }}" class="menu-item {{ request()->routeIs('programs.*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Programs</span>
                </a>

                <a href="{{ route('checklist-items.index') }}" class="menu-item {{ request()->routeIs('checklist-items.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i>
                    <span>Checklist Items</span>
                </a>
                @endcanany

                <div class="menu-section-title">Communication</div>

                <a href="{{ route('contact-submissions.index') }}" class="menu-item {{ request()->routeIs('contact-submissions.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    <span>Contact Submissions</span>
                    @php
                        $newContactCount = \App\Models\ContactSubmission::where('status', 'new')->count();
                    @endphp
                    @if($newContactCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $newContactCount }}</span>
                    @endif
                </a>

                @canany(['view documents'])
                <div class="menu-section-title">Document Management</div>

                <a href="{{ route('documents.index') }}" class="menu-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Documents</span>
                </a>
                @endcanany

                @hasanyrole('Super Admin|Admin')
                <div class="menu-section-title">System</div>

                @hasrole('Super Admin')
                <a href="{{ route('users.index') }}" class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                </a>

                <a href="{{ route('admin.roles.index') }}" class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Role Management</span>
                </a>

                <a href="{{ route('admin.email-settings.index') }}" class="menu-item {{ request()->routeIs('admin.email-settings.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>Email Settings</span>
                </a>

                <a href="{{ route('admin.evaluation-questions.index') }}" class="menu-item {{ request()->routeIs('admin.evaluation-questions.*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>Evaluation Questions</span>
                </a>

                <a href="{{ route('admin.consultant-evaluations.index') }}" class="menu-item {{ request()->routeIs('admin.consultant-evaluations.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Consultant Evaluations</span>
                </a>
                @endhasrole

                <a href="{{ route('activity-logs.index') }}" class="menu-item {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Activity Logs</span>
                </a>
                @endhasanyrole

                @canany(['view reports'])
                <div class="menu-section-title">Analytics</div>

                <a href="{{ route('reports.index') }}" class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Reports</span>
                </a>
                @endcanany

                @can('manage roles')
                <div class="menu-section-title">System</div>

                <a href="#" class="menu-item">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                </a>

                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                @endcan
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle-desktop" onclick="toggleSidebar()" title="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button class="mobile-menu-toggle" onclick="toggleMobileSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="topbar-breadcrumb">
                        @yield('breadcrumb')
                    </div>
                </div>
                <div class="topbar-right">
                    <button class="icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>

                    <div class="dropdown">
                        <div class="user-menu" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                @if(Auth::user()->photo_path)
                                    {{-- <img src="{{ storage_url(Auth::user()->photo_path) }}" alt="{{ Auth::user()->name }}"> --}}
                                    <img src="{{ asset('storage/' . Auth::user()->photo_path) }}" alt="{{ Auth::user()->name }}">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="user-info d-none d-md-flex">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="user-role">{{ Auth::user()->roles->first()->name ?? 'User' }}</span>
                            </div>
                            <i class="fas fa-chevron-down" style="color: var(--gray-400); font-size: 12px;"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.profile.show') }}">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                                    <i class="fas fa-cog me-2"></i> Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                @if(Auth::user()->hasRole('Student'))
                                <a class="dropdown-item" href="{{ route('student.logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('student.logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                                @else
                                <a class="dropdown-item" href="{{ route('admin.logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global SweetAlert Messages -->
    <script>
        // Success messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: {!! json_encode(session('success')) !!},
                confirmButtonColor: '#10B981',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // Error messages
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: {!! json_encode(session('error')) !!},
                confirmButtonColor: '#DC143C'
            });
        @endif

        // Info messages
        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: {!! json_encode(session('info')) !!},
                confirmButtonColor: '#3B82F6'
            });
        @endif

        // Warning messages
        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: '{{ session('warning') }}',
                confirmButtonColor: '#F59E0B'
            });
        @endif
    </script>

    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');

            // Store preference
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Close sidebar when clicking overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.addEventListener('click', function() {
                    toggleMobileSidebar();
                });
            }
        });

        // Restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed && window.innerWidth > 768) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.querySelector('.main-content').classList.add('expanded');
            }

            // Close mobile sidebar when clicking menu items
            if (window.innerWidth <= 768) {
                document.querySelectorAll('.sidebar .menu-item').forEach(function(item) {
                    item.addEventListener('click', function() {
                        const sidebar = document.getElementById('sidebar');
                        const overlay = document.getElementById('sidebarOverlay');
                        sidebar.classList.remove('active');
                        overlay.classList.remove('active');
                    });
                });
            }

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth > 768) {
                        document.getElementById('sidebar').classList.remove('active');
                        document.getElementById('sidebarOverlay').classList.remove('active');
                    }
                }, 250);
            });
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
