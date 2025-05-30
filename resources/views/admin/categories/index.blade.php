@extends('layouts.app')

@section('title', 'Quản lý Danh mục - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .category-card {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        height: 100%;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .category-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 0.5rem;
        font-size: 1.5rem;
        margin-right: 1rem;
    }
    
    .category-item {
        padding: 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state-icon {
        font-size: 3.5rem;
        color: #e0e0e0;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Danh mục</h1>
        <div class="d-flex">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-light btn-sm mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Thêm danh mục
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-light py-2 px-3 mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Quản lý Danh mục</li>
        </ol>
    </nav>
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tags mr-1"></i> Danh sách danh mục
                    </h6>
                    <div>
                        <span class="badge badge-info">Tổng số: {{ $categories->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($categories as $category)
                            <div class="col-md-6 col-lg-4">
                                <div class="card category-card shadow-sm border-left-{{ $category->color ? '' : 'primary' }}" 
                                     style="{{ $category->color ? 'border-left: 4px solid '.$category->color.';' : '' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="category-icon bg-light" style="{{ $category->color ? 'color: '.$category->color.';' : 'color: #4e73df;' }}">
                                                    <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                                                </div>
                                                <div>
                                                    <h5 class="card-title mb-0">{{ $category->name }}</h5>
                                                    <div class="small text-muted">
                                                        <span><i class="fas fa-question-circle mr-1"></i> {{ $category->questions_count }} câu hỏi</span>
                                                        <span class="ml-2"><i class="fas fa-file-alt mr-1"></i> {{ $category->tests_count }} bài kiểm tra</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.categories.show', $category->id) }}">
                                                            <i class="fas fa-eye mr-1"></i> Chi tiết
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.categories.edit', $category->id) }}">
                                                            <i class="fas fa-edit mr-1"></i> Chỉnh sửa
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash-alt mr-1"></i> Xóa
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        @if($category->description)
                                            <p class="card-text mb-3 text-muted small">
                                                {{ Str::limit($category->description, 100) }}
                                            </p>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('admin.questions.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-question-circle mr-1"></i> Câu hỏi
                                            </a>
                                            <a href="{{ route('admin.tests.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-file-alt mr-1"></i> Bài kiểm tra
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <h4>Chưa có danh mục nào</h4>
                                    <p class="text-muted">Hãy tạo danh mục đầu tiên để phân loại câu hỏi và bài kiểm tra của bạn.</p>
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus mr-1"></i> Thêm danh mục mới
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Phần giải thích về danh mục -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle mr-1"></i> Về Danh mục
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-tag text-primary mr-2"></i>Danh mục là gì?</h5>
                            <p class="card-text">Danh mục giúp phân loại câu hỏi và bài kiểm tra theo lĩnh vực, chủ đề hoặc cấp độ khác nhau.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-lightbulb text-warning mr-2"></i>Tại sao cần danh mục?</h5>
                            <p class="card-text">Việc phân loại giúp dễ dàng quản lý, tìm kiếm câu hỏi và tạo bài kiểm tra có chủ đề nhất quán.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-cogs text-success mr-2"></i>Quản lý danh mục</h5>
                            <p class="card-text">Bạn có thể thêm, sửa, xóa danh mục và theo dõi số lượng câu hỏi, bài kiểm tra trong mỗi danh mục.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
