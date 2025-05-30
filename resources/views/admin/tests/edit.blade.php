@extends('layouts.app')

@section('title', 'Chỉnh sửa Bài Kiểm tra - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit me-2"></i> Chỉnh sửa Bài Kiểm tra: {{ $test->title }}
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tests.index') }}">Bài Kiểm tra</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin bài kiểm tra</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tests.update', $test->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $test->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach(App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $test->category_id) == $category->id ? 'selected' : '' }}>
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
                            <option value="certification" {{ old('type', $test->type) == 'certification' ? 'selected' : '' }}>Chứng chỉ</option>
                            <option value="assessment" {{ old('type', $test->type) == 'assessment' ? 'selected' : '' }}>Đánh giá năng lực</option>
                            <option value="placement" {{ old('type', $test->type) == 'placement' ? 'selected' : '' }}>Phân loại</option>
                            <option value="practice" {{ old('type', $test->type) == 'practice' ? 'selected' : '' }}>Luyện tập</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="difficulty" class="form-label">Độ khó <span class="text-danger">*</span></label>
                        <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                            <option value="">-- Chọn độ khó --</option>
                            <option value="Dễ" {{ old('difficulty', $test->difficulty) == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                            <option value="Trung bình" {{ old('difficulty', $test->difficulty) == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                            <option value="Khó" {{ old('difficulty', $test->difficulty) == 'Khó' ? 'selected' : '' }}>Khó</option>
                        </select>
                        @error('difficulty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="duration" class="form-label">Thời gian làm bài (phút) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $test->duration) }}" min="5" max="180" required>
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
                                <option value="{{ $position->id }}" {{ old('position_id', $test->position_id) == $position->id ? 'selected' : '' }}>
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
                                <option value="{{ $shipType->id }}" {{ old('ship_type_id', $test->ship_type_id) == $shipType->id ? 'selected' : '' }}>
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
                        <input type="number" class="form-control @error('passing_score') is-invalid @enderror" id="passing_score" name="passing_score" value="{{ old('passing_score', $test->passing_score) }}" min="0" max="100" required>
                        @error('passing_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $test->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_random" name="is_random" value="1" {{ old('is_random', $test->is_random) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_random">
                            Hiển thị câu hỏi ngẫu nhiên
                        </label>
                    </div>
                </div>
                
                <div class="mb-3 random-questions-section" style="{{ !old('is_random', $test->is_random) ? 'display: none;' : '' }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="random_questions_count" class="form-label">Số lượng câu hỏi ngẫu nhiên <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('random_questions_count') is-invalid @enderror" id="random_questions_count" name="random_questions_count" value="{{ old('random_questions_count', $test->random_questions_count ?? 10) }}" min="1" max="50">
                            @error('random_questions_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="filter_difficulty" class="form-label">Chọn độ khó câu hỏi</label>
                            <select class="form-select @error('difficulty') is-invalid @enderror" id="filter_difficulty" name="difficulty">
                                <option value="">-- Chọn độ khó --</option>
                                <option value="Dễ" {{ old('difficulty', $test->difficulty) == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                                <option value="Trung bình" {{ old('difficulty', $test->difficulty) == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                                <option value="Khó" {{ old('difficulty', $test->difficulty) == 'Khó' ? 'selected' : '' }}>Khó</option>
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
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $test->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Kích hoạt bài kiểm tra
                        </label>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="question-selection-section" style="{{ old('is_random', $test->is_random) ? 'display: none;' : '' }}">
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
                                <div class="list-group question-list">
                                    @foreach($questions as $question)
                                        <div class="list-group-item question-item" 
                                             data-type="{{ $question->type }}" 
                                             data-difficulty="{{ $question->difficulty }}" 
                                             data-content="{{ $question->content }}">
                                            <div class="form-check">
                                                <input class="form-check-input question-checkbox" 
                                                       type="checkbox" 
                                                       name="question_ids[]" 
                                                       value="{{ $question->id }}" 
                                                       id="question{{ $question->id }}"
                                                       {{ in_array($question->id, $selectedQuestionIds) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="question{{ $question->id }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fw-bold">
                                                            {{ \Illuminate\Support\Str::limit(strip_tags($question->content), 100, '...') }}
                                                        </span>
                                                        <div>
                                                            <span class="badge bg-primary me-1">{{ $question->type }}</span>
                                                            <span class="badge bg-{{ $question->difficulty == 'Dễ' ? 'success' : ($question->difficulty == 'Trung bình' ? 'warning' : 'danger') }}">
                                                                {{ $question->difficulty }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card-footer bg-light d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-primary" id="selectedCount">{{ count($selectedQuestionIds) }}</span> câu hỏi đã chọn
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Chọn tất cả</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Bỏ chọn tất cả</button>
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
                                        <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions" value="1" 
                                            {{ $test->settings && $test->settings->shuffle_questions ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shuffle_questions">Xáo trộn câu hỏi</label>
                                    </div>
                                    <div class="form-text">Thứ tự câu hỏi sẽ được xáo trộn khi thuyền viên làm bài</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="shuffle_answers" name="shuffle_answers" value="1"
                                            {{ $test->settings && $test->settings->shuffle_answers ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shuffle_answers">Xáo trộn đáp án</label>
                                    </div>
                                    <div class="form-text">Thứ tự đáp án sẽ được xáo trộn khi thuyền viên làm bài</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="allow_back" name="allow_back" value="1"
                                            {{ !$test->settings || $test->settings->allow_back ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_back">Cho phép quay lại câu trước</label>
                                    </div>
                                    <div class="form-text">Thuyền viên có thể quay lại các câu trước đó trong bài kiểm tra</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_result_immediately" name="show_result_immediately" value="1"
                                            {{ $test->settings && $test->settings->show_result_immediately ? 'checked' : '' }}>
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
                                        <option value="" {{ (!$test->settings || $test->settings->max_attempts === null) ? 'selected' : '' }}>Không giới hạn</option>
                                        <option value="1" {{ ($test->settings && $test->settings->max_attempts === 1) ? 'selected' : '' }}>1 lần</option>
                                        <option value="2" {{ ($test->settings && $test->settings->max_attempts === 2) ? 'selected' : '' }}>2 lần</option>
                                        <option value="3" {{ ($test->settings && $test->settings->max_attempts === 3) ? 'selected' : '' }}>3 lần</option>
                                        <option value="5" {{ ($test->settings && $test->settings->max_attempts === 5) ? 'selected' : '' }}>5 lần</option>
                                        <option value="10" {{ ($test->settings && $test->settings->max_attempts === 10) ? 'selected' : '' }}>10 lần</option>
                                    </select>
                                    <div class="form-text">Số lần tối đa mà thuyền viên có thể làm bài kiểm tra này</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.tests.show', $test->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý hiển thị/ẩn phần ngẫu nhiên
        const isRandomCheckbox = document.getElementById('is_random');
        const randomQuestionsSection = document.querySelector('.random-questions-section');
        const questionSelectionSection = document.querySelector('.question-selection-section');
        
        isRandomCheckbox.addEventListener('change', function() {
            if (this.checked) {
                randomQuestionsSection.style.display = 'block';
                questionSelectionSection.style.display = 'none';
            } else {
                randomQuestionsSection.style.display = 'none';
                questionSelectionSection.style.display = 'block';
            }
        });
        
        // Xử lý tìm kiếm câu hỏi
        const searchInput = document.getElementById('searchQuestion');
        const filterType = document.getElementById('filterQuestionType');
        const filterDifficulty = document.getElementById('filterQuestionDifficulty');
        const questionItems = document.querySelectorAll('.question-item');
        
        function filterQuestions() {
            const searchTerm = searchInput.value.toLowerCase();
            const typeFilter = filterType.value;
            const difficultyFilter = filterDifficulty.value;
            
            questionItems.forEach(item => {
                const content = item.dataset.content.toLowerCase();
                const type = item.dataset.type;
                const difficulty = item.dataset.difficulty;
                
                const matchesSearch = searchTerm === '' || content.includes(searchTerm);
                const matchesType = typeFilter === '' || type === typeFilter;
                const matchesDifficulty = difficultyFilter === '' || difficulty === difficultyFilter;
                
                if (matchesSearch && matchesType && matchesDifficulty) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        searchInput.addEventListener('input', filterQuestions);
        filterType.addEventListener('change', filterQuestions);
        filterDifficulty.addEventListener('change', filterQuestions);
        
        // Xử lý chọn/bỏ chọn tất cả
        const selectAllBtn = document.getElementById('selectAll');
        const deselectAllBtn = document.getElementById('deselectAll');
        const questionCheckboxes = document.querySelectorAll('.question-checkbox');
        const selectedCountDisplay = document.getElementById('selectedCount');
        
        function updateSelectedCount() {
            let count = 0;
            questionCheckboxes.forEach(checkbox => {
                if (checkbox.checked) count++;
            });
            selectedCountDisplay.textContent = count;
        }
        
        selectAllBtn.addEventListener('click', function() {
            questionCheckboxes.forEach(checkbox => {
                const item = checkbox.closest('.question-item');
                if (item.style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
            updateSelectedCount();
        });
        
        deselectAllBtn.addEventListener('click', function() {
            questionCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        });
        
        questionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
    });
</script>
@endsection 