@extends('layouts.app')

@section('title', 'Thêm Câu hỏi Mới - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .answer-option {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }
    
    .answer-option.is-correct {
        background-color: #d1e7dd;
        border-left: 4px solid #198754;
    }
    
    .remove-option {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #dc3545;
        cursor: pointer;
    }
    
    .required-label::after {
        content: " *";
        color: #dc3545;
    }
    
    .form-section {
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .editor-container {
        min-height: 200px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle me-2"></i> Thêm câu hỏi mới
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Ngân hàng Câu hỏi</a></li>
            <li class="breadcrumb-item active">Thêm câu hỏi</li>
        </ol>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin câu hỏi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.questions.store') }}" method="POST">
                @csrf
                
                <div class="form-section">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label required-label">Loại câu hỏi</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">-- Chọn loại câu hỏi --</option>
                                <option value="Trắc nghiệm" {{ old('type') == 'Trắc nghiệm' ? 'selected' : '' }}>Trắc nghiệm</option>
                                <option value="Tự luận" {{ old('type') == 'Tự luận' ? 'selected' : '' }}>Tự luận</option>
                                <option value="Tình huống" {{ old('type') == 'Tình huống' ? 'selected' : '' }}>Tình huống</option>
                                <option value="Thực hành" {{ old('type') == 'Thực hành' ? 'selected' : '' }}>Thực hành</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="difficulty" class="form-label required-label">Độ khó</label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="">-- Chọn độ khó --</option>
                                <option value="Dễ" {{ old('difficulty') == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                                <option value="Trung bình" {{ old('difficulty') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                                <option value="Khó" {{ old('difficulty') == 'Khó' ? 'selected' : '' }}>Khó</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="position_id" class="form-label">Chức danh liên quan</label>
                            <select class="form-select" id="position_id" name="position_id">
                                <option value="">-- Tất cả chức danh --</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ship_type_id" class="form-label">Loại tàu liên quan</label>
                            <select class="form-select" id="ship_type_id" name="ship_type_id">
                                <option value="">-- Tất cả loại tàu --</option>
                                @foreach($shipTypes as $shipType)
                                    <option value="{{ $shipType->id }}" {{ old('ship_type_id') == $shipType->id ? 'selected' : '' }}>
                                        {{ $shipType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="mb-3">
                        <label for="content" class="form-label required-label">Nội dung câu hỏi</label>
                        <div class="editor-container">
                            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
                        </div>
                        <div class="form-text">Nhập nội dung câu hỏi chi tiết ở đây.</div>
                    </div>
                </div>
                
                <div id="multiplechoice-options" class="form-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label required-label">Các phương án trả lời (cho câu hỏi trắc nghiệm)</label>
                        <button type="button" class="btn btn-sm btn-primary" id="add-option">
                            <i class="fas fa-plus me-1"></i> Thêm phương án
                        </button>
                    </div>
                    
                    <div id="options-container">
                        @if(old('answers'))
                            @foreach(old('answers') as $key => $answer)
                                <div class="answer-option {{ old('is_correct') == $key ? 'is-correct' : '' }}">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-2">
                                                <label class="form-label">Nội dung phương án</label>
                                                <input type="text" class="form-control" name="answers[]" value="{{ $answer }}" placeholder="Nhập nội dung phương án...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <label class="form-label">Đáp án đúng?</label>
                                                <div>
                                                    <div class="form-check">
                                                        <input class="form-check-input correct-option" type="radio" name="is_correct" value="{{ $key }}" {{ old('is_correct') == $key ? 'checked' : '' }}>
                                                        <label class="form-check-label">Đây là đáp án đúng</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Giải thích (tùy chọn)</label>
                                        <input type="text" class="form-control" name="explanations[]" value="{{ old('explanations')[$key] ?? '' }}" placeholder="Giải thích tại sao đáp án này đúng/sai...">
                                    </div>
                                    <i class="fas fa-times-circle remove-option"></i>
                                </div>
                            @endforeach
                        @else
                            <div class="answer-option">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-2">
                                            <label class="form-label">Nội dung phương án</label>
                                            <input type="text" class="form-control" name="answers[]" placeholder="Nhập nội dung phương án...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">Đáp án đúng?</label>
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input correct-option" type="radio" name="is_correct" value="0" checked>
                                                    <label class="form-check-label">Đây là đáp án đúng</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Giải thích (tùy chọn)</label>
                                    <input type="text" class="form-control" name="explanations[]" placeholder="Giải thích tại sao đáp án này đúng/sai...">
                                </div>
                            </div>
                            <div class="answer-option">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-2">
                                            <label class="form-label">Nội dung phương án</label>
                                            <input type="text" class="form-control" name="answers[]" placeholder="Nhập nội dung phương án...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">Đáp án đúng?</label>
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input correct-option" type="radio" name="is_correct" value="1">
                                                    <label class="form-check-label">Đây là đáp án đúng</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Giải thích (tùy chọn)</label>
                                    <input type="text" class="form-control" name="explanations[]" placeholder="Giải thích tại sao đáp án này đúng/sai...">
                                </div>
                                <i class="fas fa-times-circle remove-option"></i>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div id="essay-options" class="form-section" style="display: none;">
                    <div class="mb-3">
                        <label for="essay_answer" class="form-label">Đáp án mẫu (cho câu hỏi tự luận)</label>
                        <textarea class="form-control" id="essay_answer" name="essay_answer" rows="5">{{ old('essay_answer') }}</textarea>
                        <div class="form-text">Đáp án mẫu giúp người chấm thi tham khảo khi chấm điểm.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="grading_rubric" class="form-label">Tiêu chí chấm điểm</label>
                        <textarea class="form-control" id="grading_rubric" name="grading_rubric" rows="5">{{ old('grading_rubric') }}</textarea>
                        <div class="form-text">Mô tả tiêu chí chấm điểm, ví dụ: 5 điểm cho nội dung đầy đủ, 3 điểm cho phân tích, 2 điểm cho giải pháp...</div>
                    </div>
                </div>
                
                <div id="practical-options" class="form-section" style="display: none;">
                    <div class="mb-3">
                        <label for="practical_instructions" class="form-label">Hướng dẫn thực hành</label>
                        <textarea class="form-control" id="practical_instructions" name="practical_instructions" rows="5">{{ old('practical_instructions') }}</textarea>
                        <div class="form-text">Mô tả chi tiết các bước thực hiện bài tập thực hành.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="evaluation_criteria" class="form-label">Tiêu chí đánh giá</label>
                        <textarea class="form-control" id="evaluation_criteria" name="evaluation_criteria" rows="3">{{ old('evaluation_criteria') }}</textarea>
                        <div class="form-text">Mô tả cách thức đánh giá kết quả thực hành.</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu câu hỏi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý hiển thị/ẩn các phần tùy theo loại câu hỏi
        $('#type').change(function() {
            var selectedType = $(this).val();
            
            if (selectedType === 'Trắc nghiệm') {
                $('#multiplechoice-options').show();
                $('#essay-options').hide();
                $('#practical-options').hide();
            } else if (selectedType === 'Tự luận' || selectedType === 'Tình huống') {
                $('#multiplechoice-options').hide();
                $('#essay-options').show();
                $('#practical-options').hide();
            } else if (selectedType === 'Thực hành') {
                $('#multiplechoice-options').hide();
                $('#essay-options').hide();
                $('#practical-options').show();
            } else {
                $('#multiplechoice-options').hide();
                $('#essay-options').hide();
                $('#practical-options').hide();
            }
        });
        
        // Kích hoạt sự kiện change để hiển thị đúng với loại câu hỏi đã chọn
        $('#type').trigger('change');
        
        // Thêm phương án trả lời mới
        $('#add-option').click(function() {
            var optionsCount = $('.answer-option').length;
            
            var newOption = `
                <div class="answer-option">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <label class="form-label">Nội dung phương án</label>
                                <input type="text" class="form-control" name="answers[]" placeholder="Nhập nội dung phương án...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label">Đáp án đúng?</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input correct-option" type="radio" name="is_correct" value="${optionsCount}">
                                        <label class="form-check-label">Đây là đáp án đúng</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Giải thích (tùy chọn)</label>
                        <input type="text" class="form-control" name="explanations[]" placeholder="Giải thích tại sao đáp án này đúng/sai...">
                    </div>
                    <i class="fas fa-times-circle remove-option"></i>
                </div>
            `;
            
            $('#options-container').append(newOption);
            updateOptionIndexes();
        });
        
        // Xóa phương án trả lời
        $(document).on('click', '.remove-option', function() {
            if ($('.answer-option').length > 2) {
                $(this).closest('.answer-option').remove();
                updateOptionIndexes();
            } else {
                alert('Phải có ít nhất 2 phương án trả lời cho câu hỏi trắc nghiệm!');
            }
        });
        
        // Cập nhật chỉ số của các phương án khi thêm/xóa
        function updateOptionIndexes() {
            $('.correct-option').each(function(index) {
                $(this).val(index);
            });
        }
        
        // Đánh dấu phương án đúng khi người dùng chọn
        $(document).on('change', '.correct-option', function() {
            $('.answer-option').removeClass('is-correct');
            $(this).closest('.answer-option').addClass('is-correct');
        });
        
        // Khởi tạo trình soạn thảo nội dung phong phú nếu cần
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#content'))
                .catch(error => {
                    console.error(error);
                });
                
            ClassicEditor
                .create(document.querySelector('#essay_answer'))
                .catch(error => {
                    console.error(error);
                });
        }
    });
</script>
@endsection 