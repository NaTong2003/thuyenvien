@extends('layouts.app')

@section('title', 'Báo cáo Xu hướng Hiệu suất - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .report-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 2rem;
    }
    
    .filter-card {
        border-radius: 8px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .filter-card .card-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1rem;
    }
    
    .filter-card .card-body {
        padding: 1.5rem;
    }
    
    .trend-arrow {
        font-size: 1.1rem;
        margin-left: 0.5rem;
    }
    
    .trend-up {
        color: var(--success);
    }
    
    .trend-down {
        color: var(--danger);
    }
    
    .trend-neutral {
        color: var(--warning);
    }
    
    .metric-card {
        border-radius: 8px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        margin-bottom: 1.5rem;
        transition: transform 0.3s;
    }
    
    .metric-card:hover {
        transform: translateY(-5px);
    }
    
    .metric-card .card-body {
        padding: 1.5rem;
    }
    
    .metric-title {
        font-size: 0.9rem;
        font-weight: bold;
        color: var(--gray-600);
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: bold;
        margin: 0.5rem 0;
    }
    
    .metric-change {
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div class="report-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Báo cáo Xu hướng Hiệu suất</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-white">Báo cáo</a></li>
                <li class="breadcrumb-item active text-white-50">Xu hướng Hiệu suất</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card filter-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.performance') }}" method="GET">
                        <div class="mb-3">
                            <label for="date_range" class="form-label">Khoảng thời gian</label>
                            <select class="form-select" id="date_range" name="date_range">
                                <option value="7" {{ request('date_range') == 7 ? 'selected' : '' }}>7 ngày gần đây</option>
                                <option value="30" {{ request('date_range') == 30 ? 'selected' : '' }}>30 ngày gần đây</option>
                                <option value="90" {{ request('date_range', 90) == 90 ? 'selected' : '' }}>90 ngày gần đây</option>
                                <option value="180" {{ request('date_range') == 180 ? 'selected' : '' }}>6 tháng gần đây</option>
                                <option value="365" {{ request('date_range') == 365 ? 'selected' : '' }}>1 năm gần đây</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="position_id" class="form-label">Chức danh</label>
                            <select class="form-select" id="position_id" name="position_id">
                                <option value="">Tất cả chức danh</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ship_type_id" class="form-label">Loại tàu</label>
                            <select class="form-select" id="ship_type_id" name="ship_type_id">
                                <option value="">Tất cả loại tàu</option>
                                @foreach($shipTypes as $shipType)
                                    <option value="{{ $shipType->id }}" {{ request('ship_type_id') == $shipType->id ? 'selected' : '' }}>
                                        {{ $shipType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="test_id" class="form-label">Bài kiểm tra</label>
                            <select class="form-select" id="test_id" name="test_id">
                                <option value="">Tất cả bài kiểm tra</option>
                                @foreach($tests as $test)
                                    <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                        {{ $test->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Áp dụng bộ lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
         
        </div>
        
        <div class="col-lg-9">
            <div class="row">
                <div class="col-md-4">
                    <div class="card metric-card">
                        <div class="card-body">
                            <div class="metric-title">Điểm trung bình</div>
                            <div class="metric-value">77.5</div>
                            <div class="metric-change">
                                <span>+3.2% </span>
                                <span class="trend-arrow trend-up"><i class="fas fa-arrow-up"></i></span>
                                <span class="text-muted ms-1">so với kỳ trước</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card metric-card">
                        <div class="card-body">
                            <div class="metric-title">Tỷ lệ đạt</div>
                            <div class="metric-value">83.2%</div>
                            <div class="metric-change">
                                <span>+1.8% </span>
                                <span class="trend-arrow trend-up"><i class="fas fa-arrow-up"></i></span>
                                <span class="text-muted ms-1">so với kỳ trước</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card metric-card">
                        <div class="card-body">
                            <div class="metric-title">Số lượt kiểm tra</div>
                            <div class="metric-value">254</div>
                            <div class="metric-change">
                                <span>-2.5% </span>
                                <span class="trend-arrow trend-down"><i class="fas fa-arrow-down"></i></span>
                                <span class="text-muted ms-1">so với kỳ trước</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-1"></i> Xu hướng điểm trung bình theo thời gian
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-1"></i> Phân bố điểm theo chức danh
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="positionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-bar me-1"></i> Phân bố điểm theo loại tàu
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="shipTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-1"></i> Thuyền viên có hiệu suất cao nhất
                    </h6>
                    <a href="{{ route('admin.reports.export') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-download me-1"></i> Xuất danh sách
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Thuyền viên</th>
                                    <th scope="col">Chức danh</th>
                                    <th scope="col">Số lượt thi</th>
                                    <th scope="col">Điểm TB</th>
                                    <th scope="col">Xu hướng</th>
                                    <th scope="col">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @foreach($topSeafarers as $seafarer)
                                    <tr>
                                        <th scope="row">{{ $rank++ }}</th>
                                        <td>{{ $seafarer->name }}</td>
                                        <td>{{ $seafarer->thuyenVien && $seafarer->thuyenVien->position ? $seafarer->thuyenVien->position->name : 'Không có' }}</td>
                                        <td>{{ $seafarer->test_attempts_count }}</td>
                                        <td>{{ number_format($seafarer->average_score, 2) }}</td>
                                        <td>
                                            @php
                                                $previousScore = $seafarer->testAttempts()->orderBy('created_at', 'desc')->skip(1)->first()->score ?? 0;
                                                $latestScore = $seafarer->testAttempts()->orderBy('created_at', 'desc')->first()->score ?? 0;
                                                $trend = $latestScore - $previousScore;
                                            @endphp
                                            
                                            @if($trend > 0)
                                                <span class="trend-arrow trend-up"><i class="fas fa-arrow-up"></i></span>
                                            @elseif($trend < 0)
                                                <span class="trend-arrow trend-down"><i class="fas fa-arrow-down"></i></span>
                                            @else
                                                <span class="trend-arrow trend-neutral"><i class="fas fa-minus"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.reports.seafarer', $seafarer->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Lấy dữ liệu từ PHP
        var performanceLabels = @json($performanceData->pluck('date')->toArray());
        var performanceValues = @json($performanceData->pluck('average_score')->toArray());
        
        // Biểu đồ xu hướng hiệu suất
        const performanceChart = new Chart(
            document.getElementById('performanceChart'),
            {
                type: 'line',
                data: {
                    labels: performanceLabels,
                    datasets: [
                        {
                            label: 'Điểm trung bình',
                            data: performanceValues,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Xu hướng hiệu suất theo thời gian'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            min: 60,
                            max: 100
                        }
                    }
                }
            }
        );
        
        // Biểu đồ theo chức danh
        const positionChart = new Chart(
            document.getElementById('positionChart'),
            {
                type: 'bar',
                data: {
                    labels: ['Thuyền trưởng', 'Máy trưởng', 'Thuyền phó 1', 'Máy phó 1', 'Thuyền phó 2'],
                    datasets: [
                        {
                            label: 'Điểm trung bình',
                            data: [85.5, 83.2, 81.7, 80.9, 79.5],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)',
                                'rgba(201, 203, 207, 0.7)'
                            ],
                            borderColor: [
                                'rgb(75, 192, 192)',
                                'rgb(54, 162, 235)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)',
                                'rgb(201, 203, 207)'
                            ],
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 70,
                            max: 100
                        }
                    }
                }
            }
        );
        
        // Biểu đồ theo loại tàu
        const shipTypeChart = new Chart(
            document.getElementById('shipTypeChart'),
            {
                type: 'radar',
                data: {
                    labels: [
                        'Tàu container',
                        'Tàu dầu',
                        'Tàu hàng rời',
                        'Tàu chở khí',
                        'Tàu khách'
                    ],
                    datasets: [{
                        label: 'Điểm trung bình',
                        data: [82.5, 78.4, 77.8, 80.2, 85.1],
                        fill: true,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgb(54, 162, 235)',
                        pointBackgroundColor: 'rgb(54, 162, 235)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(54, 162, 235)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: false,
                            min: 70,
                            max: 100
                        }
                    }
                }
            }
        );
    });
</script>
@endsection 