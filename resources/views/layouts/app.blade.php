<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Hệ thống Đánh giá Năng lực Thuyền viên')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #0ea5e9;
            --accent-color: #0284c7;
            --light-color: #f0f9ff;
            --dark-color: #0f172a;
            --navbar-height: 56px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: var(--navbar-height);
        }
        
        .navbar-custom {
            background-color: var(--primary-color);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            height: var(--navbar-height);
        }
        
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white;
        }
        
        .navbar-custom .nav-link:hover {
            color: var(--light-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 0;
            margin-top: auto;
        }
        
        .sidebar-container {
            position: fixed;
            top: var(--navbar-height);
            bottom: 0;
            left: 0;
            width: 16.66%;
            z-index: 1020;
            overflow-y: auto;
        }
        
        @media (min-width: 992px) {
            .sidebar-container {
                width: 16.66%;
            }
            .content-with-sidebar {
                margin-left: 16.66%;
            }
        }
        
        @media (min-width: 768px) and (max-width: 991.98px) {
            .sidebar-container {
                width: 25%;
            }
            .content-with-sidebar {
                margin-left: 25%;
            }
        }
        
        .sidebar {
            background-color: var(--dark-color);
            color: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }
        
        .sidebar .nav-link {
            color: #e2e8f0;
            padding: 0.5rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.25rem;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background-color: var(--accent-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .nav-flex-column {
            flex-grow: 1;
        }
        
        .sidebar .mt-auto {
            margin-top: auto !important;
        }
        
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px 8px 0 0 !important;
            font-weight: bold;
        }
        
        .alert-success {
            background-color: #d1fae5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: #b91c1c;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .container-fluid {
            flex: 1;
        }
    </style>
    
    @yield('css')
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-ship me-2"></i>
                Hệ thống Đánh giá Năng lực Thuyền viên
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login.form') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register.form') }}">
                                <i class="fas fa-user-plus me-1"></i> Đăng ký
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(Auth::user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item" href="{{ route('seafarer.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('seafarer.profile.show') }}">
                                            <i class="fas fa-user me-1"></i> Hồ sơ
                                        </a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                @auth
                    @if(Auth::user()->isAdmin() || Auth::user()->isThuyenVien())
                        <div class="d-none d-md-block px-0">
                            <div class="sidebar-container">
                                <div class="sidebar">
                                    @if(Auth::user()->isAdmin())
                                        @include('layouts.sidebar.admin')
                                    @else
                                        @include('layouts.sidebar.seafarer')
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-9 col-lg-10 py-4 content-with-sidebar">
                            @yield('content')
                        </div>
                    @else
                        <div class="col-12 px-0">
                            @yield('content')
                        </div>
                    @endif
                @else
                    <div class="col-12 px-0">
                        @yield('content')
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} Hệ thống Đánh giá Năng lực Thuyền viên. Bản quyền thuộc về ...</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    @yield('js')
    @yield('scripts')
</body>
</html> 