@extends('layouts.app')

@section('title', 'Báo cáo Bài kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .test-header {
        background-color: var(--primary-color);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    
    .test-header h2 {
        margin-bottom: 0.5rem;
    }
    
    .test-header .info-label {
        opacity: 0.8;
        margin-right: 0.5rem;
    }
    
    .test-stats {
        display: flex;
        gap: 2rem;
        margin-top: 1.5rem;
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
    
    .question-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .question-item {
        padding: 1rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    
    .question-item:hover {
        background-color: #e9ecef;
    }
    
    .question-category {
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        background-color: #e2e3e5;
        color: #495057;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    
    .difficulty-easy {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .difficulty-medium {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .difficulty-hard {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    .attempt-item {
        padding: 1rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        margin-bottom: 1rem;
        transition: all 0.3s;
        border-left: 4px solid #ddd;
    }
    
    .attempt-item:hover {
        background-color: #e9ecef;
    }
    
    .attempt-passed {
        border-left-color: #10b981;
    }
    
    .attempt-failed {
        border-left-color: #ef4444;
    }
    
    .performance-card {
        border-radius: 8px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .score-badge {
        font-weight: bold;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
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
<div class="test-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2>{{ $test->title }}</h2>
                <div class="mb-1">
                    <span class="info-label">Vị trí:</span>
                    <span>{{ $test->position->name ?? 'Không có' }}</span>
                </div>
                <div class="mb-1">
                    <span class="info-label">Loại tàu:</span>
                    <span>{{ $test->shipType->name ?? 'Không có' }}</span>
                </div>
                <div class="mb-1">
                    <span class="info-label">Thời gian:</span>
                    <span>{{ $test->duration }} phút</span>
                </div>
                <div class="test-stats">
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
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-white">Báo cáo</a></li>
                <li class="breadcrumb-item active text-white-50">Bài kiểm tra</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-1"></i> Phân bố điểm số
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="scoreDistributionChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list-alt me-1"></i> Danh sách câu hỏi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="question-list">
                        @forelse($testQuestions as $index => $question)
                            <div class="question-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span class="question-category 
                                            @if($question->difficulty == 'Dễ') difficulty-easy 
                                            @elseif($question->difficulty == 'Trung bình') difficulty-medium 
                                            @else difficulty-hard 
                                            @endif">
                                            {{ $question->difficulty ?? 'Không xác định' }}
                                        </span>
                                        <span class="question-category">
                                            @if(is_object($question->category))
                                                {{ $question->category->name ?? 'Không phân loại' }}
                                            @elseif(is_string($question->category))
                                                {{ $question->category }}
                                            @else
                                                {{ $question->category_id ? App\Models\Category::find($question->category_id)->name : 'Không phân loại' }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        Câu {{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <h6>{{ $question->content }}</h6>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted my-4">Chưa có câu hỏi nào trong bài kiểm tra này</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-1"></i> Tỷ lệ đạt/không đạt
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="passRateChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="performance-card">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user-check me-1"></i> Các lượt thi gần đây
                        </h6>
                    </div>
                    <div class="card-body">
                        @forelse($testAttempts->take(10) as $attempt)
                            <div class="attempt-item @if($attempt->isPassed()) attempt-passed @else attempt-failed @endif">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $attempt->user->name }}</h6>
                                        <div class="text-muted small">
                                            {{ $attempt->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="score-badge 
                                            @if($attempt->score >= 80) score-high 
                                            @elseif($attempt->score >= 60) score-medium 
                                            @else score-low 
                                            @endif">
                                            {{ $attempt->score }}/100
                                        </div>
                                        <div class="text-muted small">
                                            {{ $attempt->getDurationInMinutes() ?? '-' }} phút
                                        </div>
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
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Biểu đồ phân bố điểm số
    const scoreLabels = ['0-10', '11-20', '21-30', '31-40', '41-50', '51-60', '61-70', '71-80', '81-90', '91-100'];
    const scoreData = [
        {{ $scoreDistribution['0-10'] }},
        {{ $scoreDistribution['11-20'] }},
        {{ $scoreDistribution['21-30'] }},
        {{ $scoreDistribution['31-40'] }},
        {{ $scoreDistribution['41-50'] }},
        {{ $scoreDistribution['51-60'] }},
        {{ $scoreDistribution['61-70'] }},
        {{ $scoreDistribution['71-80'] }},
        {{ $scoreDistribution['81-90'] }},
        {{ $scoreDistribution['91-100'] }}
    ];
    
    new Chart(document.getElementById('scoreDistributionChart'), {
        type: 'bar',
        data: {
            labels: scoreLabels,
            datasets: [{
                label: 'Số lượt thi',
                data: scoreData,
                backgroundColor: [
                    '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16',
                    '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
    
    // Biểu đồ tỷ lệ đạt/không đạt
    new Chart(document.getElementById('passRateChart'), {
        type: 'doughnut',
        data: {
            labels: ['Đạt', 'Không đạt'],
            datasets: [{
                data: [{{ $passRate }}, {{ 100 - $passRate }}],
                backgroundColor: ['#10b981', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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