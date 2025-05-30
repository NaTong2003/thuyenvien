@extends('layouts.app')

@section('title', 'Tạo Bài Kiểm Tra Ngẫu Nhiên - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-random me-2"></i> Tạo Bài Kiểm Tra Ngẫu Nhiên
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tests.index') }}">Bài Kiểm tra</a></li>
            <li class="breadcrumb-item active">Tạo đề ngẫu nhiên</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin bài kiểm tra</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tests.random.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
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
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                <label for="difficulty" class="form-label">Độ khó câu hỏi <span class="text-danger">*</span></label>
                                <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                    <option value="">-- Tất cả độ khó --</option>
                                    <option value="Dễ" {{ old('difficulty') == 'Dễ' ? 'selected' : '' }}>Dễ</option>
                                    <option value="Trung bình" {{ old('difficulty') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="Khó" {{ old('difficulty') == 'Khó' ? 'selected' : '' }}>Khó</option>
                                </select>
                                @error('difficulty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
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
                        </div>
                        
                        <div class="row mb-3">
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
                                <label for="duration" class="form-label">Thời gian làm bài (phút) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', 60) }}" min="5" max="180" required>
                                @error('duration')
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
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="random_questions_count" class="form-label">Số lượng câu hỏi <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('random_questions_count') is-invalid @enderror" id="random_questions_count" name="random_questions_count" value="{{ old('random_questions_count', 10) }}" min="1" max="50" required>
                                @error('random_questions_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Lưu bài kiểm tra ngẫu nhiên
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin về bài kiểm tra ngẫu nhiên</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Bài kiểm tra ngẫu nhiên là gì?</h5>
                        <p>Bài kiểm tra ngẫu nhiên sẽ tự động chọn ngẫu nhiên câu hỏi từ ngân hàng đề thi mỗi khi thuyền viên bắt đầu làm bài.</p>
                    </div>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold">Ưu điểm:</h6>
                    <ul>
                        <li>Tăng tính khách quan trong đánh giá</li>
                        <li>Giảm khả năng học thuộc đáp án</li>
                        <li>Đánh giá toàn diện kiến thức thuyền viên</li>
                        <li>Giảm công sức tạo nhiều đề kiểm tra khác nhau</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold">Cách thức hoạt động:</h6>
                    <ol>
                        <li>Hệ thống lọc câu hỏi theo tiêu chí (chức danh, loại tàu, độ khó)</li>
                        <li>Khi thuyền viên bắt đầu làm bài, hệ thống chọn ngẫu nhiên số lượng câu hỏi đã chỉ định</li>
                        <li>Mỗi lần làm bài, thuyền viên sẽ nhận được một bộ câu hỏi khác nhau</li>
                    </ol>
                </div>
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
            
        // Xử lý nút kiểm tra số lượng câu hỏi có sẵn
        $('#check-available-questions').on('click', function() {
            const position_id = $('#position_id').val();
            const ship_type_id = $('#ship_type_id').val();
            const difficulty = $('#difficulty').val();
            const category_id = $('#category_id').val();
            
            // Hiển thị loader
            $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Đang kiểm tra...');
            
            // Gửi AJAX request để kiểm tra số lượng câu hỏi
            $.ajax({
                url: "{{ route('admin.questions.count') }}",
                type: "GET",
                data: {
                    position_id: position_id,
                    ship_type_id: ship_type_id,
                    difficulty: difficulty,
                    category_id: category_id
                },
                success: function(response) {
                    $('#available-count').text(response.count);
                    $('#available-questions-info').removeClass('d-none');
                    
                    // Cập nhật màu sắc thông báo dựa trên số lượng câu hỏi
                    if (response.count === 0) {
                        $('#available-questions-info').removeClass('alert-info').addClass('alert-danger');
                    } else if (response.count < $('#random_questions_count').val()) {
                        $('#available-questions-info').removeClass('alert-info alert-danger').addClass('alert-warning');
                    } else {
                        $('#available-questions-info').removeClass('alert-danger alert-warning').addClass('alert-info');
                    }
                    
                    // Đặt lại nút
                    $('#check-available-questions').html('<i class="fas fa-search me-1"></i> Kiểm tra số lượng câu hỏi');
                    
                    // Cập nhật giá trị tối đa cho số lượng câu hỏi
                    if (response.count > 0) {
                        $('#random_questions_count').attr('max', response.count);
                        if ($('#random_questions_count').val() > response.count) {
                            $('#random_questions_count').val(response.count);
                        }
                    }
                },
                error: function(error) {
                    console.error('Error checking question count:', error);
                    $('#available-questions-info').removeClass('d-none alert-info').addClass('alert-danger');
                    $('#available-count').text('0');
                    $('#available-questions-info').append('<br><small>Đã xảy ra lỗi khi kiểm tra số câu hỏi</small>');
                    
                    // Đặt lại nút
                    $('#check-available-questions').html('<i class="fas fa-search me-1"></i> Kiểm tra số lượng câu hỏi');
                }
            });
        });
    });
</script>
@endsection 