@extends('layouts.app')

@section('title', 'Chi tiết Thuyền viên - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .user-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .user-meta-card {
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
    
    .badge-position {
        background-color: #d1e7dd;
        color: #0f5132;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
    }
    
    .badge-ship-type {
        background-color: #cfe2ff;
        color: #084298;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
    }
    
    .test-attempt-card {
        border: 1px solid #eaeaea;
        border-radius: 8px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .test-attempt-card:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .test-attempt-header {
        padding: 1rem;
        border-bottom: 1px solid #eaeaea;
        background-color: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .test-attempt-body {
        padding: 1rem;
    }
    
    .test-attempt-footer {
        padding: 1rem;
        border-top: 1px solid #eaeaea;
        background-color: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .score-badge {
        font-size: 1rem;
        padding: 0.3rem 0.6rem;
    }
</style>
@endsection

@section('content')
<div class="user-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>{{ $user->name }}</h2>
                <div class="d-flex gap-2 mt-2">
                    @if($user->thuyenVien && $user->thuyenVien->position)
                        <span class="badge-position">{{ $user->thuyenVien->position->name }}</span>
                    @endif
                    
                    @if($user->thuyenVien && $user->thuyenVien->shipType)
                        <span class="badge-ship-type">{{ $user->thuyenVien->shipType->name }}</span>
                    @endif
                </div>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-white">Thuyền viên</a></li>
                <li class="breadcrumb-item active text-white-50">Chi tiết</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card user-meta-card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i> Thông tin cá nhân
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="meta-item">
                        <div class="meta-label">Email</div>
                        <div class="meta-value">{{ $user->email }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Số điện thoại</div>
                        <div class="meta-value">{{ $user->phone ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Mã thuyền viên</div>
                        <div class="meta-value">{{ $user->seafarer_id ?? 'Chưa cập nhật' }}</div>
                    </div>
                </div>
            </div>

            <div class="card user-meta-card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-ship me-2"></i> Thông tin chuyên môn
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="meta-item">
                        <div class="meta-label">Chức danh</div>
                        <div class="meta-value">
                            @if($user->thuyenVien && $user->thuyenVien->position)
                                {{ $user->thuyenVien->position->name }}
                            @else
                                <span class="text-muted">Chưa cập nhật</span>
                            @endif
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Loại tàu</div>
                        <div class="meta-value">
                            @if($user->thuyenVien && $user->thuyenVien->shipType)
                                {{ $user->thuyenVien->shipType->name }}
                            @else
                                <span class="text-muted">Chưa cập nhật</span>
                            @endif
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Kinh nghiệm</div>
                        <div class="meta-value">
                            @if($user->thuyenVien && $user->thuyenVien->experience)
                                {{ $user->thuyenVien->experience }} năm
                            @else
                                <span class="text-muted">Chưa cập nhật</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-3">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa thông tin
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i> Lịch sử làm bài
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->testAttempts && $user->testAttempts->count() > 0)
                        @foreach($user->testAttempts as $attempt)
                            <div class="test-attempt-card">
                                <div class="test-attempt-header">
                                    <h6 class="mb-0">{{ $attempt->test->title }}</h6>
                                    <span class="badge bg-{{ $attempt->score >= $attempt->test->passing_score ? 'success' : 'danger' }} score-badge">
                                        {{ $attempt->score }}%
                                    </span>
                                </div>
                                <div class="test-attempt-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong>Loại bài kiểm tra:</strong>
                                                @if($attempt->test->type == 'certification')
                                                    <span class="badge bg-primary">Chứng chỉ</span>
                                                @elseif($attempt->test->type == 'assessment')
                                                    <span class="badge bg-info">Đánh giá năng lực</span>
                                                @elseif($attempt->test->type == 'placement')
                                                    <span class="badge bg-secondary">Phân loại</span>
                                                @elseif($attempt->test->type == 'practice')
                                                    <span class="badge bg-success">Luyện tập</span>
                                                @else
                                                    <span class="badge bg-dark">{{ $attempt->test->type }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <strong>Danh mục:</strong> {{ $attempt->test->category ? $attempt->test->category->name : 'Chưa phân loại' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong>Thời gian bắt đầu:</strong> {{ $attempt->started_at }}
                                            </div>
                                            <div>
                                                <strong>Hoàn thành:</strong> 
                                                @if($attempt->completed_at)
                                                    {{ $attempt->completed_at }}
                                                @else
                                                    <span class="text-warning">Chưa hoàn thành</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="test-attempt-footer">
                                    <div>
                                        <span class="badge bg-{{ $attempt->score >= $attempt->test->passing_score ? 'success' : 'danger' }}">
                                            {{ $attempt->score >= $attempt->test->passing_score ? 'Đạt' : 'Không đạt' }}
                                        </span>
                                        <span class="text-muted ms-2">Điểm đạt yêu cầu: {{ $attempt->test->passing_score }}%</span>
                                    </div>
                                    <a href="{{ route('admin.reports.attempt', $attempt->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye me-1"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Thuyền viên chưa thực hiện bài kiểm tra nào.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 