@extends('layouts.app')

@section('title', 'Chỉnh sửa Danh mục - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa Danh mục</h1>
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Báo cáo & Thống kê</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Chỉnh sửa danh mục: {{ $category->name }}</h6>
                    <div>
                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye mr-1"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Tên danh mục <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color">Màu sắc</label>
                                    <input type="color" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $category->color) }}">
                                    <small class="form-text text-muted">Màu sắc hiển thị cho danh mục</small>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="icon">Icon</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i id="icon-preview" class="{{ $category->icon ?? 'fas fa-folder' }}"></i></span>
                                        </div>
                                        <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="fas fa-folder">
                                    </div>
                                    <small class="form-text text-muted">Nhập tên icon từ <a href="https://fontawesome.com/icons" target="_blank">Font Awesome</a></small>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-right">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Vùng nguy hiểm</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="text-danger">Xóa danh mục này</h5>
                            <p class="mb-0">Khi xóa danh mục, tất cả liên kết với bài kiểm tra và câu hỏi sẽ bị mất. Hành động này không thể hoàn tác.</p>
                        </div>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt mr-1"></i> Xóa danh mục
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cập nhật icon preview khi thay đổi input
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('icon-preview');
        
        iconInput.addEventListener('input', function() {
            iconPreview.className = this.value || 'fas fa-folder';
        });
        
        // Cập nhật preview ban đầu
        iconPreview.className = iconInput.value || 'fas fa-folder';
    });
</script>
@endsection 