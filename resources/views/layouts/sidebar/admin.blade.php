<div class="px-3 mb-4">
    <h5 class="text-uppercase text-muted mb-0"><small>Dashboard Admin</small></h5>
</div>
<ul class="nav flex-column flex-grow-1">
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            Quản lý Thuyền viên
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.questions.index') }}" class="nav-link {{ request()->routeIs('admin.questions*') ? 'active' : '' }}">
            <i class="fas fa-question-circle"></i>
            Ngân hàng Câu hỏi
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.tests.index') }}" class="nav-link {{ request()->routeIs('admin.tests*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            Bài Kiểm tra
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') || request()->routeIs('admin.reports.performance') || request()->routeIs('admin.reports.test') || request()->routeIs('admin.reports.seafarer') || request()->routeIs('admin.reports.attempt') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            Báo cáo & Thống kê
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            Quản lý Danh mục
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.reports.marking') }}" class="nav-link {{ request()->routeIs('admin.reports.marking') || request()->routeIs('admin.reports.mark.attempt') ? 'active' : '' }}">
            <i class="fas fa-pen"></i>
            Chấm điểm bài thi
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.certificates.index') }}" class="nav-link {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
            <i class="fas fa-certificate"></i>
            Quản lý Chứng chỉ
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
            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-1"></i> Đăng xuất</a></li>
        </ul>
    </div>
</div> 