{{-- 
    Accounting Module Navigation
    Add this to your sidebar navigation file (e.g., layouts/sidebar.blade.php)
    Place it where appropriate in your menu structure
--}}

@canany(['view-accounting', 'view-transaction', 'approve-transaction'])
<!-- Accounting Module -->
<li class="nav-item {{ request()->is('accounting*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('accounting*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-calculator"></i>
        <p>
            Accounting
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        @can('view-accounting-summary')
        <li class="nav-item">
            <a href="{{ route('office.accounting.summary') }}" 
               class="nav-link {{ request()->routeIs('office.accounting.summary') ? 'active' : '' }}">
                <i class="far fa-chart-bar nav-icon"></i>
                <p>Summary Dashboard</p>
            </a>
        </li>
        @endcan

        @can('view-transaction')
        <li class="nav-item">
            <a href="{{ route('office.accounting.transactions.index') }}" 
               class="nav-link {{ request()->routeIs('office.accounting.transactions.index') ? 'active' : '' }}">
                <i class="far fa-list-alt nav-icon"></i>
                <p>All Transactions</p>
            </a>
        </li>
        @endcan

        @can('create-transaction')
        <li class="nav-item">
            <a href="{{ route('office.accounting.transactions.create') }}" 
               class="nav-link {{ request()->routeIs('office.accounting.transactions.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle nav-icon"></i>
                <p>Add Transaction</p>
            </a>
        </li>
        @endcan

        @can('approve-transaction')
        <li class="nav-item">
            <a href="{{ route('office.accounting.transactions.pending') }}" 
               class="nav-link {{ request()->routeIs('office.accounting.transactions.pending') ? 'active' : '' }}">
                <i class="fas fa-clock nav-icon"></i>
                <p>
                    Pending Approvals
                    @php
                        $pendingCount = \App\Models\Transaction::pending()->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge badge-warning right">{{ $pendingCount }}</span>
                    @endif
                </p>
            </a>
        </li>
        @endcan
    </ul>
</li>
@endcanany

{{-- 
    Alternative: Bootstrap 5 Sidebar Version
    Use this if your sidebar uses Bootstrap 5 classes
--}}
@canany(['view-accounting', 'view-transaction', 'approve-transaction'])
<li class="sidebar-item {{ request()->is('accounting*') ? 'active' : '' }}">
    <a href="#accounting" class="sidebar-link collapsed" data-bs-toggle="collapse" 
       aria-expanded="{{ request()->is('accounting*') ? 'true' : 'false' }}">
        <i class="fas fa-calculator"></i>
        <span>Accounting</span>
    </a>
    <ul id="accounting" class="sidebar-dropdown list-unstyled collapse {{ request()->is('accounting*') ? 'show' : '' }}" 
        data-bs-parent="#sidebar">
        
        @can('view-accounting-summary')
        <li class="sidebar-item">
            <a href="{{ route('office.accounting.summary') }}" 
               class="sidebar-link {{ request()->routeIs('office.accounting.summary') ? 'active' : '' }}">
                <i class="far fa-chart-bar"></i> Summary Dashboard
            </a>
        </li>
        @endcan

        @can('view-transaction')
        <li class="sidebar-item">
            <a href="{{ route('office.accounting.transactions.index') }}" 
               class="sidebar-link {{ request()->routeIs('office.accounting.transactions.index') ? 'active' : '' }}">
                <i class="far fa-list-alt"></i> All Transactions
            </a>
        </li>
        @endcan

        @can('create-transaction')
        <li class="sidebar-item">
            <a href="{{ route('office.accounting.transactions.create') }}" 
               class="sidebar-link {{ request()->routeIs('office.accounting.transactions.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i> Add Transaction
            </a>
        </li>
        @endcan

        @can('approve-transaction')
        <li class="sidebar-item">
            <a href="{{ route('office.accounting.transactions.pending') }}" 
               class="sidebar-link {{ request()->routeIs('office.accounting.transactions.pending') ? 'active' : '' }}">
                <i class="fas fa-clock"></i> Pending Approvals
                @php
                    $pendingCount = \App\Models\Transaction::pending()->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-warning ms-2">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
        @endcan
    </ul>
</li>
@endcanany
