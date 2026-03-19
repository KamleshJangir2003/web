<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Employee Dashboard') | Kwikster</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 270px;
            --primary: #667eea;
            --secondary: #764ba2;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --body-bg: #f3f4f6;
            --card-bg: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--body-bg);
            color: var(--text-dark);
        }

        a {
            text-decoration: none;
        }

        button {
            outline: none;
        }

        .app-shell {
            min-height: 100vh;
            width: 100%;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 55%, #5b3c96 100%);
            color: #fff;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            box-shadow: 8px 0 30px rgba(31, 41, 55, 0.12);
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 22px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .sidebar-brand-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.2px;
        }

        .brand-subtitle {
            margin: 4px 0 0;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.75);
        }

        .sidebar-close-btn {
            display: none;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 10px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .sidebar-close-btn:hover {
            background: rgba(255,255,255,0.18);
        }

        .sidebar-user {
            margin: 18px 16px 12px;
            padding: 14px;
            border-radius: 18px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sidebar-user-name {
            margin: 0;
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-user-role {
            margin: 3px 0 0;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.72);
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 8px 10px 18px;
        }

        .menu-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.55);
            margin: 10px 14px 10px;
            font-weight: 700;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.82);
            padding: 12px 14px;
            border-radius: 14px;
            margin: 6px 8px;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .sidebar .nav-link i {
            width: 18px;
            text-align: center;
            font-size: 0.95rem;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.10);
            color: #fff;
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.16);
            color: #fff;
            font-weight: 700;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.12);
            background: rgba(0,0,0,0.08);
        }

        .logout-btn {
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 600;
            border-color: rgba(255,255,255,0.35);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 22px;
            transition: margin-left 0.3s ease;
            width: calc(100% - var(--sidebar-width));
            overflow: visible;
        }

        .topbar {
            position: relative;
            overflow: visible;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229,231,235,0.8);
            border-radius: 18px;
            padding: 14px 18px;
            margin-bottom: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            z-index: 20;
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            position: relative;
            overflow: visible;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1;
        }

        .sidebar-toggle {
            display: none;
            border: none;
            background: #eef2ff;
            color: #4f46e5;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .page-heading {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .page-subtext {
            margin: 2px 0 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .topbar-user {
            flex-shrink: 0;
            position: relative;
            z-index: 50;
        }

        .topbar-user .dropdown {
            position: relative;
        }

        .topbar-user-btn {
            border: 1px solid var(--border-color);
            background: #fff;
            border-radius: 14px;
            padding: 8px 12px;
            color: var(--text-dark);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            outline: none;
            box-shadow: none;
            cursor: pointer;
        }

        .topbar-user-btn:hover,
        .topbar-user-btn:focus,
        .topbar-user-btn:active,
        .topbar-user-btn.show {
            background: #fff !important;
            color: var(--text-dark) !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .topbar-user-btn::after {
            margin-left: 6px;
        }

        .user-name-text {
            display: inline-block;
            vertical-align: middle;
        }

        .topbar-user .dropdown-menu {
            position: absolute !important;
            top: calc(100% + 10px) !important;
            right: 0 !important;
            left: auto !important;
            inset: auto 0 auto auto !important;
            transform: none !important;
            min-width: 190px;
            border: none;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
            padding: 8px;
            z-index: 3000;
        }

        .topbar-user .dropdown-item {
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.92rem;
        }

        .topbar-user .dropdown-item:hover {
            background: #f3f4f6;
        }

        .content-wrapper {
            animation: fadeInUp 0.35s ease;
            width: 100%;
            overflow: visible;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.45);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-close-btn {
                display: inline-flex;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 16px;
            }

            .sidebar-toggle {
                display: inline-flex;
            }

            .topbar {
                padding: 12px 14px;
                margin-bottom: 16px;
            }

            .page-heading {
                font-size: 1.05rem;
                line-height: 1.2;
            }

            .page-subtext {
                display: none;
            }

            .topbar-user-btn {
                padding: 7px 10px;
                font-size: 0.88rem;
                border-radius: 12px;
            }

            .user-name-text {
                max-width: 78px;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
        }

        @media (min-width: 992px) {
            .sidebar-close-btn {
                display: none !important;
            }
        }

        @media (max-width: 575.98px) {
            .sidebar {
                width: 85%;
                max-width: 320px;
            }

            .main-content {
                padding: 12px;
            }

            .topbar {
                padding: 10px 12px;
                border-radius: 14px;
            }

            .topbar-inner {
                flex-wrap: nowrap;
                align-items: center;
            }

            .topbar-left {
                gap: 10px;
                min-width: 0;
            }

            .sidebar-toggle {
                width: 38px;
                height: 38px;
                font-size: 16px;
                border-radius: 10px;
            }

            .page-heading {
                font-size: 0.98rem;
            }

            .topbar-user-btn {
                padding: 7px 9px;
                font-size: 0.84rem;
                min-height: 38px;
            }

            .user-name-text {
                max-width: 62px;
            }
        }

        @media (max-width: 420px) {
            .user-name-text {
                display: none;
            }

            .topbar-user-btn::after {
                display: none;
            }

            .topbar-user-btn {
                width: 38px;
                justify-content: center;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-top">
                    <div>
                        <h4 class="brand-title">Kwikster</h4>
                        <p class="brand-subtitle">Employee Panel</p>
                    </div>

                    <button type="button" class="sidebar-close-btn" id="sidebarClose" aria-label="Close sidebar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <p class="sidebar-user-name">
                        {{ Auth::user()->full_name ?? trim((Auth::user()->first_name ?? '') . ' ' . (Auth::user()->last_name ?? '')) }}
                    </p>
                    <p class="sidebar-user-role">
                        {{ Auth::user()->department ?? 'Employee' }}
                    </p>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-label">Main Menu</div>

                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
                        <i class="fas fa-house"></i>
                        <span>Dashboard</span>
                    </a>

                    <a class="nav-link" href="#">
                        <i class="fas fa-clock"></i>
                        <span>Attendance</span>
                    </a>

                    <a class="nav-link" href="#">
                        <i class="fas fa-flask"></i>
                        <span>Test</span>
                    </a>

                    <a class="nav-link {{ request()->routeIs('employee.leaves.*') ? 'active' : '' }}" href="{{ route('employee.leaves.index') }}">
                        <i class="fas fa-calendar-days"></i>
                        <span>Leave</span>
                    </a>

                    <a class="nav-link {{ request()->routeIs('employee.tickets*') ? 'active' : '' }}" href="{{ route('employee.tickets') }}">
                        <i class="fas fa-ticket"></i>
                        <span>Tickets</span>
                    </a>

                    <a class="nav-link {{ request()->routeIs('employee.expenses*') ? 'active' : '' }}" href="{{ route('employee.expenses.index') }}">
                        <i class="fas fa-wallet"></i>
                        <span>Expense Management</span>
                    </a>

                    <a class="nav-link" href="#">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Salary Slips</span>
                    </a>

                    <a class="nav-link" href="#">
                        <i class="fas fa-list-check"></i>
                        <span>My Tasks</span>
                    </a>

                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>

                    <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
                        <i class="fas fa-user-gear"></i>
                        <span>Profile</span>
                    </a>
                </nav>
            </div>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light logout-btn w-100">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <nav class="topbar">
                <div class="topbar-inner">
                    <div class="topbar-left">
                        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
                            <i class="fas fa-bars"></i>
                        </button>

                        <div style="min-width: 0;">
                            <h5 class="page-heading">@yield('page-title', 'Dashboard')</h5>
                            <p class="page-subtext">Welcome to your employee workspace</p>
                        </div>
                    </div>

                    <div class="topbar-user">
                        <div class="dropdown">
                            <button
                                class="dropdown-toggle topbar-user-btn"
                                type="button"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="true"
                                aria-expanded="false"
                            >
                                <i class="fas fa-user-circle"></i>
                                <span class="user-name-text">
                                    {{ Auth::user()->full_name ?? Auth::user()->first_name ?? 'User' }}
                                </span>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="fas fa-user me-2"></i>Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        function openSidebar() {
            sidebar.classList.add('show');
            sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                if (window.innerWidth <= 991.98) {
                    if (sidebar.classList.contains('show')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                }
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        sidebar.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 991.98) {
                    closeSidebar();
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 991.98) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>