@extends('layouts.app')

@section('title', 'Chấm điểm Bài thi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .grading-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .test-info-card {
        margin-bottom: 1.5rem;
    }
    
    .info-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eaeaea;
        display: flex;
        justify-content: space-between;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #6c757d;
        font-weight: 500;
    }
    
    .info-value {
        font-weight: 600;
    }
    
    .question-card {
        margin-bottom: 2rem;
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
        border-bottom: 1px solid #eaeaea;
    }
    
    .answer-content {
        padding: 1.5rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #eaeaea;
    }
    
    .grading-section {
        padding: 1.5rem;
    }
    
    .sample-answer {
        background-color: #f0f7ff;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #4e73df;
    }
    
    .rubric-card {
        background-color: #fff9f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #f6c23e;
    }
    
    .criteria-item {
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px dashed #e3e6f0;
    }
    
    .criteria-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .points-input {
        width: 80px;
        text-align: center;
        font-weight: bold;
    }
    
    .feedback-textarea {
        width: 100%;
        min-height: 100px;
        margin-top: 0.5rem;
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 0.5rem;
    }
    
    .student-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .student-name {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .student-meta {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .total-score-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .score-summary {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed #e3e6f0;
    }
    
    .score-summary:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .score-label {
        color: #5a5c69;
    }
    
    .score-value {
        color: #4e73df;
    }
    
    .pass-status {
        font-weight: bold;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
        margin-top: 0.5rem;
    }
    
    .pass-status.passed {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .pass-status.failed {
        background-color: #f8d7da;
        color: #842029;
    }
</style>
@endsection

@section('content')
<div class="grading-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Chấm điểm Bài thi</h2>
                <p class="mb-0">{{ $testAttempt->test->title }}</p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.grade.index') }}" class="text-white">Danh sách chờ chấm</a></li>
                <li class="breadcrumb-item active text-white-50">Chấm điểm</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>Vui lòng kiểm tra lại thông tin chấm điểm
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.grade.save', $testAttempt->id) }}" method="POST">
                @csrf
                
                @foreach($essayQuestions as $index => $question)
                    <div class="question-card">
                        <div class="question-header">
                            <div>
                                <strong>Câu {{ $index + 1 }}</strong>
                                <span class="ms-2">
                                    @if($question->type == 'Tự luận')
                                        <span class="badge bg-info">Tự luận</span>
                                    @elseif($question->type == 'Tình huống')
                                        <span class="badge bg-warning">Tình huống</span>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="me-2">Điểm tối đa: {{ $question->pivot->points }}</span>
                            </div>
                        </div>
                        
                        <div class="question-content">
                            <h6 class="fw-bold mb-3">Câu hỏi:</h6>
                            {!! $question->content !!}
                        </div>
                        
                        <div class="answer-content">
                            <h6 class="fw-bold mb-3">Câu trả lời của thí sinh:</h6>
                            @if(isset($answers[$question->id]) && $answers[$question->id])
                                {!! $answers[$question->id] !!}
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Thí sinh không trả lời câu hỏi này.
                                </div>
                            @endif
                        </div>
                        
                        <div class="grading-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Đáp án tham khảo:</h6>
                                    <div class="sample-answer">
                                        @if(isset($sampleAnswers[$question->id]) && $sampleAnswers[$question->id])
                                            {!! $sampleAnswers[$question->id] !!}
                                        @else
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-1"></i> Không có đáp án mẫu cho câu hỏi này.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Tiêu chí chấm điểm:</h6>
                                    <div class="rubric-card">
                                        @if(isset($gradingRubrics[$question->id]) && $gradingRubrics[$question->id])
                                            {!! $gradingRubrics[$question->id] !!}
                                        @else
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-1"></i> Không có tiêu chí chấm điểm cho câu hỏi này.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label for="points{{ $question->id }}" class="form-label me-2 mb-0">Điểm số:</label>
                                        <input type="number" id="points{{ $question->id }}" name="points[{{ $question->id }}]" class="form-control points-input" 
                                            min="0" max="{{ $question->pivot->points }}" step="0.1" 
                                            value="{{ old('points.' . $question->id, isset($gradedPoints[$question->id]) ? $gradedPoints[$question->id] : '') }}" required>
                                        <span class="ms-2">/ {{ $question->pivot->points }}</span>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="autoGrade{{ $question->id }}" data-max-points="{{ $question->pivot->points }}" data-question-id="{{ $question->id }}">
                                        <label class="form-check-label" for="autoGrade{{ $question->id }}">
                                            Gợi ý đánh giá nội dung trả lời
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label for="feedback{{ $question->id }}" class="form-label">Nhận xét đánh giá:</label>
                                <textarea id="feedback{{ $question->id }}" name="feedback[{{ $question->id }}]" class="feedback-textarea">{{ old('feedback.' . $question->id, isset($gradedFeedback[$question->id]) ? $gradedFeedback[$question->id] : '') }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="d-flex justify-content-between mb-4">
                    <a href="{{ route('admin.grade.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                    </a>
                    <div>
                        <button type="submit" name="action" value="save_draft" class="btn btn-info me-2">
                            <i class="fas fa-save me-1"></i> Lưu bản nháp
                        </button>
                        <button type="submit" name="action" value="complete" class="btn btn-primary">
                            <i class="fas fa-check-circle me-1"></i> Hoàn tất chấm điểm
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="student-info mb-4">
                <div class="student-name mb-2">{{ $testAttempt->user->name }}</div>
                <div class="student-meta">
                    <div><i class="fas fa-envelope me-1"></i> {{ $testAttempt->user->email }}</div>
                    <div><i class="fas fa-id-card me-1"></i> ID thuyền viên: {{ $testAttempt->user->thuyenvien ? $testAttempt->user->thuyenvien->seafarer_id : 'N/A' }}</div>
                    <div><i class="fas fa-user-tie me-1"></i> Chức danh: {{ $testAttempt->user->thuyenvien ? $testAttempt->user->thuyenvien->position->name : 'N/A' }}</div>
                </div>
            </div>
            
            <div class="card shadow test-info-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> Thông tin bài thi
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="info-item">
                        <div class="info-label">ID Bài làm</div>
                        <div class="info-value">{{ $testAttempt->id }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Bài kiểm tra</div>
                        <div class="info-value">{{ $testAttempt->test->title }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Thời gian làm bài</div>
                        <div class="info-value">{{ $testAttempt->started_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Thời gian nộp bài</div>
                        <div class="info-value">{{ $testAttempt->completed_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Thời gian làm</div>
                        <div class="info-value">{{ $testAttempt->started_at->diffInMinutes($testAttempt->completed_at) }} phút</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Trạng thái</div>
                        <div class="info-value">
                            @if($testAttempt->status == 'completed')
                                <span class="badge bg-success">Đã hoàn thành</span>
                            @elseif($testAttempt->status == 'grading')
                                <span class="badge bg-warning">Đang chấm điểm</span>
                            @elseif($testAttempt->status == 'graded')
                                <span class="badge bg-info">Đã chấm điểm</span>
                            @else
                                <span class="badge bg-secondary">{{ $testAttempt->status }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator me-1"></i> Tính điểm
                    </h6>
                </div>
                <div class="card-body">
                    <div class="total-score-card">
                        <div class="score-summary">
                            <div class="score-label">Điểm trắc nghiệm:</div>
                            <div class="score-value">{{ $mcqScore }} / {{ $mcqTotalPoints }}</div>
                        </div>
                        <div class="score-summary">
                            <div class="score-label">Điểm tự luận:</div>
                            <div class="score-value" id="essayScoreDisplay">{{ $essayScore }} / {{ $essayTotalPoints }}</div>
                        </div>
                        <div class="score-summary">
                            <div class="score-label">Tổng điểm:</div>
                            <div class="score-value" id="totalScoreDisplay">{{ $mcqScore + $essayScore }} / {{ $testAttempt->test->total_points }}</div>
                        </div>
                    </div>
                    
                    @if($testAttempt->test->passing_score !== null)
                        <div class="text-center">
                            <div>Điểm đạt: {{ $testAttempt->test->passing_score }} / {{ $testAttempt->test->total_points }}</div>
                            <div id="passStatusDisplay" class="pass-status {{ ($mcqScore + $essayScore) >= $testAttempt->test->passing_score ? 'passed' : 'failed' }}">
                                {{ ($mcqScore + $essayScore) >= $testAttempt->test->passing_score ? 'ĐẠT' : 'CHƯA ĐẠT' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-comment-alt me-1"></i> Nhận xét tổng quát
                    </h6>
                </div>
                <div class="card-body">
                    <textarea id="overallFeedback" name="overall_feedback" class="feedback-textarea" rows="5">{{ old('overall_feedback', $testAttempt->feedback) }}</textarea>
                    <div class="d-grid mt-3">
                        <button type="button" id="saveOverallFeedback" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-save me-1"></i> Lưu nhận xét
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cập nhật tổng điểm khi thay đổi điểm số
        const pointInputs = document.querySelectorAll('input[name^="points["]');
        const essayScoreDisplay = document.getElementById('essayScoreDisplay');
        const totalScoreDisplay = document.getElementById('totalScoreDisplay');
        const passStatusDisplay = document.getElementById('passStatusDisplay');
        
        // Điểm ban đầu
        const mcqScore = {{ $mcqScore }};
        const essayTotalPoints = {{ $essayTotalPoints }};
        const passingScore = {{ $testAttempt->test->passing_score ?? 0 }};
        const totalPoints = {{ $testAttempt->test->total_points }};
        
        function updateTotalScore() {
            let essayScore = 0;
            pointInputs.forEach(input => {
                essayScore += parseFloat(input.value || 0);
            });
            
            essayScoreDisplay.textContent = essayScore.toFixed(1) + ' / ' + essayTotalPoints;
            const totalScore = mcqScore + essayScore;
            totalScoreDisplay.textContent = totalScore.toFixed(1) + ' / ' + totalPoints;
            
            // Cập nhật trạng thái đạt/không đạt
            if (passStatusDisplay) {
                if (totalScore >= passingScore) {
                    passStatusDisplay.textContent = 'ĐẠT';
                    passStatusDisplay.classList.remove('failed');
                    passStatusDisplay.classList.add('passed');
                } else {
                    passStatusDisplay.textContent = 'CHƯA ĐẠT';
                    passStatusDisplay.classList.remove('passed');
                    passStatusDisplay.classList.add('failed');
                }
            }
        }
        
        pointInputs.forEach(input => {
            input.addEventListener('input', updateTotalScore);
        });
        
        // Khởi tạo tổng điểm
        updateTotalScore();
        
        // Lưu nhận xét tổng quát
        const saveOverallFeedbackBtn = document.getElementById('saveOverallFeedback');
        const overallFeedbackTextarea = document.getElementById('overallFeedback');
        
        saveOverallFeedbackBtn.addEventListener('click', function() {
            const feedback = overallFeedbackTextarea.value;
            const testAttemptId = {{ $testAttempt->id }};
            
            fetch("{{ route('admin.grade.saveFeedback') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    test_attempt_id: testAttemptId,
                    feedback: feedback
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Nhận xét đã được lưu thành công!');
                } else {
                    alert('Có lỗi xảy ra. Vui lòng thử lại!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại!');
            });
        });
        
        // Gợi ý đánh giá
        const autoGradeCheckboxes = document.querySelectorAll('input[id^="autoGrade"]');
        
        autoGradeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    const questionId = this.dataset.questionId;
                    const maxPoints = parseFloat(this.dataset.maxPoints);
                    const answerContent = document.querySelector(`.answer-content:has(+ .grading-section input[id="points${questionId}"])`).textContent.trim();
                    
                    if (!answerContent || answerContent.includes('Thí sinh không trả lời câu hỏi này.')) {
                        alert('Không có câu trả lời để đánh giá!');
                        this.checked = false;
                        return;
                    }
                    
                    // Hiển thị thông báo đang đánh giá
                    const pointInput = document.getElementById(`points${questionId}`);
                    const feedbackTextarea = document.getElementById(`feedback${questionId}`);
                    
                    pointInput.disabled = true;
                    feedbackTextarea.disabled = true;
                    feedbackTextarea.value = 'Đang phân tích câu trả lời...';
                    
                    // Giả lập đánh giá (trong môi trường thực tế, đây sẽ là một API call đến hệ thống AI)
                    setTimeout(() => {
                        // Mô phỏng điểm ngẫu nhiên và nhận xét
                        const randomScore = Math.min(Math.max((Math.random() * 0.7 + 0.3) * maxPoints, 0), maxPoints).toFixed(1);
                        const feedback = generateFeedback(randomScore, maxPoints);
                        
                        pointInput.value = randomScore;
                        feedbackTextarea.value = feedback;
                        
                        pointInput.disabled = false;
                        feedbackTextarea.disabled = false;
                        
                        updateTotalScore();
                        this.checked = false;
                    }, 1500);
                }
            });
        });
        
        // Tạo nhận xét dựa trên điểm số
        function generateFeedback(score, maxPoints) {
            const percentage = score / maxPoints;
            
            if (percentage >= 0.9) {
                return 'Câu trả lời rất tốt, thể hiện sự hiểu biết toàn diện về chủ đề. Phân tích sâu sắc và đầy đủ các yếu tố được yêu cầu.';
            } else if (percentage >= 0.8) {
                return 'Câu trả lời tốt, thể hiện sự hiểu biết vững về chủ đề. Đã bao gồm hầu hết các điểm chính yếu.';
            } else if (percentage >= 0.7) {
                return 'Câu trả lời khá tốt, thể hiện sự hiểu biết cơ bản về chủ đề. Đã bao gồm các điểm chính nhưng chưa phân tích sâu.';
            } else if (percentage >= 0.6) {
                return 'Câu trả lời đạt yêu cầu, thể hiện một số hiểu biết về chủ đề. Tuy nhiên, còn thiếu một số điểm quan trọng.';
            } else if (percentage >= 0.5) {
                return 'Câu trả lời cơ bản, chỉ thể hiện mức độ hiểu biết hạn chế về chủ đề. Thiếu nhiều điểm quan trọng và phân tích không đủ sâu.';
            } else if (percentage >= 0.3) {
                return 'Câu trả lời còn yếu, chỉ nêu rất ít điểm liên quan đến chủ đề. Thiếu phân tích và không bao gồm nhiều điểm quan trọng.';
            } else {
                return 'Câu trả lời chưa đạt yêu cầu, không thể hiện sự hiểu biết về chủ đề. Cần cải thiện nhiều về nội dung và cách trình bày.';
            }
        }
    });
</script>
@endsection 