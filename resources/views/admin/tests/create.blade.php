@extends('layouts.app')

@section('title', 'Thêm Bài Kiểm tra Mới - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt me-2"></i> Thêm Bài Kiểm tra Mới
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tests.index') }}">Bài Kiểm tra</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin bài kiểm tra</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tests.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach(App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Loại bài kiểm tra <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="certification" {{ old('type') == 'certification' ? 'selected' : '' }}>Chứng chỉ</option>
                            <option value="assessment" {{ old('type') == 'assessment' ? 'selected' : '' }}>Đánh giá năng lực</option>
                            <option value="placement" {{ old('type') == 'placement' ? 'selected' : '' }}>Phân loại</option>
                            <option value="practice" {{ old('type') == 'practice' ? 'selected' : '' }}>Luyện tập</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="difficulty" class="form-label">Độ khó <span class="text-danger">*</span></label>
                        <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                            <option value="">-- Chọn độ khó --</option>
                            <option value="Dễ" {{ old('difficulty') == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                            <option value="Trung bình" {{ old('difficulty') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                            <option value="Khó" {{ old('difficulty') == 'Khó' ? 'selected' : '' }}>Khó</option>
                        </select>
                        @error('difficulty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="duration" class="form-label">Thời gian làm bài (phút) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', 60) }}" min="5" max="180" required>
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="position_id" class="form-label">Chức danh</label>
                        <select class="form-select @error('position_id') is-invalid @enderror" id="position_id" name="position_id">
                            <option value="">-- Tất cả chức danh --</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="ship_type_id" class="form-label">Loại tàu</label>
                        <select class="form-select @error('ship_type_id') is-invalid @enderror" id="ship_type_id" name="ship_type_id">
                            <option value="">-- Tất cả loại tàu --</option>
                            @foreach($shipTypes as $shipType)
                                <option value="{{ $shipType->id }}" {{ old('ship_type_id') == $shipType->id ? 'selected' : '' }}>
                                    {{ $shipType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('ship_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="passing_score" class="form-label">Điểm đạt (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('passing_score') is-invalid @enderror" id="passing_score" name="passing_score" value="{{ old('passing_score', 60) }}" min="0" max="100" required>
                        @error('passing_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_random" name="is_random" value="1" {{ old('is_random') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_random">
                            Hiển thị câu hỏi ngẫu nhiên
                        </label>
                    </div>
                </div>
                
                <div class="mb-3 random-questions-section" >
                    <div class="row">
                        <div class="col-md-4">
                            <label for="random_questions_count" class="form-label">Số lượng câu hỏi ngẫu nhiên <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('random_questions_count') is-invalid @enderror" id="random_questions_count" name="random_questions_count" value="{{ old('random_questions_count', 10) }}" min="1" max="50">
                            @error('random_questions_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="filter_difficulty" class="form-label">Chọn độ khó câu hỏi <span class="text-danger">*</span></label>
                            <select class="form-select @error('difficulty') is-invalid @enderror" id="filter_difficulty" name="difficulty">
                                <option value="">-- Chọn độ khó --</option>
                                <option value="Dễ" {{ old('difficulty') == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                                <option value="Trung bình" {{ old('difficulty') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                                <option value="Khó" {{ old('difficulty') == 'Khó' ? 'selected' : '' }}>Khó</option>
                            </select>
                            @error('difficulty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-3 alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Hệ thống sẽ tự động chọn câu hỏi ngẫu nhiên từ ngân hàng câu hỏi phù hợp với chức danh, loại tàu và độ khó mỗi khi thuyền viên bắt đầu làm bài kiểm tra.
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Kích hoạt bài kiểm tra
                        </label>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="question-selection-section" style="{{ old('is_random') ? 'display: none;' : '' }}">
                    <h5 class="mb-3">Chọn câu hỏi cho bài kiểm tra</h5>
                
                    <div class="mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="searchQuestion" placeholder="Tìm kiếm câu hỏi...">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filterQuestionType">
                                            <option value="">Tất cả loại câu hỏi</option>
                                            <option value="Trắc nghiệm">Trắc nghiệm</option>
                                            <option value="Tự luận">Tự luận</option>
                                            <option value="Tình huống">Tình huống</option>
                                            <option value="Thực hành">Thực hành</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filterQuestionDifficulty">
                                            <option value="">Tất cả độ khó</option>
                                            <option value="Dễ">Dễ</option>
                                            <option value="Trung bình">Trung bình</option>
                                            <option value="Khó">Khó</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">Chọn</th>
                                                <th width="5%">ID</th>
                                                <th width="40%">Nội dung</th>
                                                <th width="15%">Loại</th>
                                                <th width="15%">Độ khó</th>
                                                <th width="20%">Danh mục</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($questions as $question)
                                                <tr class="question-row" 
                                                    data-type="{{ $question->type }}" 
                                                    data-difficulty="{{ $question->difficulty }}"
                                                    data-category="{{ $question->category }}">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input question-checkbox" type="checkbox" 
                                                                name="question_ids[]" value="{{ $question->id }}" 
                                                                id="question{{ $question->id }}"
                                                                {{ in_array($question->id, old('question_ids', [])) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>{{ $question->id }}</td>
                                                    <td>
                                                        <span class="question-content">{{ Str::limit($question->content, 100) }}</span>
                                                        <button type="button" class="btn btn-sm btn-link view-question" 
                                                            data-bs-toggle="modal" data-bs-target="#questionModal" 
                                                            data-content="{{ $question->content }}"
                                                            data-id="{{ $question->id }}">
                                                            Xem chi tiết
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $question->type == 'Trắc nghiệm' ? 'primary' : ($question->type == 'Tự luận' ? 'success' : ($question->type == 'Tình huống' ? 'warning' : 'info')) }}">
                                                            {{ $question->type }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $question->difficulty == 'Dễ' ? 'success' : ($question->difficulty == 'Trung bình' ? 'warning' : 'danger') }}">
                                                            {{ $question->difficulty }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $question->category }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span id="selectedCount" class="badge bg-primary">0</span> câu hỏi được chọn
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-secondary" id="clearSelection">Bỏ chọn tất cả</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog me-2"></i> Cài đặt bài kiểm tra
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions" value="1">
                                        <label class="form-check-label" for="shuffle_questions">Xáo trộn câu hỏi</label>
                                    </div>
                                    <div class="form-text">Thứ tự câu hỏi sẽ được xáo trộn khi thuyền viên làm bài</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="shuffle_answers" name="shuffle_answers" value="1">
                                        <label class="form-check-label" for="shuffle_answers">Xáo trộn đáp án</label>
                                    </div>
                                    <div class="form-text">Thứ tự đáp án sẽ được xáo trộn khi thuyền viên làm bài</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="allow_back" name="allow_back" value="1" checked>
                                        <label class="form-check-label" for="allow_back">Cho phép quay lại câu trước</label>
                                    </div>
                                    <div class="form-text">Thuyền viên có thể quay lại các câu trước đó trong bài kiểm tra</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_result_immediately" name="show_result_immediately" value="1">
                                        <label class="form-check-label" for="show_result_immediately">Hiển thị kết quả ngay sau khi làm</label>
                                    </div>
                                    <div class="form-text">Thuyền viên sẽ thấy kết quả đúng/sai ngay sau khi nộp bài</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_attempts" class="form-label">Tối đa số lần làm</label>
                                    <select class="form-select" id="max_attempts" name="max_attempts">
                                        <option value="">Không giới hạn</option>
                                        <option value="1">1 lần</option>
                                        <option value="2">2 lần</option>
                                        <option value="3">3 lần</option>
                                        <option value="5">5 lần</option>
                                        <option value="10">10 lần</option>
                                    </select>
                                    <div class="form-text">Số lần tối đa mà thuyền viên có thể làm bài kiểm tra này</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu bài kiểm tra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal xem chi tiết câu hỏi -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">Chi tiết câu hỏi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="questionContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Khởi tạo CKEditor cho trường mô tả
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
        
        // Xử lý hiển thị/ẩn phần tạo câu hỏi ngẫu nhiên
        $("#is_random").change(function() {
            if($(this).is(":checked")) {
                $(".random-questions-section").show();
                $(".question-selection-section").hide();
            } else {
                $(".random-questions-section").hide();
                $(".question-selection-section").show();
            }
        });
        
        // Xử lý tìm kiếm câu hỏi
        $("#searchQuestion").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".question-row").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        // Xử lý lọc theo loại câu hỏi
        $("#filterQuestionType").on("change", function() {
            var value = $(this).val();
            if (value === "") {
                $(".question-row").show();
            } else {
                $(".question-row").hide();
                $(".question-row[data-type='" + value + "']").show();
            }
        });
        
        // Xử lý lọc theo độ khó
        $("#filterQuestionDifficulty").on("change", function() {
            var value = $(this).val();
            if (value === "") {
                $(".question-row").show();
            } else {
                $(".question-row").hide();
                $(".question-row[data-difficulty='" + value + "']").show();
            }
        });
        
        // Xử lý hiển thị chi tiết câu hỏi
        $(".view-question").on("click", function() {
            var content = $(this).data("content");
            $("#questionContent").html(content);
        });
        
        // Xử lý đếm số câu hỏi được chọn
        function updateSelectedCount() {
            var count = $(".question-checkbox:checked").length;
            $("#selectedCount").text(count);
        }
        
        $(".question-checkbox").on("change", function() {
            updateSelectedCount();
        });
        
        // Xử lý nút bỏ chọn tất cả
        $("#clearSelection").on("click", function() {
            $(".question-checkbox").prop("checked", false);
            updateSelectedCount();
        });
        
        // Khởi tạo đếm số câu hỏi được chọn
        updateSelectedCount();
    });
</script>
@endsection 