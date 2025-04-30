@extends('layouts.app')

@section('title', 'Làm bài kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .countdown-timer {
        font-size: 1.2rem;
        font-weight: bold;
    }
    
    .countdown-timer.warning {
        color: #FFA500;
    }
    
    .countdown-timer.danger {
        color: #FF0000;
        animation: blink 1s linear infinite;
    }
    
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .question-nav-btn {
        width: 40px;
        height: 40px;
        margin: 5px;
        font-weight: bold;
    }
    
    .question-nav-btn.current {
        border: 2px solid #3490dc;
    }
    
    .question-nav-btn.answered {
        background-color: #38c172;
        color: white;
    }
    
    .question-nav-btn.marked {
        background-color: #ffed4a;
        color: black;
    }
    
    .fixed-bottom-container {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 10px 0;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        z-index: 100;
    }
</style>
@endsection

@section('content')
<div class="container-fluid mb-5 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            {{ $test->title }}
        </h1>
        <div class="d-flex align-items-center">
            <div class="me-3">
                <span class="text-muted me-2">Thời gian còn lại:</span>
                <span id="countdown" class="countdown-timer" data-end="{{ session('test_end_time') }}"></span>
            </div>
            <div class="progress" style="width: 200px; height: 10px;">
                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="test-form" action="{{ route('seafarer.tests.submit', $attempt->id) }}" method="POST">
                        @csrf
                        
                        @php $questions = $testQuestions; @endphp
                        
                        <div id="questions-container">
                            @foreach($questions as $index => $testQuestion)
                                @php $question = $testQuestion->question; @endphp
                                <div id="question-{{ $index + 1 }}" class="question-section mb-4 {{ $index > 0 ? 'd-none' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title">Câu {{ $index + 1 }}/{{ $questions->count() }}</h5>
                                        <button type="button" class="btn btn-outline-warning btn-sm mark-question" data-index="{{ $index + 1 }}">
                                            <i class="fas fa-bookmark me-1"></i> Đánh dấu để xem lại
                                        </button>
                                    </div>
                                    
                                    <p class="mb-4">{!! $question->content !!}</p>
                                    
                                    @if($question->type == 'Trắc nghiệm')
                                        <div class="mb-3">
                                            @foreach($question->answers as $answer)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input answer-input" type="radio" 
                                                        name="responses[{{ $question->id }}][answer_id]" 
                                                        id="answer-{{ $question->id }}-{{ $answer->id }}" 
                                                        value="{{ $answer->id }}"
                                                        data-question="{{ $index + 1 }}">
                                                    <label class="form-check-label" for="answer-{{ $question->id }}-{{ $answer->id }}">
                                                        {{ $answer->content }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($question->type == 'Tự luận')
                                        <div class="mb-3">
                                            <textarea class="form-control answer-input" 
                                                name="responses[{{ $question->id }}][text_response]" 
                                                rows="5" 
                                                placeholder="Nhập câu trả lời của bạn..."
                                                data-question="{{ $index + 1 }}"></textarea>
                                        </div>
                                    @elseif($question->type == 'Tình huống')
                                        <div class="mb-3">
                                            <textarea class="form-control answer-input" 
                                                name="responses[{{ $question->id }}][text_response]" 
                                                rows="5" 
                                                placeholder="Nhập phân tích tình huống của bạn..."
                                                data-question="{{ $index + 1 }}"></textarea>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-secondary prev-question" 
                                            {{ $index == 0 ? 'disabled' : '' }}
                                            data-index="{{ $index + 1 }}">
                                            <i class="fas fa-arrow-left me-1"></i> Câu trước
                                        </button>
                                        <button type="button" class="btn btn-primary next-question" 
                                            {{ $index == $questions->count() - 1 ? 'disabled' : '' }}
                                            data-index="{{ $index + 1 }}">
                                            Câu tiếp theo <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="fixed-bottom-container">
                            <div class="container">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" id="btn-review">
                                            <i class="fas fa-search me-1"></i> Xem lại bài làm
                                        </button>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-success" id="btn-submit" onclick="return confirm('Bạn có chắc chắn muốn nộp bài?')">
                                            <i class="fas fa-paper-plane me-1"></i> Nộp bài
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách câu hỏi</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-center" id="question-navigator">
                        @foreach($questions as $index => $testQuestion)
                            <button type="button" 
                                class="btn btn-outline-secondary question-nav-btn {{ $index == 0 ? 'current' : '' }}" 
                                data-index="{{ $index + 1 }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                    
                    <hr>
                    
                    <div class="mt-3">
                        <p class="mb-2"><small>Chú thích:</small></p>
                        <div class="d-flex align-items-center mb-2">
                            <div class="btn btn-outline-secondary question-nav-btn me-2" style="width: 30px; height: 30px;"></div>
                            <small>Chưa trả lời</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="btn btn-outline-secondary question-nav-btn current me-2" style="width: 30px; height: 30px;"></div>
                            <small>Câu hỏi hiện tại</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="btn btn-success question-nav-btn me-2" style="width: 30px; height: 30px;"></div>
                            <small>Đã trả lời</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="btn btn-warning question-nav-btn me-2" style="width: 30px; height: 30px;"></div>
                            <small>Đã đánh dấu để xem lại</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mt-3">
                        <p class="mb-2"><strong>Thông tin bài kiểm tra:</strong></p>
                        <ul class="list-unstyled">
                            <li><small><i class="fas fa-clock me-2"></i>Thời gian: {{ $test->duration }} phút</small></li>
                            <li><small><i class="fas fa-question-circle me-2"></i>Số câu hỏi: {{ $questions->count() }}</small></li>
                            <li><small><i class="fas fa-check-circle me-2"></i>Điểm đạt: {{ $test->passing_score }}/100</small></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        let currentQuestion = 1;
        const totalQuestions = {{ $testQuestions->count() }};
        const answeredQuestions = new Set();
        const markedQuestions = new Set();
        
        // Cập nhật tiến độ
        function updateProgress() {
            const progress = Math.round((answeredQuestions.size / totalQuestions) * 100);
            $('#progress-bar').css('width', progress + '%');
            $('#progress-bar').attr('aria-valuenow', progress);
        }
        
        // Chuyển đến câu hỏi được chỉ định
        function goToQuestion(index) {
            $('.question-section').addClass('d-none');
            $('#question-' + index).removeClass('d-none');
            
            // Cập nhật trạng thái nút điều hướng
            $('.question-nav-btn').removeClass('current');
            $('.question-nav-btn[data-index="' + index + '"]').addClass('current');
            
            currentQuestion = index;
        }
        
        // Đánh dấu câu hỏi đã trả lời
        $('.answer-input').on('change', function() {
            const questionIndex = $(this).data('question');
            answeredQuestions.add(questionIndex);
            
            // Cập nhật giao diện
            $('.question-nav-btn[data-index="' + questionIndex + '"]').addClass('answered');
            updateProgress();
            
            // Lưu tự động câu trả lời
            saveProgress();
        });
        
        // Đánh dấu câu hỏi để xem lại
        $('.mark-question').on('click', function() {
            const questionIndex = $(this).data('index');
            
            if (markedQuestions.has(questionIndex)) {
                markedQuestions.delete(questionIndex);
                $('.question-nav-btn[data-index="' + questionIndex + '"]').removeClass('marked');
                $(this).html('<i class="fas fa-bookmark me-1"></i> Đánh dấu để xem lại');
            } else {
                markedQuestions.add(questionIndex);
                $('.question-nav-btn[data-index="' + questionIndex + '"]').addClass('marked');
                $(this).html('<i class="fas fa-bookmark me-1"></i> Bỏ đánh dấu');
            }
        });
        
        // Xử lý nút câu trước
        $('.prev-question').on('click', function() {
            if (currentQuestion > 1) {
                goToQuestion(currentQuestion - 1);
            }
        });
        
        // Xử lý nút câu tiếp theo
        $('.next-question').on('click', function() {
            if (currentQuestion < totalQuestions) {
                goToQuestion(currentQuestion + 1);
            }
        });
        
        // Xử lý nút điều hướng
        $('.question-nav-btn').on('click', function() {
            const index = $(this).data('index');
            goToQuestion(index);
        });
        
        // Xử lý nút xem lại bài làm
        $('#btn-review').on('click', function() {
            $('#modal-review').modal('show');
        });
        
        // Đếm ngược thời gian
        function updateCountdown() {
            const endTime = parseInt($('#countdown').data('end'));
            const now = Math.floor(Date.now() / 1000);
            const remaining = endTime - now;
            
            if (remaining <= 0) {
                // Hết giờ, tự động nộp bài
                $('#test-form').submit();
                return;
            }
            
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            
            // Hiển thị thời gian
            $('#countdown').text(minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
            
            // Đổi màu khi gần hết giờ
            if (remaining < 300) { // 5 phút
                $('#countdown').removeClass('warning').addClass('danger');
            } else if (remaining < 600) { // 10 phút
                $('#countdown').removeClass('danger').addClass('warning');
            }
        }
        
        // Cập nhật đồng hồ đếm ngược mỗi giây
        updateCountdown();
        setInterval(updateCountdown, 1000);
        
        // Lưu câu trả lời định kỳ
        function saveProgress() {
            // Lấy dữ liệu form
            const formData = $('#test-form').serialize();
            
            // Lưu vào localStorage
            localStorage.setItem('testProgress_' + {{ $attempt->id }}, formData);
            
            const answeredCount = answeredQuestions.size;
            const markedCount = markedQuestions.size;
            
            // Lưu danh sách câu đã trả lời và đánh dấu
            localStorage.setItem('testAnswered_' + {{ $attempt->id }}, Array.from(answeredQuestions).join(','));
            localStorage.setItem('testMarked_' + {{ $attempt->id }}, Array.from(markedQuestions).join(','));
        }
        
        // Tự động lưu mỗi 30 giây
        setInterval(saveProgress, 30000);
        
        // Khôi phục tiến trình nếu có
        function restoreProgress() {
            const savedAnswered = localStorage.getItem('testAnswered_' + {{ $attempt->id }});
            const savedMarked = localStorage.getItem('testMarked_' + {{ $attempt->id }});
            const savedProgress = localStorage.getItem('testProgress_' + {{ $attempt->id }});
            
            if (savedProgress) {
                // Phân tích dữ liệu đã lưu
                const formData = new URLSearchParams(savedProgress);
                
                // Khôi phục các câu trả lời
                for (const [name, value] of formData.entries()) {
                    if (name.includes('responses')) {
                        const input = $('[name="' + name + '"]');
                        if (input.attr('type') === 'radio') {
                            $('[name="' + name + '"][value="' + value + '"]').prop('checked', true);
                        } else {
                            input.val(value);
                        }
                    }
                }
                
                // Khôi phục danh sách câu đã trả lời
                if (savedAnswered) {
                    savedAnswered.split(',').forEach(index => {
                        if (index) {
                            answeredQuestions.add(parseInt(index));
                            $('.question-nav-btn[data-index="' + index + '"]').addClass('answered');
                        }
                    });
                }
                
                // Khôi phục danh sách câu đã đánh dấu
                if (savedMarked) {
                    savedMarked.split(',').forEach(index => {
                        if (index) {
                            markedQuestions.add(parseInt(index));
                            $('.question-nav-btn[data-index="' + index + '"]').addClass('marked');
                            $('[data-index="' + index + '"].mark-question').html('<i class="fas fa-bookmark me-1"></i> Bỏ đánh dấu');
                        }
                    });
                }
                
                // Cập nhật tiến độ
                updateProgress();
                console.log('Đã khôi phục bài làm đã lưu');
            }
        }
        
        // Khôi phục tiến trình khi tải trang
        restoreProgress();
    });
</script>
@endsection 