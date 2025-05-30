@extends('layouts.app')

@section('title', 'Dashboard Thuyền viên - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard Thuyền viên
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hồ sơ cá nhân</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-6x text-primary mb-3"></i>
                        <h5 class="font-weight-bold">{{ Auth::user()->name }}</h5>
                        <p class="text-muted">
                            {{ Auth::user()->thuyenVien && Auth::user()->thuyenVien->position ? Auth::user()->thuyenVien->position->name : 'Chưa cập nhật chức danh' }}
                        </p>
                    </div>
                    
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-envelope me-2"></i> Email</span>
                            <span>{{ Auth::user()->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-phone me-2"></i> Số điện thoại</span>
                            <span>{{ Auth::user()->phone ?? 'Chưa cập nhật' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-id-card me-2"></i> Mã thuyền viên</span>
                            <span>{{ Auth::user()->seafarer_id ?? 'Chưa cập nhật' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar me-2"></i> Kinh nghiệm</span>
                            <span>{{ Auth::user()->thuyenVien && Auth::user()->thuyenVien->experience ? Auth::user()->thuyenVien->experience . ' năm' : 'Chưa cập nhật' }}</span>
                        </li>
                    </ul>
                    
                    <a href="{{ route('seafarer.profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Cập nhật hồ sơ
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kết quả kiểm tra gần đây</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentTestAttempts) && count($recentTestAttempts) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bài kiểm tra</th>
                                        <th>Ngày thi</th>
                                        <th>Điểm số</th>
                                        <th>Kết quả</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTestAttempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->test->title }}</td>
                                            <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $attempt->score }}/100</td>
                                            <td>
                                                <span class="badge bg-{{ $attempt->isPassed() ? 'success' : 'danger' }}">
                                                    {{ $attempt->isPassed() ? 'Đạt' : 'Không đạt' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('seafarer.tests.result', $attempt->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Xem chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <p>Bạn chưa có kết quả kiểm tra nào.</p>
                            <a href="{{ route('seafarer.tests.index') }}" class="btn btn-primary">
                                <i class="fas fa-file-alt me-1"></i> Xem danh sách bài kiểm tra
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kết quả theo kỹ năng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="skillRadarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Bài kiểm tra có sẵn</h6>
                    <a href="{{ route('seafarer.tests.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-1"></i> Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($availableTests) && count($availableTests) > 0)
                        <div class="row">
                            @foreach($availableTests as $test)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $test->title }}</h5>
                                            <p class="card-text text-muted small">
                                                <i class="fas fa-clock me-1"></i> {{ $test->duration }} phút
                                                @if($test->position)
                                                    | <i class="fas fa-user-tie me-1"></i> {{ $test->position->name }}
                                                @endif
                                            </p>
                                            <p class="card-text">{{ Str::limit($test->description, 100) }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between">
                                            <small class="text-muted">{{ $test->questions->count() }} câu hỏi</small>
                                            <a href="{{ route('seafarer.tests.show', $test->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-info-circle me-1"></i> Chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                            <p>Không có bài kiểm tra nào dành cho bạn.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sử dụng dữ liệu thật từ controller
    const skillScores = @json($skillScores);
    const skillLabels = Object.keys(skillScores);
    const skillData = Object.values(skillScores);
    
    // Initialize chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('skillRadarChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: skillLabels,
                datasets: [{
                    label: 'Điểm số của bạn',
                    data: skillData,
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(54, 162, 235)'
                }]
            },
            options: {
                elements: {
                    line: {
                        borderWidth: 3
                    }
                },
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        });
    });
</script>
@endsection 