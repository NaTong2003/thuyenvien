@extends('layouts.app')

@section('title', 'Chỉnh sửa Câu hỏi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .question-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .required-label::after {
        content: " *";
        color: red;
    }
    
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eaeaea;
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .form-section-title {
        margin-bottom: 1.5rem;
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .answer-option {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }
    
    .remove-option {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #dc3545;
        cursor: pointer;
    }
    
    .is-correct-option {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px dashed #dee2e6;
    }
    
    .editor-container {
        min-height: 200px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    #editor-toolbar {
        border-bottom: 1px solid #ced4da;
    }
    
    .ck-content {
        min-height: 200px;
    }
</style>
@endsection

@section('content')
<div class="question-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Chỉnh sửa câu hỏi #{{ $question->id }}</h2>
                <div class="d-flex gap-2 mt-2">
                    @if($question->type == 'Trắc nghiệm')
                        <span class="badge bg-primary">Trắc nghiệm</span>
                    @elseif($question->type == 'Tự luận')
                        <span class="badge bg-info">Tự luận</span>
                    @elseif($question->type == 'Tình huống')
                        <span class="badge bg-warning">Tình huống</span>
                    @elseif($question->type == 'Thực hành')
                        <span class="badge bg-success">Thực hành</span>
                    @else
                        <span class="badge bg-secondary">{{ $question->type }}</span>
                    @endif
                </div>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}" class="text-white">Ngân hàng Câu hỏi</a></li>
                <li class="breadcrumb-item active text-white-50">Chỉnh sửa</li>
            </ol>
        </div>
    </div>
</div>

