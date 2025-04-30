@extends('layouts.app')

@section('title', 'Chỉnh sửa hồ sơ - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .profile-header {
        background-color: var(--primary-color);
        padding: 1.5rem 0;
        color: white;
        margin-bottom: 2rem;
    }
    
    .profile-header h1 {
        margin-bottom: 0.5rem;
    }
    
    .avatar-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 1rem;
    }
    
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid rgba(255, 255, 255, 0.5);
        overflow: hidden;
    }
    
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-edit {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #fff;
        text-align: center;
        line-height: 40px;
        font-size: 18px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        cursor: pointer;
    }
    
    .section-heading {
        border-bottom: 2px solid #e3e6f0;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .form-label {
        font-weight: 500;
    }
    
    .password-toggle {
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="profile-header text-center">
    <div class="container">
        <h1>Chỉnh sửa hồ sơ</h1>
        <p class="mb-0">Cập nhật thông tin cá nhân và thông tin chuyên môn của bạn</p>
    </div>
</div>

<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <i class="fas fa-exclamation-triangle me-1"></i> Vui lòng kiểm tra lại thông tin:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('seafarer.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row mb-5">
            <div class="col-lg-3 mb-4 mb-lg-0">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <div class="avatar-container mb-4">
                            <div class="profile-avatar">
                                <img src="https://via.placeholder.com/150" alt="{{ $user->name }}">
                            </div>
                            <div class="avatar-edit">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <h5>{{ $user->name }}</h5>
                        <p class="text-muted mb-0">
                            @if($thuyenVien && $thuyenVien->position)
                                {{ $thuyenVien->position->name }}
                            @else
                                Chưa cập nhật chức danh
                            @endif
                        </p>
                        <input type="file" name="avatar" id="avatar-upload" class="d-none">
                    </div>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="section-heading">
                            <i class="fas fa-user me-1"></i> Thông tin cá nhân
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seafarer_id" class="form-label">Mã thuyền viên (Seafarer ID)</label>
                                    <input type="text" class="form-control" id="seafarer_id" name="seafarer_id" value="{{ old('seafarer_id', $user->seafarer_id) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="section-heading">
                            <i class="fas fa-ship me-1"></i> Thông tin chuyên môn
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="position_id" class="form-label">Chức danh</label>
                                    <select class="form-select" id="position_id" name="position_id">
                                        <option value="">-- Chọn chức danh --</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}" {{ old('position_id', $thuyenVien->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ship_type_id" class="form-label">Loại tàu</label>
                                    <select class="form-select" id="ship_type_id" name="ship_type_id">
                                        <option value="">-- Chọn loại tàu --</option>
                                        @foreach($shipTypes as $shipType)
                                            <option value="{{ $shipType->id }}" {{ old('ship_type_id', $thuyenVien->ship_type_id ?? '') == $shipType->id ? 'selected' : '' }}>
                                                {{ $shipType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="experience" class="form-label">Kinh nghiệm (năm)</label>
                                    <input type="number" class="form-control" id="experience" name="experience" value="{{ old('experience', $thuyenVien->experience ?? '') }}" min="0" max="50">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="section-heading">
                            <i class="fas fa-lock me-1"></i> Đổi mật khẩu
                            <small class="text-muted ms-2">(để trống nếu không muốn thay đổi)</small>
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                        <span class="input-group-text password-toggle" data-target="current_password">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <span class="input-group-text password-toggle" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                        <span class="input-group-text password-toggle" data-target="password_confirmation">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('seafarer.profile.show') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý ẩn/hiện mật khẩu
        $('.password-toggle').click(function() {
            const targetId = $(this).data('target');
            const passwordInput = $('#' + targetId);
            const icon = $(this).find('i');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        
        // Xử lý upload ảnh đại diện
        $('.avatar-edit').click(function() {
            $('#avatar-upload').click();
        });
        
        $('#avatar-upload').change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('.profile-avatar img').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection 