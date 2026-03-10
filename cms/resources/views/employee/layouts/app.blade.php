<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Employee Dashboard') | Kwikster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 15px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stats-card {
            border-left: 4px solid #667eea;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .sidebar-toggle {
            display: none;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 60px;
                left: -100%;
                max-height: calc(100vh - 60px);
                border-radius: 0;
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .sidebar-toggle {
                display: block;
            }
            .navbar {
                margin-bottom: 10px;
            }
            .stats-card {
                margin-bottom: 15px;
            }
        }
        @media (max-width: 576px) {
            .main-content {
                padding: 10px;
            }
            .sidebar .nav-link {
                padding: 10px 15px;
                margin: 3px 10px;
                font-size: 0.9rem;
            }
            .navbar h5 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
    <div class="p-3 border-bottom d-flex justify-content-center align-items-center">
    <small>
        {{ Auth::user()->full_name ?? Auth::user()->first_name . ' ' . Auth::user()->last_name }}
    </small>
</div>
        <nav class="nav flex-column mt-3">
            <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <!-- <a class="nav-link {{ request()->routeIs('employee.documents*') ? 'active' : '' }}" href="{{ route('employee.documents') }}">
                <i class="fas fa-file-alt me-2"></i>My Documents
            </a> -->
            <a class="nav-link" href="#">
                <i class="fas fa-clock me-2"></i>Attendance
            </a>
            <a class="nav-link" href="#">
                <i class="fas fa-clock me-2"></i>Test
            </a>
            <a class="nav-link" href="{{ route('employee.leaves.index') }}">
                <i class="fas fa-clock me-2"></i>Leave
            </a>
            <a class="nav-link {{ request()->routeIs('employee.tickets*') ? 'active' : '' }}" href="{{ route('employee.tickets') }}">
                <i class="fas fa-ticket-alt me-2"></i>Tickets
            </a>
            <a class="nav-link {{ request()->routeIs('employee.expenses*') ? 'active' : '' }}" href="{{ route('employee.expenses.index') }}">
                <i class="fas fa-money-bill me-2"></i>Expense Management
            </a>

            <a class="nav-link" href="#">
                <i class="fas fa-money-bill me-2"></i>Salary Slips
            </a>
            <a class="nav-link" href="#">
                <i class="fas fa-tasks me-2"></i>My Tasks
            </a>
            <a class="nav-link" href="#">
                <i class="fas fa-chart-line me-2"></i>Performance
            </a>
            <a class="nav-link" href="{{ route('profile') }}">
                <i class="fas fa-user me-2"></i>Profile
            </a>
        </nav>
        <div class="mt-auto p-3 border-top">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button class="btn btn-link sidebar-toggle" id="sidebarToggle" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->full_name ?? Auth::user()->first_name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
        
        sidebar.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>