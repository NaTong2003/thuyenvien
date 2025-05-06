@extends('layouts.app')

@section('title', 'Chi tiết Bài Làm - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .test-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .attempt-info-card {
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .stats-card {
        margin-bottom: 1.5rem;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .stat-item {
        padding: 1.5rem;
        text-align: center;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .answer-item {
        margin-bottom: 1.5rem;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .answer-header {
        padding: 1rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e3e6f0;
        font-weight: 600;
    }
    
    .answer-content {
        padding: 1.5rem;
    }
    
    .answer-option {
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }
    
    .answer-option.selected {
        background-color: #e8f4ff;
        border-color: #4e73df;
    }
    
    .answer-option.correct {
        background-color: #d1e7dd;
        border-color: #198754;
    }
    
    .answer-option.incorrect {
        background-color: #f8d7da;
        border-color: #dc3545;
    }
    
    .correctness-indicator {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }
    
    .test-description {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary-color);
    }
    
    .user-info-list {
        margin-bottom: 0;
    }
    
    .user-info-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
    }
    
    .user-info-item:last-child {
        border-bottom: none;
    }
    
    .user-info-label {
        color: #6c757d;
    }
    
    .user-info-value {
        font-weight: 600;
    }
    
    .result-pill {
        font-size: 0.85rem;
        padding: 0.35rem 0.8rem;
        border-radius: 30px;
    }
</style>
@endsection

@section('content')
<div class="test-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Chi tiết Bài Làm: {{ $test->title }}</h2>
                <p class="mb-0 text-white-50">
                    Thuyền viên: {{ $user->name }} | Ngày làm: {{ $testAttempt->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-white">Báo cáo</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.test', $test->id) }}" class="text-white">Thống kê bài kiểm tra</a></li>
                <li class="breadcrumb-item active text-white-50">Chi tiết bài làm</li>
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
                        <i class="fas fa-clipboard-check me-1"></i> Kết quả chi tiết
                    </h6>
                    <div>
                        <span class="result-pill bg-{{ $isPassed ? 'success' : 'danger' }} text-white">
                            {{ $isPassed ? 'Đạt' : 'Không đạt' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-value text-primary">{{ $testAttempt->score }}</div>
                                <div class="stat-label">Điểm số</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-value text-success">{{ $correctAnswers }}</div>
                                <div class="stat-label">Câu đúng</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-value text-danger">{{ $incorrectAnswers }}</div>
                                <div class="stat-label">Câu sai</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-value text-info">{{ $accuracy }}</div>
                                <div class="stat-label">Độ chính xác</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($isPassed)
                        <div class="certificate-section p-3 bg-light rounded mb-4 text-center">
                            @if($hasCertificate)
                                <h5 class="text-success mb-3">
                                    <i class="fas fa-certificate me-2"></i> Thuyền viên đã được cấp chứng chỉ
                                </h5>
                                @foreach($testAttempt->certificates as $certificate)
                                    <a href="{{ route('admin.certificates.show', $certificate->id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Xem chứng chỉ {{ $certificate->certificate_number }}
                                    </a>
                                @endforeach
                            @else
                                <h5 class="text-warning mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Thuyền viên chưa được cấp chứng chỉ
                                </h5>
                                <a href="{{ route('admin.certificates.create.from.attempt', $testAttempt->id) }}" class="btn btn-success">
                                    <i class="fas fa-certificate me-1"></i> Cấp chứng chỉ
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Các câu trả lời:</h5>
                    
                    @foreach($testAttempt->userResponses as $index => $response)
                        <div class="answer-item">
                            <div class="answer-header">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        Câu {{ $index + 1 }}: 
                                        <span class="badge bg-info ms-2">{{ $response->question->type }}</span>
                                        @if($response->question->type == 'Trắc nghiệm')
                                            @if($response->answer && $response->answer->is_correct)
                                                <span class="badge bg-success ms-2">Đúng</span>
                                            @else
                                                <span class="badge bg-danger ms-2">Sai</span>
                                            @endif
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-muted">{{ $response->question->difficulty }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="answer-content">
                                <div class="mb-3">
                                    {!! $response->question->content !!}
                                </div>
                                
                                @if($response->question->type == 'Trắc nghiệm')
                                    @foreach($response->question->answers as $answerOption)
                                        <div class="answer-option 
                                            {{ $response->answer_id == $answerOption->id ? 'selected' : '' }}
                                            {{ $answerOption->is_correct ? 'correct' : '' }}
                                            {{ $response->answer_id == $answerOption->id && !$answerOption->is_correct ? 'incorrect' : '' }}">
                                            <div class="d-flex align-items-center">
                                                @if($answerOption->is_correct)
                                                    <span class="correctness-indicator text-success">
                                                        <i class="fas fa-check-circle"></i>
                                                    </span>
                                                @elseif($response->answer_id == $answerOption->id && !$answerOption->is_correct)
                                                    <span class="correctness-indicator text-danger">
                                                        <i class="fas fa-times-circle"></i>
                                                    </span>
                                                @endif
                                                {{ $answerOption->content }}
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($response->question->type == 'Tự luận')
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Câu trả lời của thuyền viên:</label>
                                        <div class="answer-option">
                                            {!! nl2br(e($response->text_response)) !!}
                                        </div>
                                    </div>

                                    @if($response->admin_comment)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nhận xét của giám khảo:</label>
                                        <div class="answer-option bg-light">
                                            {!! nl2br(e($response->admin_comment)) !!}
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="mt-2">
                                        <span class="badge bg-primary">Điểm: {{ $response->score }}</span>
                                    </div>
                                @elseif($response->question->type == 'Tình huống')
                                    <div class="mb-3">
                                        @php
                                            $situationResponse = json_decode($response->text_response, true);
                                        @endphp
                                        
                                        <label class="form-label fw-bold">Phân tích tình huống:</label>
                                        <div class="answer-option">
                                            {!! nl2br(e($situationResponse['analysis'] ?? 'Không có phân tích')) !!}
                                        </div>
                                        
                                        <label class="form-label fw-bold mt-3">Giải pháp đề xuất:</label>
                                        <div class="answer-option">
                                            {!! nl2br(e($situationResponse['solution'] ?? 'Không có giải pháp đề xuất')) !!}
                                        </div>
                                    </div>

                                    @if($response->admin_comment)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nhận xét của giám khảo:</label>
                                        <div class="answer-option bg-light">
                                            {!! nl2br(e($response->admin_comment)) !!}
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mt-2">
                                        <span class="badge bg-primary">Điểm: {{ $response->score }}</span>
                                    </div>
                                @elseif($response->question->type == 'Thực hành')
                                    <div class="mb-3">
                                        @php
                                            $practicalResponse = json_decode($response->text_response, true);
                                        @endphp
                                        
                                        <label class="form-label fw-bold">Quy trình thực hiện:</label>
                                        <div class="answer-option">
                                            {!! nl2br(e($practicalResponse['process'] ?? 'Không có mô tả quy trình')) !!}
                                        </div>
                                        
                                        <label class="form-label fw-bold mt-3">Kết quả đạt được:</label>
                                        <div class="answer-option">
                                            {!! nl2br(e($practicalResponse['result'] ?? 'Không có kết quả')) !!}
                                        </div>
                                        
                                        @if(isset($practicalResponse['evidence_file']) && $practicalResponse['evidence_file'])
                                            <label class="form-label fw-bold mt-3">Bằng chứng đính kèm:</label>
                                            <div class="answer-option">
                                                <a href="{{ asset('storage/' . $practicalResponse['evidence_file']) }}" 
                                                   target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-download me-1"></i> Tải file bằng chứng
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    @if($response->admin_comment)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nhận xét của giám khảo:</label>
                                        <div class="answer-option bg-light">
                                            {!! nl2br(e($response->admin_comment)) !!}
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mt-2">
                                        <span class="badge bg-primary">Điểm: {{ $response->score }}</span>
                                    </div>
                                @elseif($response->question->type == 'Mô phỏng')
                                    <div class="mb-3">
                                        @php
                                            $simulationResponse = json_decode($response->text_response, true);
                                        @endphp
                                        
                                        <label class="form-label fw-bold">Các bước đã thực hiện:</label>
                                        <div class="answer-option">
                                            {!! nl2br(e($simulationResponse['steps'] ?? 'Không có mô tả các bước')) !!}
                                        </div>
                                        
                                        <label class="form-label fw-bold mt-3">Kết quả mô phỏng:</label>
                                        <div class="answer-option">
                                            {!! nl2br(e($simulationResponse['result'] ?? 'Không có kết quả')) !!}
                                        </div>
                                    </div>

                                    @if($response->admin_comment)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nhận xét của giám khảo:</label>
                                        <div class="answer-option bg-light">
                                            {!! nl2br(e($response->admin_comment)) !!}
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mt-2">
                                        <span class="badge bg-primary">Điểm: {{ $response->score }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> Thông tin bài làm
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="user-info-list">
                        <div class="user-info-item">
                            <div class="user-info-label">Thuyền viên:</div>
                            <div class="user-info-value">{{ $user->name }}</div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">Chức danh:</div>
                            <div class="user-info-value">
                                {{ $user->thuyenVien && $user->thuyenVien->position ? $user->thuyenVien->position->name : 'Không có' }}
                            </div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">Bài kiểm tra:</div>
                            <div class="user-info-value">{{ $test->title }}</div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">Thời gian làm bài:</div>
                            <div class="user-info-value">{{ $duration ? $duration . ' phút' : 'Không xác định' }}</div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">Ngày làm bài:</div>
                            <div class="user-info-value">{{ $testAttempt->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">Điểm số:</div>
                            <div class="user-info-value">{{ $testAttempt->score }}</div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">Điểm đạt yêu cầu:</div>
                            <div class="user-info-value">{{ $test->passing_score }}</div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">Kết quả:</div>
                            <div class="user-info-value">
                                <span class="badge bg-{{ $isPassed ? 'success' : 'danger' }}">
                                    {{ $isPassed ? 'Đạt' : 'Không đạt' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-1"></i> Phân tích kết quả
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="mb-2">Tỷ lệ câu trả lời đúng: {{ $accuracy }}%</h6>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success text-white" role="progressbar" 
                                 style="width: {{ $accuracy }}%;" 
                                 aria-valuenow="{{ $accuracy }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $accuracy }}%
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        <div class="d-inline-block me-3">
                            <span class="d-block text-center text-success h4">{{ $correctAnswers }}</span>
                            <span class="small text-muted">Đúng</span>
                        </div>
                        <div class="d-inline-block">
                            <span class="d-block text-center text-danger h4">{{ $incorrectAnswers }}</span>
                            <span class="small text-muted">Sai</span>
                        </div>
                    </div>
                    
                    @if($isPassed)
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-1"></i>
                            Thuyền viên đã đạt yêu cầu của bài kiểm tra này.
                        </div>
                    @else
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            Thuyền viên chưa đạt yêu cầu của bài kiểm tra này.
                        </div>
                    @endif
                </div>
            </div>
            
            @if($isPassed)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-certificate me-1"></i> Chứng chỉ
                    </h6>
                </div>
                <div class="card-body">
                    @if($hasCertificate)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Thuyền viên đã được cấp chứng chỉ cho bài kiểm tra này.
                        </div>
                        <a href="{{ route('admin.certificates.index', ['user_id' => $user->id]) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-search me-1"></i> Xem lịch sử chứng chỉ
                        </a>
                    @else
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Thuyền viên chưa được cấp chứng chỉ cho bài kiểm tra này.
                        </div>
                        <a href="{{ route('admin.certificates.create', ['attempt_id' => $testAttempt->id]) }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus-circle me-1"></i> Cấp chứng chỉ
                        </a>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.reports.test', $test->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại báo cáo
                </a>
                <button class="btn btn-primary" id="printReport">
                    <i class="fas fa-print me-1"></i> In báo cáo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('printReport').addEventListener('click', function() {
            window.print();
        });
    });
</script>
@endsection 