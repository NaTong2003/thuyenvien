@extends('layouts.app')

@section('title', 'Chứng chỉ của tôi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-certificate me-2"></i> Chứng chỉ của tôi
    </h1>

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
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số chứng chỉ
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $activeCertificates->count() + $expiredCertificates->count() + $revokedCertificates->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Chứng chỉ hiện còn hiệu lực
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $activeCertificates->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Bài thi đạt chuẩn (chưa cấp chứng chỉ)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $successfulTestsWithoutCertificate->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chứng chỉ đang hiệu lực -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-check-circle me-1"></i> Chứng chỉ hiện còn hiệu lực
            </h6>
        </div>
        <div class="card-body">
            @if($activeCertificates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mã chứng chỉ</th>
                                <th>Tên chứng chỉ</th>
                                <th>Bài kiểm tra</th>
                                <th>Ngày cấp</th>
                                <th>Hạn sử dụng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeCertificates as $certificate)
                                <tr>
                                    <td>{{ $certificate->certificate_number }}</td>
                                    <td>{{ $certificate->title }}</td>
                                    <td>
                                        @if($certificate->test)
                                            {{ $certificate->test->title }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($certificate->expiry_date)
                                            {{ $certificate->expiry_date->format('d/m/Y') }}
                                            @if($certificate->expiry_date->diffInDays(now()) < 30)
                                                <span class="badge bg-warning text-dark">Sắp hết hạn</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Không có hạn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('seafarer.certificates.show', $certificate->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('seafarer.certificates.download', $certificate->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Bạn chưa có chứng chỉ nào đang hiệu lực.
                </div>
            @endif
        </div>
    </div>

    <!-- Bài thi đạt chuẩn -->
    @if($successfulTestsWithoutCertificate->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-clipboard-check me-1"></i> Bài thi đạt chuẩn (chưa cấp chứng chỉ)
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-1"></i> Các bài thi dưới đây đã đạt điểm chuẩn và đủ điều kiện để cấp chứng chỉ. Bạn có thể liên hệ quản trị viên để được cấp chứng chỉ.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Bài kiểm tra</th>
                                <th>Ngày thi</th>
                                <th>Điểm số</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($successfulTestsWithoutCertificate as $attempt)
                                <tr>
                                    <td>{{ $attempt->test->title }}</td>
                                    <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="fw-bold text-success">{{ $attempt->score }}/100</td>
                                    <td>
                                        <a href="{{ route('seafarer.tests.result', $attempt->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Chứng chỉ đã hết hạn -->
    @if($expiredCertificates->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-calendar-times me-1"></i> Chứng chỉ đã hết hạn
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mã chứng chỉ</th>
                                <th>Tên chứng chỉ</th>
                                <th>Bài kiểm tra</th>
                                <th>Ngày cấp</th>
                                <th>Ngày hết hạn</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiredCertificates as $certificate)
                                <tr>
                                    <td>{{ $certificate->certificate_number }}</td>
                                    <td>{{ $certificate->title }}</td>
                                    <td>
                                        @if($certificate->test)
                                            {{ $certificate->test->title }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                                    <td>{{ $certificate->expiry_date ? $certificate->expiry_date->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('seafarer.certificates.show', $certificate->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('seafarer.certificates.download', $certificate->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Chứng chỉ đã bị thu hồi -->
    @if($revokedCertificates->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-ban me-1"></i> Chứng chỉ đã bị thu hồi
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mã chứng chỉ</th>
                                <th>Tên chứng chỉ</th>
                                <th>Bài kiểm tra</th>
                                <th>Ngày cấp</th>
                                <th>Lý do thu hồi</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revokedCertificates as $certificate)
                                <tr>
                                    <td>{{ $certificate->certificate_number }}</td>
                                    <td>{{ $certificate->title }}</td>
                                    <td>
                                        @if($certificate->test)
                                            {{ $certificate->test->title }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                                    <td>{{ $certificate->revocation_reason ?? 'Không có thông tin' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('seafarer.certificates.show', $certificate->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 