@extends('layouts.app')

@section('title', 'Admin Dashboard - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>

    <div class="row">
        <!-- Thống kê thuyền viên -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số thuyền viên</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $seafarerCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê câu hỏi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng số câu hỏi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $questionCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê bài kiểm tra -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Bài kiểm tra</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $testCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê lượt thi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Lượt thi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $testAttemptCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Biểu đồ số lượng thuyền viên theo chức danh -->
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold ">Thống kê thuyền viên theo chức danh</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="seafarerByPositionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ điểm trung bình theo loại bài kiểm tra -->
        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold ">Điểm trung bình theo loại bài kiểm tra</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="testScoreAvgChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bài kiểm tra gần đây -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bài kiểm tra gần đây</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentTests) && count($recentTests) > 0)
                        <div class="list-group">
                            @foreach($recentTests as $test)
                                <a href="{{ route('admin.tests.show', $test->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $test->title }}</h5>
                                        <small>{{ $test->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($test->description, 100) }}</p>
                                    <small>{{ $test->position ? $test->position->name : 'Tất cả vị trí' }} | {{ $test->shipType ? $test->shipType->name : 'Tất cả loại tàu' }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                            <p>Chưa có bài kiểm tra nào.</p>
                            <a href="{{ route('admin.tests.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Tạo bài kiểm tra
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kết quả kiểm tra gần đây -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kết quả kiểm tra gần đây</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentTestAttempts) && count($recentTestAttempts) > 0)
                        <div class="list-group">
                            @foreach($recentTestAttempts as $attempt)
                                <a href="{{ route('admin.reports.seafarer', $attempt->user_id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $attempt->user->name }}</h5>
                                        <small>{{ $attempt->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $attempt->test->title }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>Điểm: {{ $attempt->score }}/100</small>
                                        <span class="badge bg-{{ $attempt->isPassed() ? 'success' : 'danger' }}">
                                            {{ $attempt->isPassed() ? 'Đạt' : 'Không đạt' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <p>Chưa có kết quả kiểm tra nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dummy data for charts (thay bằng dữ liệu thực từ controller)
    const positionLabels = ['Thuyền trưởng', 'Thuyền phó 1', 'Thuyền phó 2', 'Máy trưởng', 'Thủy thủ'];
    const positionData = [5, 10, 8, 6, 15];
    
    const testLabels = ['Hàng hải', 'An toàn', 'Bảo trì', 'Tiếng Anh', 'Kỹ thuật'];
    const testData = [75, 82, 65, 70, 80];
    
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        // Seafarer by position chart
        const seafarerCtx = document.getElementById('seafarerByPositionChart').getContext('2d');
        new Chart(seafarerCtx, {
            type: 'bar',
            data: {
                labels: positionLabels,
                datasets: [{
                    label: 'Số lượng thuyền viên',
                    data: positionData,
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Test score average chart
        const testScoreCtx = document.getElementById('testScoreAvgChart').getContext('2d');
        new Chart(testScoreCtx, {
            type: 'doughnut',
            data: {
                labels: testLabels,
                datasets: [{
                    label: 'Điểm trung bình',
                    data: testData,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection 