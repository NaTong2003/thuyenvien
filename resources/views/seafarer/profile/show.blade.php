@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .profile-header {
        background-color: var(--primary-color);
        padding: 2rem 0;
        color: white;
        margin-bottom: 2rem;
    }
    
    .profile-header h1 {
        margin-bottom: 0.5rem;
    }
    
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid rgba(255, 255, 255, 0.5);
        overflow: hidden;
        margin: 0 auto 1rem;
    }
    
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .info-card {
        transition: all 0.3s;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .info-card .card-header {
        font-weight: bold;
    }
    
    .certificate-card {
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
    }
    
    .certificate-header {
        background-color: #f8f9fa;
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .certificate-body {
        padding: 1rem;
    }
    
    .certificate-footer {
        padding: 0.5rem 1rem;
        background-color: #f8f9fa;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="profile-header text-center">
    <div class="container">
        <div class="profile-avatar">
            <img src="https://via.placeholder.com/150" alt="{{ $user->name }}">
        </div>
        <h1>{{ $user->name }}</h1>
        <p class="lead">
            @if($thuyenVien && $thuyenVien->position)
                {{ $thuyenVien->position->name }}
            @else
                Chưa cập nhật chức danh
            @endif
        </p>
        <a href="{{ route('seafarer.profile.edit') }}" class="btn btn-light">
            <i class="fas fa-edit me-1"></i> Chỉnh sửa hồ sơ
        </a>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4 info-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-1"></i> Thông tin cá nhân
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th width="35%">Họ và tên</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                                </tr>
                                <tr>
                                    <th>Mã thuyền viên (Seafarer ID)</th>
                                    <td>{{ $user->seafarer_id ?? 'Chưa cập nhật' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow mb-4 info-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-ship me-1"></i> Thông tin chuyên môn
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th width="35%">Chức danh</th>
                                    <td>
                                        @if($thuyenVien && $thuyenVien->position)
                                            {{ $thuyenVien->position->name }}
                                        @else
                                            <span class="text-muted">Chưa cập nhật</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Loại tàu</th>
                                    <td>
                                        @if($thuyenVien && $thuyenVien->shipType)
                                            {{ $thuyenVien->shipType->name }}
                                        @else
                                            <span class="text-muted">Chưa cập nhật</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kinh nghiệm</th>
                                    <td>
                                        @if($thuyenVien && $thuyenVien->experience)
                                            {{ $thuyenVien->experience }} năm
                                        @else
                                            <span class="text-muted">Chưa cập nhật</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Bài kiểm tra đã làm</th>
                                    <td>
                                        <a href="{{ route('seafarer.tests.index') }}">
                                            Xem tất cả bài kiểm tra
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-certificate me-1"></i> Chứng chỉ & Giấy phép
            </h6>
            <button class="btn btn-primary btn-sm" disabled>
                <i class="fas fa-plus me-1"></i> Thêm chứng chỉ
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="certificate-card shadow-sm">
                        <div class="certificate-header">
                            <h6 class="mb-0">Chứng chỉ Sỹ quan Boong</h6>
                        </div>
                        <div class="certificate-body">
                            <p class="mb-1"><small><strong>Số:</strong> ABC1234567</small></p>
                            <p class="mb-1"><small><strong>Ngày cấp:</strong> 15/05/2022</small></p>
                            <p class="mb-0"><small><strong>Ngày hết hạn:</strong> 15/05/2027</small></p>
                        </div>
                        <div class="certificate-footer d-flex justify-content-between">
                            <span class="badge bg-success">Còn hạn</span>
                            <button class="btn btn-link btn-sm p-0" disabled>Xem chi tiết</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="certificate-card shadow-sm">
                        <div class="certificate-header">
                            <h6 class="mb-0">Chứng chỉ GMDSS</h6>
                        </div>
                        <div class="certificate-body">
                            <p class="mb-1"><small><strong>Số:</strong> GDM987654</small></p>
                            <p class="mb-1"><small><strong>Ngày cấp:</strong> 10/03/2023</small></p>
                            <p class="mb-0"><small><strong>Ngày hết hạn:</strong> 10/03/2028</small></p>
                        </div>
                        <div class="certificate-footer d-flex justify-content-between">
                            <span class="badge bg-success">Còn hạn</span>
                            <button class="btn btn-link btn-sm p-0" disabled>Xem chi tiết</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="certificate-card shadow-sm border-danger">
                        <div class="certificate-header">
                            <h6 class="mb-0">Chứng chỉ Hàng hóa Nguy hiểm</h6>
                        </div>
                        <div class="certificate-body">
                            <p class="mb-1"><small><strong>Số:</strong> DGM456789</small></p>
                            <p class="mb-1"><small><strong>Ngày cấp:</strong> 20/01/2020</small></p>
                            <p class="mb-0"><small><strong>Ngày hết hạn:</strong> 20/01/2023</small></p>
                        </div>
                        <div class="certificate-footer d-flex justify-content-between">
                            <span class="badge bg-danger">Hết hạn</span>
                            <button class="btn btn-link btn-sm p-0 text-danger" disabled>Gia hạn</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 text-center mt-2">
                    <p class="text-muted"><small>Chức năng quản lý chứng chỉ sẽ được cập nhật trong thời gian tới</small></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-line me-1"></i> Tổng quan kết quả kiểm tra
            </h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i> Thống kê kết quả kiểm tra sẽ hiển thị sau khi bạn hoàn thành ít nhất một bài kiểm tra.
            </div>
        </div>
    </div>
</div>
@endsection 