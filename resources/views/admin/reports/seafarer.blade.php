@extends('layouts.app')

@section('title', 'Báo cáo Thuyền viên - ' . $user->name)

@section('css')
<style>
    .report-header {
        background-color: var(--primary-color);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    
    .chart-container {
        height: 300px;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 2rem;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .profile-details {
        flex: 1;
    }
    
    .profile-details h1 {
        margin-bottom: 0.5rem;
    }
    
    .profile-details .subtitle {
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 1rem;
    }
    
    .profile-stats {
        display: flex;
        gap: 2rem;
    }
    
    .profile-stat {
        text-align: center;
    }
    
    .profile-stat .value {
        font-size: 1.8rem;
        font-weight: bold;
    }
    
    .profile-stat .label {
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    .certificate-item {
        border-left: 4px solid var(--primary-color);
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: #f8f9fa;
        transition: all 0.3s;
    }
    
    .certificate-item:hover {
        background-color: #e9ecef;
    }
    
    .strength-item, .weakness-item {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 8px;
    }
    
    .strength-item {
        background-color: rgba(40, 167, 69, 0.1);
        border-left: 4px solid #28a745;
    }
    
    .weakness-item {
        background-color: rgba(220, 53, 69, 0.1);
        border-left: 4px solid #dc3545;
    }
    
    .test-attempt-item {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: all 0.3s;
    }
    
    .test-attempt-item:hover {
        background-color: #e9ecef;
    }
    
    .progress {
        height: 10px;
        margin-bottom: 0.5rem;
    }
    
    .progress-bar-success {
        background-color: #28a745;
    }
    
    .progress-bar-danger {
        background-color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="report-header">
    <div class="container">
        <div class="profile-header">
            <img src="https://via.placeholder.com/150" alt="{{ $user->name }}" class="profile-avatar">
            <div class="profile-details">
                <h1>{{ $user->name }}</h1>
                <div class="subtitle">
                    @if($user->thuyenVien && $user->thuyenVien->position)
                        {{ $user->thuyenVien->position->name }}
                        @if($user->thuyenVien->shipType)
                            - {{ $user->thuyenVien->shipType->name }}
                        @endif
                    @else
                        Thuyền viên
                    @endif
                </div>
                <div class="profile-stats">
                    <div class="profile-stat">
                        <div class="value">{{ $totalAttempts }}</div>
                        <div class="label">Lượt thi</div>
                    </div>
                    <div class="profile-stat">
                        <div class="value">{{ number_format($averageScore, 1) }}</div>
                        <div class="label">Điểm trung bình</div>
                    </div>
                    <div class="profile-stat">
                        <div class="value">{{ $passRate }}%</div>
                        <div class="label">Tỷ lệ đạt</div>
                    </div>
                    <div class="profile-stat">
                        <div class="value">{{ $certificates->count() }}</div>
                        <div class="label">Chứng chỉ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <!-- Thông tin cơ bản -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4">
                    <i class="fas fa-chart-line me-2"></i> Thống kê tổng quan
                </h2>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại báo cáo tổng quan
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card h-100 bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list stat-icon"></i>
                    <h5>Tổng số lượt thi</h5>
                    <h2 class="mt-3">{{ $totalAttempts }}</h2>
                    <p class="mb-0">{{ $completedAttempts }} lượt đã hoàn thành</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100 bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle stat-icon"></i>
                    <h5>Số lượt đạt</h5>
                    <h2 class="mt-3">{{ $passedAttempts }}</h2>
                    <p class="mb-0">{{ $passRate }}% tỷ lệ đạt</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100 bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle stat-icon"></i>
                    <h5>Số lượt không đạt</h5>
                    <h2 class="mt-3">{{ $failedAttempts }}</h2>
                    <p class="mb-0">{{ 100 - $passRate }}% tỷ lệ không đạt</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100 bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-star stat-icon"></i>
                    <h5>Điểm trung bình</h5>
                    <h2 class="mt-3">{{ number_format($averageScore, 1) }}</h2>
                    <p class="mb-0">trên thang điểm 100</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ lịch sử điểm -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử điểm số</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="scoreHistoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân phối điểm số</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="scoreDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hiệu suất theo loại bài kiểm tra -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hiệu suất theo loại bài kiểm tra</h6>
                </div>
                <div class="card-body">
                    @if(count($testPerformance) > 0)
                        <div class="chart-container">
                            <canvas id="testPerformanceChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                            <p>Chưa có đủ dữ liệu để hiển thị biểu đồ.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Điểm mạnh và điểm yếu -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Điểm mạnh</h6>
                </div>
                <div class="card-body">
                    @if(count($strengths) > 0)
                        @foreach($strengths as $strength)
                            <div class="strength-item">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $strength['category'] }}</h6>
                                    <span class="badge bg-success">{{ $strength['correct_rate'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: {{ $strength['correct_rate'] }}%" aria-valuenow="{{ $strength['correct_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Dựa trên {{ $strength['total'] }} câu trả lời</small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-medal fa-3x text-gray-300 mb-3"></i>
                            <p>Chưa có đủ dữ liệu để xác định điểm mạnh.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Điểm yếu</h6>
                </div>
                <div class="card-body">
                    @if(count($weaknesses) > 0)
                        @foreach($weaknesses as $weakness)
                            <div class="weakness-item">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $weakness['category'] }}</h6>
                                    <span class="badge bg-danger">{{ $weakness['correct_rate'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" style="width: {{ $weakness['correct_rate'] }}%" aria-valuenow="{{ $weakness['correct_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Dựa trên {{ $weakness['total'] }} câu trả lời</small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-gray-300 mb-3"></i>
                            <p>Chưa có đủ dữ liệu để xác định điểm yếu.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chứng chỉ và lượt thi -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Chứng chỉ đã đạt được</h6>
                </div>
                <div class="card-body">
                    @if($certificates->count() > 0)
                        @foreach($certificates as $certificate)
                            <div class="certificate-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $certificate->title }}</h6>
                                        <p class="mb-1 text-muted">
                                            @if($certificate->test)
                                                <i class="fas fa-clipboard-check me-1"></i> {{ $certificate->test->title }}
                                            @endif
                                        </p>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i> Cấp ngày: {{ $certificate->issue_date->format('d/m/Y') }}
                                            @if($certificate->expiry_date)
                                                <span class="mx-2">|</span>
                                                <i class="far fa-clock me-1"></i> Hết hạn: {{ $certificate->expiry_date->format('d/m/Y') }}
                                            @endif
                                        </small>
                                    </div>
                                    <a href="{{ route('admin.certificates.show', $certificate->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-certificate fa-3x text-gray-300 mb-3"></i>
                            <p>Thuyền viên chưa có chứng chỉ nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử làm bài</h6>
                </div>
                <div class="card-body">
                    @if($testAttempts->count() > 0)
                        @foreach($testAttempts as $attempt)
                            <div class="test-attempt-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $attempt->test->title ?? 'N/A' }}</h6>
                                        <p class="mb-1">
                                            <span class="badge bg-{{ $attempt->is_completed ? ($attempt->isPassed() ? 'success' : 'danger') : 'warning' }}">
                                                @if(!$attempt->is_completed)
                                                    Chưa hoàn thành
                                                @elseif($attempt->isPassed())
                                                    Đạt
                                                @else
                                                    Không đạt
                                                @endif
                                            </span>
                                            @if($attempt->is_completed)
                                                <span class="ms-2">Điểm: {{ $attempt->score }}/100</span>
                                            @endif
                                        </p>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i> {{ $attempt->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <a href="{{ route('admin.reports.attempt', $attempt->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <p>Thuyền viên chưa có lượt thi nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Lịch sử điểm số
        var scoreHistoryData = @json($scoreHistory);
        
        if (scoreHistoryData.length > 0) {
            var scoreHistoryCtx = document.getElementById('scoreHistoryChart').getContext('2d');
            var scoreHistoryChart = new Chart(scoreHistoryCtx, {
                type: 'line',
                data: {
                    labels: scoreHistoryData.map(function(item) { return item.date; }),
                    datasets: [{
                        label: 'Điểm số',
                        data: scoreHistoryData.map(function(item) { return item.score; }),
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '/100';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    var dataPoint = scoreHistoryData[context.dataIndex];
                                    return label + ': ' + context.parsed.y + '/100 - ' + dataPoint.test_name;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Phân phối điểm số
        var scoreDistributionLabels = @json($scoreRanges);
        var scoreDistributionData = @json($scoreDistribution);
        
        var scoreDistributionCtx = document.getElementById('scoreDistributionChart').getContext('2d');
        var scoreDistributionChart = new Chart(scoreDistributionCtx, {
            type: 'pie',
            data: {
                labels: scoreDistributionLabels,
                datasets: [{
                    data: scoreDistributionData,
                    backgroundColor: [
                        '#dc3545', '#e83e8c', '#6f42c1', '#007bff', '#17a2b8',
                        '#20c997', '#28a745', '#ffc107', '#fd7e14', '#f8f9fa'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Hiệu suất theo loại bài kiểm tra
        var testPerformanceData = @json($testPerformance);
        
        if (testPerformanceData.length > 0) {
            var testPerformanceCtx = document.getElementById('testPerformanceChart').getContext('2d');
            var testPerformanceChart = new Chart(testPerformanceCtx, {
                type: 'bar',
                data: {
                    labels: testPerformanceData.map(function(item) { return item.test_name; }),
                    datasets: [{
                        label: 'Điểm trung bình',
                        data: testPerformanceData.map(function(item) { return item.average_score; }),
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '/100';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    var dataPoint = testPerformanceData[context.dataIndex];
                                    return label + ': ' + context.parsed.y.toFixed(1) + '/100 (' + dataPoint.attempts_count + ' lượt thi)';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
