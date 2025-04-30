@extends('layouts.app')

@section('title', 'Thống kê Bài Kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .test-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .stats-card {
        margin-bottom: 1.5rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .stat-trend {
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    
    .score-distribution {
        height: 300px;
    }
    
    .attempts-timeline {
        height: 250px;
    }
    
    .attempts-list {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .attempt-item {
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .attempt-score {
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    .attempt-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .pass-rate-donut {
        height: 220px;
    }
    
    .leaderboard-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .leaderboard-score {
        font-weight: 700;
    }
    
    .table-responsive {
        margin-bottom: 0;
    }
</style>
@endsection

@section('content')
<div class="test-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Thống kê: {{ $test->title }}</h2>
                <p class="mb-0 text-white-50">
                    Chi tiết kết quả và phân tích dữ liệu của bài kiểm tra.
                </p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tests.index') }}" class="text-white">Bài Kiểm tra</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tests.show', $test->id) }}" class="text-white">Chi tiết</a></li>
                <li class="breadcrumb-item active text-white-50">Thống kê</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-pie me-1"></i> Tổng quan thống kê
            </h6>
        </div>
        <div class="card-body">
            @if(isset($stats) && isset($stats['totalAttempts']) && $stats['totalAttempts'] > 0)
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card text-center p-3">
                            <div class="stat-number text-primary">{{ $stats['totalAttempts'] }}</div>
                            <div class="stat-label">Tổng số lượt làm bài</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card text-center p-3">
                            <div class="stat-number text-info">{{ number_format($stats['avgScore'], 1) }}</div>
                            <div class="stat-label">Điểm trung bình</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card text-center p-3">
                            <div class="stat-number text-success">{{ number_format($stats['passRate'], 1) }}%</div>
                            <div class="stat-label">Tỷ lệ đạt</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card text-center p-3">
                            <div class="stat-number text-warning">{{ number_format($stats['highestScore'], 1) }}</div>
                            <div class="stat-label">Điểm cao nhất</div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Phân bố điểm số</h6>
                            </div>
                            <div class="card-body">
                                <div id="scoreDistributionChart" class="score-distribution"></div>
                            </div>
                        </div>
                        
                        <div class="card shadow-sm">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Số lượt làm bài theo thời gian</h6>
                            </div>
                            <div class="card-body">
                                <div id="attemptsTimelineChart" class="attempts-timeline"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ đạt/không đạt</h6>
                            </div>
                            <div class="card-body text-center">
                                <div id="passRateChart" class="pass-rate-donut"></div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-center">
                                        <div class="me-3">
                                            <span class="badge bg-success p-2">Đạt: {{ $stats['passCount'] }}</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-danger p-2">Không đạt: {{ $stats['failCount'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow-sm">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Top thuyền viên</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Thuyền viên</th>
                                                <th>Điểm</th>
                                                <th>Thời gian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $topAttempts = $testAttempts->sortByDesc('score')->take(5);
                                            @endphp
                                            
                                            @foreach($topAttempts as $index => $attempt)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $attempt->user->name }}</td>
                                                    <td>
                                                        <span class="leaderboard-score {{ $attempt->score >= $test->passing_score ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($attempt->score, 1) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $attempt->created_at->format('d/m/Y H:i') }}</small>
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
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Lịch sử làm bài</h6>
                                <div>
                                    <a href="#" class="btn btn-sm btn-primary" id="exportData">
                                        <i class="fas fa-download me-1"></i> Xuất dữ liệu
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Thuyền viên</th>
                                                <th>Điểm số</th>
                                                <th>Tình trạng</th>
                                                <th>Thời gian làm bài</th>
                                                <th>Thời gian hoàn thành</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($testAttempts as $attempt)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.users.show', $attempt->user_id) }}">
                                                            {{ $attempt->user->name }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold {{ $attempt->score >= $test->passing_score ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($attempt->score, 1) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($attempt->score >= $test->passing_score)
                                                            <span class="badge bg-success">Đạt</span>
                                                        @else
                                                            <span class="badge bg-danger">Không đạt</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attempt->started_at && $attempt->completed_at)
                                                            {{ round((strtotime($attempt->completed_at) - strtotime($attempt->started_at)) / 60) }} phút
                                                        @else
                                                            --
                                                        @endif
                                                    </td>
                                                    <td>{{ $attempt->completed_at ? $attempt->completed_at->format('d/m/Y H:i') : '--' }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.reports.attempt', $attempt->id) }}" class="btn btn-sm btn-info">
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
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Chưa có dữ liệu thống kê cho bài kiểm tra này. Thống kê sẽ được hiển thị khi có thuyền viên hoàn thành bài kiểm tra.
                </div>
            @endif
        </div>
    </div>
    
    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.tests.show', $test->id) }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại chi tiết bài kiểm tra
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($stats) && isset($stats['totalAttempts']) && $stats['totalAttempts'] > 0)
            // Biểu đồ phân bố điểm số
            var scoreOptions = {
                series: [{
                    name: 'Số lượt',
                    data: {!! json_encode($stats['scoreDistribution'] ?? []) !!}
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '55%',
                        distributed: true
                    },
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#fff']
                    }
                },
                colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#36b9cc', '#1cc88a', '#4e73df', '#f6c23e'],
                xaxis: {
                    categories: {!! json_encode($stats['scoreLabels'] ?? []) !!},
                    title: {
                        text: 'Điểm số'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Số lượt thi'
                    }
                },
                title: {
                    text: 'Phân bố điểm số',
                    align: 'left',
                    style: {
                        fontSize: '16px',
                        fontWeight: 'bold',
                        color: '#263238'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " lượt"
                        }
                    }
                }
            };
            
            var scoreChart = new ApexCharts(document.querySelector("#scoreDistributionChart"), scoreOptions);
            scoreChart.render();
            
            // Biểu đồ số lượt làm bài theo thời gian
            var timelineOptions = {
                series: [{
                    name: 'Số lượt làm bài',
                    data: {!! json_encode($stats['timeData'] ?? []) !!}
                }],
                chart: {
                    height: 250,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'category',
                    categories: {!! json_encode($stats['dateLabels'] ?? []) !!}
                },
                yaxis: {
                    title: {
                        text: 'Số lượt làm bài'
                    },
                    min: 0
                },
                tooltip: {
                    x: {
                        format: 'dd/MM'
                    },
                    y: {
                        formatter: function (val) {
                            return val + " lượt"
                        }
                    }
                },
                colors: ['#4e73df'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    }
                }
            };
            
            var timelineChart = new ApexCharts(document.querySelector("#attemptsTimelineChart"), timelineOptions);
            timelineChart.render();
            
            // Biểu đồ tỷ lệ đạt/không đạt
            var passRateOptions = {
                series: [{{ $stats['passRate'] ?? 0 }}, {{ 100 - ($stats['passRate'] ?? 0) }}],
                chart: {
                    type: 'donut',
                    height: 220
                },
                labels: ['Đạt', 'Không đạt'],
                colors: ['#1cc88a', '#e74a3b'],
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Tỷ lệ đạt',
                                    formatter: function (w) {
                                        return {{ $stats['passRate'] ?? 0 }} + '%'
                                    }
                                }
                            }
                        }
                    }
                }
            };
            
            var passRateChart = new ApexCharts(document.querySelector("#passRateChart"), passRateOptions);
            passRateChart.render();
            
            // Xử lý sự kiện xuất dữ liệu
            document.getElementById('exportData').addEventListener('click', function(e) {
                e.preventDefault();
                alert('Chức năng xuất dữ liệu sẽ được triển khai trong phiên bản tiếp theo!');
            });
        @endif
    });
</script>
@endsection 