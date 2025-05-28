@extends('layouts.app')

@section('title', 'Chỉnh sửa Chứng chỉ - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-certificate me-2"></i> Chỉnh sửa Chứng chỉ
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Chứng chỉ</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chung</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Mã chứng chỉ:</label>
                        <p class="mb-0">{{ $certificate->certificate_number }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Thuyền viên:</label>
                        <p class="mb-0">{{ $certificate->user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Bài kiểm tra:</label>
                        <p class="mb-0">
                            @if($certificate->test)
                                {{ $certificate->test->title }}
                            @else
                                <span class="text-muted">Không có</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Ngày tạo:</label>
                        <p class="mb-0">{{ $certificate->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Người cấp:</label>
                        <p class="mb-0">
                            @if($certificate->issuer)
                                {{ $certificate->issuer->name }}
                            @else
                                <span class="text-muted">Không có thông tin</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Các trường có dấu <span class="text-danger">*</span> là bắt buộc.
                    </div>
                </div>
            </div>
            
            @if($certificate->certificate_file)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">File hiện tại</h6>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $fileExtension = pathinfo($certificate->certificate_file, PATHINFO_EXTENSION);
                            $isPdf = strtolower($fileExtension) === 'pdf';
                        @endphp
                        
                        @if($isPdf)
                            <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                        @else
                            <img src="{{ Storage::url($certificate->certificate_file) }}" 
                                alt="Certificate" class="img-fluid mb-3" 
                                style="max-height: 200px; border: 1px solid #ddd;">
                        @endif
                        
                        <div class="d-grid">
                            <a href="{{ Storage::url($certificate->certificate_file) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-eye me-1"></i> Xem file
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chứng chỉ</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.certificates.update', $certificate->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title" class="form-label fw-bold">Tên chứng chỉ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $certificate->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="issue_date" class="form-label fw-bold">Ngày cấp <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('issue_date') is-invalid @enderror" id="issue_date" name="issue_date" value="{{ old('issue_date', $certificate->issue_date->format('Y-m-d')) }}" required>
                                    @error('issue_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiry_date" class="form-label fw-bold">Ngày hết hạn</label>
                                    <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $certificate->expiry_date ? $certificate->expiry_date->format('Y-m-d') : '') }}">
                                    <small class="form-text text-muted">Để trống nếu chứng chỉ không có thời hạn</small>
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $certificate->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="expired" {{ old('status', $certificate->status) == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                        <option value="revoked" {{ old('status', $certificate->status) == 'revoked' ? 'selected' : '' }}>Thu hồi</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="certificate_file" class="form-label fw-bold">Thay đổi file chứng chỉ</label>
                                    <input type="file" class="form-control @error('certificate_file') is-invalid @enderror" id="certificate_file" name="certificate_file">
                                    <small class="form-text text-muted">File PDF, JPG, PNG (tối đa 10MB)</small>
                                    @error('certificate_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="revocation_reason_container" style="{{ old('status', $certificate->status) == 'revoked' ? '' : 'display: none;' }}">
                            <label for="revocation_reason" class="form-label fw-bold">Lý do thu hồi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('revocation_reason') is-invalid @enderror" id="revocation_reason" name="revocation_reason" rows="3">{{ old('revocation_reason', $certificate->revocation_reason) }}</textarea>
                            @error('revocation_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Mô tả chứng chỉ</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $certificate->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.certificates.show', $certificate->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử kiểm tra</h6>
                </div>
                <div class="card-body">
                    @if($testAttempts && $testAttempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bài kiểm tra</th>
                                        <th>Ngày thi</th>
                                        <th>Điểm số</th>
                                        <th>Kết quả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($testAttempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->test->title }}</td>
                                            <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $attempt->score }}/100</td>
                                            <td>
                                                @if($attempt->isPassed())
                                                    <span class="badge bg-success">Đạt</span>
                                                @else
                                                    <span class="badge bg-danger">Không đạt</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> Không có lịch sử kiểm tra.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Hiển thị/ẩn ô lý do thu hồi khi thay đổi trạng thái
        $('#status').change(function() {
            if ($(this).val() === 'revoked') {
                $('#revocation_reason_container').show();
                $('#revocation_reason').prop('required', true);
            } else {
                $('#revocation_reason_container').hide();
                $('#revocation_reason').prop('required', false);
            }
        });
    });
</script>
@endsection 