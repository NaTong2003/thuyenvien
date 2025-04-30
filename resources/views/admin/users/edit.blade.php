@extends('layouts.app')

@section('title', 'Chỉnh sửa Thuyền viên - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-edit me-2"></i> Chỉnh sửa Thuyền viên: {{ $user->name }}
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Thuyền viên</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin thuyền viên</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Thông tin cá nhân</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="seafarer_id" class="form-label">Mã thuyền viên</label>
                                    <input type="text" class="form-control @error('seafarer_id') is-invalid @enderror" id="seafarer_id" name="seafarer_id" value="{{ old('seafarer_id', $user->seafarer_id) }}">
                                    @error('seafarer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <hr>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    <div class="form-text">Để trống nếu không muốn đổi mật khẩu.</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Thông tin chuyên môn</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="position_id" class="form-label">Chức danh</label>
                                    <select class="form-select @error('position_id') is-invalid @enderror" id="position_id" name="position_id">
                                        <option value="">-- Chọn chức danh --</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}" {{ old('position_id', $user->thuyenVien->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="ship_type_id" class="form-label">Loại tàu</label>
                                    <select class="form-select @error('ship_type_id') is-invalid @enderror" id="ship_type_id" name="ship_type_id">
                                        <option value="">-- Chọn loại tàu --</option>
                                        @foreach($shipTypes as $shipType)
                                            <option value="{{ $shipType->id }}" {{ old('ship_type_id', $user->thuyenVien->ship_type_id ?? '') == $shipType->id ? 'selected' : '' }}>
                                                {{ $shipType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ship_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="experience" class="form-label">Kinh nghiệm (năm)</label>
                                    <input type="number" class="form-control @error('experience') is-invalid @enderror" id="experience" name="experience" value="{{ old('experience', $user->thuyenVien->experience ?? '') }}" min="0" max="50">
                                    @error('experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại chi tiết
                    </a>
                    <div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Hiển thị cảnh báo khi thay đổi mật khẩu
        $('#password').on('input', function() {
            if ($(this).val().length > 0 && $(this).val().length < 8) {
                $(this).addClass('is-invalid');
                $(this).after('<div class="invalid-feedback">Mật khẩu phải có ít nhất 8 ký tự</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });
        
        // Kiểm tra xác nhận mật khẩu
        $('#password_confirmation').on('input', function() {
            if ($(this).val() !== $('#password').val()) {
                $(this).addClass('is-invalid');
                $(this).after('<div class="invalid-feedback">Mật khẩu xác nhận không khớp</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });
    });
</script>
@endsection 