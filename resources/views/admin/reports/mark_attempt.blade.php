@extends('layouts.app')

@section('title', 'Chấm điểm bài thi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-pen me-2"></i> Chấm điểm bài thi
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.marking') }}">Chấm điểm</a></li>
            <li class="breadcrumb-item active">Bài thi #{{ $attempt->id }}</li>
        </ol>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin bài thi</h6>
                    <span class="badge rounded-pill {{ $attempt->is_marked ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $attempt->is_marked ? 'Đã chấm' : 'Chưa chấm' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%">ID bài thi:</th>
                                <td>{{ $attempt->id }}</td>
                            </tr>
                            <tr>
                                <th>Thuyền viên:</th>
                                <td>
                                    <a href="{{ route('admin.users.show', $attempt->user_id) }}">
                                        {{ $attempt->user->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Bài kiểm tra:</th>
                                <td>
                                    <a href="{{ route('admin.tests.show', $attempt->test_id) }}">
                                        {{ $attempt->test->title }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Thời gian bắt đầu:</th>
                                <td>{{ $attempt->start_time ? $attempt->start_time->format('d/m/Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Thời gian kết thúc:</th>
                                <td>{{ $attempt->end_time ? $attempt->end_time->format('d/m/Y H:i') : 'Chưa hoàn thành' }}</td>
                            </tr>
                            <tr>
                                <th>Điểm hiện tại:</th>
                                <td>{{ $attempt->score }}/100</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.reports.attempt', $attempt->id) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i> Xem chi tiết bài làm
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Chấm điểm bài làm</h6>
                    <div>
                        <span class="badge bg-info me-2">{{ $subjectiveResponses->count() }} câu hỏi cần xem xét</span>
                        <span class="badge bg-warning me-2">{{ $subjectiveResponses->whereNull('score')->count() }} câu chưa chấm</span>
                        <span class="badge bg-success">{{ $subjectiveResponses->whereNotNull('score')->count() }} câu đã chấm</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($subjectiveResponses->count() > 0)
                        <form action="{{ route('admin.reports.save.marking', $attempt->id) }}" method="POST">
                            @csrf
                            
                            @if($subjectiveResponses->whereNull('score')->count() == 0)
                                <div class="alert alert-success mb-4">
                                    <i class="fas fa-check-circle me-1"></i> Tất cả câu hỏi đã được chấm điểm. Bạn có thể chỉnh sửa điểm nếu cần.
                                </div>
                            @endif
                            
                            <div class="accordion" id="markingAccordion">
                                @foreach($subjectiveResponses as $index => $response)
                                    <div class="accordion-item mb-3 border shadow-sm">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                    <div>
                                                        <span class="me-3 fw-bold">Câu hỏi {{ $index + 1 }}</span>
                                                        <span class="badge bg-primary">{{ $response->question->type }}</span>
                                                        @if($response->score !== null)
                                                            <span class="badge bg-success ms-2">Đã chấm: {{ $response->score }}</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark ms-2">Chưa chấm</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted small">
                                                        @if(is_object($response->question->category))
                                                            {{ $response->question->category->name ?? 'Chưa phân loại' }}
                                                        @elseif(is_string($response->question->category))
                                                            {{ $response->question->category }}
                                                        @else
                                                            {{ $response->question->category_id ? App\Models\Category::find($response->question->category_id)->name : 'Chưa phân loại' }}
                                                        @endif
                                                        | 
                                                        @if($response->question->difficulty == 'Dễ')
                                                            <span class="text-success">Dễ</span>
                                                        @elseif($response->question->difficulty == 'Trung bình')
                                                            <span class="text-primary">Trung bình</span>
                                                        @else
                                                            <span class="text-danger">Khó</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}">
                                            <div class="accordion-body">
                                                <div class="card bg-light mb-3">
                                                    <div class="card-body">
                                                        <p class="mb-0"><strong>Câu hỏi:</strong> {!! $response->question->content !!}</p>
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                
                                                @if($response->question->type == 'Tự luận')
                                                    <div class="mb-3">
                                                        <p><strong>Câu trả lời của thuyền viên:</strong></p>
                                                        <div class="card mb-3">
                                                            <div class="card-body bg-white">
                                                                {!! nl2br(e($response->text_response)) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($response->question->type == 'Tình huống')
                                                    <div class="mb-3">
                                                        @php
                                                            $situationResponse = json_decode($response->text_response, true);
                                                        @endphp
                                                        
                                                        <p><strong>Phân tích tình huống:</strong></p>
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                {!! nl2br(e($situationResponse['analysis'] ?? 'Không có phân tích')) !!}
                                                            </div>
                                                        </div>
                                                        
                                                        <p><strong>Giải pháp đề xuất:</strong></p>
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                {!! nl2br(e($situationResponse['solution'] ?? 'Không có giải pháp đề xuất')) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($response->question->type == 'Thực hành')
                                                    <div class="mb-3">
                                                        @php
                                                            $practicalResponse = json_decode($response->text_response, true);
                                                        @endphp
                                                        
                                                        <p><strong>Quy trình thực hiện:</strong></p>
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                {!! nl2br(e($practicalResponse['process'] ?? 'Không có mô tả quy trình')) !!}
                                                            </div>
                                                        </div>
                                                        
                                                        <p><strong>Kết quả đạt được:</strong></p>
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                {!! nl2br(e($practicalResponse['result'] ?? 'Không có kết quả')) !!}
                                                            </div>
                                                        </div>
                                                        
                                                        @if(isset($practicalResponse['evidence_file']) && $practicalResponse['evidence_file'])
                                                            <p><strong>Bằng chứng đính kèm:</strong></p>
                                                            <div class="card mb-3">
                                                                <div class="card-body">
                                                                    <a href="{{ asset('storage/' . $practicalResponse['evidence_file']) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                        <i class="fas fa-file-download me-1"></i> Tải file bằng chứng
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($response->question->type == 'Mô phỏng')
                                                    <div class="mb-3">
                                                        @php
                                                            $simulationResponse = json_decode($response->text_response, true);
                                                        @endphp
                                                        
                                                        <p><strong>Các bước đã thực hiện:</strong></p>
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                {!! nl2br(e($simulationResponse['steps'] ?? 'Không có mô tả các bước')) !!}
                                                            </div>
                                                        </div>
                                                        
                                                        <p><strong>Kết quả mô phỏng:</strong></p>
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                {!! nl2br(e($simulationResponse['result'] ?? 'Không có kết quả')) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <div class="card shadow-sm border-left-warning mb-3">
                                                    <div class="card-header bg-light">
                                                        <h6 class="m-0 font-weight-bold text-primary">Chấm điểm</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label fw-bold">Điểm (0.0 - 1.0):</label>
                                                                    <div class="input-group">
                                                                        <input type="number" step="0.1" min="0" max="1" class="form-control" name="score[{{ $index }}]" value="{{ $response->score ?? '' }}" required>
                                                                        <span class="input-group-text"><i class="fas fa-star"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label fw-bold">Nhận xét:</label>
                                                                    <textarea class="form-control" name="comment[{{ $index }}]" rows="3" placeholder="Nhập nhận xét của bạn...">{{ $response->admin_comment ?? '' }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="response_id[{{ $index }}]" value="{{ $response->id }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.reports.marking') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Lưu điểm
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> Không có câu hỏi tự luận nào cần chấm điểm.
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.reports.marking') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 