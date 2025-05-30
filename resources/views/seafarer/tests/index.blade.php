@extends('layouts.app')

@section('title', 'Danh sách Bài Kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt me-2"></i> Danh sách Bài Kiểm tra
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('seafarer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Bài Kiểm tra</li>
        </ol>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Phần Lọc và Tìm kiếm -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tìm kiếm và Lọc</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('seafarer.tests.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Tên bài kiểm tra...">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Loại bài kiểm tra</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tất cả</option>
                        <option value="certification" {{ request('type') == 'certification' ? 'selected' : '' }}>Chứng chỉ</option>
                        <option value="assessment" {{ request('type') == 'assessment' ? 'selected' : '' }}>Đánh giá năng lực</option>
                        <option value="placement" {{ request('type') == 'placement' ? 'selected' : '' }}>Phân loại</option>
                        <option value="practice" {{ request('type') == 'practice' ? 'selected' : '' }}>Luyện tập</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="difficulty" class="form-label">Độ khó</label>
                    <select class="form-select" id="difficulty" name="difficulty">
                        <option value="">Tất cả</option>
                        <option value="Dễ" {{ request('difficulty') == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                        <option value="Trung bình" {{ request('difficulty') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                        <option value="Khó" {{ request('difficulty') == 'Khó' ? 'selected' : '' }}>Khó</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Sắp xếp</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="duration_asc" {{ request('sort') == 'duration_asc' ? 'selected' : '' }}>Thời gian (Tăng dần)</option>
                        <option value="duration_desc" {{ request('sort') == 'duration_desc' ? 'selected' : '' }}>Thời gian (Giảm dần)</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('seafarer.tests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-1"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách bài kiểm tra -->
    <div class="row">
        @if($tests->count() > 0)
            @foreach($tests as $test)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between
                            {{ $test->type == 'certification' ? 'bg-primary' : ($test->type == 'assessment' ? 'bg-info' : ($test->type == 'placement' ? 'bg-secondary' : 'bg-success')) }} text-white">
                            <h6 class="m-0 font-weight-bold">{{ $test->title }}</h6>
                            <span class="badge bg-light text-dark">
                                {{ $test->type == 'certification' ? 'Chứng chỉ' : ($test->type == 'assessment' ? 'Đánh giá' : ($test->type == 'placement' ? 'Phân loại' : 'Luyện tập')) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="d-block text-muted mb-1">Loại bài kiểm tra</small>
                                    <span class="badge bg-{{ $test->type == 'certification' ? 'primary' : ($test->type == 'assessment' ? 'info' : ($test->type == 'placement' ? 'secondary' : 'success')) }}">
                                        {{ $test->type == 'certification' ? 'Chứng chỉ' : ($test->type == 'assessment' ? 'Đánh giá' : ($test->type == 'placement' ? 'Phân loại' : 'Luyện tập')) }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="d-block text-muted mb-1">Độ khó</small>
                                    <span class="badge bg-{{ $test->difficulty == 'Dễ' ? 'success' : ($test->difficulty == 'Trung bình' ? 'warning' : 'danger') }}">
                                        {{ $test->difficulty }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="d-block text-muted mb-1">Thời gian</small>
                                    <span class="text-dark">
                                        <i class="far fa-clock me-1"></i> {{ $test->duration }} phút
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="d-block text-muted mb-1">Số câu hỏi</small>
                                    <span class="text-dark">
                                        <i class="fas fa-question-circle me-1"></i> {{ $test->questions->count() }} câu
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="d-block text-muted mb-1">Danh mục</small>
                                    <span class="text-dark">
                                        <i class="fas fa-tag me-1"></i> {{ $test->category ? $test->category->name : 'Chung' }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="d-block text-muted mb-1">Điểm đạt</small>
                                    <span class="text-dark">
                                        <i class="fas fa-award me-1"></i> {{ $test->passing_score }}%
                                    </span>
                                </div>
                            </div>
                            
                            @if($test->position)
                            <div class="mb-3">
                                <small class="d-block text-muted mb-1">Chức danh</small>
                                <span class="badge bg-dark">
                                    <i class="fas fa-user-tie me-1"></i> {{ $test->position->name }}
                                </span>
                            </div>
                            @endif
                            
                            @if($test->shipType)
                            <div class="mb-3">
                                <small class="d-block text-muted mb-1">Loại tàu</small>
                                <span class="badge bg-dark">
                                    <i class="fas fa-ship me-1"></i> {{ $test->shipType->name }}
                                </span>
                            </div>
                            @endif

                            <div class="mb-3">
                                <small class="d-block text-muted mb-1">Mô tả</small>
                                <p class="card-text text-truncate">{{ $test->description }}</p>
                            </div>

                            @php
                                $latestAttempt = isset($testAttempts[$test->id]) ? $testAttempts[$test->id]->first() : null;
                            @endphp

                            @if($latestAttempt && $latestAttempt->is_completed)
                                <div class="alert alert-info mb-3">
                                    <small class="d-block fw-bold mb-1">Kết quả gần nhất:</small>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Điểm: <strong>{{ $latestAttempt->score }}%</strong></span>
                                        <span class="badge bg-{{ $latestAttempt->score >= $test->passing_score ? 'success' : 'danger' }}">
                                            {{ $latestAttempt->score >= $test->passing_score ? 'Đạt' : 'Chưa đạt' }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <div class="d-grid">
                                @if($latestAttempt && !$latestAttempt->is_completed)
                                    <a href="{{ route('seafarer.tests.start', $test->id) }}" class="btn btn-warning">
                                        <i class="fas fa-play-circle me-1"></i> Tiếp tục làm bài
                                    </a>
                                @else
                                    <a href="{{ route('seafarer.tests.show', $test->id) }}" class="btn btn-primary">
                                        <i class="fas fa-info-circle me-1"></i> Xem chi tiết
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                @if($latestAttempt)
                                    <i class="fas fa-history me-1"></i> Làm gần nhất: {{ $latestAttempt->created_at->format('d/m/Y H:i') }}
                                @else
                                    <i class="fas fa-history me-1"></i> Chưa làm bài này
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Không tìm thấy bài kiểm tra nào phù hợp với tiêu chí đã chọn.
                </div>
            </div>
        @endif
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-4">
        {{ $tests->links() }}
    </div>
</div>
@endsection 