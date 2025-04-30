@extends('layouts.app')

@section('title', 'Quản lý Ngân hàng Câu hỏi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .filter-card {
        background-color: #f8f9fc;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .question-type-badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
        border-radius: 20px;
    }
    
    .difficulty-badge {
        font-size: 0.8rem;
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
    
    .question-content {
        max-width: 400px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .question-actions {
        white-space: nowrap;
    }
    
    .content-preview {
        cursor: pointer;
    }
    
    .clear-filter {
        cursor: pointer;
        color: var(--primary);
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-question-circle me-2"></i> Quản lý Ngân hàng Câu hỏi
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Ngân hàng Câu hỏi</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc tìm kiếm</h6>
            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Thêm câu hỏi mới
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.questions.index') }}" method="GET" class="mb-0">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Từ khóa</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm...">
                            <button class="btn btn-outline-secondary clear-filter" type="button" data-target="search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="type" class="form-label">Loại câu hỏi</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tất cả loại</option>
                            <option value="Trắc nghiệm" {{ request('type') == 'Trắc nghiệm' ? 'selected' : '' }}>Trắc nghiệm</option>
                            <option value="Tự luận" {{ request('type') == 'Tự luận' ? 'selected' : '' }}>Tự luận</option>
                            <option value="Tình huống" {{ request('type') == 'Tình huống' ? 'selected' : '' }}>Tình huống</option>
                            <option value="Thực hành" {{ request('type') == 'Thực hành' ? 'selected' : '' }}>Thực hành</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="difficulty" class="form-label">Độ khó</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Tất cả độ khó</option>
                            <option value="Dễ" {{ request('difficulty') == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                            <option value="Trung bình" {{ request('difficulty') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                            <option value="Khó" {{ request('difficulty') == 'Khó' ? 'selected' : '' }}>Khó</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="position_id" class="form-label">Chức danh</label>
                        <select class="form-select" id="position_id" name="position_id">
                            <option value="">Tất cả chức danh</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="ship_type_id" class="form-label">Loại tàu</label>
                        <select class="form-select" id="ship_type_id" name="ship_type_id">
                            <option value="">Tất cả loại tàu</option>
                            @foreach($shipTypes as $shipType)
                                <option value="{{ $shipType->id }}" {{ request('ship_type_id') == $shipType->id ? 'selected' : '' }}>
                                    {{ $shipType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="sort" class="form-label">Sắp xếp theo</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="content" {{ request('sort') == 'content' ? 'selected' : '' }}>Nội dung (A-Z)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-sync me-1"></i> Làm mới
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Danh sách câu hỏi
                @if($questions->total() > 0)
                    <span class="text-muted">({{ $questions->total() }} câu hỏi)</span>
                @endif
            </h6>
            <div>
                <a href="#" class="btn btn-sm btn-success me-2">
                    <i class="fas fa-file-excel me-1"></i> Xuất Excel
                </a>
                <a href="#" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Xuất PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="30%">Nội dung</th>
                            <th width="10%">Loại</th>
                            <th width="10%">Độ khó</th>
                            <th width="15%">Chức danh</th>
                            <th width="15%">Loại tàu</th>
                            <th width="15%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>
                                    <span class="content-preview" data-bs-toggle="tooltip" title="{{ $question->content }}">
                                        {{ \Illuminate\Support\Str::limit($question->content, 80) }}
                                    </span>
                                </td>
                                <td>
                                    @if($question->type == 'Trắc nghiệm')
                                        <span class="badge bg-primary question-type-badge">Trắc nghiệm</span>
                                    @elseif($question->type == 'Tự luận')
                                        <span class="badge bg-info question-type-badge">Tự luận</span>
                                    @elseif($question->type == 'Tình huống')
                                        <span class="badge bg-warning question-type-badge">Tình huống</span>
                                    @elseif($question->type == 'Thực hành')
                                        <span class="badge bg-success question-type-badge">Thực hành</span>
                                    @else
                                        <span class="badge bg-secondary question-type-badge">{{ $question->type }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($question->difficulty == 'Dễ')
                                        <span class="badge difficulty-badge difficulty-easy">Dễ</span>
                                    @elseif($question->difficulty == 'Trung bình')
                                        <span class="badge difficulty-badge difficulty-medium">Trung bình</span>
                                    @elseif($question->difficulty == 'Khó')
                                        <span class="badge difficulty-badge difficulty-hard">Khó</span>
                                    @endif
                                </td>
                                <td>{{ $question->position ? $question->position->name : 'Tất cả' }}</td>
                                <td>{{ $question->shipType ? $question->shipType->name : 'Tất cả' }}</td>
                                <td class="question-actions">
                                    <a href="{{ route('admin.questions.show', $question->id) }}" class="btn btn-sm btn-info me-1" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-sm btn-warning me-1" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Không tìm thấy câu hỏi nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $questions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Khởi tạo tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Xử lý nút clear filter
        $('.clear-filter').click(function() {
            var targetId = $(this).data('target');
            $('#' + targetId).val('');
        });
        
        // Modal xem trước nội dung (nếu cần)
        $('.content-preview').click(function() {
            var content = $(this).data('bs-original-title');
            $('#contentPreviewModal .modal-body').text(content);
            $('#contentPreviewModal').modal('show');
        });
    });
</script>
@endsection 