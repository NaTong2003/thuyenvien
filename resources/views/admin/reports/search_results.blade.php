@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .search-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .stats-card {
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .stats-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
    }
    
    .seafarer-item {
        margin-bottom: 1rem;
        padding: 1rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        transition: all 0.3s;
    }
    
    .seafarer-item:hover {
        background-color: #e9ecef;
    }
    
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .attempt-item {
        padding: 0.75rem 1rem;
        border-left: 3px solid var(--primary-color);
        margin-bottom: 0.75rem;
        background-color: #f8f9fa;
    }
    
    .attempt-item:hover {
        background-color: #e9ecef;
    }
    
    .score-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-weight: bold;
    }
    
    .score-high {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .score-medium {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .score-low {
        background-color: #fee2e2;
        color: #b91c1c;
    }
</style>
@endsection

@section('content')
<div class="search-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">Kết quả tìm kiếm: "{{ $search }}"</h2>
                <p class="mb-0 text-white-50">
                    Tìm thấy {{ $stats['userCount'] }} thuyền viên và {{ $stats['attemptCount'] }} lượt thi
                </p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-white">Báo cáo & Thống kê</a></li>
                <li class="breadcrumb-item active text-white-50">Kết quả tìm kiếm</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    @if($stats['userCount'] > 0)
        <!-- Thống kê tổng quan -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center p-3">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number">{{ $stats['userCount'] }}</div>
                    <div class="stats-label">Thuyền viên</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center p-3">
                    <div class="stats-icon text-info">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="stats-number">{{ $stats['attemptCount'] }}</div>
                    <div class="stats-label">Lượt thi</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center p-3">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-number">{{ number_format($stats['averageScore'], 1) }}</div>
                    <div class="stats-label">Điểm trung bình</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center p-3">
                    <div class="stats-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number">{{ number_format($stats['passRate'], 1) }}%</div>
                    <div class="stats-label">Tỷ lệ đạt</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Danh sách thuyền viên -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users me-1"></i> Thuyền viên ({{ $users->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.reports.search') }}" method="GET" class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm thuyền viên..." name="search" value="{{ $search }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        @forelse($users as $user)
                            <div class="seafarer-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="https://via.placeholder.com/150" alt="{{ $user->name }}" class="avatar me-3">
                                    <div>
                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                        <small class="text-muted">
                                            @if($user->thuyenVien && $user->thuyenVien->position)
                                                {{ $user->thuyenVien->position->name }}
                                            @else
                                                Chưa cập nhật chức danh
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    @php
                                        $userAttempts = $testAttempts->where('user_id', $user->id);
                                        $attemptCount = $userAttempts->count();
                                        $avgScore = $attemptCount > 0 ? $userAttempts->avg('score') : 0;
                                    @endphp
                                    <div class="fw-bold">{{ number_format($avgScore, 1) }}</div>
                                    <small class="text-muted">{{ $attemptCount }} lượt thi</small>
                                </div>
                                <a href="{{ route('admin.reports.seafarer', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center text-muted my-4">
                                <i class="fas fa-search fa-2x mb-3"></i>
                                <p>Không tìm thấy thuyền viên nào phù hợp.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Danh sách lượt thi -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clipboard-check me-1"></i> Kết quả bài kiểm tra ({{ $testAttempts->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        @forelse($testAttempts as $attempt)
                            <div class="attempt-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $attempt->user->name }}</h6>
                                        <div class="text-muted small">{{ $attempt->test->title }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="score-badge 
                                            @if($attempt->score >= 80) score-high 
                                            @elseif($attempt->score >= 60) score-medium 
                                            @else score-low 
                                            @endif">
                                            {{ $attempt->score }}/100
                                        </div>
                                        <small class="text-muted">{{ $attempt->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <a href="{{ route('admin.reports.attempt', $attempt->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted my-4">
                                <i class="fas fa-clipboard-check fa-2x mb-3"></i>
                                <p>Không tìm thấy lượt thi nào cho các thuyền viên này.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                <h5 class="mb-3">Không tìm thấy kết quả nào cho từ khóa "{{ $search }}"</h5>
                <p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc điều chỉnh bộ lọc tìm kiếm.</p>
                
                <form action="{{ route('admin.reports.search') }}" method="GET" class="mt-4 col-md-6 mx-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Tìm kiếm thuyền viên..." name="search">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
                
                <div class="mt-4">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại trang Báo cáo
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 