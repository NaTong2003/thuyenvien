@extends('layouts.app')

@section('title', 'Chi tiết Chứng chỉ - ' . $certificate->certificate_number)

@section('css')
<style>
    .certificate-container {
        border: 10px solid #1a73e8;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 50px;
        box-sizing: border-box;
        position: relative;
        margin-bottom: 2rem;
    }
    
    .certificate-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #1a73e8;
        padding-bottom: 20px;
    }
    
    .certificate-title {
        font-size: 30px;
        font-weight: bold;
        color: #1a73e8;
        margin-bottom: 10px;
    }
    
    .certificate-subtitle {
        font-size: 18px;
        color: #555;
    }
    
    .certificate-number {
        font-size: 14px;
        color: #777;
        margin-top: 5px;
    }
    
    .certificate-body {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .issued-to-label {
        font-size: 16px;
        color: #555;
        margin-bottom: 10px;
    }
    
    .seafarer-name {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }
    
    .certificate-description {
        font-size: 16px;
        color: #555;
        line-height: 1.6;
        margin-bottom: 30px;
        text-align: justify;
    }
    
    .certificate-details {
        margin-bottom: 40px;
    }
    
    .certificate-details table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .certificate-details th {
        text-align: left;
        padding: 10px;
        font-weight: bold;
        color: #555;
        width: 40%;
        border-bottom: 1px solid #ddd;
    }
    
    .certificate-details td {
        text-align: left;
        padding: 10px;
        color: #333;
        border-bottom: 1px solid #ddd;
    }
    
    .certificate-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
    }
    
    .signature-container {
        text-align: center;
        width: 200px;
    }
    
    .signature-line {
        border-top: 1px solid #555;
        margin-bottom: 10px;
    }
    
    .signature-title {
        font-size: 16px;
        color: #555;
    }
    
    .stamp-container {
        text-align: center;
        width: 100px;
    }
    
    .stamp {
        width: 100px;
        height: 100px;
        border: 2px dashed #e74a3b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #e74a3b;
    }
    
    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.05;
        font-size: 100px;
        color: #1a73e8;
        z-index: -1;
        transform: translate(-50%, -50%) rotate(-45deg);
    }
    
    .status-revoked, .status-expired {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 80px;
        font-weight: bold;
        opacity: 0.2;
        z-index: 10;
        padding: 10px 20px;
        border-radius: 10px;
    }
    
    .status-revoked {
        color: #e74a3b;
        border: 10px solid #e74a3b;
    }
    
    .status-expired {
        color: #858796;
        border: 10px solid #858796;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-certificate me-2"></i> Chi tiết Chứng chỉ
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('seafarer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('seafarer.certificates.index') }}">Chứng chỉ của tôi</a></li>
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
        <div class="col-lg-9">
            <div class="certificate-container">
                @if($certificate->status == 'revoked')
                    <div class="status-revoked">ĐÃ THU HỒI</div>
                @elseif($certificate->isExpired())
                    <div class="status-expired">HẾT HẠN</div>
                @endif
                
                <div class="watermark">CHỨNG CHỈ</div>
                
                <div class="certificate-header">
                    <div class="certificate-title">{{ $certificate->title }}</div>
                    <div class="certificate-subtitle">Hệ thống Đánh giá Năng lực Thuyền viên</div>
                    <div class="certificate-number">Số: {{ $certificate->certificate_number }}</div>
                </div>
                
                <div class="certificate-body">
                    <div class="issued-to-label">Được cấp cho:</div>
                    <div class="seafarer-name">{{ Auth::user()->name }}</div>
                    <div class="certificate-description">{{ $certificate->description }}</div>
                </div>
                
                <div class="certificate-details">
                    <table>
                        <tr>
                            <th>Ngày cấp:</th>
                            <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                        </tr>
                        @if($certificate->expiry_date)
                            <tr>
                                <th>Ngày hết hạn:</th>
                                <td>
                                    {{ $certificate->expiry_date->format('d/m/Y') }}
                                    @if($certificate->isExpired())
                                        <span class="badge bg-danger ms-2">Đã hết hạn</span>
                                    @elseif($certificate->expiry_date->diffInDays(now()) < 30)
                                        <span class="badge bg-warning text-dark ms-2">Sắp hết hạn</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if($certificate->test)
                            <tr>
                                <th>Bài kiểm tra:</th>
                                <td>{{ $certificate->test->title }}</td>
                            </tr>
                        @endif
                        @if($certificate->testAttempt)
                            <tr>
                                <th>Điểm số:</th>
                                <td>{{ $certificate->testAttempt->score }}/100</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                @if($certificate->status == 'active')
                                    @if($certificate->expiry_date && $certificate->expiry_date->isPast())
                                        <span class="badge bg-secondary">Hết hạn</span>
                                    @else
                                        <span class="badge bg-success">Hoạt động</span>
                                    @endif
                                @elseif($certificate->status == 'expired')
                                    <span class="badge bg-secondary">Hết hạn</span>
                                @elseif($certificate->status == 'revoked')
                                    <span class="badge bg-danger">Đã thu hồi</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                
                @if($certificate->status == 'revoked')
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">Lý do thu hồi:</h5>
                        <p class="mb-0">{{ $certificate->revocation_reason ?? 'Không có thông tin' }}</p>
                    </div>
                @endif
                
                <div class="certificate-footer">
                    <div class="signature-container">
                        <div class="signature-line"></div>
                        <div class="signature-title">Chữ ký người cấp</div>
                        <div>{{ $certificate->issuer?->name ?? '' }}</div>
                    </div>
                    
                    <div class="stamp-container">
                        <div class="stamp">Dấu</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('seafarer.certificates.download', $certificate->id) }}" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i> Tải xuống PDF
                        </a>
                        
                        @if($certificate->test && $certificate->testAttempt)
                            <a href="{{ route('seafarer.tests.result', $certificate->testAttempt->id) }}" class="btn btn-info">
                                <i class="fas fa-eye me-1"></i> Xem kết quả bài thi
                            </a>
                        @endif
                        
                        <a href="{{ route('seafarer.certificates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
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
                                <div class="d-flex justify-content-center mb-3">
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('printCertificate')?.addEventListener('click', function() {
            window.print();
        });
    });
</script>
@endsection 