@extends('layouts.app')

@section('title', 'Cấp chứng chỉ từ bài thi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-certificate me-2"></i> Cấp chứng chỉ từ bài thi
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Chứng chỉ</a></li>
            <li class="breadcrumb-item active">Cấp chứng chỉ</li>
        </ol>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin bài thi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%">Bài kiểm tra:</th>
                                <td>
                                    <a href="{{ route('admin.tests.show', $attempt->test_id) }}">
                                        {{ $attempt->test->title }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Thuyền viên:</th>
                                <td>
                                    <a href="{{ route('admin.users.show', $attempt->user_id) }}">
                                        {{ $attempt->user->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Thời gian làm bài:</th>
                                <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Điểm đạt được:</th>
                                <td class="fw-bold text-success">{{ $attempt->score }}/100</td>
                            </tr>
                            <tr>
                                <th>Điểm chuẩn:</th>
                                <td>{{ $attempt->test->passing_score ?? 50 }}/100</td>
                            </tr>
                            <tr>
                                <th>Kết quả:</th>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Đạt
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.reports.attempt', $attempt->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> Xem chi tiết bài thi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chứng chỉ</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.certificates.store.from.attempt', $attempt->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label fw-bold">Tên chứng chỉ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $attempt->test->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="issue_date" class="form-label fw-bold">Ngày cấp <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('issue_date') is-invalid @enderror" id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                    @error('issue_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiry_date" class="form-label fw-bold">Ngày hết hạn</label>
                                    <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime('+2 years'))) }}">
                                    <small class="form-text text-muted">Để trống nếu chứng chỉ không có thời hạn</small>
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="certificate_file" class="form-label fw-bold">File chứng chỉ</label>
                                    <input type="file" class="form-control @error('certificate_file') is-invalid @enderror" id="certificate_file" name="certificate_file">
                                    <small class="form-text text-muted">File PDF, JPG, PNG (tối đa 10MB)</small>
                                    @error('certificate_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Mô tả chứng chỉ</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', 'Chứng chỉ này được cấp dựa trên kết quả bài kiểm tra ' . $attempt->test->title . ' hoàn thành ngày ' . $attempt->created_at->format('d/m/Y') . ' với số điểm ' . $attempt->score . '/100.') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.certificates.test.history', $attempt->user_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-certificate me-1"></i> Cấp chứng chỉ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 