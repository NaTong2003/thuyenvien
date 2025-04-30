<?php

namespace App\Http\Controllers\Seafarer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Question;
use App\Models\Answer;
use App\Models\UserResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TestQuestion;

class TestController extends Controller
{
    /**
     * Hiển thị danh sách bài kiểm tra có sẵn
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $thuyenVien = $user->thuyenVien;
        
        // Lấy position_id và ship_type_id của thuyền viên
        $position_id = $thuyenVien ? $thuyenVien->position_id : null;
        $ship_type_id = $thuyenVien ? $thuyenVien->ship_type_id : null;
        
        // Xây dựng query với các bộ lọc
        $query = Test::where('is_active', true)
                    ->where(function($q) use ($position_id) {
                        $q->where('position_id', $position_id)
                          ->orWhereNull('position_id');
                    })
                    ->where(function($q) use ($ship_type_id) {
                        $q->where('ship_type_id', $ship_type_id)
                          ->orWhereNull('ship_type_id');
                    });
        
        // Lọc theo tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }
        
        // Lọc theo loại bài kiểm tra
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }
        
        // Lọc theo độ khó
        if ($request->has('difficulty') && !empty($request->difficulty)) {
            $query->where('difficulty', $request->difficulty);
        }
        
        // Sắp xếp
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'duration_asc':
                    $query->orderBy('duration', 'asc');
                    break;
                case 'duration_desc':
                    $query->orderBy('duration', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Lấy bài kiểm tra với phân trang
        $tests = $query->with(['position', 'shipType', 'questions'])
                      ->paginate(9);
        
        // Đảm bảo các tham số lọc được giữ lại trong liên kết phân trang
        $tests->appends($request->except('page'));
        
        // Lấy các lượt thi của người dùng
        $testAttempts = TestAttempt::where('user_id', $user->id)
                                   ->with('test')
                                   ->orderBy('created_at', 'desc')
                                   ->get()
                                   ->groupBy('test_id');
        
        return view('seafarer.tests.index', compact('tests', 'testAttempts'));
    }

    /**
     * Hiển thị thông tin chi tiết bài kiểm tra
     */
    public function show($id)
    {
        $test = Test::with(['position', 'shipType', 'questions' => function($query) {
                        $query->orderBy('order');
                    }])
                    ->findOrFail($id);
        
        // Kiểm tra xem thuyền viên đã làm bài kiểm tra này chưa
        $user = Auth::user();
        $attempts = TestAttempt::where('user_id', $user->id)
                                ->where('test_id', $test->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        return view('seafarer.tests.show', compact('test', 'attempts'));
    }

    /**
     * Hiển thị bài kiểm tra để làm
     */
    public function start($id)
    {
        // Lấy bài kiểm tra kèm theo các câu hỏi và đáp án
        $test = Test::findOrFail($id);
        
        // Kiểm tra xem bài kiểm tra có được kích hoạt không
        if (!$test->is_active) {
            return redirect()->route('seafarer.tests.index')
                            ->with('error', 'Bài kiểm tra này hiện không khả dụng.');
        }
        
        // Tạo một lượt thi mới
        $attempt = TestAttempt::create([
            'user_id' => Auth::id(),
            'test_id' => $test->id,
            'start_time' => Carbon::now(),
            'is_completed' => false,
        ]);
        
        // Xử lý nếu là bài kiểm tra ngẫu nhiên
        if ($test->is_random) {
            // Lấy tất cả các câu hỏi phù hợp với chức danh và loại tàu
            $query = Question::query();
            
            if ($test->position_id) {
                $query->where(function($q) use ($test) {
                    $q->where('position_id', $test->position_id)
                      ->orWhereNull('position_id');
                });
            }
            
            if ($test->ship_type_id) {
                $query->where(function($q) use ($test) {
                    $q->where('ship_type_id', $test->ship_type_id)
                      ->orWhereNull('ship_type_id');
                });
            }
            
            // Lọc theo độ khó nếu có
            if ($test->difficulty) {
                $query->where('difficulty', $test->difficulty);
            }
            
            // Lọc theo danh mục nếu có
            if ($test->category) {
                $query->where('category', $test->category);
            }
            
            // Lấy ngẫu nhiên số lượng câu hỏi cần thiết
            $randomQuestions = $query->inRandomOrder()
                                    ->limit($test->random_questions_count ?? 10)
                                    ->get();
            
            // Tạo các bản ghi TestQuestion tạm thời cho lần thi này
            foreach ($randomQuestions as $index => $question) {
                TestQuestion::create([
                    'test_id' => $test->id,
                    'question_id' => $question->id,
                    'order' => $index + 1,
                    'is_temporary' => true,
                    'test_attempt_id' => $attempt->id // Thêm trường để biết đây là câu hỏi tạm cho lần thi nào
                ]);
            }
        }
        
        // Lấy các câu hỏi từ pivot table test_questions
        $testQuestions = TestQuestion::where('test_id', $test->id)
                        ->when($test->is_random, function($query) use ($attempt) {
                            // Nếu là bài kiểm tra ngẫu nhiên, chỉ lấy câu hỏi tạm thời cho lần thi này
                            return $query->where('test_attempt_id', $attempt->id);
                        })
                        ->with(['question' => function($q) {
                            $q->with('answers');
                        }])
                        ->orderBy('order')
                        ->get();
        
        // Đối với bài kiểm tra không phải ngẫu nhiên, trộn thứ tự câu hỏi nếu cần
        if (!$test->is_random && $test->shuffle_questions) {
            $testQuestions = $testQuestions->shuffle();
        }
        
        // Thiết lập thời gian kết thúc
        session(['test_end_time' => Carbon::now()->addMinutes($test->duration)->timestamp]);
        
        return view('seafarer.tests.take', compact('test', 'attempt', 'testQuestions'));
    }

    /**
     * Lưu kết quả bài kiểm tra
     */
    public function submit(Request $request, $id)
    {
        $attempt = TestAttempt::findOrFail($id);
        
        // Kiểm tra xem lượt thi có thuộc về người dùng hiện tại không
        if ($attempt->user_id != Auth::id()) {
            abort(403, 'Bạn không có quyền nộp bài kiểm tra này.');
        }
        
        // Kiểm tra xem lượt thi đã kết thúc chưa
        if ($attempt->is_completed) {
            return redirect()->route('seafarer.tests.result', $attempt->id);
        }
        
        // Lưu thời gian kết thúc
        $attempt->end_time = Carbon::now();
        $attempt->is_completed = true;
        
        // Lấy bài kiểm tra và câu hỏi
        $test = Test::findOrFail($attempt->test_id);
        $testQuestions = TestQuestion::where('test_id', $test->id)
                        ->with(['question' => function($q) {
                            $q->with('answers');
                        }])
                        ->orderBy('order')
                        ->get();
        
        // Tính điểm
        $totalQuestions = $testQuestions->count();
        $correctAnswers = 0;
        
        // Lưu câu trả lời của người dùng
        foreach ($request->input('responses', []) as $questionId => $responseData) {
            $question = Question::findOrFail($questionId);
            
            // Xử lý câu trả lời dựa vào loại câu hỏi
            if ($question->type == 'Trắc nghiệm') {
                // Câu hỏi trắc nghiệm
                $answerId = $responseData['answer_id'] ?? null;
                
                if ($answerId) {
                    $answer = Answer::find($answerId);
                    
                    // Tạo câu trả lời của người dùng
                    UserResponse::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                        'answer_id' => $answerId,
                        'text_response' => null,
                        'is_marked' => false,
                        'score' => $answer && $answer->is_correct ? 1 : 0,
                    ]);
                    
                    // Kiểm tra nếu đúng thì tăng số câu đúng
                    if ($answer && $answer->is_correct) {
                        $correctAnswers++;
                    }
                }
            } elseif ($question->type == 'Tự luận' || $question->type == 'Tình huống' || $question->type == 'Thực hành') {
                // Câu hỏi tự luận hoặc tình huống
                $textResponse = $responseData['text_response'] ?? null;
                
                if ($textResponse) {
                    // Tạo câu trả lời của người dùng (câu tự luận cần chấm điểm riêng)
                    UserResponse::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                        'answer_id' => null,
                        'text_response' => $textResponse,
                        'is_marked' => false,
                        'score' => null, // Cần đánh giá bởi admin
                    ]);
                }
            }
        }
        
        // Tính điểm nếu không có câu hỏi tự luận (chấm tự động)
        if ($totalQuestions > 0) {
            $score = ($correctAnswers / $totalQuestions) * 100;
            $attempt->score = round($score, 2);
        }
        
        $attempt->save();
        
        return redirect()->route('seafarer.tests.result', $attempt->id)
                        ->with('success', 'Bạn đã hoàn thành bài kiểm tra thành công!');
    }

    /**
     * Hiển thị kết quả bài kiểm tra
     */
    public function result($id)
    {
        $attempt = TestAttempt::with(['test', 'user', 'userResponses.question', 'userResponses.answer'])
                               ->findOrFail($id);
        
        // Kiểm tra quyền truy cập
        if ($attempt->user_id != Auth::id()) {
            abort(403, 'Bạn không có quyền xem kết quả này.');
        }
        
        $test = $attempt->test;
        
        // Lấy tất cả câu hỏi trong bài kiểm tra - sửa cách lấy để tránh lỗi
        $testQuestions = TestQuestion::where('test_id', $test->id)
                        ->with(['question' => function($q) {
                            $q->with('answers');
                        }])
                        ->orderBy('order')
                        ->get();
        
        return view('seafarer.tests.result', compact('attempt', 'test', 'testQuestions'));
    }
}
