<div class="px-3 mb-4">
    <h5 class="text-uppercase text-muted mb-0"><small>Dashboard Thuyền viên</small></h5>
</div>
<ul class="nav flex-column flex-grow-1">
    <li class="nav-item">
        <a href="{{ route('seafarer.dashboard') }}" class="nav-link {{ request()->routeIs('seafarer.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('seafarer.tests.index') }}" class="nav-link {{ request()->routeIs('seafarer.tests*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            Bài Kiểm tra
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('seafarer.certificates.index') }}" class="nav-link {{ request()->routeIs('seafarer.certificates*') ? 'active' : '' }}">
            <i class="fas fa-certificate"></i>
            Chứng chỉ của tôi
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('seafarer.profile.show') }}" class="nav-link {{ request()->routeIs('seafarer.profile*') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            Hồ sơ cá nhân
        </a>
    </li>
</ul>

<div class="mt-auto">
    <hr>
    <div class="dropdown px-3 mb-3">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle me-2 fs-5"></i>
            <strong>{{ Auth::user()->name }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="{{ route('seafarer.profile.show') }}"><i class="fas fa-user me-1"></i> Hồ sơ</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-1"></i> Đăng xuất</a></li>
        </ul>
    </div>
</div> 