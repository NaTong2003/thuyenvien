@extends('layouts.app')

@section('title', 'Báo cáo Thuyền viên - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .seafarer-header {
        background-color: var(--primary-color);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid rgba(255, 255, 255, 0.3);
        margin-right: 2rem;
        overflow: hidden;
    }
    
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .seafarer-info h2 {
        margin-bottom: 0.5rem;
    }
    
    .seafarer-info .position {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        opacity: 0.9;
    }
    
    .seafarer-stats {
        display: flex;
        gap: 2rem;
        margin-top: 1rem;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 1.5rem;
    }
    
    .skill-progress {
        height: 8px;
        border-radius: 4px;
        margin-bottom: 1.5rem;
    }
    
    .test-item {
        padding: 1rem;
        border-radius: 8px;
        background-color: #f8f9fa;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    
    .test-item:hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        transform: translateY(-3px);
    }
    
    .test-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .test-title {
        font-weight: bold;
        font-size: 1.1rem;
    }
    
    .test-score {
        font-weight: bold;
    }
    
    .test-info {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .score-high {
        color: #28a745;
    }
    
    .score-medium {
        color: #fd7e14;
    }
    
    .score-low {
        color: #dc3545;
    }
    
    .recommendation-item {
        padding: 1rem;
        border-left: 4px solid var(--primary-color);
        background-color: #f8f9fa;
        margin-bottom: 1rem;
    }
    
    .progress-title {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .progress-label {
        font-weight: 500;
    }
    
    .progress-value {
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="seafarer-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="profile-avatar">
                    <img src="https://via.placeholder.com/150" alt="{{ $user->name }}">
                </div>
                <div class="seafarer-info">
                    <h2>{{ $user->name }}</h2>
                    <div class="position">
                        @if($user->thuyenVien && $user->thuyenVien->position)
                            {{ $user->thuyenVien->position->name }}
                        @else
                            Chưa cập nhật chức danh
                        @endif
                    </div>
                    <div class="seafarer-stats">
                        <div class="stat-item">
                            <div class="stat-value">{{ $testAttempts->count() }}</div>
                            <div class="stat-label">Lượt thi</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ number_format($averageScore, 1) }}</div>
                            <div class="stat-label">Điểm TB</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $passRate }}%</div>
                            <div class="stat-label">Tỷ lệ đạt</div>
                        </div>
                    </div>
                </div>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-white">Báo cáo</a></li>
                <li class="breadcrumb-item active text-white-50">Thuyền viên</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-1"></i> Xu hướng điểm theo thời gian
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="scoreChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-list me-1"></i> Kết quả bài kiểm tra gần đây
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentTestAttempts->count() > 0)
                        @foreach($recentTestAttempts as $attempt)
                            <div class="test-item">
                                <div class="test-header">
                                    <div class="test-title">{{ $attempt->test->title }}</div>
                                    <div class="test-score 
                                        @if($attempt->score >= 80) score-high 
                                        @elseif($attempt->score >= 60) score-medium 
                                        @else score-low @endif">
                                        {{ number_format($attempt->score, 1) }}/100
                                    </div>
                                </div>
                                <div class="test-info">
                                    <div>
                                        <i class="fas fa-calendar-alt me-1"></i> {{ $attempt->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div>
                                        <i class="fas fa-clock me-1"></i> {{ $attempt->getDurationInMinutes() }} phút
                                    </div>
                                    <div>
                                        @if($attempt->isPassed())
                                            <span class="badge bg-success">Đạt</span>
                                        @else
                                            <span class="badge bg-danger">Chưa đạt</span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.reports.test', $attempt->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> Thuyền viên chưa tham gia bài kiểm tra nào.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-1"></i> Lịch sử kiểm tra
                    </h6>
                    <a href="#" class="btn btn-sm btn-primary">
                        <i class="fas fa-download me-1"></i> Xuất báo cáo
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Bài kiểm tra</th>
                                    <th>Thời gian</th>
                                    <th>Điểm số</th>
                                    <th>Kết quả</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($testAttempts->count() > 0)
                                    @foreach($testAttempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->test->title }}</td>
                                            <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ number_format($attempt->score, 1) }}/100</td>
                                            <td>
                                                @if($attempt->isPassed())
                                                    <span class="badge bg-success">Đạt</span>
                                                @else
                                                    <span class="badge bg-danger">Chưa đạt</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.reports.test', $attempt->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> Thông tin thuyền viên
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th width="40%">ID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Mã thuyền viên</th>
                                    <td>{{ $user->seafarer_id ?? 'Chưa cập nhật' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Điện thoại</th>
                                    <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                                </tr>
                                <tr>
                                    <th>Chức danh</th>
                                    <td>
                                        @if($user->thuyenVien && $user->thuyenVien->position)
                                            {{ $user->thuyenVien->position->name }}
                                        @else
                                            <span class="text-muted">Chưa cập nhật</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Loại tàu</th>
                                    <td>
                                        @if($user->thuyenVien && $user->thuyenVien->shipType)
                                            {{ $user->thuyenVien->shipType->name }}
                                        @else
                                            <span class="text-muted">Chưa cập nhật</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kinh nghiệm</th>
                                    <td>
                                        @if($user->thuyenVien && $user->thuyenVien->experience)
                                            {{ $user->thuyenVien->experience }} năm
                                        @else
                                            <span class="text-muted">Chưa cập nhật</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày đăng ký</th>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-radar me-1"></i> Phân tích kỹ năng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="skillChart"></canvas>
                    </div>
                    
                    <div class="mt-3">
                        <div class="progress-title">
                            <span class="progress-label">Kỹ năng hàng hải</span>
                            <span class="progress-value">85%</span>
                        </div>
                        <div class="progress skill-progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="progress-title">
                            <span class="progress-label">An toàn hàng hải</span>
                            <span class="progress-value">92%</span>
                        </div>
                        <div class="progress skill-progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 92%" aria-valuenow="92" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="progress-title">
                            <span class="progress-label">Xử lý tình huống</span>
                            <span class="progress-value">78%</span>
                        </div>
                        <div class="progress skill-progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 78%" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="progress-title">
                            <span class="progress-label">Luật hàng hải</span>
                            <span class="progress-value">65%</span>
                        </div>
                        <div class="progress skill-progress">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="progress-title">
                            <span class="progress-label">Tiếng Anh chuyên ngành</span>
                            <span class="progress-value">72%</span>
                        </div>
                        <div class="progress skill-progress">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-lightbulb me-1"></i> Đề xuất phát triển
                    </h6>
                </div>
                <div class="card-body">
                    <div class="recommendation-item">
                        <h6 class="mb-2">Cải thiện kiến thức về luật hàng hải quốc tế</h6>
                        <p class="mb-0 small">Dựa trên kết quả kiểm tra, thuyền viên cần tăng cường hiểu biết về các quy định mới của IMO và các công ước quốc tế.</p>
                    </div>
                    <div class="recommendation-item">
                        <h6 class="mb-2">Rèn luyện thêm kỹ năng tiếng Anh chuyên ngành</h6>
                        <p class="mb-0 small">Khuyến nghị tham gia khóa học tiếng Anh hàng hải chuyên sâu để nâng cao khả năng giao tiếp trong môi trường quốc tế.</p>
                    </div>
                    <div class="recommendation-item">
                        <h6 class="mb-2">Thực hành thêm tình huống khẩn cấp</h6>
                        <p class="mb-0 small">Mặc dù kết quả khá tốt, nhưng cần thêm thời gian thực hành mô phỏng các tình huống khẩn cấp trên biển.</p>
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
        // Biểu đồ điểm theo thời gian
        const scoreChart = new Chart(
            document.getElementById('scoreChart'),
            {
                type: 'line',
                data: {
                    labels: ['T1/2023', 'T2/2023', 'T3/2023', 'T4/2023', 'T5/2023', 'T6/2023', 'T7/2023', 'T8/2023'],
                    datasets: [
                        {
                            label: 'Điểm kiểm tra',
                            data: [78, 75, 82, 87, 84, 86, 90, 88],
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            tension: 0.1,
                            fill: true
                        },
                        {
                            label: 'Điểm trung bình chung',
                            data: [72, 74, 75, 76, 76, 77, 78, 79],
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderDash: [5, 5],
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
        
        // Biểu đồ kỹ năng
        const skillChart = new Chart(
            document.getElementById('skillChart'),
            {
                type: 'radar',
                data: {
                    labels: [
                        'Kỹ năng hàng hải',
                        'An toàn hàng hải',
                        'Xử lý tình huống',
                        'Luật hàng hải',
                        'Tiếng Anh chuyên ngành'
                    ],
                    datasets: [{
                        label: 'Kỹ năng (%)',
                        data: [85, 92, 78, 65, 72],
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
                    elements: {
                        line: {
                            borderWidth: 3
                        }
                    },
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            suggestedMin: 50,
                            suggestedMax: 100
                        }
                    }
                }
            }
        );
    });
</script>
@endsection 