@extends('layouts.app')

@section('title', 'Chi tiết Bài Kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .test-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .test-meta-card {
        margin-bottom: 1.5rem;
    }
    
    .meta-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eaeaea;
        display: flex;
        justify-content: space-between;
    }
    
    .meta-item:last-child {
        border-bottom: none;
    }
    
    .meta-label {
        color: #6c757d;
        font-weight: 500;
    }
    
    .meta-value {
        font-weight: 600;
    }
    
    .test-description {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary-color);
    }
    
    .question-card {
        margin-bottom: 1.5rem;
        border: 1px solid #eaeaea;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .question-header {
        background-color: #f8f9fa;
        padding: 1rem;
        border-bottom: 1px solid #eaeaea;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .question-content {
        padding: 1.5rem;
    }
    
    .question-footer {
        padding: 1rem;
        background-color: #f8f9fa;
        border-top: 1px solid #eaeaea;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .answer-option {
        margin-bottom: 1rem;
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background-color: #fff;
    }
    
    .answer-option.is-correct {
        border-color: #198754;
        background-color: #d1e7dd;
    }
    
    .test-type-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        border-radius: 20px;
    }
    
    .difficulty-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        border-radius: 20px;
    }
    
    .difficulty-easy {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .difficulty-medium {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .difficulty-hard {
        background-color: #f8d7da;
        color: #842029;
    }
    
    .test-stats-card {
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .stat-item {
        padding: 1rem;
        text-align: center;
        border-bottom: 1px solid #eaeaea;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .passing-score {
        background-color: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 6px;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .passing-label {
        font-weight: 600;
    }
    
    .passing-value {
        font-weight: 700;
        color: var(--primary-color);
    }
</style>
@endsection

@section('content')
<div class="test-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>{{ $test->title }}</h2>
                <div class="d-flex gap-2 mt-2">
                    @if($test->type == 'certification')
                        <span class="badge bg-primary test-type-badge">Chứng chỉ</span>
                    @elseif($test->type == 'assessment')
                        <span class="badge bg-info test-type-badge">Đánh giá định kỳ</span>
                    @elseif($test->type == 'placement')
                        <span class="badge bg-secondary test-type-badge">Xếp loại</span>
                    @elseif($test->type == 'practice')
                        <span class="badge bg-success test-type-badge">Luyện tập</span>
                    @else
                        <span class="badge bg-dark test-type-badge">{{ $test->type }}</span>
                    @endif
                    
                    @if($test->difficulty == 'Dễ')
                        <span class="badge difficulty-badge difficulty-easy">Dễ</span>
                    @elseif($test->difficulty == 'Trung bình')
                        <span class="badge difficulty-badge difficulty-medium">Trung bình</span>
                    @elseif($test->difficulty == 'Khó')
                        <span class="badge difficulty-badge difficulty-hard">Khó</span>
                    @else
                        <span class="badge bg-secondary difficulty-badge">{{ $test->difficulty }}</span>
                    @endif
                    
                    <span class="badge bg-{{ $test->is_active ? 'success' : 'danger' }}">{{ $test->is_active ? 'Hoạt động' : 'Tạm dừng' }}</span>
                </div>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tests.index') }}" class="text-white">Bài Kiểm tra</a></li>
                <li class="breadcrumb-item active text-white-50">Chi tiết</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-lg-8">
            @if($test->description)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-1"></i> Mô tả bài kiểm tra
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="test-description">
                            {!! $test->description !!}
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-1"></i> Danh sách câu hỏi ({{ $test->questions->count() }} câu)
                    </h6>
                    <a href="{{ route('admin.tests.edit', $test->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa câu hỏi
                    </a>
                </div>
                <div class="card-body">
                    @if($test->questions->count() > 0)
                        @foreach($test->questions as $index => $question)
                            <div class="question-card">
                                <div class="question-header">
                                    <div>
                                        <strong>Câu {{ $index + 1 }}</strong>
                                        <span class="ms-2">
                                            @if($question->type == 'Trắc nghiệm')
                                                <span class="badge bg-primary">Trắc nghiệm</span>
                                            @elseif($question->type == 'Tự luận')
                                                <span class="badge bg-info">Tự luận</span>
                                            @elseif($question->type == 'Tình huống')
                                                <span class="badge bg-warning">Tình huống</span>
                                            @elseif($question->type == 'Thực hành')
                                                <span class="badge bg-success">Thực hành</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $question->type }}</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="me-2">Điểm: {{ $question->pivot->points }}</span>
                                        <a href="{{ route('admin.questions.show', $question->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="question-content">
                                    <div class="mb-3">{!! $question->content !!}</div>
                                    
                                    @if($question->type == 'Trắc nghiệm' && $question->answers->count() > 0)
                                        <div>
                                            @foreach($question->answers as $answerIndex => $answer)
                                                <div class="answer-option {{ $answer->is_correct ? 'is-correct' : '' }}">
                                                    <div class="d-flex align-items-start">
                                                        <div class="me-2">{{ chr(65 + $answerIndex) }}.</div>
                                                        <div class="flex-grow-1">
                                                            {{ $answer->content }}
                                                            @if($answer->is_correct)
                                                                <div class="mt-2 small">
                                                                    <i class="fas fa-check-circle text-success me-1"></i>
                                                                    <strong>Đáp án đúng</strong>
                                                                    @if($answer->explanation)
                                                                        <div class="mt-1">{{ $answer->explanation }}</div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($question->type == 'Tự luận')
                                        <div class="alert alert-light">
                                            <i class="fas fa-edit me-1"></i> Câu hỏi tự luận - Xem chi tiết đáp án mẫu trong trang chi tiết câu hỏi.
                                        </div>
                                    @elseif($question->type == 'Tình huống')
                                        <div class="alert alert-light">
                                            <i class="fas fa-people-arrows me-1"></i> Câu hỏi tình huống - Xem chi tiết tiêu chí đánh giá trong trang chi tiết câu hỏi.
                                        </div>
                                    @elseif($question->type == 'Thực hành')
                                        <div class="alert alert-light">
                                            <i class="fas fa-tools me-1"></i> Câu hỏi thực hành - Xem chi tiết hướng dẫn và tiêu chí trong trang chi tiết câu hỏi.
                                        </div>
                                    @endif
                                </div>
                                <div class="question-footer">
                                    <div>
                                        @if($question->difficulty == 'Dễ')
                                            <span class="badge difficulty-badge difficulty-easy">Dễ</span>
                                        @elseif($question->difficulty == 'Trung bình')
                                            <span class="badge difficulty-badge difficulty-medium">Trung bình</span>
                                        @elseif($question->difficulty == 'Khó')
                                            <span class="badge difficulty-badge difficulty-hard">Khó</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($question->category)
                                            <span class="small text-muted">Danh mục: {{ is_object($question->category) ? $question->category->name : $question->category }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> Bài kiểm tra này chưa có câu hỏi nào. Vui lòng thêm câu hỏi để hoàn thiện bài kiểm tra.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-1"></i> Thống kê kết quả
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($stats) && isset($stats['totalAttempts']) && $stats['totalAttempts'] > 0)
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="stat-item bg-light">
                                    <div class="stat-value">{{ $stats['totalAttempts'] }}</div>
                                    <div class="stat-label">Lượt thi</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item bg-light">
                                    <div class="stat-value">{{ number_format($stats['avgScore'], 1) }}</div>
                                    <div class="stat-label">Điểm trung bình</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item bg-light">
                                    <div class="stat-value">{{ number_format($stats['passRate'], 1) }}%</div>
                                    <div class="stat-label">Tỷ lệ đạt</div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="scoreChart" style="height: 300px;"></div>
                        
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Thông tin chi tiết</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-1"></i> Số lượt đạt: {{ $stats['passCount'] }} ({{ number_format($stats['passRate'], 1) }}%)
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-times-circle text-danger me-1"></i> Số lượt không đạt: {{ $stats['failCount'] }} ({{ number_format(100 - $stats['passRate'], 1) }}%)
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-star text-warning me-1"></i> Điểm cao nhất: {{ number_format($stats['highestScore'], 1) }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-star-half-alt text-warning me-1"></i> Điểm thấp nhất: {{ number_format($stats['lowestScore'], 1) }}
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> Chưa có dữ liệu thống kê cho bài kiểm tra này. Thống kê sẽ được hiển thị khi có thuyền viên hoàn thành bài kiểm tra.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog me-1"></i> Thao tác
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.tests.edit', $test->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Chỉnh sửa bài kiểm tra
                        </a>
                        <a href="{{ route('admin.tests.preview', $test->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i> Xem trước bài kiểm tra
                        </a>
                        <a href="{{ route('admin.tests.statistics', $test->id) }}" class="btn btn-info">
                            <i class="fas fa-chart-bar me-1"></i> Thống kê chi tiết
                        </a>
                        <form action="{{ route('admin.tests.toggle', $test->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            @if($test->is_active)
                                <button type="submit" class="btn btn-secondary" onclick="return confirm('Bạn có chắc chắn muốn tạm dừng bài kiểm tra này?')">
                                    <i class="fas fa-pause me-1"></i> Tạm dừng bài kiểm tra
                                </button>
                            @else
                                <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc chắn muốn kích hoạt bài kiểm tra này?')">
                                    <i class="fas fa-play me-1"></i> Kích hoạt bài kiểm tra
                                </button>
                            @endif
                        </form>
                        <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bài kiểm tra này? Thao tác này không thể hoàn tác.')">
                                <i class="fas fa-trash me-1"></i> Xóa bài kiểm tra
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card shadow test-meta-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> Thông tin bài kiểm tra
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="meta-item">
                        <div class="meta-label">Mã bài kiểm tra</div>
                        <div class="meta-value">{{ $test->code }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Thời gian làm bài</div>
                        <div class="meta-value">{{ $test->duration }} phút</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Số lượng câu hỏi</div>
                        <div class="meta-value">{{ $test->questions->count() }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Tổng điểm</div>
                        <div class="meta-value">{{ $test->total_points }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Áp dụng cho</div>
                        <div class="meta-value">
                            {{ $test->position ? $test->position->name : 'Tất cả chức danh' }}
                            @if($test->shipType)
                                <span class="text-muted">/ {{ $test->shipType->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Ngày tạo</div>
                        <div class="meta-value">{{ $test->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Cập nhật lần cuối</div>
                        <div class="meta-value">{{ $test->updated_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Người tạo</div>
                        <div class="meta-value">{{ $test->created_by ? $test->createdBy->name : 'Không rõ' }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="passing-score">
                        <div class="passing-label">Điểm đạt tối thiểu:</div>
                        <div class="passing-value">{{ $test->passing_score }} / {{ $test->total_points }}</div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sliders-h me-1"></i> Cài đặt bài kiểm tra
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="meta-item">
                        <div class="meta-label">Xáo trộn câu hỏi</div>
                        <div class="meta-value">
                            @if($test->shuffle_questions)
                                <span class="text-success"><i class="fas fa-check-circle me-1"></i>Có</span>
                            @else
                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Không</span>
                            @endif
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Xáo trộn đáp án</div>
                        <div class="meta-value">
                            @if($test->shuffle_answers)
                                <span class="text-success"><i class="fas fa-check-circle me-1"></i>Có</span>
                            @else
                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Không</span>
                            @endif
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Cho phép quay lại câu trước</div>
                        <div class="meta-value">
                            @if($test->allow_back)
                                <span class="text-success"><i class="fas fa-check-circle me-1"></i>Có</span>
                            @else
                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Không</span>
                            @endif
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Hiển thị kết quả ngay sau khi làm</div>
                        <div class="meta-value">
                            @if($test->show_result)
                                <span class="text-success"><i class="fas fa-check-circle me-1"></i>Có</span>
                            @else
                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Không</span>
                            @endif
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Tối đa số lần làm</div>
                        <div class="meta-value">{{ $test->max_attempts ? $test->max_attempts : 'Không giới hạn' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    @if(isset($stats) && isset($stats['totalAttempts']) && $stats['totalAttempts'] > 0)
    // Khởi tạo biểu đồ điểm số
    var scoreOptions = {
        series: [{
            name: 'Số lượt thi',
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
            },
        },
        dataLabels: {
            enabled: false
        },
        colors: ['#4e73df'],
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

    var scoreChart = new ApexCharts(document.querySelector("#scoreChart"), scoreOptions);
    scoreChart.render();
    @endif
</script>
@endsection 