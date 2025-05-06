@extends('layouts.app')

@section('title', 'Chi tiết Chứng chỉ - ' . $certificate->certificate_number)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-certificate me-2"></i> Chi tiết Chứng chỉ
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Chứng chỉ</a></li>
            <li class="breadcrumb-item active">{{ $certificate->certificate_number }}</li>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="certificate-header">
                    <div class="certificate-title">
                        <h2>{{ $certificate->title }}</h2>
                        <p class="certificate-number">{{ $certificate->certificate_number }}</p>
                    </div>
                    <div class="certificate-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    </div>
                </div>
                
                <div class="certificate-body">
                    <div class="certificate-content">
                        <p class="issued-to">Được cấp cho:</p>
                        <h3 class="seafarer-name">{{ $certificate->user->name }}</h3>
                        <p class="certificate-description">{{ $certificate->description }}</p>
                        
                        <div class="certificate-details">
                            <div class="detail-item">
                                <i class="fas fa-calendar-check"></i>
                                <div>
                                    <span class="detail-label">Ngày cấp:</span>
                                    <span class="detail-value">{{ $certificate->issue_date->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-calendar-times"></i>
                                <div>
                                    <span class="detail-label">Ngày hết hạn:</span>
                                    <span class="detail-value">
                                        @if($certificate->expiry_date)
                                            {{ $certificate->expiry_date->format('d/m/Y') }}
                                            @if($certificate->isExpired())
                                                <span class="badge bg-danger ms-2">Đã hết hạn</span>
                                            @elseif($certificate->expiry_date->diffInDays(now()) < 30)
                                                <span class="badge bg-warning text-dark ms-2">Sắp hết hạn</span>
                                            @endif
                                        @else
                                            <em>Không có thời hạn</em>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-tasks"></i>
                                <div>
                                    <span class="detail-label">Bài kiểm tra:</span>
                                    <span class="detail-value">
                                        @if($certificate->test)
                                            <a href="{{ route('admin.tests.show', $certificate->test_id) }}">
                                                {{ $certificate->test->title }}
                                            </a>
                                        @else
                                            <em>N/A</em>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-chart-line"></i>
                                <div>
                                    <span class="detail-label">Điểm số:</span>
                                    <span class="detail-value">
                                        @if($certificate->testAttempt)
                                            <span class="text-success fw-bold">{{ $certificate->testAttempt->score }}/100</span>
                                        @else
                                            <em>N/A</em>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-user-shield"></i>
                                <div>
                                    <span class="detail-label">Cấp bởi:</span>
                                    <span class="detail-value">
                                        {{ $certificate->issuer?->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-tag"></i>
                                <div>
                                    <span class="detail-label">Trạng thái:</span>
                                    <span class="detail-value">
                                        @if($certificate->status == 'active')
                                            <span class="badge bg-success">Hoạt động</span>
                                        @elseif($certificate->status == 'expired')
                                            <span class="badge bg-secondary">Hết hạn</span>
                                        @elseif($certificate->status == 'revoked')
                                            <span class="badge bg-danger">Đã thu hồi</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        @if($certificate->status == 'revoked')
                            <div class="revocation-reason">
                                <h5>Lý do thu hồi:</h5>
                                <p>{{ $certificate->revocation_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="certificate-footer">
                    <div class="signature">
                        <div class="signature-line"></div>
                        <p>Chữ ký người cấp</p>
                    </div>
                    <div class="stamp">
                        <div class="stamp-circle">Dấu</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.certificates.edit', $certificate->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.certificates.pdf', $certificate->id) }}" class="btn btn-secondary">
                            <i class="fas fa-file-pdf me-1"></i> Tạo file PDF
                        </a>
                        
                        @if($certificate->status == 'active')
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#revokeModal">
                                <i class="fas fa-ban me-1"></i> Thu hồi chứng chỉ
                            </button>
                        @endif
                        
                        <form action="{{ route('admin.certificates.destroy', $certificate->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chứng chỉ này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-1"></i> Xóa chứng chỉ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            @if($certificate->certificate_file)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">File chứng chỉ</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @php
                                $extension = pathinfo($certificate->certificate_file, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                            @endphp
                            
                            @if($isImage)
                                <img src="{{ asset('storage/' . $certificate->certificate_file) }}" class="img-fluid mb-3" alt="Certificate">
                            @else
                                <div class="pdf-preview mb-3">
                                    <i class="fas fa-file-pdf fa-5x text-danger"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="d-grid">
                            <a href="{{ asset('storage/' . $certificate->certificate_file) }}" class="btn btn-info" target="_blank">
                                <i class="fas fa-download me-1"></i> Tải xuống file
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thuyền viên</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-container me-3">
                            <div class="avatar bg-primary text-white">
                                {{ substr($certificate->user->name, 0, 1) }}
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $certificate->user->name }}</h5>
                            <p class="text-muted mb-0">
                                {{ $certificate->user->thuyenVien?->position?->name ?? 'Chưa có chức danh' }}
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%">Email:</th>
                                <td>{{ $certificate->user->email }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td>{{ $certificate->user->phone ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.users.show', $certificate->user_id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-user me-1"></i> Xem hồ sơ
                        </a>
                        <a href="{{ route('admin.certificates.test.history', $certificate->user_id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-history me-1"></i> Lịch sử bài thi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thu hồi chứng chỉ -->
<div class="modal fade" id="revokeModal" tabindex="-1" aria-labelledby="revokeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.certificates.update', $certificate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="title" value="{{ $certificate->title }}">
                <input type="hidden" name="issue_date" value="{{ $certificate->issue_date->format('Y-m-d') }}">
                <input type="hidden" name="expiry_date" value="{{ $certificate->expiry_date ? $certificate->expiry_date->format('Y-m-d') : '' }}">
                <input type="hidden" name="status" value="revoked">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="revokeModalLabel">Thu hồi chứng chỉ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="revocation_reason" class="form-label">Lý do thu hồi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="revocation_reason" name="revocation_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Thu hồi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .certificate-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e3e6f0;
        background-color: #f8f9fc;
    }
    
    .certificate-title h2 {
        font-size: 24px;
        margin-bottom: 5px;
        color: #2e59d9;
    }
    
    .certificate-number {
        font-size: 14px;
        color: #858796;
        font-family: monospace;
    }
    
    .certificate-logo img {
        height: 60px;
    }
    
    .certificate-body {
        padding: 30px;
    }
    
    .issued-to {
        text-align: center;
        font-size: 16px;
        color: #858796;
        margin-bottom: 5px;
    }
    
    .seafarer-name {
        text-align: center;
        font-size: 28px;
        margin-bottom: 20px;
        color: #5a5c69;
    }
    
    .certificate-description {
        text-align: center;
        margin-bottom: 30px;
        line-height: 1.6;
        color: #5a5c69;
    }
    
    .certificate-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .detail-item {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }
    
    .detail-item i {
        color: #4e73df;
    }
    
    .detail-label {
        display: block;
        font-weight: bold;
        color: #5a5c69;
    }
    
    .revocation-reason {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8d7da;
        border-radius: 5px;
        border-left: 5px solid #e74a3b;
    }
    
    .certificate-footer {
        display: flex;
        justify-content: space-evenly;
        padding: 20px 50px 40px;
    }
    
    .signature {
        text-align: center;
    }
    
    .signature-line {
        width: 200px;
        height: 1px;
        background-color: #858796;
        margin-bottom: 10px;
    }
    
    .stamp {
        text-align: center;
    }
    
    .stamp-circle {
        width: 80px;
        height: 80px;
        border: 2px dashed #e74a3b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e74a3b;
    }
    
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
    
    .pdf-preview {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 150px;
        background-color: #f8f9fc;
        border-radius: 5px;
    }
</style>
@endsection 