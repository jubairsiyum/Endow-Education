<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Endow Global Education') }} - @yield('title')</title>

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
            transition: transform 0.3s ease;
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .menu-badge {
            margin-left: auto;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 700;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
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

        .stat-icon.primary { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); }
        .stat-icon.success { background: linear-gradient(135deg, #10B981 0%, #34D399 100%); }
        .stat-icon.warning { background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%); }
        .stat-icon.danger { background: linear-gradient(135deg, #EF4444 0%, #F87171 100%); }
        .stat-icon.info { background: linear-gradient(135deg, #3B82F6 0%, #60A5FA 100%); }

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
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .btn-outline-secondary {
            border: 1.5px solid var(--gray-300);
            color: var(--gray-600);
            background: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-outline-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
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

        /* ========== ACTION BUTTONS ========== */
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
        }

        .action-btn.view { background: #DBEAFE; color: #1E40AF; border-color: #BFDBFE; }
        .action-btn.edit { background: #FEF3C7; color: #92400E; border-color: #FDE68A; }
        .action-btn.delete { background: #FEE2E2; color: #991B1B; border-color: #FECACA; }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

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
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-area {
                padding: 20px 16px;
            }

            .topbar {
                padding: 12px 16px;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .page-title {
                font-size: 24px;
            }

            .stat-value {
                font-size: 28px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Endow Global</span>
                </a>
            </div>

            <div class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                @canany(['view students', 'create students', 'edit students', 'delete students'])
                <div class="menu-section-title">Student Management</div>

                <a href="{{ route('students.index') }}" class="menu-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="menu-badge">{{ $pendingCount }}</span>
                    @endif
                </a>
                @endcanany

                @canany(['view checklists', 'create checklists'])
                <a href="{{ route('checklist-items.index') }}" class="menu-item {{ request()->routeIs('checklist-items.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i>
                    <span>Checklist Items</span>
                </a>
                @endcanany

                @canany(['view documents'])
                <div class="menu-section-title">Document Management</div>

                <a href="{{ route('documents.index') }}" class="menu-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Documents</span>
                </a>
                @endcanany

                @canany(['view reports'])
                <div class="menu-section-title">Analytics</div>

                <a href="#" class="menu-item">
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
                    <button class="mobile-menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('active')">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4>@yield('page-title', 'Dashboard')</h4>
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
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="user-info d-none d-md-flex">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="user-role">{{ Auth::user()->roles->first()->name ?? 'User' }}</span>
                            </div>
                            <i class="fas fa-chevron-down" style="color: var(--gray-400); font-size: 12px;"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2"></i> Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

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
    @stack('scripts')
</body>
</html>
