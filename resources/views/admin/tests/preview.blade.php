@extends('layouts.app')

@section('title', 'Xem trước Bài Kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .test-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .test-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary-color);
    }
    
    .question-card {
        margin-bottom: 1.5rem;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .question-header {
        background-color: #f8f9fa;
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        font-weight: 600;
    }
    
    .question-content {
        padding: 1.5rem;
    }
    
    .answer-option {
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        border: 1px solid #e3e6f0;
        border-radius: 6px;
        background-color: white;
    }
    
    .answer-option.is-correct {
        border-color: #198754;
        background-color: #d1e7dd;
    }
    
    .timer-panel {
        position: sticky;
        top: 1rem;
        padding: 1rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .timer-display {
        font-size: 2rem;
        font-weight: 700;
        text-align: center;
    }
    
    .test-progress {
        margin-bottom: 1rem;
    }
    
    .test-info-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .test-info-label {
        color: #6c757d;
    }
    
    .test-info-value {
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="test-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Xem trước: {{ $test->title }}</h2>
                <p class="mb-0 text-white-50">
                    Đây là chế độ xem trước bài kiểm tra dành cho quản trị viên.
                </p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tests.index') }}" class="text-white">Bài Kiểm tra</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tests.show', $test->id) }}" class="text-white">Chi tiết</a></li>
                <li class="breadcrumb-item active text-white-50">Xem trước</li>
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
                        <i class="fas fa-info-circle me-1"></i> Hướng dẫn làm bài
                    </h6>
                </div>
                <div class="card-body">
                    <div class="test-info">
                        <p>Bài kiểm tra gồm <strong>{{ $testQuestions->count() }}</strong> câu hỏi, thời gian làm bài là <strong>{{ $test->duration }} phút</strong>.</p>
                        <p>Điểm đạt yêu cầu: <strong>{{ $test->passing_score }}%</strong>.</p>
                        <p>Vui lòng đọc kỹ câu hỏi và các phương án trả lời trước khi chọn câu trả lời.</p>
                    </div>
                </div>
            </div>
            
            @foreach($testQuestions as $index => $testQuestion)
                @php
                    $question = $testQuestion->question;
                @endphp
                <div class="question-card">
                    <div class="question-header">
                        <div class="d-flex justify-content-between">
                            <div>Câu {{ $index + 1 }}: <span class="badge bg-info ms-1">{{ $question->difficulty }}</span></div>
                            <div>Điểm: {{ $testQuestion->points ?? 1 }}</div>
                        </div>
                    </div>
                    <div class="question-content">
                        <div class="mb-4">
                            {!! $question->content !!}
                        </div>
                        
                        @if($question->type == 'Trắc nghiệm' && $question->answers->count() > 0)
                            <div class="mb-3">
                                @foreach($question->answers as $answerIndex => $answer)
                                    <div class="answer-option {{ $answer->is_correct ? 'is-correct' : '' }}">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="question_{{ $question->id }}" id="answer_{{ $answer->id }}" disabled>
                                            <label class="form-check-label" for="answer_{{ $answer->id }}">
                                                {{ chr(65 + $answerIndex) }}. {{ $answer->content }}
                                            </label>
                                            @if($answer->is_correct)
                                                <div class="text-success small mt-1">
                                                    <i class="fas fa-check-circle me-1"></i> Đáp án đúng
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->type == 'Tự luận')
                            <div class="mb-3">
                                <textarea class="form-control" rows="4" placeholder="Nhập câu trả lời của bạn..." disabled></textarea>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.tests.show', $test->id) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại chi tiết bài kiểm tra
                </a>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="timer-panel mb-4">
                <div class="timer-display mb-2">{{ $test->duration }}:00</div>
                <div class="text-center text-muted mb-3">Thời gian còn lại</div>
                
                <div class="test-progress">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Tiến độ làm bài</span>
                        <span>0/{{ $testQuestions->count() }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <hr>
                
                <div class="test-info-list">
                    <div class="test-info-item">
                        <div class="test-info-label">Tổng số câu hỏi:</div>
                        <div class="test-info-value">{{ $testQuestions->count() }}</div>
                    </div>
                    <div class="test-info-item">
                        <div class="test-info-label">Độ khó:</div>
                        <div class="test-info-value">{{ $test->difficulty }}</div>
                    </div>
                    <div class="test-info-item">
                        <div class="test-info-label">Điểm đạt:</div>
                        <div class="test-info-value">{{ $test->passing_score }}%</div>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <button class="btn btn-primary w-100" disabled>
                        <i class="fas fa-paper-plane me-1"></i> Nộp bài
                    </button>
                </div>
                
                <div class="alert alert-info small mb-0">
                    <i class="fas fa-info-circle me-1"></i> Đây là chế độ xem trước. Bạn không thể thực sự làm bài kiểm tra này.
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-question-circle me-1"></i> Các câu hỏi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($testQuestions as $index => $testQuestion)
                            <div class="col-3 mb-2">
                                <button class="btn btn-outline-secondary w-100">{{ $index + 1 }}</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 