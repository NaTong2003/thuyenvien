@extends('layouts.app')

@section('title', 'Tạo Chứng chỉ mới - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-certificate me-2"></i> Tạo Chứng chỉ mới
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Chứng chỉ</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
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
                    <h6 class="m-0 font-weight-bold text-primary">Hướng dẫn</h6>
                </div>
                <div class="card-body">
                    <p>Điền thông tin chứng chỉ mới bạn muốn cấp cho thuyền viên.</p>
                    <p>Các trường có dấu <span class="text-danger">*</span> là bắt buộc.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Bạn có thể cấp chứng chỉ mà không cần liên kết với bất kỳ bài kiểm tra nào, hoặc chọn một bài kiểm tra cụ thể.
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
                    <form action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="form-label fw-bold">Thuyền viên <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">-- Chọn thuyền viên --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="test_id" class="form-label fw-bold">Bài kiểm tra</label>
                                    <select class="form-select @error('test_id') is-invalid @enderror" id="test_id" name="test_id">
                                        <option value="">-- Không liên kết bài kiểm tra --</option>
                                        @foreach($tests as $test)
                                            <option value="{{ $test->id }}" {{ old('test_id') == $test->id ? 'selected' : '' }}>
                                                {{ $test->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('test_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title" class="form-label fw-bold">Tên chứng chỉ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
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
                                    <input type="date" class="form-control @error('issue_date') is-invalid @enderror" id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                    @error('issue_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
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
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', 'Chứng nhận thuyền viên đã hoàn thành đánh giá và đạt yêu cầu của hệ thống đánh giá năng lực thuyền viên.') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Lưu chứng chỉ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Khi chọn test, thay đổi tiêu đề chứng chỉ tương ứng
        $('#test_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                const testTitle = selectedOption.text().trim();
                $('#title').val('Chứng chỉ ' + testTitle);
            }
        });
    });
</script>
@endsection 