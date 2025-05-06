@extends('layouts.app')

@section('title', 'Lịch sử bài thi - ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history me-2"></i> Lịch sử Bài thi của Thuyền viên
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></li>
            <li class="breadcrumb-item active">Lịch sử bài thi</li>
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

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin Thuyền viên</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-container me-3">
                            <div class="avatar bg-primary text-white">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">
                                {{ $user->thuyenVien?->position?->name ?? 'Chưa có chức danh' }}
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%">Email:</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Loại tàu:</th>
                                <td>{{ $user->thuyenVien?->shipType?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Tổng số bài thi:</th>
                                <td>{{ $testAttempts->total() }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-user me-1"></i> Xem hồ sơ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử Bài thi</h6>
                </div>
                <div class="card-body">
                    @if($testAttempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bài kiểm tra</th>
                                        <th>Ngày thi</th>
                                        <th>Điểm số</th>
                                        <th>Kết quả</th>
                                        <th>Chứng chỉ</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($testAttempts as $attempt)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.tests.show', $attempt->test_id) }}">
                                                    {{ $attempt->test->title }}
                                                </a>
                                            </td>
                                            <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="fw-bold">
                                                <span class="{{ $attempt->score >= $attempt->test->passing_score ? 'text-success' : 'text-danger' }}">
                                                    {{ $attempt->score }}/100
                                                </span>
                                            </td>
                                            <td>
                                                @if($attempt->score >= $attempt->test->passing_score)
                                                    <span class="badge bg-success">Đạt</span>
                                                @else
                                                    <span class="badge bg-danger">Không đạt</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->certificates->count() > 0)
                                                    @foreach($attempt->certificates as $certificate)
                                                        <a href="{{ route('admin.certificates.show', $certificate->id) }}" class="badge bg-info text-decoration-none">
                                                            <i class="fas fa-certificate me-1"></i> {{ $certificate->certificate_number }}
                                                        </a>
                                                    @endforeach
                                                @else
                                                    @if($attempt->score >= $attempt->test->passing_score)
                                                        <span class="badge bg-warning text-dark">Chưa cấp chứng chỉ</span>
                                                    @else
                                                        <span class="badge bg-secondary">Không đủ điều kiện</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.reports.attempt', $attempt->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($attempt->score >= $attempt->test->passing_score && $attempt->certificates->count() == 0)
                                                        <a href="{{ route('admin.certificates.create.from.attempt', $attempt->id) }}" class="btn btn-sm btn-success">
                                                            <i class="fas fa-certificate"></i> Cấp chứng chỉ
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-3">
                            {{ $testAttempts->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> Thuyền viên này chưa tham gia bài kiểm tra nào.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-container {
        width: 50px;
        height: 50px;
    }
    .avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
    }
</style>
@endsection 