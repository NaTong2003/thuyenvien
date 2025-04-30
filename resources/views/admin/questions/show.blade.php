@extends('layouts.app')

@section('title', 'Chi tiết Câu hỏi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .question-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .question-content {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary-color);
    }
    
    .question-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .meta-item i {
        margin-right: 0.5rem;
    }
    
    .answer-option {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #6c757d;
    }
    
    .answer-option.is-correct {
        background-color: #d1e7dd;
        border-left: 4px solid #198754;
    }
    
    .answer-option h5 {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    
    .answer-option .badge {
        margin-left: 0.75rem;
    }
    
    .explanation {
        font-style: italic;
        margin-top: 0.75rem;
        padding: 0.75rem;
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 4px;
    }
    
    .related-tests {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
    }
    
    .test-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .test-item:last-child {
        border-bottom: none;
    }
    
    .difficulty-badge {
        font-size: 0.8rem;
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
    
    .question-type-badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
        border-radius: 20px;
    }
</style>
@endsection

@section('content')
<div class="question-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Chi tiết câu hỏi #{{ $question->id }}</h2>
                <div class="d-flex gap-2 mt-2">
                    @if($question->type == 'Trắc nghiệm')
                        <span class="badge bg-primary question-type-badge">Trắc nghiệm</span>
                    @elseif($question->type == 'Tự luận')
                        <span class="badge bg-info question-type-badge">Tự luận</span>
                    @elseif($question->type == 'Tình huống')
                        <span class="badge bg-warning question-type-badge">Tình huống</span>
                    @elseif($question->type == 'Thực hành')
                        <span class="badge bg-success question-type-badge">Thực hành</span>
                    @else
                        <span class="badge bg-secondary question-type-badge">{{ $question->type }}</span>
                    @endif
                    
                    @if($question->difficulty == 'Dễ')
                        <span class="badge difficulty-badge difficulty-easy">Dễ</span>
                    @elseif($question->difficulty == 'Trung bình')
                        <span class="badge difficulty-badge difficulty-medium">Trung bình</span>
                    @elseif($question->difficulty == 'Khó')
                        <span class="badge difficulty-badge difficulty-hard">Khó</span>
                    @endif
                </div>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}" class="text-white">Ngân hàng Câu hỏi</a></li>
                <li class="breadcrumb-item active text-white-50">Chi tiết</li>
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
                        <i class="fas fa-question-circle me-1"></i> Nội dung câu hỏi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="question-content">
                        {!! $question->content !!}
                    </div>
                    
                    <div class="question-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i> Tạo: {{ $question->created_at->format('d/m/Y') }}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-plus"></i> Cập nhật: {{ $question->updated_at->format('d/m/Y') }}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-user"></i> Người tạo: {{ $question->createdBy ? $question->createdBy->name : 'Không rõ' }}
                        </div>
                    </div>
                    
                    <div class="question-meta">
                        <div class="meta-item">
                            <i class="fas fa-user-tie"></i> Chức danh: {{ $question->position ? $question->position->name : 'Tất cả' }}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-ship"></i> Loại tàu: {{ $question->shipType ? $question->shipType->name : 'Tất cả' }}
                        </div>
                    </div>
                </div>
            </div>
            
            @if($question->type == 'Trắc nghiệm')
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list-ul me-1"></i> Các phương án trả lời
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($question->answers->count() > 0)
                            @foreach($question->answers as $index => $answer)
                                <div class="answer-option {{ $answer->is_correct ? 'is-correct' : '' }}">
                                    <h5>
                                        <span>Phương án {{ chr(65 + $index) }}: {{ $answer->content }}</span>
                                        @if($answer->is_correct)
                                            <span class="badge bg-success">Đáp án đúng</span>
                                        @endif
                                    </h5>
                                    
                                    @if($answer->explanation)
                                        <div class="explanation">
                                            <strong>Giải thích:</strong> {{ $answer->explanation }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-1"></i> Câu hỏi này chưa có phương án trả lời nào.
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($question->type == 'Tự luận' || $question->type == 'Tình huống')
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-edit me-1"></i> Đáp án tham khảo
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($essayAnswer) && $essayAnswer)
                            <div class="mb-4">
                                <h6 class="font-weight-bold">Đáp án mẫu:</h6>
                                <div class="p-3 bg-light rounded">
                                    {!! $essayAnswer !!}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i> Câu hỏi này chưa có đáp án mẫu.
                            </div>
                        @endif
                        
                        @if(isset($gradingRubric) && $gradingRubric)
                            <div>
                                <h6 class="font-weight-bold">Tiêu chí chấm điểm:</h6>
                                <div class="p-3 bg-light rounded">
                                    {!! $gradingRubric !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($question->type == 'Thực hành')
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-tasks me-1"></i> Hướng dẫn thực hành
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($practicalInstructions) && $practicalInstructions)
                            <div class="mb-4">
                                <h6 class="font-weight-bold">Hướng dẫn chi tiết:</h6>
                                <div class="p-3 bg-light rounded">
                                    {!! $practicalInstructions !!}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i> Câu hỏi này chưa có hướng dẫn thực hành chi tiết.
                            </div>
                        @endif
                        
                        @if(isset($evaluationCriteria) && $evaluationCriteria)
                            <div>
                                <h6 class="font-weight-bold">Tiêu chí đánh giá:</h6>
                                <div class="p-3 bg-light rounded">
                                    {!! $evaluationCriteria !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
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
                        <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit me-1"></i> Chỉnh sửa câu hỏi
                        </a>
                        <button type="button" class="btn btn-primary btn-block" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                            <i class="fas fa-copy me-1"></i> Sao chép câu hỏi
                        </button>
                        <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')">
                                <i class="fas fa-trash me-1"></i> Xóa câu hỏi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-list me-1"></i> Bài kiểm tra có câu hỏi này
                    </h6>
                </div>
                <div class="card-body">
                    @if($question->tests->count() > 0)
                        <div class="related-tests">
                            @foreach($question->tests as $test)
                                <div class="test-item">
                                    <a href="{{ route('admin.tests.show', $test->id) }}">{{ $test->title }}</a>
                                    <div class="small text-muted">
                                        Điểm: {{ $test->pivot->points ?? 'Chưa phân điểm' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> Câu hỏi này chưa được sử dụng trong bài kiểm tra nào.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-1"></i> Thống kê câu hỏi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h4 font-weight-bold">{{ $stats['totalAttempts'] ?? 0 }}</div>
                            <div class="small text-muted">Lượt trả lời</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 font-weight-bold">{{ isset($stats['correctPercentage']) ? number_format($stats['correctPercentage'], 1) : 0 }}%</div>
                            <div class="small text-muted">Tỷ lệ đúng</div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light mb-0 small">
                                <i class="fas fa-info-circle me-1"></i> Thống kê dựa trên dữ liệu từ các bài kiểm tra.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sao chép câu hỏi -->
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="duplicateModalLabel">Sao chép câu hỏi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.questions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="duplicate_from" value="{{ $question->id }}">
                    
                    <div class="mb-3">
                        <label for="new_content" class="form-label">Nội dung câu hỏi mới</label>
                        <textarea class="form-control" id="new_content" name="content" rows="3">{{ $question->content }}</textarea>
                        <div class="form-text">Bạn có thể chỉnh sửa nội dung câu hỏi mới tại đây.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="new_difficulty" class="form-label">Độ khó</label>
                                <select class="form-select" id="new_difficulty" name="difficulty">
                                    <option value="Dễ" {{ $question->difficulty == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                                    <option value="Trung bình" {{ $question->difficulty == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="Khó" {{ $question->difficulty == 'Khó' ? 'selected' : '' }}>Khó</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="new_type" class="form-label">Loại câu hỏi</label>
                                <input type="text" class="form-control" id="new_type" value="{{ $question->type }}" readonly>
                                <input type="hidden" name="type" value="{{ $question->type }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo bản sao</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 