@extends('layouts.app')

@section('title', 'Báo cáo & Thống kê - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .dashboard-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .overview-card {
        border-radius: 8px;
        transition: transform 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .overview-card:hover {
        transform: translateY(-5px);
    }
    
    .card-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .report-group {
        margin-bottom: 3rem;
    }
    
    .report-group h3 {
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .report-card {
        border-radius: 8px;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        height: 100%;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .report-card .card-body {
        padding: 1.5rem;
    }
    
    .report-card .report-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }
    
    .chart-container {
        height: 300px;
        margin-bottom: 1.5rem;
    }
    
    .seafarer-item {
        margin-bottom: 1rem;
        padding: 0.8rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        transition: all 0.3s;
    }
    
    .seafarer-item:hover {
        background-color: #e9ecef;
    }
    
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .recent-test-item {
        padding: 1rem;
        border-left: 4px solid var(--primary-color);
        margin-bottom: 1rem;
        background-color: #f8f9fa;
        transition: all 0.3s;
    }
    
    .recent-test-item:hover {
        background-color: #e9ecef;
    }
    
    .score-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-weight: bold;
    }
    
    .score-high {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .score-medium {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .score-low {
        background-color: #fee2e2;
        color: #b91c1c;
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Báo cáo & Thống kê</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item active text-white-50">Báo cáo & Thống kê</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    <!-- Tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card overview-card">
                <div class="card-body text-center">
                    <div class="card-icon text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Thuyền viên</h5>
                    <h2 class="mb-0">{{ $seafarerCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card overview-card">
                <div class="card-body text-center">
                    <div class="card-icon text-success">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="card-title">Bài kiểm tra</h5>
                    <h2 class="mb-0">{{ $testCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card overview-card">
                <div class="card-body text-center">
                    <div class="card-icon text-info">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h5 class="card-title">Lượt thi</h5>
                    <h2 class="mb-0">{{ $testAttemptCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card overview-card">
                <div class="card-body text-center">
                    <div class="card-icon text-warning">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="card-title">Điểm trung bình</h5>
                    <h2 class="mb-0">{{ number_format($averageScore, 1) }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Các loại báo cáo -->
    <div class="report-group">
        <h3><i class="fas fa-file-contract me-2"></i> Báo cáo chính</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card report-card shadow">
                    <div class="card-body text-center">
                        <div class="report-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="card-title">Xu hướng Hiệu suất</h5>
                        <p class="card-text">Phân tích xu hướng điểm số theo thời gian của các thuyền viên.</p>
                        <a href="{{ route('admin.reports.performance') }}" class="btn btn-primary">Xem báo cáo</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card report-card shadow">
                    <div class="card-body text-center">
                        <div class="report-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h5 class="card-title">Tìm kiếm kết quả</h5>
                        <p class="card-text">Tìm kiếm nhanh kết quả bài kiểm tra của thuyền viên.</p>
                        <form action="{{ route('admin.reports.search') }}" method="GET" class="mt-3">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Tên thuyền viên..." name="search" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card report-card shadow">
                    <div class="card-body text-center">
                        <div class="report-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h5 class="card-title">Quản lý danh mục</h5>
                        <p class="card-text">Quản lý các danh mục bài kiểm tra và câu hỏi.</p>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">Xem danh mục</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Biểu đồ Thuyền viên theo chức danh -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-1"></i> Thuyền viên theo chức danh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="seafarersByPositionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Biểu đồ Điểm trung bình theo loại bài kiểm tra -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-1"></i> Điểm trung bình theo bài kiểm tra
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="averageScoresByTestChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Thuyền viên xuất sắc nhất -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-1"></i> Thuyền viên xuất sắc nhất
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($topSeafarers as $seafarer)
                        <div class="seafarer-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="https://via.placeholder.com/150" alt="{{ $seafarer->name }}" class="avatar me-3">
                                <div>
                                    <h6 class="mb-0">{{ $seafarer->name }}</h6>
                                    <small class="text-muted">
                                        @if($seafarer->thuyenVien && $seafarer->thuyenVien->position)
                                            {{ $seafarer->thuyenVien->position->name }}
                                        @else
                                            Chưa cập nhật chức danh
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ number_format($seafarer->average_score, 1) }}</div>
                                <small class="text-muted">{{ $seafarer->test_attempts_count }} lượt thi</small>
                            </div>
                            <a href="{{ route('admin.reports.seafarer', $seafarer->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-chart-line"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-center text-muted my-4">Chưa có dữ liệu thuyền viên</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Lượt thi gần đây -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-1"></i> Lượt thi gần đây
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($recentAttempts as $attempt)
                        <div class="recent-test-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ $attempt->user->name }}</h6>
                                    <div class="text-muted small">{{ $attempt->test->title }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="score-badge 
                                        @if($attempt->score >= 80) score-high 
                                        @elseif($attempt->score >= 60) score-medium 
                                        @else score-low 
                                        @endif">
                                        {{ $attempt->score }}/100
                                    </div>
                                    <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted my-4">Chưa có lượt thi nào</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Biểu đồ Thuyền viên theo chức danh
    const positionLabels = {!! json_encode($seafarersByPosition->pluck('name')) !!};
    const positionData = {!! json_encode($seafarersByPosition->pluck('count')) !!};
    
    new Chart(document.getElementById('seafarersByPositionChart'), {
        type: 'pie',
        data: {
            labels: positionLabels,
            datasets: [{
                data: positionData,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#6f42c1', '#fd7e14', '#20c997', '#67c7db'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    
    // Biểu đồ Điểm trung bình theo loại bài kiểm tra
    const testLabels = {!! json_encode($averageScoresByTest->pluck('title')) !!};
    const testData = {!! json_encode($averageScoresByTest->pluck('average_score')) !!};
    
    new Chart(document.getElementById('averageScoresByTestChart'), {
        type: 'bar',
        data: {
            labels: testLabels,
            datasets: [{
                label: 'Điểm trung bình',
                data: testData,
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script>
@endsection 