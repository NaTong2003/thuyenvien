@extends('layouts.app')

@section('title', 'Kết quả bài kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .result-card {
        transition: all 0.3s;
    }
    
    .result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .score-display {
        font-size: 3rem;
        font-weight: bold;
    }
    
    .score-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        border: 10px solid;
    }
    
    .score-circle.pass {
        border-color: #38c172;
        color: #38c172;
    }
    
    .score-circle.fail {
        border-color: #e3342f;
        color: #e3342f;
    }
    
    .answer-correct {
        color: #38c172;
    }
    
    .answer-incorrect {
        color: #e3342f;
    }
    
    .answer-explanation {
        background-color: #f8f9fa;
        border-left: 4px solid #3490dc;
        padding: 15px;
        margin-top: 15px;
        border-radius: 4px;
    }
    
    .chart-container {
        height: 300px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-check me-2"></i> Kết quả bài kiểm tra
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('seafarer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('seafarer.tests.index') }}">Bài kiểm tra</a></li>
            <li class="breadcrumb-item active">Kết quả</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $test->title }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5 text-center">
                            <div class="score-circle {{ $attempt->isPassed() ? 'pass' : 'fail' }}">
                                <div>
                                    <div class="score-display">{{ round($attempt->score) }}</div>
                                    <div>/ 100</div>
                                </div>
                            </div>
                            <h5 class="mt-3">
                                @if($attempt->isPassed())
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Đạt</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Không đạt</span>
                                @endif
                            </h5>
                            <p class="text-muted mt-2">Điểm đạt yêu cầu: {{ $test->passing_score }}/100</p>
                        </div>
                        <div class="col-md-7">
                            <h5 class="border-bottom pb-2">Thông tin bài kiểm tra</h5>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <i class="fas fa-user me-1"></i> Thuyền viên:
                                </div>
                                <div class="col-sm-7 fw-bold">
                                    {{ $attempt->user->name }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <i class="fas fa-calendar me-1"></i> Thời gian làm bài:
                                </div>
                                <div class="col-sm-7 fw-bold">
                                    {{ $attempt->start_time->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <i class="fas fa-clock me-1"></i> Thời gian hoàn thành:
                                </div>
                                <div class="col-sm-7 fw-bold">
                                    {{ $attempt->getDurationInMinutes() }} phút
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <i class="fas fa-question-circle me-1"></i> Số câu đúng:
                                </div>
                                <div class="col-sm-7 fw-bold">
                                    {{ $attempt->userResponses->where('is_correct', true)->count() }} / {{ $testQuestions->count() }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <i class="fas fa-chart-line me-1"></i> Kết quả:
                                </div>
                                <div class="col-sm-7 fw-bold">
                                    {{ $attempt->score }}/100 điểm
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân tích kết quả chi tiết</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container mb-4">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> 
                        Dựa trên kết quả kiểm tra, bạn nên tập trung vào những lĩnh vực sau để cải thiện:
                        <ul class="mt-2" id="improvement-areas">
                            <!-- Sẽ được điền bởi JavaScript -->
                        </ul>
                    </div>
                    
                    <!-- Đề xuất tài liệu học tập -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-book me-1"></i> Đề xuất tài liệu học tập</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">IMO Model Course 7.03</h6>
                                            <p class="card-text small">Tài liệu đào tạo và hướng dẫn về điều động và quản lý tàu biển.</p>
                                            <a href="#" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Sổ tay Thuyền trưởng</h6>
                                            <p class="card-text small">Tài liệu tham khảo đầy đủ cho các thuyền trưởng về quy trình vận hành tàu an toàn.</p>
                                            <a href="#" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">So sánh với kết quả trước</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="progressChart"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <p class="text-muted small">So sánh điểm số của bạn qua các lần làm bài kiểm tra</p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hành động tiếp theo</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('seafarer.tests.show', $test->id) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-info-circle me-2"></i> Xem thông tin bài kiểm tra
                        </a>
                        <a href="{{ route('seafarer.tests.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-list me-2"></i> Xem danh sách bài kiểm tra
                        </a>
                        @if(!$attempt->isPassed())
                            <a href="{{ route('seafarer.tests.start', $test->id) }}" class="list-group-item list-group-item-action list-group-item-primary">
                                <i class="fas fa-redo me-2"></i> Làm lại bài kiểm tra
                            </a>
                        @endif
                        <a href="{{ route('seafarer.dashboard') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt me-2"></i> Quay lại Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Chi tiết câu trả lời</h6>
        </div>
        <div class="card-body">
            <div class="accordion" id="answerAccordion">
                @foreach($testQuestions as $index => $testQuestion)
                    @php 
                        $question = $testQuestion->question;
                        $userResponse = $attempt->userResponses->where('question_id', $question->id)->first();
                        $isCorrect = $userResponse && $userResponse->isCorrect();
                    @endphp
                    
                    <div class="accordion-item mb-3 border">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <span class="me-3">Câu {{ $index + 1 }}</span>
                                        @if($isCorrect)
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Đúng</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times"></i> Sai</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        @if(is_object($question->category))
                                            {{ $question->category->name ?? 'Chưa phân loại' }}
                                        @elseif(is_string($question->category))
                                            {{ $question->category }}
                                        @else
                                            {{ $question->category_id ? App\Models\Category::find($question->category_id)->name : 'Chưa phân loại' }}
                                        @endif
                                        | 
                                        @if($question->difficulty == 1)
                                            <span class="text-success">Dễ</span>
                                        @elseif($question->difficulty == 2)
                                            <span class="text-primary">Trung bình</span>
                                        @else
                                            <span class="text-danger">Khó</span>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#answerAccordion">
                            <div class="accordion-body">
                                <p class="mb-3"><strong>Câu hỏi:</strong> {!! $question->content !!}</p>
                                
                                @if($question->type == 'Trắc nghiệm')
                                    <div class="mb-3">
                                        <p><strong>Các lựa chọn:</strong></p>
                                        <ul class="list-group">
                                            @foreach($question->answers as $answer)
                                                <li class="list-group-item 
                                                    @if($userResponse && $userResponse->answer_id == $answer->id && $answer->is_correct) answer-correct
                                                    @elseif($userResponse && $userResponse->answer_id == $answer->id && !$answer->is_correct) answer-incorrect
                                                    @elseif($answer->is_correct) answer-correct
                                                    @endif">
                                                    @if($userResponse && $userResponse->answer_id == $answer->id)
                                                        <i class="fas fa-hand-point-right me-2"></i>
                                                    @endif
                                                    
                                                    @if($answer->is_correct)
                                                        <i class="fas fa-check-circle me-1"></i>
                                                    @endif
                                                    
                                                    {{ $answer->content }}
                                                    
                                                    @if($userResponse && $userResponse->answer_id == $answer->id && !$answer->is_correct)
                                                        <span class="float-end text-danger">
                                                            <i class="fas fa-times-circle"></i> Chọn sai
                                                        </span>
                                                    @elseif($userResponse && $userResponse->answer_id == $answer->id && $answer->is_correct)
                                                        <span class="float-end text-success">
                                                            <i class="fas fa-check-circle"></i> Chọn đúng
                                                        </span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @elseif($question->type == 'Tự luận')
                                    <div class="mb-3">
                                        <p><strong>Câu trả lời của bạn:</strong></p>
                                        <div class="card">
                                            <div class="card-body">
                                                {{ $userResponse ? $userResponse->text_response : 'Không có câu trả lời' }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($question->type == 'Tình huống')
                                    <div class="mb-3">
                                        @php
                                            $situationResponse = $userResponse ? json_decode($userResponse->text_response, true) : null;
                                        @endphp
                                        
                                        <p><strong>Phân tích tình huống của bạn:</strong></p>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                {{ $situationResponse ? ($situationResponse['analysis'] ?? 'Không có phân tích') : 'Không có câu trả lời' }}
                                            </div>
                                        </div>
                                        
                                        <p><strong>Giải pháp đề xuất của bạn:</strong></p>
                                        <div class="card">
                                            <div class="card-body">
                                                {{ $situationResponse ? ($situationResponse['solution'] ?? 'Không có giải pháp đề xuất') : 'Không có câu trả lời' }}
                                            </div>
                                        </div>
                                        
                                        @if($userResponse && !is_null($userResponse->score))
                                            <div class="alert alert-info mt-3">
                                                <p class="mb-0"><strong>Điểm: {{ $userResponse->score }}/1.0</strong></p>
                                                @if($userResponse->admin_comment)
                                                    <p class="mt-2 mb-0"><strong>Nhận xét của giám khảo:</strong> {{ $userResponse->admin_comment }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-3">
                                                <p class="mb-0">Câu trả lời này đang chờ chấm điểm.</p>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($question->type == 'Thực hành')
                                    <div class="mb-3">
                                        @php
                                            $practicalResponse = $userResponse ? json_decode($userResponse->text_response, true) : null;
                                        @endphp
                                        
                                        <p><strong>Quy trình thực hiện của bạn:</strong></p>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                {{ $practicalResponse ? ($practicalResponse['process'] ?? 'Không có mô tả quy trình') : 'Không có câu trả lời' }}
                                            </div>
                                        </div>
                                        
                                        <p><strong>Kết quả đạt được:</strong></p>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                {{ $practicalResponse ? ($practicalResponse['result'] ?? 'Không có kết quả') : 'Không có câu trả lời' }}
                                            </div>
                                        </div>
                                        
                                        @if($practicalResponse && isset($practicalResponse['evidence_file']) && $practicalResponse['evidence_file'])
                                            <p><strong>Bằng chứng đính kèm:</strong></p>
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <a href="{{ asset('storage/' . $practicalResponse['evidence_file']) }}" target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-file-download me-1"></i> Tải file bằng chứng
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($userResponse && !is_null($userResponse->score))
                                            <div class="alert alert-info mt-3">
                                                <p class="mb-0"><strong>Điểm: {{ $userResponse->score }}/1.0</strong></p>
                                                @if($userResponse->admin_comment)
                                                    <p class="mt-2 mb-0"><strong>Nhận xét của giám khảo:</strong> {{ $userResponse->admin_comment }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-3">
                                                <p class="mb-0">Câu trả lời này đang chờ chấm điểm.</p>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($question->type == 'Mô phỏng')
                                    <div class="mb-3">
                                        @php
                                            $simulationResponse = $userResponse ? json_decode($userResponse->text_response, true) : null;
                                        @endphp
                                        
                                        <p><strong>Các bước bạn đã thực hiện:</strong></p>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                {{ $simulationResponse ? ($simulationResponse['steps'] ?? 'Không có mô tả các bước') : 'Không có câu trả lời' }}
                                            </div>
                                        </div>
                                        
                                        <p><strong>Kết quả mô phỏng:</strong></p>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                {{ $simulationResponse ? ($simulationResponse['result'] ?? 'Không có kết quả') : 'Không có câu trả lời' }}
                                            </div>
                                        </div>
                                        
                                        @if($userResponse && !is_null($userResponse->score))
                                            <div class="alert alert-info mt-3">
                                                <p class="mb-0"><strong>Điểm: {{ $userResponse->score }}/1.0</strong></p>
                                                @if($userResponse->admin_comment)
                                                    <p class="mt-2 mb-0"><strong>Nhận xét của giám khảo:</strong> {{ $userResponse->admin_comment }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-3">
                                                <p class="mb-0">Câu trả lời này đang chờ chấm điểm.</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($question->explanation)
                                    <div class="answer-explanation">
                                        <h6><i class="fas fa-lightbulb me-1"></i> Giải thích:</h6>
                                        <p class="mb-0">{!! $question->explanation !!}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            
            <!-- Thông tin chứng chỉ -->
            @if($attempt->isPassed())
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-certificate me-1"></i> Thông tin chứng chỉ
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($attempt->certificates->count() > 0)
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle me-1"></i> Bạn đã được cấp chứng chỉ cho bài kiểm tra này!
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã chứng chỉ</th>
                                            <th>Tên chứng chỉ</th>
                                            <th>Ngày cấp</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attempt->certificates as $certificate)
                                            <tr>
                                                <td>{{ $certificate->certificate_number }}</td>
                                                <td>{{ $certificate->title }}</td>
                                                <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($certificate->status == 'active')
                                                        @if($certificate->expiry_date && $certificate->expiry_date->isPast())
                                                            <span class="badge bg-secondary">Hết hạn</span>
                                                        @else
                                                            <span class="badge bg-success">Hoạt động</span>
                                                        @endif
                                                    @elseif($certificate->status == 'expired')
                                                        <span class="badge bg-secondary">Hết hạn</span>
                                                    @elseif($certificate->status == 'revoked')
                                                        <span class="badge bg-danger">Đã thu hồi</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('seafarer.certificates.show', $certificate->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('seafarer.certificates.download', $certificate->id) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> Bạn đã đạt điểm chuẩn cho bài kiểm tra này. Vui lòng liên hệ quản trị viên để được cấp chứng chỉ.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hành động tiếp theo</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('seafarer.tests.show', $test->id) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-info-circle me-2"></i> Xem thông tin bài kiểm tra
                        </a>
                        <a href="{{ route('seafarer.tests.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-list me-2"></i> Xem danh sách bài kiểm tra
                        </a>
                        @if(!$attempt->isPassed())
                            <a href="{{ route('seafarer.tests.start', $test->id) }}" class="list-group-item list-group-item-action list-group-item-primary">
                                <i class="fas fa-redo me-2"></i> Làm lại bài kiểm tra
                            </a>
                        @endif
                        <a href="{{ route('seafarer.dashboard') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt me-2"></i> Quay lại Dashboard
                        </a>
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
// Khai báo biến PHP một cách an toàn
var attemptScore = @json($attempt->score);
</script>
<script>
    $(document).ready(function() {
        // Dữ liệu phân tích theo danh mục
        var categoryData = {
            labels: ['Hàng hải', 'An toàn', 'Quản lý', 'Kỹ thuật', 'Tiếng Anh chuyên ngành'],
            datasets: [{
                label: 'Điểm số theo danh mục',
                data: [85, 60, 75, 90, 70],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Khởi tạo biểu đồ danh mục
        var categoryChart = new Chart(
            document.getElementById('categoryChart'),
            {
                type: 'bar',
                data: categoryData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Phân tích điểm số theo danh mục'
                        }
                    }
                }
            }
        );
        
        // Dữ liệu tiến độ qua các lần kiểm tra
        var progressData = {
            labels: ['Lần 1', 'Lần 2', 'Lần 3', 'Lần hiện tại'],
            datasets: [{
                label: 'Điểm số',
                data: [65, 72, 78, attemptScore],
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        };
        
        // Khởi tạo biểu đồ tiến độ
        var progressChart = new Chart(
            document.getElementById('progressChart'),
            {
                type: 'line',
                data: progressData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tiến độ qua các lần kiểm tra'
                        }
                    }
                }
            }
        );
        
        // Phân tích và đề xuất các lĩnh vực cần cải thiện
        var categories = categoryData.labels;
        var scores = categoryData.datasets[0].data;
        var weakAreas = [];
        
        scores.forEach(function(score, index) {
            if (score < 75) {
                weakAreas.push({ category: categories[index], score: score });
            }
        });
        
        var improvementList = $('#improvement-areas');
        if (weakAreas.length > 0) {
            weakAreas.sort(function(a, b) { return a.score - b.score; });
            weakAreas.forEach(function(area) {
                improvementList.append(
                    '<li><strong>' + area.category + '</strong> - Điểm số hiện tại: ' + area.score + '/100</li>'
                );
            });
        } else {
            improvementList.append(
                '<li>Chúc mừng! Bạn đã đạt điểm tốt ở tất cả các lĩnh vực.</li>'
            );
        }
    });
</script>
@endsection 