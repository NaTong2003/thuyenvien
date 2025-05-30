@extends('layouts.app')

@section('title', 'Chi tiết Danh mục - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .stats-card {
        border-radius: 8px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .stats-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
    }
    
    .list-item {
        padding: 0.75rem 1rem;
        border-left: 3px solid;
        margin-bottom: 0.75rem;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .list-item:hover {
        transform: translateX(5px);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết Danh mục</h1>
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Báo cáo & Thống kê</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
            <li class="breadcrumb-item active">Chi tiết</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Thông tin cơ bản -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="{{ $category->icon ?? 'fas fa-folder' }} mr-1" style="color: {{ $category->color ?? '#4e73df' }}"></i>
                        {{ $category->name }}
                    </h6>
                    <div>
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit mr-1"></i> Chỉnh sửa
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Thông tin danh mục</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 150px;">Tên danh mục</th>
                                    <td>{{ $category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Slug</th>
                                    <td><code>{{ $category->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Mô tả</th>
                                    <td>{{ $category->description ?: 'Chưa có mô tả' }}</td>
                                </tr>
                                <tr>
                                    <th>Màu sắc</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 20px; height: 20px; background-color: {{ $category->color }}; border-radius: 4px; margin-right: 10px;"></div>
                                            <code>{{ $category->color }}</code>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Icon</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $category->icon }} mr-2" style="font-size: 20px; color: {{ $category->color }}"></i>
                                            <code>{{ $category->icon }}</code>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo</th>
                                    <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Cập nhật cuối</th>
                                    <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Thống kê</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="stats-card text-center p-3" style="border-left: 4px solid {{ $category->color }}">
                                        <div class="stats-icon text-primary">
                                            <i class="fas fa-question-circle"></i>
                                        </div>
                                        <div class="stats-number">{{ $questionCount }}</div>
                                        <div class="stats-label">Câu hỏi</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stats-card text-center p-3" style="border-left: 4px solid {{ $category->color }}">
                                        <div class="stats-icon text-success">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="stats-number">{{ $testCount }}</div>
                                        <div class="stats-label">Bài kiểm tra</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Biểu đồ phân bố câu hỏi theo độ khó -->
                            <div class="card shadow-sm mt-3">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Phân bố câu hỏi theo độ khó</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="questionDifficultyChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Câu hỏi trong danh mục -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-question-circle mr-1"></i> Câu hỏi trong danh mục ({{ $questionCount }})
                            </h6>
                            <div>
                                <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Thêm câu hỏi
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($category->questions->isEmpty())
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Chưa có câu hỏi nào trong danh mục này.
                                </div>
                            @else
                                <div class="list-group">
                                    @foreach($category->questions->take(5) as $question)
                                        <div class="list-item" style="border-left-color: {{ $category->color }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ Str::limit($question->content, 50) }}</h6>
                                                    <div class="small text-muted">
                                                        <span class="badge badge-primary">{{ $question->type }}</span>
                                                        <span class="badge badge-secondary">{{ $question->difficulty }}</span>
                                                    </div>
                                                </div>
                                                <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($questionCount > 5)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.questions.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-list mr-1"></i> Xem tất cả {{ $questionCount }} câu hỏi
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-file-alt mr-1"></i> Bài kiểm tra trong danh mục ({{ $testCount }})
                            </h6>
                            <div>
                                <a href="{{ route('admin.tests.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Thêm bài kiểm tra
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($category->tests->isEmpty())
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Chưa có bài kiểm tra nào trong danh mục này.
                                </div>
                            @else
                                <div class="list-group">
                                    @foreach($category->tests->take(5) as $test)
                                        <div class="list-item" style="border-left-color: {{ $category->color }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $test->title }}</h6>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-clock mr-1"></i> {{ $test->duration }} phút
                                                        <i class="fas fa-trophy ml-2 mr-1"></i> {{ $test->passing_score }}% để đạt
                                                    </div>
                                                </div>
                                                <a href="{{ route('admin.tests.show', $test->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($testCount > 5)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.tests.index', ['category' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-list mr-1"></i> Xem tất cả {{ $testCount }} bài kiểm tra
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
        </a>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Biểu đồ phân bố câu hỏi theo độ khó
        const difficultyData = @json($questionsByDifficulty);
        
        if (difficultyData && difficultyData.length > 0) {
            new Chart(document.getElementById('questionDifficultyChart'), {
                type: 'pie',
                data: {
                    labels: difficultyData.map(item => item.label),
                    datasets: [{
                        data: difficultyData.map(item => item.count),
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                            '#5a5c69', '#6f42c1', '#fd7e14', '#20c997', '#67c7db'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    });
</script>
@endsection 