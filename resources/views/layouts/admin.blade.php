<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Leveler</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="logo-text">Leveler</span>
            </div>
            <div class="header-right">
                <div class="user-menu" id="userMenu">
                    <i class="fas fa-user"></i>
                    <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                    <i class="fas fa-chevron-down"></i>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="admin-wrapper">
            <!-- Sidebar -->
            <aside class="admin-sidebar" id="sidebar">
                <form action="{{ route('admin.trainees.index') }}" method="GET" class="sidebar-search-form">
                    <div class="sidebar-search">
                        <input type="text" name="search" placeholder="Surname..." class="search-input" value="{{ request('search') }}">
                        <button type="submit" class="search-icon-btn">
                            <i class="fas fa-search search-icon"></i>
                        </button>
                    </div>
                </form>

                <nav class="sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="nav-group">
                        <div class="nav-item nav-parent trainees-parent {{ request()->routeIs('admin.trainees.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Trainees</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div class="nav-submenu trainees-submenu {{ request()->routeIs('admin.trainees.*') ? 'show' : '' }}">
                            <a href="{{ route('admin.trainees.index') }}" class="nav-item nav-child {{ request()->routeIs('admin.trainees.index') ? 'active' : '' }}">
                                <i class="fas fa-user"></i>
                                <span>View Trainee Profile</span>
                                <i class="fas fa-chevron-right nav-arrow"></i>
                            </a>
                            <a href="{{ route('admin.trainees.create') }}" class="nav-item nav-child {{ request()->routeIs('admin.trainees.create') ? 'active' : '' }}">
                                <i class="fas fa-user-plus"></i>
                                <span>Add Trainee</span>
                            </a>
                            <div class="nav-item nav-parent nav-child manage-trainees-parent {{ request()->routeIs('admin.trainees.manage') || request()->routeIs('admin.trainees.activate') || request()->routeIs('admin.trainees.deactivate') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>
                                <span>Manage Trainees</span>
                                <i class="fas fa-chevron-down nav-arrow"></i>
                            </div>
                            <div class="nav-submenu manage-trainees-submenu {{ request()->routeIs('admin.trainees.manage') || request()->routeIs('admin.trainees.activate') || request()->routeIs('admin.trainees.deactivate') ? 'show' : '' }}">
                                <a href="{{ route('admin.trainees.activate') }}" class="nav-item nav-child {{ request()->routeIs('admin.trainees.activate') ? 'active' : '' }}">
                                    <i class="fas fa-unlock"></i>
                                    <span>Activate</span>
                                </a>
                                <a href="{{ route('admin.trainees.deactivate') }}" class="nav-item nav-child {{ request()->routeIs('admin.trainees.deactivate') ? 'active' : '' }}">
                                    <i class="fas fa-lock"></i>
                                    <span>Deactivate</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('admin.schedules.index') }}" class="nav-item {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar"></i>
                        <span>Schedules</span>
                    </a>

                    <div class="nav-group">
                        <div class="nav-item nav-parent {{ request()->routeIs('admin.payments.*') || request()->routeIs('admin.manual-payments.*') ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Payments</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div class="nav-submenu {{ request()->routeIs('admin.payments.*') || request()->routeIs('admin.manual-payments.*') ? 'show' : '' }}">
                            <a href="{{ route('admin.payments.index') }}" class="nav-item nav-child {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>
                                <span>Payment Records</span>
                            </a>
                            <a href="{{ route('admin.manual-payments.index') }}" class="nav-item nav-child {{ request()->routeIs('admin.manual-payments.*') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i>
                                <span>Manual Payment Settings</span>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.question-pool.index') }}" class="nav-item {{ request()->routeIs('admin.question-pool.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Question Pool</span>
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>

                    <div class="nav-group">
                        <div class="nav-item nav-parent {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span>Admin Users</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div class="nav-submenu {{ request()->routeIs('admin.admin-users.*') ? 'show' : '' }}">
                            <a href="{{ route('admin.admin-users.view') }}" class="nav-item nav-child">
                                <i class="fas fa-list"></i>
                                <span>View</span>
                            </a>
                        </div>
                    </div>

                    <div class="nav-group">
                        <div class="nav-item nav-parent {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <span>Courses</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div class="nav-submenu {{ request()->routeIs('admin.courses.*') ? 'show' : '' }}">
                            <a href="{{ route('admin.courses.view') }}" class="nav-item nav-child">
                                <i class="fas fa-list"></i>
                                <span>View Courses</span>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.results.index') }}" class="nav-item {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
                        <i class="fas fa-file-check"></i>
                        <span>View Results</span>
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Reports</span>
                    </a>

                    <a href="{{ route('admin.trained.index') }}" class="nav-item {{ request()->routeIs('admin.trained.*') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Trained</span>
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>

                    <a href="{{ route('admin.pages.index') }}" class="nav-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Pages Management</span>
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="admin-main">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>