<div class="container mb-5">
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> Vui lòng kiểm tra lại thông tin
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('admin.questions.update', $question->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Thêm trường ẩn để lưu loại câu hỏi -->
        <input type="hidden" name="question_type" value="{{ $question->type }}">
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-question-circle me-1"></i> Thông tin câu hỏi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-section">
                            <h5 class="form-section-title">Nội dung câu hỏi</h5>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label required-label">Nội dung câu hỏi</label>
                                <div class="editor-container">
                                    <div id="editor-toolbar"></div>
                                    <textarea id="content" name="content" class="form-control d-none">{{ old('content', $question->content) }}</textarea>
                                </div>
                                <div class="form-text">Nhập nội dung câu hỏi. Có thể sử dụng định dạng văn bản và chèn hình ảnh nếu cần.</div>
                            </div>
                        </div>
                        
                        <!-- Tùy theo loại câu hỏi -->
                        @if($question->type == 'Trắc nghiệm')
                            <div class="form-section">
                                <h5 class="form-section-title">Các phương án trả lời</h5>
                                
                                <div id="answer-options-container">
                                    @foreach($question->answers as $index => $answer)
                                        <div class="answer-option" data-option-id="{{ $index }}">
                                            <div class="mb-3">
                                                <label class="form-label required-label">Phương án {{ chr(65 + $index) }}</label>
                                                <textarea class="form-control" name="answers[{{ $index }}][content]" rows="2" required>{{ old("answers.$index.content", $answer->content) }}</textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Giải thích (tùy chọn)</label>
                                                <textarea class="form-control" name="answers[{{ $index }}][explanation]" rows="2">{{ old("answers.$index.explanation", $answer->explanation) }}</textarea>
                                            </div>
                                            
                                            <div class="is-correct-option">
                                                <div class="form-check">
                                                    <input class="form-check-input correct-answer" type="radio" name="correct_answer" id="correct{{ $index }}" value="{{ $index }}" {{ old('correct_answer', $answer->is_correct ? $index : '') == $index ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="correct{{ $index }}">
                                                        Đây là đáp án đúng
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            @if($index > 1)
                                                <div class="remove-option" data-id="{{ $index }}">
                                                    <i class="fas fa-times-circle"></i>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-outline-primary" id="add-option">
                                        <i class="fas fa-plus me-1"></i> Thêm phương án
                                    </button>
                                    <div class="form-text text-end pt-2">Phải có ít nhất 2 phương án và tối đa 6 phương án.</div>
                                </div>
                            </div>
                        @elseif($question->type == 'Tự luận' || $question->type == 'Tình huống')
                            <div class="form-section">
                                <h5 class="form-section-title">Đáp án tham khảo</h5>
                                
                                <div class="mb-3">
                                    <label for="essayAnswer" class="form-label">Đáp án mẫu</label>
                                    <div class="editor-container">
                                        <div id="essayAnswer-toolbar"></div>
                                        <textarea id="essayAnswer" name="essay_answer" class="form-control d-none">{{ old('essay_answer', $essayAnswer ?? '') }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="gradingRubric" class="form-label">Tiêu chí chấm điểm</label>
                                    <div class="editor-container">
                                        <div id="gradingRubric-toolbar"></div>
                                        <textarea id="gradingRubric" name="grading_rubric" class="form-control d-none">{{ old('grading_rubric', $gradingRubric ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @elseif($question->type == 'Thực hành')
                            <div class="form-section">
                                <h5 class="form-section-title">Hướng dẫn thực hành</h5>
                                
                                <div class="mb-3">
                                    <label for="practicalInstructions" class="form-label">Hướng dẫn chi tiết</label>
                                    <div class="editor-container">
                                        <div id="practicalInstructions-toolbar"></div>
                                        <textarea id="practicalInstructions" name="practical_instructions" class="form-control d-none">{{ old('practical_instructions', $practicalInstructions ?? '') }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="evaluationCriteria" class="form-label">Tiêu chí đánh giá</label>
                                    <div class="editor-container">
                                        <div id="evaluationCriteria-toolbar"></div>
                                        <textarea id="evaluationCriteria" name="evaluation_criteria" class="form-control d-none">{{ old('evaluation_criteria', $evaluationCriteria ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog me-1"></i> Cài đặt và Phân loại
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="difficulty" class="form-label required-label">Độ khó</label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="">-- Chọn độ khó --</option>
                                <option value="Dễ" {{ old('difficulty', $question->difficulty) == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                                <option value="Trung bình" {{ old('difficulty', $question->difficulty) == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                                <option value="Khó" {{ old('difficulty', $question->difficulty) == 'Khó' ? 'selected' : '' }}>Khó</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="position_id" class="form-label">Chức danh</label>
                            <select class="form-select" id="position_id" name="position_id">
                                <option value="">-- Áp dụng cho tất cả --</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id', $question->position_id) == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ship_type_id" class="form-label">Loại tàu</label>
                            <select class="form-select" id="ship_type_id" name="ship_type_id">
                                <option value="">-- Áp dụng cho tất cả --</option>
                                @foreach($shipTypes as $shipType)
                                    <option value="{{ $shipType->id }}" {{ old('ship_type_id', $question->ship_type_id) == $shipType->id ? 'selected' : '' }}>{{ $shipType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $question->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Nếu không tìm thấy danh mục phù hợp, vui lòng nhập tên danh mục mới bên dưới</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Tên danh mục khác</label>
                            <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $question->category) }}" placeholder="Nhập tên danh mục nếu không có trong danh sách trên">
                        </div>
                    </div>
                </div>
                
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                            <a href="{{ route('admin.questions.show', $question->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM đã sẵn sàng');
        
        // Gắn sự kiện cho các nút xóa phương án
        setupRemoveOptionEvents();
        
        // Khởi tạo CKEditor cho nội dung câu hỏi
        initializeCKEditors();
        
        // Xử lý nút thêm phương án mới
        setupAddOptionButton();
        
        // Xử lý form submit để chuyển đổi correct_answer thành is_correct
        setupFormSubmit();
    });
    
    // Thiết lập các sự kiện xóa phương án
    function setupRemoveOptionEvents() {
        console.log('Thiết lập sự kiện xóa phương án');
        document.querySelectorAll('.remove-option').forEach(function(button) {
            button.addEventListener('click', function() {
                console.log('Đã nhấp vào nút xóa');
                const optionId = this.getAttribute('data-id');
                console.log('ID phương án cần xóa:', optionId);
                removeOption(optionId);
            });
        });
    }
    
    // Xóa phương án
    function removeOption(optionId) {
        console.log('Đang thực hiện xóa phương án với ID:', optionId);
        if (confirm('Bạn có chắc chắn muốn xóa phương án này?')) {
            const container = document.getElementById('answer-options-container');
            const options = container.querySelectorAll('.answer-option');
            console.log('Tổng số phương án hiện tại:', options.length);
            
            if (options.length <= 2) {
                alert('Phải có ít nhất 2 phương án trả lời.');
                return;
            }
            
            // Tìm và xóa phương án theo ID
            let removed = false;
            for (let i = 0; i < options.length; i++) {
                if (options[i].dataset.optionId == optionId) {
                    console.log('Đã tìm thấy phương án cần xóa:', options[i]);
                    options[i].remove();
                    removed = true;
                    break;
                }
            }
            
            if (!removed) {
                console.error('Không tìm thấy phương án với ID:', optionId);
                return;
            }
            
            // Cập nhật lại nhãn và ID cho các phương án còn lại
            updateRemainingOptions();
        }
    }
    
    // Cập nhật lại các phương án còn lại sau khi xóa
    function updateRemainingOptions() {
        console.log('Cập nhật lại các phương án còn lại');
        const container = document.getElementById('answer-options-container');
        const remainingOptions = container.querySelectorAll('.answer-option');
        
        for (let i = 0; i < remainingOptions.length; i++) {
            const optionLabel = remainingOptions[i].querySelector('.form-label');
            optionLabel.textContent = `Phương án ${String.fromCharCode(65 + i)}`;
            
            const contentTextarea = remainingOptions[i].querySelector('textarea[name^="answers"][name$="[content]"]');
            contentTextarea.name = `answers[${i}][content]`;
            
            const explanationTextarea = remainingOptions[i].querySelector('textarea[name^="answers"][name$="[explanation]"]');
            explanationTextarea.name = `answers[${i}][explanation]`;
            
            const radioInput = remainingOptions[i].querySelector('.correct-answer');
            radioInput.id = `correct${i}`;
            radioInput.value = i;
            
            const radioLabel = remainingOptions[i].querySelector('.form-check-label');
            radioLabel.setAttribute('for', `correct${i}`);
            
            // Cập nhật data-option-id
            remainingOptions[i].dataset.optionId = i;
            
            // Cập nhật data-id cho nút xóa
            const removeButton = remainingOptions[i].querySelector('.remove-option');
            if (removeButton) {
                removeButton.setAttribute('data-id', i);
            }
        }
    }
    
    // Thiết lập nút thêm phương án mới
    function setupAddOptionButton() {
        const addButton = document.getElementById('add-option');
        if (addButton) {
            addButton.addEventListener('click', function() {
                console.log('Thêm phương án mới');
                const container = document.getElementById('answer-options-container');
                const optionCount = container.children.length;
                
                if (optionCount >= 6) {
                    alert('Số lượng phương án tối đa là 6.');
                    return;
                }
                
                const optionLetter = String.fromCharCode(65 + optionCount);
                const newOptionId = optionCount;
                
                const newOption = document.createElement('div');
                newOption.className = 'answer-option';
                newOption.dataset.optionId = newOptionId;
                
                newOption.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label required-label">Phương án ${optionLetter}</label>
                        <textarea class="form-control" name="answers[${newOptionId}][content]" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Giải thích (tùy chọn)</label>
                        <textarea class="form-control" name="answers[${newOptionId}][explanation]" rows="2"></textarea>
                    </div>
                    
                    <div class="is-correct-option">
                        <div class="form-check">
                            <input class="form-check-input correct-answer" type="radio" name="correct_answer" id="correct${newOptionId}" value="${newOptionId}">
                            <label class="form-check-label" for="correct${newOptionId}">
                                Đây là đáp án đúng
                            </label>
                        </div>
                    </div>
                    
                    <div class="remove-option" data-id="${newOptionId}">
                        <i class="fas fa-times-circle"></i>
                    </div>
                `;
                
                container.appendChild(newOption);
                
                // Gắn sự kiện cho nút xóa mới thêm vào
                const newRemoveButton = newOption.querySelector('.remove-option');
                newRemoveButton.addEventListener('click', function() {
                    console.log('Đã nhấp vào nút xóa mới thêm');
                    const id = this.getAttribute('data-id');
                    removeOption(id);
                });
            });
        }
    }
    
    // Khởi tạo các trình soạn thảo CKEditor
    function initializeCKEditors() {
        // Khởi tạo CKEditor cho nội dung câu hỏi
        if (document.querySelector('#content')) {
            ClassicEditor
                .create(document.querySelector('#content'))
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#content').value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error('Lỗi khi khởi tạo CKEditor cho nội dung:', error);
                });
        }
        
        // Khởi tạo CKEditor cho các trường khác tùy theo loại câu hỏi
        const questionType = document.querySelector('input[name="question_type"]').value;
        
        // Khởi tạo cho câu hỏi tự luận hoặc tình huống
        if (questionType === 'Tự luận' || questionType === 'Tình huống') {
            // Khởi tạo CKEditor cho đáp án tự luận
            if (document.querySelector('#essayAnswer')) {
                ClassicEditor
                    .create(document.querySelector('#essayAnswer'))
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            document.querySelector('#essayAnswer').value = editor.getData();
                        });
                    })
                    .catch(error => {
                        console.error('Lỗi khi khởi tạo CKEditor cho đáp án:', error);
                    });
            }
            
            // Khởi tạo CKEditor cho tiêu chí chấm điểm
            if (document.querySelector('#gradingRubric')) {
                ClassicEditor
                    .create(document.querySelector('#gradingRubric'))
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            document.querySelector('#gradingRubric').value = editor.getData();
                        });
                    })
                    .catch(error => {
                        console.error('Lỗi khi khởi tạo CKEditor cho tiêu chí chấm điểm:', error);
                    });
            }
        }
        
        // Khởi tạo cho câu hỏi thực hành
        if (questionType === 'Thực hành') {
            if (document.querySelector('#practicalInstructions')) {
                ClassicEditor
                    .create(document.querySelector('#practicalInstructions'))
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            document.querySelector('#practicalInstructions').value = editor.getData();
                        });
                    })
                    .catch(error => {
                        console.error('Lỗi khi khởi tạo CKEditor cho hướng dẫn:', error);
                    });
            }
            
            if (document.querySelector('#evaluationCriteria')) {
                ClassicEditor
                    .create(document.querySelector('#evaluationCriteria'))
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            document.querySelector('#evaluationCriteria').value = editor.getData();
                        });
                    })
                    .catch(error => {
                        console.error('Lỗi khi khởi tạo CKEditor cho tiêu chí đánh giá:', error);
                    });
            }
        }
    }
    
    // Xử lý form submit
    function setupFormSubmit() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // Ngăn chặn submit mặc định
            e.preventDefault();
            
            // Xử lý đáp án cho câu hỏi trắc nghiệm
            const questionType = document.querySelector('input[name="question_type"]').value;
            if (questionType === 'Trắc nghiệm') {
                // Lấy giá trị của correct_answer
                const correctAnswerRadios = document.querySelectorAll('input[name="correct_answer"]');
                let correctIndex = null;
                let hasCheckedAnswer = false;
                
                for (let i = 0; i < correctAnswerRadios.length; i++) {
                    if (correctAnswerRadios[i].checked) {
                        correctIndex = correctAnswerRadios[i].value;
                        hasCheckedAnswer = true;
                        break;
                    }
                }
                
                // Kiểm tra xem đã chọn đáp án đúng chưa
                if (!hasCheckedAnswer) {
                    alert('Vui lòng chọn một đáp án đúng');
                    return;
                }
                
                // Chuyển đổi tên trường từ dạng cũ sang dạng mới
                    const answerOptions = document.querySelectorAll('.answer-option');
                    for (let i = 0; i < answerOptions.length; i++) {
                    // Các textarea nội dung và giải thích
                    const contentTextarea = answerOptions[i].querySelector('textarea[name^="answers"][name$="[content]"]');
                    const explanationTextarea = answerOptions[i].querySelector('textarea[name^="answers"][name$="[explanation]"]');
                    
                    if (contentTextarea) {
                        contentTextarea.name = `answers[${i}][content]`;
                    }
                    
                    if (explanationTextarea) {
                        explanationTextarea.name = `answers[${i}][explanation]`;
                    }
                    
                        // Tạo input ẩn để đánh dấu câu trả lời đúng
                        const isCorrectInput = document.createElement('input');
                        isCorrectInput.type = 'hidden';
                        isCorrectInput.name = `answers[${i}][is_correct]`;
                        isCorrectInput.value = (i == correctIndex) ? '1' : '0';
                        answerOptions[i].appendChild(isCorrectInput);
                    }
                }
            
            // Đảm bảo trường type luôn được gửi lên
            if (!form.querySelector('input[name="type"]')) {
                const typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = 'type';
                typeInput.value = questionType;
                form.appendChild(typeInput);
            }
            
            // Submit form
            form.submit();
        });
    }
</script>
@endsection 