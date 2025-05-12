@extends('layouts.app')

@section('title', 'Quản lý Bài Kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .test-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .filter-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #eaeaea;
    }
    
    .test-type-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        border-radius: 20px;
    }
    
    .difficulty-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        border-radius: 20px;
    }
    
    .difficulty-easy {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .difficulty-medium {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .difficulty-hard {
        background-color: #f8d7da;
        color: #842029;
    }
    
    .test-status {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    .status-active {
        background-color: #28a745;
    }
    
    .status-draft {
        background-color: #6c757d;
    }
    
    .status-inactive {
        background-color: #dc3545;
    }
    
    .test-stats {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
    }
    
    .pagination {
        margin-bottom: 0;
    }
    
    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .page-link {
        color: var(--primary-color);
    }
    
    .page-link:hover {
        color: var(--primary-color-dark);
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="test-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Quản lý Bài Kiểm tra</h2>
                <p class="mb-0">Quản lý tất cả các bài kiểm tra trong hệ thống</p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item active text-white-50">Bài Kiểm tra</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
            <a href="javascript:void(0)" id="clearFilters" class="text-secondary font-weight-medium">
                <i class="fas fa-undo me-1"></i>Xóa bộ lọc
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tests.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Theo tiêu đề, mã bài...">
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="is_active" class="form-label">Trạng thái</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="type" class="form-label">Loại bài kiểm tra</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tất cả loại</option>
                            <option value="certification" {{ request('type') == 'certification' ? 'selected' : '' }}>Chứng chỉ</option>
                            <option value="assessment" {{ request('type') == 'assessment' ? 'selected' : '' }}>Đánh giá định kỳ</option>
                            <option value="placement" {{ request('type') == 'placement' ? 'selected' : '' }}>Xếp loại</option>
                            <option value="practice" {{ request('type') == 'practice' ? 'selected' : '' }}>Luyện tập</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="difficulty" class="form-label">Độ khó</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Tất cả độ khó</option>
                            <option value="Dễ" {{ request('difficulty') == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                            <option value="Trung bình" {{ request('difficulty') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                            <option value="Khó" {{ request('difficulty') == 'Khó' ? 'selected' : '' }}>Khó</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="position_id" class="form-label">Chức danh</label>
                        <select class="form-select" id="position_id" name="position_id">
                            <option value="">Tất cả chức danh</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="ship_type_id" class="form-label">Loại tàu</label>
                        <select class="form-select" id="ship_type_id" name="ship_type_id">
                            <option value="">Tất cả loại tàu</option>
                            @foreach($shipTypes as $shipType)
                                <option value="{{ $shipType->id }}" {{ request('ship_type_id') == $shipType->id ? 'selected' : '' }}>{{ $shipType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="sort" class="form-label">Sắp xếp theo</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Mới nhất trước</option>
                            <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Cũ nhất trước</option>
                            <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Tiêu đề (A-Z)</option>
                            <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Tiêu đề (Z-A)</option>
                            <option value="attempts_desc" {{ request('sort') == 'attempts_desc' ? 'selected' : '' }}>Lượt thi nhiều nhất</option>
                            <option value="duration_asc" {{ request('sort') == 'duration_asc' ? 'selected' : '' }}>Thời gian ngắn nhất</option>
                            <option value="duration_desc" {{ request('sort') == 'duration_desc' ? 'selected' : '' }}>Thời gian dài nhất</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 d-flex align-items-end mb-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Lọc
                        </button>
                        <a href="{{ route('admin.tests.create') }}" class="btn btn-success me-2">
                            <i class="fas fa-plus me-1"></i>Thêm mới
                        </a>
                        <a href="{{ route('admin.tests.random.create') }}" class="btn btn-info">
                            <i class="fas fa-random me-1"></i>Tạo đề ngẫu nhiên
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Loại</th>
                            <th>Thời gian</th>
                            <th>Độ khó</th>
                            <th>Thống kê</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($tests->count() > 0)
                            @foreach($tests as $test)
                                <tr>
                                    <td>{{ $test->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $test->title }}</div>
                                        <div class="small">Mã: {{ $test->code }}</div>
                                        <div class="small text-muted">
                                            @if($test->position)
                                                <i class="fas fa-user-tie me-1"></i>{{ $test->position->name }}
                                            @endif
                                            @if($test->shipType)
                                                <i class="fas fa-ship ms-2 me-1"></i>{{ $test->shipType->name }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($test->type == 'certification')
                                            <span class="badge bg-primary test-type-badge">Chứng chỉ</span>
                                        @elseif($test->type == 'assessment')
                                            <span class="badge bg-info test-type-badge">Đánh giá</span>
                                        @elseif($test->type == 'placement')
                                            <span class="badge bg-secondary test-type-badge">Xếp loại</span>
                                        @elseif($test->type == 'practice')
                                            <span class="badge bg-success test-type-badge">Luyện tập</span>
                                        @else
                                            <span class="badge bg-dark test-type-badge">{{ $test->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $test->duration }} phút</td>
                                    <td>
                                        @if($test->difficulty == 'Dễ')
                                            <span class="badge difficulty-badge difficulty-easy">Dễ</span>
                                        @elseif($test->difficulty == 'Trung bình')
                                            <span class="badge difficulty-badge difficulty-medium">Trung bình</span>
                                        @elseif($test->difficulty == 'Khó')
                                            <span class="badge difficulty-badge difficulty-hard">Khó</span>
                                        @else
                                            <span class="badge bg-secondary difficulty-badge">{{ $test->difficulty }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="test-stats">
                                            <div><i class="fas fa-users me-1"></i> {{ $test->attempts_count ?? 0 }} lượt thi</div>
                                            <div><i class="fas fa-chart-line me-1"></i> {{ $test->avg_score ? number_format($test->avg_score, 1) : 'N/A' }} điểm TB</div>
                                            <div><i class="fas fa-check-circle me-1"></i> {{ $test->pass_rate ? number_format($test->pass_rate, 1) : 'N/A' }}% đạt</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($test->is_active == '1')
                                            <span><span class="test-status status-active"></span>Hoạt động</span>
                                        @elseif($test->is_active == '0')
                                            <span><span class="test-status status-draft"></span>Tạm dừng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.tests.show', $test->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tests.edit', $test->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa bài kiểm tra" onclick="return confirm('Bạn có chắc chắn muốn xóa bài kiểm tra này không? Thao tác này không thể hoàn tác.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted mb-2"><i class="fas fa-search me-2"></i>Không tìm thấy bài kiểm tra nào</div>
                                    <a href="{{ route('admin.tests.create') }}" class="btn btn-sm btn-primary">Tạo bài kiểm tra mới</a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Hiển thị {{ $tests->firstItem() ?? 0 }} - {{ $tests->lastItem() ?? 0 }} của {{ $tests->total() ?? 0 }} bài kiểm tra
                </div>
                <div>
                    {{ $tests->appends(request()->query())->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Khởi tạo tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Xử lý nút xóa bộ lọc
    document.getElementById('clearFilters').addEventListener('click', function() {
        window.location.href = "{{ route('admin.tests.index') }}";
    });
    
    // Xem thống kê bài kiểm tra
    function viewStats(testId) {
        // Chuyển hướng đến trang thống kê của bài kiểm tra
        window.location.href = "{{ url('admin/tests') }}/" + testId + "/statistics";
    }
</script>
@endsection 