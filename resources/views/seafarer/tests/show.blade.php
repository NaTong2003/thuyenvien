@extends('layouts.app')

@section('title', 'Chi tiết Bài Kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt me-2"></i> {{ $test->title }}
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('seafarer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('seafarer.tests.index') }}">Bài Kiểm tra</a></li>
            <li class="breadcrumb-item active">Chi tiết</li>
        </ol>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @php
        // Lấy lượt thi gần nhất từ danh sách
        $latestAttempt = $attempts->first();
    @endphp

    <div class="row">
        <!-- Thông tin bài kiểm tra -->
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between
                    {{ $test->type == 'certification' ? 'bg-primary' : ($test->type == 'assessment' ? 'bg-info' : ($test->type == 'placement' ? 'bg-secondary' : 'bg-success')) }} text-white">
                    <h6 class="m-0 font-weight-bold">Thông tin chi tiết</h6>
                    <span class="badge bg-light text-dark">
                        {{ $test->type == 'certification' ? 'Chứng chỉ' : ($test->type == 'assessment' ? 'Đánh giá' : ($test->type == 'placement' ? 'Phân loại' : 'Luyện tập')) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-3 border-bottom pb-2">Mô tả bài kiểm tra</h5>
                        <div class="test-description">
                            {!! $test->description !!}
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3 border-bottom pb-2">Thông tin chung</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Loại bài kiểm tra:</span>
                                    <span class="badge bg-{{ $test->type == 'certification' ? 'primary' : ($test->type == 'assessment' ? 'info' : ($test->type == 'placement' ? 'secondary' : 'success')) }}">
                                        {{ $test->type == 'certification' ? 'Chứng chỉ' : ($test->type == 'assessment' ? 'Đánh giá' : ($test->type == 'placement' ? 'Phân loại' : 'Luyện tập')) }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Độ khó:</span>
                                    <span class="badge bg-{{ $test->difficulty == 'Dễ' ? 'success' : ($test->difficulty == 'Trung bình' ? 'warning' : 'danger') }}">
                                        {{ $test->difficulty }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Thời gian làm bài:</span>
                                    <span><i class="far fa-clock me-1"></i> {{ $test->duration }} phút</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Số câu hỏi:</span>
                                    <span><i class="fas fa-question-circle me-1"></i> {{ $test->questions->count() }} câu</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Điểm đạt:</span>
                                    <span><i class="fas fa-award me-1"></i> {{ $test->passing_score }}%</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Danh mục:</span>
                                    <span><i class="fas fa-tag me-1"></i> {{ $test->category ? $test->category->name : 'Chung' }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3 border-bottom pb-2">Yêu cầu</h5>
                            <ul class="list-group list-group-flush">
                                @if($test->position)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Chức danh:</span>
                                    <span class="badge bg-dark">
                                        <i class="fas fa-user-tie me-1"></i> {{ $test->position->name }}
                                    </span>
                                </li>
                                @endif
                                
                                @if($test->shipType)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Loại tàu:</span>
                                    <span class="badge bg-dark">
                                        <i class="fas fa-ship me-1"></i> {{ $test->shipType->name }}
                                    </span>
                                </li>
                                @endif
                                
                                <li class="list-group-item px-0">
                                    <div class="alert alert-warning mb-0 mt-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Lưu ý:
                                        <ul class="mb-0 mt-2">
                                            <li>Bạn cần hoàn thành bài kiểm tra trong thời gian quy định.</li>
                                            <li>Không được rời khỏi trang trong khi làm bài.</li>
                                            <li>Không được sử dụng các thiết bị hỗ trợ khác.</li>
                                            @if($test->type == 'certification')
                                            <li>Đây là bài kiểm tra chứng chỉ, kết quả sẽ được lưu vào hồ sơ.</li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if($test->type == 'certification')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Bài kiểm tra này dùng để cấp chứng chỉ. Kết quả sẽ được ghi vào hồ sơ và có giá trị xác nhận năng lực của bạn.
                    </div>
                    @elseif($test->type == 'assessment')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Bài kiểm tra này dùng để đánh giá năng lực. Kết quả sẽ giúp bạn và đơn vị quản lý đánh giá trình độ hiện tại.
                    </div>
                    @elseif($test->type == 'placement')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Bài kiểm tra này dùng để phân loại trình độ. Kết quả sẽ giúp xác định vị trí công việc phù hợp.
                    </div>
                    @elseif($test->type == 'practice')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Bài kiểm tra này dùng để luyện tập. Bạn có thể làm nhiều lần để nâng cao kỹ năng.
                    </div>
                    @endif
                </div>

                <div class="card-footer">
                    <div class="d-grid gap-2">
                        @if($latestAttempt && !$latestAttempt->is_completed)
                            <a href="{{ route('seafarer.tests.start', $test->id) }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-play-circle me-1"></i> Tiếp tục làm bài
                            </a>
                        @else
                            <a href="{{ route('seafarer.tests.start', $test->id) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-play-circle me-1"></i> Bắt đầu làm bài
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử làm bài và Thống kê -->
        <div class="col-lg-4 col-md-12 mb-4">
            <!-- Thống kê cá nhân -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê cá nhân</h6>
                </div>
                <div class="card-body">
                    @if(count($attempts) > 0)
                        <div class="mb-3">
                            <h6 class="small font-weight-bold">Điểm cao nhất <span class="float-end">{{ $attempts->max('score') }}%</span></h6>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attempts->max('score') }}%;" 
                                    aria-valuenow="{{ $attempts->max('score') }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="small font-weight-bold">Điểm trung bình <span class="float-end">{{ round($attempts->avg('score'), 1) }}%</span></h6>
                            <div class="progress">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ round($attempts->avg('score'), 1) }}%;" 
                                    aria-valuenow="{{ round($attempts->avg('score'), 1) }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="small font-weight-bold">Lần làm gần nhất <span class="float-end">{{ $attempts->first()->score }}%</span></h6>
                            <div class="progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $attempts->first()->score }}%;" 
                                    aria-valuenow="{{ $attempts->first()->score }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="small font-weight-bold">Điểm đạt <span class="float-end">{{ $test->passing_score }}%</span></h6>
                            <div class="progress">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $test->passing_score }}%;" 
                                    aria-valuenow="{{ $test->passing_score }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <div class="d-inline-block me-3">
                                <h6 class="text-primary mb-0">{{ count($attempts) }}</h6>
                                <small class="text-muted">Lần thực hiện</small>
                            </div>
                            <div class="d-inline-block me-3">
                                <h6 class="text-success mb-0">{{ $attempts->where('score', '>=', $test->passing_score)->count() }}</h6>
                                <small class="text-muted">Lần đạt</small>
                            </div>
                            <div class="d-inline-block">
                                <h6 class="text-danger mb-0">{{ $attempts->where('score', '<', $test->passing_score)->count() }}</h6>
                                <small class="text-muted">Lần chưa đạt</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p>Bạn chưa thực hiện bài kiểm tra này lần nào.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lịch sử làm bài -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử làm bài</h6>
                </div>
                <div class="card-body">
                    @if(count($attempts) > 0)
                        <div class="list-group">
                            @foreach($attempts as $attempt)
                                <a href="{{ route('seafarer.tests.result', $attempt->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Lần thứ {{ $loop->iteration }}</h6>
                                        <small>{{ $attempt->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1">Điểm số: <strong>{{ $attempt->score }}%</strong></p>
                                            <small>Thời gian: {{ $attempt->getDurationInMinutes() }} phút</small>
                                        </div>
                                        <span class="badge bg-{{ $attempt->score >= $test->passing_score ? 'success' : 'danger' }}">
                                            {{ $attempt->score >= $test->passing_score ? 'Đạt' : 'Chưa đạt' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <p>Chưa có lịch sử làm bài.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 