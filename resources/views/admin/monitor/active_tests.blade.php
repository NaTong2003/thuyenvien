@extends('layouts.app')

@section('title', 'Theo dõi Bài thi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .monitor-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .monitor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .active-test-card {
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s;
        cursor: pointer;
    }
    
    .active-test-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .active-user-count {
        font-size: 0.9rem;
        padding: 0.2rem 0.5rem;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
    }
    
    .user-status {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .user-status:last-child {
        border-bottom: none;
    }
    
    .user-status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 10px;
    }
    
    .status-active {
        background-color: #28a745;
    }
    
    .status-idle {
        background-color: #ffc107;
    }
    
    .status-warning {
        background-color: #dc3545;
        animation: pulse 2s infinite;
    }
    
    .user-info {
        flex-grow: 1;
    }
    
    .user-name {
        font-weight: 600;
        margin-bottom: 0.2rem;
    }
    
    .user-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .user-progress {
        width: 60px;
        text-align: right;
    }
    
    .progress-text {
        font-weight: 600;
        color: #495057;
    }
    
    .time-remaining {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .warning-badge {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        background-color: #dc3545;
        color: white;
        border-radius: 20px;
        font-size: 0.75rem;
        margin-left: 0.5rem;
        animation: pulse 2s infinite;
    }
    
    .info-badge {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        background-color: #17a2b8;
        color: white;
        border-radius: 20px;
        font-size: 0.75rem;
        margin-left: 0.5rem;
    }
    
    .filter-card {
        margin-bottom: 2rem;
    }
    
    .active-tests-count {
        margin-bottom: 1rem;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }
    
    .monitoring-dashboard {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .dashboard-card {
        flex: 1;
        min-width: 200px;
        text-align: center;
        padding: 1.5rem;
        border-radius: 8px;
        background-color: #f8f9fa;
        border: 1px solid #eaeaea;
    }
    
    .card-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }
    
    .card-label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .dashboard-card-primary {
        background-color: var(--primary-color);
        color: white;
    }
    
    .dashboard-card-primary .card-value,
    .dashboard-card-primary .card-label {
        color: white;
    }
    
    .dashboard-card-warning {
        background-color: #fff3cd;
    }
    
    .dashboard-card-warning .card-value {
        color: #856404;
    }
    
    .dashboard-card-danger {
        background-color: #f8d7da;
    }
    
    .dashboard-card-danger .card-value {
        color: #842029;
    }
</style>
@endsection

@section('content')
<div class="monitor-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Theo dõi Bài thi Trực tuyến</h2>
                <p class="mb-0">Giám sát các bài thi đang diễn ra</p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item active text-white-50">Theo dõi Bài thi</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="monitoring-dashboard">
        <div class="dashboard-card dashboard-card-primary">
            <div class="card-value" id="total-active-users">{{ $totalActiveUsers }}</div>
            <div class="card-label">Thí sinh đang thi</div>
        </div>
        <div class="dashboard-card">
            <div class="card-value" id="total-active-tests">{{ $activeTests->count() }}</div>
            <div class="card-label">Bài thi đang diễn ra</div>
        </div>
        <div class="dashboard-card dashboard-card-warning">
            <div class="card-value" id="total-suspicious">{{ $totalSuspicious }}</div>
            <div class="card-label">Hành vi đáng ngờ</div>
        </div>
        <div class="dashboard-card dashboard-card-danger">
            <div class="card-value" id="total-warnings">{{ $totalWarnings }}</div>
            <div class="card-label">Cảnh báo</div>
        </div>
    </div>
    
    <div class="card shadow filter-card">
        <div class="card-body">
            <form action="{{ route('admin.monitor.active') }}" method="GET" class="row">
                <div class="col-md-4 mb-3">
                    <label for="test_type" class="form-label">Loại bài thi</label>
                    <select class="form-select" id="test_type" name="test_type">
                        <option value="">Tất cả loại</option>
                        <option value="certification" {{ request('test_type') == 'certification' ? 'selected' : '' }}>Chứng chỉ</option>
                        <option value="assessment" {{ request('test_type') == 'assessment' ? 'selected' : '' }}>Đánh giá định kỳ</option>
                        <option value="placement" {{ request('test_type') == 'placement' ? 'selected' : '' }}>Xếp loại</option>
                        <option value="practice" {{ request('test_type') == 'practice' ? 'selected' : '' }}>Luyện tập</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="position_id" class="form-label">Chức danh</label>
                    <select class="form-select" id="position_id" name="position_id">
                        <option value="">Tất cả chức danh</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="warnings_only" class="form-label">Hiển thị</label>
                    <div class="d-flex align-items-center h-75">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" id="warnings_only" name="warnings_only" value="1" {{ request('warnings_only') ? 'checked' : '' }}>
                            <label class="form-check-label" for="warnings_only">
                                Chỉ hiện thí sinh có cảnh báo
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Lọc</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @if($activeTests->count() > 0)
        <div class="active-tests-count">
            Đang hiển thị {{ $activeTests->count() }} bài thi đang diễn ra
        </div>
        
        <div class="monitor-grid">
            @foreach($activeTests as $test)
                <div class="card shadow active-test-card" onclick="window.location.href='{{ route('admin.monitor.test', $test->id) }}'">
                    <div class="card-header py-3 bg-{{ $test->type == 'certification' ? 'primary' : ($test->type == 'assessment' ? 'info' : ($test->type == 'placement' ? 'secondary' : 'success')) }} text-white">
                        <h6 class="m-0 font-weight-bold">{{ $test->title }}</h6>
                        <span class="active-user-count">{{ $test->active_users_count }} thí sinh</span>
                    </div>
                    <div class="card-body p-0">
                        @forelse($test->activeAttempts as $attempt)
                            <div class="user-status">
                                <div class="user-status-indicator status-{{ $attempt->warning_level > 0 ? 'warning' : ($attempt->is_idle ? 'idle' : 'active') }}"></div>
                                <div class="user-info">
                                    <div class="user-name">{{ $attempt->user->name }}
                                        @if($attempt->warning_level > 0)
                                            <span class="warning-badge">{{ $attempt->warning_level }} cảnh báo</span>
                                        @endif
                                        @if($attempt->is_idle)
                                            <span class="info-badge">Không hoạt động</span>
                                        @endif
                                    </div>
                                    <div class="user-meta">
                                        <span>{{ optional($attempt->user->thuyenvien)->position->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="user-progress">
                                    <div class="progress-text">{{ $attempt->progress }}%</div>
                                    <div class="time-remaining">{{ $attempt->time_remaining }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center text-muted">
                                <i class="fas fa-info-circle me-1"></i> Không có thí sinh đang làm bài
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Hiện tại không có bài thi nào đang diễn ra.
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Cập nhật dữ liệu theo dõi real-time
    function refreshMonitorData() {
        fetch("{{ route('admin.monitor.refresh') }}")
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-active-users').textContent = data.totalActiveUsers;
                document.getElementById('total-active-tests').textContent = data.totalActiveTests;
                document.getElementById('total-suspicious').textContent = data.totalSuspicious;
                document.getElementById('total-warnings').textContent = data.totalWarnings;
                
                // Nếu có thay đổi lớn trong dữ liệu, reload trang
                if (data.shouldRefresh) {
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error refreshing monitoring data:', error));
    }
    
    // Cập nhật dữ liệu mỗi 30 giây
    setInterval(refreshMonitorData, 30000);
    
    // Thiết lập các cảnh báo âm thanh cho giám thị
    const warningSound = new Audio('{{ asset('sounds/warning.mp3') }}');
    
    function checkNewWarnings() {
        fetch("{{ route('admin.monitor.checkWarnings') }}")
            .then(response => response.json())
            .then(data => {
                if (data.newWarnings > 0) {
                    warningSound.play();
                    alert(`Có ${data.newWarnings} cảnh báo mới từ các thí sinh!`);
                }
            })
            .catch(error => console.error('Error checking warnings:', error));
    }
    
    // Kiểm tra cảnh báo mới mỗi 60 giây
    setInterval(checkNewWarnings, 60000);
</script>
@endsection 