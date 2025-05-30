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
            <div>
                <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-1"></i> Import câu hỏi
                </button>
            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Thêm câu hỏi mới
            </a>
            </div>
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

<!-- Modal Import Câu hỏi -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-import me-2"></i>Import Câu hỏi từ Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-lg me-3"></i>
                            <div>
                                <p class="mb-1"><strong>Hướng dẫn import câu hỏi:</strong></p>
                                <ol class="mb-0">
                                    <li>Tải xuống file mẫu Excel (đã bao gồm danh sách chức danh và loại tàu hiện có)</li>
                                    <li>Điền thông tin câu hỏi theo mẫu</li>
                                    <li>Tải lên file Excel đã điền thông tin</li>
                                    <li>Kiểm tra kết quả import</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <p class="mb-3">
                        <a href="{{ route('admin.questions.export.template') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i> Tải xuống file mẫu
                        </a>
                    </p>
                    
                    <!-- Thêm tab để hiển thị thông tin tham khảo -->
                    <ul class="nav nav-tabs" id="importTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="import-tab" data-bs-toggle="tab" data-bs-target="#import-content" type="button" role="tab" aria-controls="import-content" aria-selected="true">Import</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="positions-tab" data-bs-toggle="tab" data-bs-target="#positions-content" type="button" role="tab" aria-controls="positions-content" aria-selected="false">Danh sách chức danh</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="shiptypes-tab" data-bs-toggle="tab" data-bs-target="#shiptypes-content" type="button" role="tab" aria-controls="shiptypes-content" aria-selected="false">Danh sách loại tàu</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="importTabsContent">
                        <div class="tab-pane fade show active" id="import-content" role="tabpanel" aria-labelledby="import-tab">
                            <form action="{{ route('admin.questions.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="excel_file" class="form-label">File Excel chứa câu hỏi</label>
                                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx, .xls" required>
                                    <div class="form-text">Hỗ trợ định dạng .xlsx, .xls (Excel)</div>
                                </div>
                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" checked>
                                    <label class="form-check-label" for="skip_duplicates">
                                        Bỏ qua câu hỏi trùng lặp
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="create_new_entities" name="create_new_entities" value="1">
                                    <label class="form-check-label" for="create_new_entities">
                                        Tự động tạo chức danh và loại tàu mới nếu không tìm thấy
                                    </label>
                                </div>
                                
                                <div class="progress d-none mb-3" id="import-progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                                </div>
                                
                                <div id="import-result" class="d-none mb-3">
                                    <!-- Kết quả import sẽ hiển thị ở đây -->
                                </div>
                            </form>
                        </div>
                        
                        <div class="tab-pane fade" id="positions-content" role="tabpanel" aria-labelledby="positions-tab">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="position-search" placeholder="Tìm kiếm chức danh...">
                                <button class="btn btn-outline-secondary" type="button" id="clear-position-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped table-hover">
                                    <thead class="sticky-top bg-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên chức danh</th>
                                        </tr>
                                    </thead>
                                    <tbody id="positions-table-body">
                                        @foreach($positions as $position)
                                        <tr>
                                            <td>{{ $position->id }}</td>
                                            <td>{{ $position->name }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="shiptypes-content" role="tabpanel" aria-labelledby="shiptypes-tab">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="shiptype-search" placeholder="Tìm kiếm loại tàu...">
                                <button class="btn btn-outline-secondary" type="button" id="clear-shiptype-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped table-hover">
                                    <thead class="sticky-top bg-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên loại tàu</th>
                                        </tr>
                                    </thead>
                                    <tbody id="shiptypes-table-body">
                                        @foreach($shipTypes as $shipType)
                                        <tr>
                                            <td>{{ $shipType->id }}</td>
                                            <td>{{ $shipType->name }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Đóng
                </button>
                <button type="button" class="btn btn-success" id="btn-import">
                    <i class="fas fa-upload me-1"></i> Tải lên và import
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Xử lý tìm kiếm chức danh
        $('#position-search').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            $('#positions-table-body tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
            });
        });
        
        // Xóa tìm kiếm chức danh
        $('#clear-position-search').on('click', function() {
            $('#position-search').val('');
            $('#positions-table-body tr').show();
        });
        
        // Xử lý tìm kiếm loại tàu
        $('#shiptype-search').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            $('#shiptypes-table-body tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
            });
        });
        
        // Xóa tìm kiếm loại tàu
        $('#clear-shiptype-search').on('click', function() {
            $('#shiptype-search').val('');
            $('#shiptypes-table-body tr').show();
        });

        // Xử lý nút import
        $('#btn-import').on('click', function() {
            const fileInput = $('#excel_file');
            
            // Kiểm tra file đã được chọn chưa
            if (fileInput[0].files.length === 0) {
                alert('Vui lòng chọn file Excel để import.');
                return;
            }
            
            // Kiểm tra định dạng file
            const fileName = fileInput[0].files[0].name;
            const fileExt = fileName.split('.').pop().toLowerCase();
            
            if (fileExt !== 'xlsx' && fileExt !== 'xls') {
                alert('Vui lòng chọn file Excel có định dạng .xlsx hoặc .xls');
                return;
            }
            
            // Hiển thị thanh tiến trình
            $('#import-progress').removeClass('d-none');
            
            // Vô hiệu hóa nút import để tránh click nhiều lần
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Đang import...');
            
            // Tạo đối tượng FormData
            const formData = new FormData($('#importForm')[0]);
        
            // Gửi request AJAX
            $.ajax({
                url: $('#importForm').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    
                    // Thêm event listener cho tiến trình upload
                    xhr.upload.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            $('#import-progress .progress-bar').css('width', percentComplete + '%');
                            $('#import-progress .progress-bar').attr('aria-valuenow', percentComplete);
                        }
                    }, false);
                    
                    return xhr;
                },
                success: function(response) {
                    // Hiển thị kết quả import
                    $('#import-progress').addClass('d-none');
                    
                    let resultHtml = '';
                    if (response.success) {
                        resultHtml = `
                            <div class="alert alert-success">
                                <h6 class="alert-heading"><i class="fas fa-check-circle me-1"></i> Import hoàn tất</h6>
                                <p class="mb-0">Đã import thành công <strong>${response.imported_count}</strong> câu hỏi.</p>
                            </div>
                        `;
                        
                        // Hiển thị cảnh báo về chức danh và loại tàu không tìm thấy nếu có
                        if (response.warnings && response.warnings.length > 0) {
                            resultHtml += `
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-1"></i> Cảnh báo</h6>
                                    <ul class="mb-0">
                                        ${response.warnings.map(warning => `<li>${warning}</li>`).join('')}
                                    </ul>
                                </div>
                            `;
                        }
                    } else {
                        resultHtml = `
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="fas fa-exclamation-circle me-1"></i> Lỗi import</h6>
                                <p class="mb-0">${response.message}</p>
                                ${response.error_details ? `<p class="mt-2 small"><strong>Chi tiết lỗi:</strong> ${response.error_details}</p>` : ''}
                            </div>
                        `;
                    }
                    
                    // Hiển thị lỗi chi tiết nếu có
                    if (response.errors && response.errors.length > 0) {
                        resultHtml += `
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="fas fa-exclamation-circle me-1"></i> Lỗi chi tiết</h6>
                                <ul class="mb-0 small" style="max-height: 200px; overflow-y: auto;">
                                    ${response.errors.map(error => `<li>${error}</li>`).join('')}
                                </ul>
                            </div>
                        `;
                    }
                    
                    $('#import-result').html(resultHtml).removeClass('d-none');
                    
                    // Thay đổi nội dung nút
                    $('#btn-import').prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Hoàn tất');
                    
                    // Làm mới trang sau 3 giây nếu thành công
                    if (response.success && response.imported_count > 0) {
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    // Hiển thị lỗi
                    $('#import-progress').addClass('d-none');
                    
                    let errorMessage = 'Đã xảy ra lỗi trong quá trình import.';
                    let errorDetails = '';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.exception) {
                            errorDetails = xhr.responseJSON.exception;
                        }
                    }
                    
                    // Tạo thông báo lỗi
                    const errorAlert = `
                        <div class="alert alert-danger">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-circle me-1"></i> Lỗi import</h6>
                            <p class="mb-0">${errorMessage}</p>
                            ${errorDetails ? `<p class="mt-2 small"><strong>Chi tiết lỗi:</strong> ${errorDetails}</p>` : ''}
                        </div>
                    `;
                    
                    $('#import-result').html(errorAlert).removeClass('d-none');
                    
                    // Khôi phục nút import
                    $('#btn-import').prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Thử lại');
                }
            });
        });
        
        // Xử lý đóng modal, reset form
        $('#importModal').on('hidden.bs.modal', function () {
            $('#importForm')[0].reset();
            $('#import-progress').addClass('d-none');
            $('#import-result').addClass('d-none');
            $('#btn-import').prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Tải lên và import');
        });
        
        // Khởi tạo tooltip cho nội dung câu hỏi
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Xử lý xóa bộ lọc
        $('.clear-filter').click(function() {
            const target = $(this).data('target');
            $('#' + target).val('');
        });
    });
</script>
@endsection 