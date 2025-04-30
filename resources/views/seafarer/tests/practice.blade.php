@extends('layouts.app')

@section('title', 'Kiểm tra thử - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .practice-mode-banner {
        background-color: #ffd43b;
        padding: 10px 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .practice-mode-banner i {
        margin-right: 8px;
        font-size: 1.1em;
    }
    
    .question-container {
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .question-number {
        font-weight: bold;
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .question-text {
        font-size: 1.1em;
        margin-bottom: 20px;
        font-weight: 500;
    }
    
    .option-list {
        list-style-type: none;
        padding: 0;
    }
    
    .option-item {
        padding: 12px 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .option-item:hover {
        background-color: #f8f9fa;
    }
    
    .option-item.selected {
        background-color: #e2f0ff;
        border-color: #4e73df;
    }
    
    .option-item.correct {
        background-color: #d4edda;
        border-color: #28a745;
    }
    
    .option-item.incorrect {
        background-color: #f8d7da;
        border-color: #dc3545;
    }
    
    .explanation-box {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-left: 4px solid #4e73df;
        border-radius: 4px;
    }
    
    .explanation-box h6 {
        margin-bottom: 10px;
        color: #3a3b45;
    }
    
    .explanation-box p {
        margin-bottom: 0;
        color: #6e707e;
    }
    
    .nav-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }
    
    .hint-button {
        position: absolute;
        top: 20px;
        right: 20px;
    }
    
    .difficulty-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8em;
        margin-left: 10px;
    }
    
    .difficulty-easy {
        background-color: #d4edda;
        color: #155724;
    }
    
    .difficulty-medium {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .difficulty-hard {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="practice-mode-banner">
        <div>
            <i class="fas fa-graduation-cap"></i> Bạn đang ở chế độ kiểm tra thử. Kết quả sẽ không được lưu.
        </div>
        <a href="{{ route('seafarer.tests.show', $test->id) }}" class="btn btn-sm btn-outline-dark">
            Quay lại bài kiểm tra
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-list me-1"></i> {{ $test->title }} - Bài tập thử
                    </h6>
                </div>
                <div class="card-body">
                    <div class="question-container position-relative" id="question-container">
                        <div class="question-number">
                            Câu hỏi 1
                            <span class="difficulty-badge difficulty-medium">Trung bình</span>
                        </div>
                        <div class="question-text">
                            Khi gặp sự cố trên biển, thuyền trưởng nên ưu tiên hành động nào đầu tiên?
                        </div>
                        <ul class="option-list" id="option-list">
                            <li class="option-item" data-option="A" onclick="selectOption(this)">
                                A. Thông báo cho công ty quản lý
                            </li>
                            <li class="option-item" data-option="B" onclick="selectOption(this)">
                                B. Liên lạc với các tàu lân cận để yêu cầu hỗ trợ
                            </li>
                            <li class="option-item" data-option="C" onclick="selectOption(this)">
                                C. Đánh giá tình hình và đảm bảo an toàn cho thuyền viên
                            </li>
                            <li class="option-item" data-option="D" onclick="selectOption(this)">
                                D. Ghi nhận vào nhật ký hàng hải
                            </li>
                        </ul>
                        <button class="btn btn-sm btn-outline-primary hint-button" onclick="showHint()">
                            <i class="fas fa-lightbulb me-1"></i> Gợi ý
                        </button>
                        
                        <div id="explanation-box" class="explanation-box mt-4" style="display: none;">
                            <h6><i class="fas fa-info-circle me-1"></i> Giải thích</h6>
                            <p>Đáp án đúng là C. Trong mọi tình huống khẩn cấp, việc đầu tiên là đánh giá tình hình và đảm bảo an toàn cho thuyền viên và hành khách. Điều này tuân theo nguyên tắc an toàn hàng hải và quy định SOLAS (Safety of Life at Sea).</p>
                        </div>
                    </div>
                    
                    <div class="nav-buttons">
                        <button class="btn btn-secondary" id="prev-btn" disabled>
                            <i class="fas fa-arrow-left me-1"></i> Câu trước
                        </button>
                        <button class="btn btn-primary" id="check-btn" onclick="checkAnswer()">
                            Kiểm tra đáp án
                        </button>
                        <button class="btn btn-primary" id="next-btn" onclick="nextQuestion()" style="display: none;">
                            Câu tiếp theo <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> Hướng dẫn chế độ luyện tập
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li class="mb-2">Chế độ luyện tập cho phép bạn làm thử các câu hỏi tương tự như trong bài kiểm tra thật.</li>
                        <li class="mb-2">Bạn có thể kiểm tra đáp án ngay sau khi trả lời mỗi câu hỏi.</li>
                        <li class="mb-2">Hệ thống sẽ hiển thị giải thích chi tiết để giúp bạn hiểu rõ hơn về câu hỏi.</li>
                        <li class="mb-2">Kết quả luyện tập không được tính vào điểm đánh giá chính thức.</li>
                        <li>Bạn có thể luyện tập nhiều lần để nâng cao kỹ năng trước khi thực hiện bài kiểm tra thật.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mock data for demo purposes
    const practiceQuestions = [
        {
            id: 1,
            text: "Khi gặp sự cố trên biển, thuyền trưởng nên ưu tiên hành động nào đầu tiên?",
            difficulty: "medium",
            options: [
                {label: "A", text: "Thông báo cho công ty quản lý"},
                {label: "B", text: "Liên lạc với các tàu lân cận để yêu cầu hỗ trợ"},
                {label: "C", text: "Đánh giá tình hình và đảm bảo an toàn cho thuyền viên"},
                {label: "D", text: "Ghi nhận vào nhật ký hàng hải"}
            ],
            correctAnswer: "C",
            explanation: "Đáp án đúng là C. Trong mọi tình huống khẩn cấp, việc đầu tiên là đánh giá tình hình và đảm bảo an toàn cho thuyền viên và hành khách. Điều này tuân theo nguyên tắc an toàn hàng hải và quy định SOLAS (Safety of Life at Sea).",
            hint: "Hãy nhớ rằng an toàn con người luôn được ưu tiên cao nhất trong mọi tình huống."
        },
        {
            id: 2,
            text: "Điều nào sau đây KHÔNG phải là nhiệm vụ của sỹ quan trực ca?",
            difficulty: "easy",
            options: [
                {label: "A", text: "Duy trì cảnh giác và quan sát thường xuyên"},
                {label: "B", text: "Điều chỉnh và sửa chữa các thiết bị máy móc chính của tàu"},
                {label: "C", text: "Đảm bảo tuân thủ kế hoạch hành trình"},
                {label: "D", text: "Ghi chép đầy đủ sự kiện vào nhật ký hàng hải"}
            ],
            correctAnswer: "B",
            explanation: "Đáp án đúng là B. Điều chỉnh và sửa chữa các thiết bị máy móc chính của tàu là nhiệm vụ của bộ phận kỹ thuật và máy tàu, không phải của sỹ quan trực ca boong. Sỹ quan trực ca chủ yếu chịu trách nhiệm về an toàn hàng hải, quan sát, điều hướng và xử lý tình huống trong ca trực của mình.",
            hint: "Hãy nghĩ về phạm vi trách nhiệm của sỹ quan trực ca boong và sỹ quan máy."
        },
        {
            id: 3,
            text: "Khi gặp tình huống cứu nạn người trên biển (Man Overboard), hành động đầu tiên cần thực hiện là gì?",
            difficulty: "hard",
            options: [
                {label: "A", text: "Phát tín hiệu Mayday"},
                {label: "B", text: "Quay tàu theo phương pháp Williamson"},
                {label: "C", text: "Ném phao cứu sinh và hô lớn \"Người rơi xuống biển\""},
                {label: "D", text: "Dừng máy chính ngay lập tức"}
            ],
            correctAnswer: "C",
            explanation: "Đáp án đúng là C. Khi phát hiện người rơi xuống biển, hành động đầu tiên là ném phao cứu sinh và hô lớn \"Người rơi xuống biển\" (Man Overboard). Điều này giúp đánh dấu vị trí nạn nhân và cảnh báo toàn đội. Sau đó mới thực hiện các bước tiếp theo như quay tàu, dừng máy và phát tín hiệu cứu nạn nếu cần.",
            hint: "Hãy nghĩ về thứ tự ưu tiên: đánh dấu vị trí, cảnh báo, rồi mới thực hiện các thao tác kỹ thuật."
        }
    ];
    
    let currentQuestionIndex = 0;
    let selectedOption = null;
    let isAnswerChecked = false;
    
    function selectOption(optionElement) {
        if (isAnswerChecked) return;
        
        // Remove selected class from all options
        document.querySelectorAll('.option-item').forEach(item => {
            item.classList.remove('selected');
        });
        
        // Add selected class to clicked option
        optionElement.classList.add('selected');
        selectedOption = optionElement.getAttribute('data-option');
    }
    
    function checkAnswer() {
        if (!selectedOption || isAnswerChecked) return;
        
        const correctAnswer = practiceQuestions[currentQuestionIndex].correctAnswer;
        isAnswerChecked = true;
        
        // Mark correct and incorrect options
        document.querySelectorAll('.option-item').forEach(item => {
            const option = item.getAttribute('data-option');
            
            if (option === correctAnswer) {
                item.classList.add('correct');
            } else if (option === selectedOption) {
                item.classList.add('incorrect');
            }
        });
        
        // Show explanation
        document.getElementById('explanation-box').style.display = 'block';
        
        // Show next button and hide check button
        document.getElementById('check-btn').style.display = 'none';
        document.getElementById('next-btn').style.display = 'block';
    }
    
    function showHint() {
        alert(practiceQuestions[currentQuestionIndex].hint);
    }
    
    function nextQuestion() {
        if (currentQuestionIndex < practiceQuestions.length - 1) {
            currentQuestionIndex++;
            renderQuestion();
            
            // Reset state
            isAnswerChecked = false;
            selectedOption = null;
            
            // Toggle navigation buttons
            document.getElementById('prev-btn').disabled = false;
            document.getElementById('check-btn').style.display = 'block';
            document.getElementById('next-btn').style.display = 'none';
            document.getElementById('explanation-box').style.display = 'none';
        } else {
            alert('Bạn đã hoàn thành tất cả câu hỏi thử nghiệm!');
        }
    }
    
    function prevQuestion() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            renderQuestion();
            
            // Reset state
            isAnswerChecked = false;
            selectedOption = null;
            
            // Toggle navigation buttons
            document.getElementById('prev-btn').disabled = currentQuestionIndex === 0;
            document.getElementById('check-btn').style.display = 'block';
            document.getElementById('next-btn').style.display = 'none';
            document.getElementById('explanation-box').style.display = 'none';
        }
    }
    
    function renderQuestion() {
        const question = practiceQuestions[currentQuestionIndex];
        
        // Update question number and difficulty
        document.querySelector('.question-number').innerHTML = `
            Câu hỏi ${question.id}
            <span class="difficulty-badge difficulty-${question.difficulty}">
                ${getDifficultyText(question.difficulty)}
            </span>
        `;
        
        // Update question text
        document.querySelector('.question-text').textContent = question.text;
        
        // Update options
        const optionListHTML = question.options.map(option => 
            `<li class="option-item" data-option="${option.label}" onclick="selectOption(this)">
                ${option.label}. ${option.text}
            </li>`
        ).join('');
        
        document.getElementById('option-list').innerHTML = optionListHTML;
        
        // Update explanation
        document.querySelector('#explanation-box p').textContent = question.explanation;
    }
    
    function getDifficultyText(difficulty) {
        switch(difficulty) {
            case 'easy': return 'Dễ';
            case 'medium': return 'Trung bình';
            case 'hard': return 'Khó';
            default: return '';
        }
    }
    
    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('prev-btn').addEventListener('click', prevQuestion);
    });
</script>
@endsection 