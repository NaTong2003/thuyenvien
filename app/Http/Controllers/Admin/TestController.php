<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\TestQuestion;
use App\Models\TestAttempt;
use App\Models\Position;
use App\Models\ShipType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TestController extends Controller
{
    /**
     * Hiển thị danh sách bài kiểm tra
     */
    public function index(Request $request)
    {
        $query = Test::with('position', 'shipType');
        
        // Lọc theo chức danh
        if ($request->has('position_id') && !empty($request->position_id)) {
            $query->where('position_id', $request->position_id);
        }
        
        // Lọc theo loại tàu
        if ($request->has('ship_type_id') && !empty($request->ship_type_id)) {
            $query->where('ship_type_id', $request->ship_type_id);
        }
        
        // Lọc theo từ khóa
        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        $tests = $query->orderBy('created_at', 'desc')->paginate(10);
        $positions = Position::all();
        $shipTypes = ShipType::all();
        
        return view('admin.tests.index', compact('tests', 'positions', 'shipTypes'));
    }

    /**
     * Hiển thị form tạo bài kiểm tra mới
     */
    public function create()
    {
        $positions = Position::all();
        $shipTypes = ShipType::all();
        $questions = Question::with('position', 'shipType')
                            ->orderBy('category')
                            ->orderBy('difficulty')
                            ->get();
        
        return view('admin.tests.create', compact('positions', 'shipTypes', 'questions'));
    }

    /**
     * Lưu bài kiểm tra mới vào database
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'duration' => 'required|integer|min:5|max:180',
                'passing_score' => 'required|integer|min:0|max:100',
                'position_id' => 'nullable|exists:positions,id',
                'ship_type_id' => 'nullable|exists:ship_types,id',
                'question_ids' => $request->has('is_random') ? 'nullable|array' : 'required|array|min:1',
                'question_ids.*' => 'exists:questions,id',
                'category' => 'required|string|max:50',
                'is_active' => 'nullable|boolean',
                'is_random' => 'nullable|boolean',
                'random_questions_count' => 'required_if:is_random,1|nullable|integer|min:1',
                'difficulty' => 'required|string',
                'type' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Tạo bài kiểm tra mới
            $test = Test::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'passing_score' => $request->passing_score,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'category' => $request->category,
                'is_active' => $request->has('is_active') ? true : false,
                'is_random' => $request->has('is_random') ? true : false,
                'difficulty' => $request->difficulty,
                'type' => $request->type,
                'created_by' => auth()->id(),
            ]);
            
            // Nếu không phải bài kiểm tra ngẫu nhiên, thêm các câu hỏi cố định
            if (!$request->has('is_random')) {
                foreach ($request->question_ids as $index => $question_id) {
                    TestQuestion::create([
                        'test_id' => $test->id,
                        'question_id' => $question_id,
                        'order' => $index + 1,
                    ]);
                }
            } else {
                // Lưu thông tin về số lượng câu hỏi ngẫu nhiên vào bảng cài đặt
                // hoặc một trường metadata của bài kiểm tra nếu có
                $test->random_questions_count = $request->random_questions_count;
                $test->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.tests.index')
                            ->with('success', 'Thêm bài kiểm tra thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi thêm bài kiểm tra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị thông tin chi tiết bài kiểm tra
     */
    public function show($id)
    {
        $test = Test::with(['position', 'shipType', 'questions.question', 'testAttempts.user'])
                     ->findOrFail($id);
        
        // Sắp xếp các câu hỏi theo thứ tự
        $testQuestions = $test->questions()->with('question')->orderBy('order')->get();
        
        // Tính toán thống kê
        $stats = [];
        $testAttempts = $test->testAttempts;
        
        if ($testAttempts->count() > 0) {
            // Tổng số lượt làm bài
            $stats['totalAttempts'] = $testAttempts->count();
            
            // Điểm trung bình
            $stats['avgScore'] = $testAttempts->avg('score');
            
            // Điểm cao nhất và thấp nhất
            $stats['highestScore'] = $testAttempts->max('score');
            $stats['lowestScore'] = $testAttempts->min('score');
            
            // Số lượt đạt và tỷ lệ đạt
            $passingScore = $test->passing_score;
            $passCount = $testAttempts->filter(function ($attempt) use ($passingScore) {
                return $attempt->score >= $passingScore;
            })->count();
            
            $stats['passCount'] = $passCount;
            $stats['failCount'] = $stats['totalAttempts'] - $passCount;
            $stats['passRate'] = ($passCount / $stats['totalAttempts']) * 100;
            
            // Tạo dữ liệu cho biểu đồ phân phối điểm số
            $scoreRanges = [
                '0-10', '11-20', '21-30', '31-40', '41-50', 
                '51-60', '61-70', '71-80', '81-90', '91-100'
            ];
            
            $scoreDistribution = array_fill(0, count($scoreRanges), 0);
            
            foreach ($testAttempts as $attempt) {
                $score = $attempt->score;
                $index = min(floor($score / 10), 9); // Đảm bảo điểm số 100 nằm trong khoảng 91-100
                $scoreDistribution[$index]++;
            }
            
            $stats['scoreLabels'] = $scoreRanges;
            $stats['scoreDistribution'] = $scoreDistribution;
        }
        
        return view('admin.tests.show', compact('test', 'testQuestions', 'stats'));
    }

    /**
     * Hiển thị form chỉnh sửa bài kiểm tra
     */
    public function edit($id)
    {
        $test = Test::with('questions')->findOrFail($id);
        $positions = Position::all();
        $shipTypes = ShipType::all();
        
        // Lấy danh sách câu hỏi
        $questions = Question::with('position', 'shipType')
                            ->orderBy('category')
                            ->orderBy('difficulty')
                            ->get();
        
        // Lấy các ID câu hỏi hiện đang có trong bài kiểm tra
        $selectedQuestionIds = $test->questions()->pluck('question_id')->toArray();
        
        return view('admin.tests.edit', compact('test', 'positions', 'shipTypes', 'questions', 'selectedQuestionIds'));
    }

    /**
     * Cập nhật thông tin bài kiểm tra
     */
    public function update(Request $request, $id)
    {
        $test = Test::findOrFail($id);
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'duration' => 'required|integer|min:5|max:180',
                'passing_score' => 'required|integer|min:0|max:100',
                'position_id' => 'nullable|exists:positions,id',
                'ship_type_id' => 'nullable|exists:ship_types,id',
                'question_ids' => $request->has('is_random') ? 'nullable|array' : 'required|array|min:1',
                'question_ids.*' => 'exists:questions,id',
                'category' => 'required|string|max:50',
                'is_active' => 'nullable|boolean',
                'is_random' => 'nullable|boolean',
                'random_questions_count' => 'required_if:is_random,1|nullable|integer|min:1',
                'difficulty' => 'required|string',
                'type' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Cập nhật thông tin bài kiểm tra
            $test->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'passing_score' => $request->passing_score,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'category' => $request->category,
                'is_active' => $request->has('is_active') ? true : false,
                'is_random' => $request->has('is_random') ? true : false,
                'difficulty' => $request->difficulty,
                'type' => $request->type,
            ]);
            
            // Xóa tất cả câu hỏi cũ
            $test->questions()->delete();
            
            // Nếu không phải bài kiểm tra ngẫu nhiên, thêm các câu hỏi cố định
            if (!$request->has('is_random')) {
                foreach ($request->question_ids as $index => $question_id) {
                    TestQuestion::create([
                        'test_id' => $test->id,
                        'question_id' => $question_id,
                        'order' => $index + 1,
                    ]);
                }
            } else {
                // Lưu thông tin về số lượng câu hỏi ngẫu nhiên
                $test->random_questions_count = $request->random_questions_count;
                $test->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.tests.index')
                            ->with('success', 'Cập nhật bài kiểm tra thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật bài kiểm tra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa bài kiểm tra
     */
    public function destroy($id)
    {
        $test = Test::findOrFail($id);
        
        // Kiểm tra xem bài kiểm tra đã có ai làm chưa
        if ($test->testAttempts()->count() > 0) {
            return redirect()->route('admin.tests.index')
                            ->with('error', 'Không thể xóa bài kiểm tra này vì đã có thuyền viên làm bài!');
        }
        
        // Xóa tất cả câu hỏi của bài kiểm tra
        $test->questions()->delete();
        
        // Xóa bài kiểm tra
        $test->delete();
        
        return redirect()->route('admin.tests.index')
                        ->with('success', 'Xóa bài kiểm tra thành công!');
    }
    
    /**
     * Hiển thị kết quả của bài kiểm tra
     */
    public function results($id)
    {
        $test = Test::with('position', 'shipType')->findOrFail($id);
        $testAttempts = TestAttempt::with('user')
                                   ->where('test_id', $id)
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(20);
        
        return view('admin.tests.results', compact('test', 'testAttempts'));
    }

    /**
     * Hiển thị form tạo bài kiểm tra ngẫu nhiên
     */
    public function createRandom()
    {
        $positions = Position::all();
        $shipTypes = ShipType::all();
        
        return view('admin.tests.create_random', compact('positions', 'shipTypes'));
    }

    /**
     * Lưu bài kiểm tra ngẫu nhiên vào database
     */
    public function storeRandom(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'duration' => 'required|integer|min:5|max:180',
                'passing_score' => 'required|integer|min:0|max:100',
                'position_id' => 'nullable|exists:positions,id',
                'ship_type_id' => 'nullable|exists:ship_types,id',
                'random_questions_count' => 'required|integer|min:1|max:50',
                'category' => 'required|string|max:50',
                'difficulty' => 'required|string',
                'type' => 'required|string',
                'is_active' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Tạo bài kiểm tra ngẫu nhiên mới
            $test = Test::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'passing_score' => $request->passing_score,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'category' => $request->category,
                'difficulty' => $request->difficulty,
                'type' => $request->type,
                'is_active' => $request->has('is_active') ? true : false,
                'is_random' => true,
                'random_questions_count' => $request->random_questions_count,
                'created_by' => auth()->id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.tests.index')
                            ->with('success', 'Thêm bài kiểm tra ngẫu nhiên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi thêm bài kiểm tra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xem trước bài kiểm tra
     */
    public function preview($id)
    {
        $test = Test::with(['questions.question.answers'])
                     ->findOrFail($id);
        
        // Sắp xếp các câu hỏi theo thứ tự
        $testQuestions = $test->questions()->with('question.answers')->orderBy('order')->get();
        
        return view('admin.tests.preview', compact('test', 'testQuestions'));
    }

    /**
     * Hiển thị thống kê chi tiết của bài kiểm tra
     */
    public function statistics($id)
    {
        $test = Test::with(['testAttempts.user', 'testAttempts.userResponses'])
                     ->findOrFail($id);
        
        $testAttempts = $test->testAttempts()->with('user', 'userResponses.question', 'userResponses.answer')
                               ->orderBy('created_at', 'desc')
                               ->get();
        
        // Tính toán thống kê chi tiết
        $stats = [];
        
        if ($testAttempts->count() > 0) {
            // Tổng số lượt làm bài
            $stats['totalAttempts'] = $testAttempts->count();
            
            // Điểm trung bình
            $stats['avgScore'] = $testAttempts->avg('score');
            
            // Điểm cao nhất và thấp nhất
            $stats['highestScore'] = $testAttempts->max('score');
            $stats['lowestScore'] = $testAttempts->min('score');
            
            // Số lượt đạt và tỷ lệ đạt
            $passingScore = $test->passing_score;
            $passCount = $testAttempts->filter(function ($attempt) use ($passingScore) {
                return $attempt->score >= $passingScore;
            })->count();
            
            $stats['passCount'] = $passCount;
            $stats['failCount'] = $stats['totalAttempts'] - $passCount;
            $stats['passRate'] = ($passCount / $stats['totalAttempts']) * 100;
            
            // Tạo dữ liệu cho biểu đồ phân phối điểm số
            $scoreRanges = [
                '0-10', '11-20', '21-30', '31-40', '41-50', 
                '51-60', '61-70', '71-80', '81-90', '91-100'
            ];
            
            $scoreDistribution = array_fill(0, count($scoreRanges), 0);
            
            foreach ($testAttempts as $attempt) {
                $score = $attempt->score;
                $index = min(floor($score / 10), 9); // Đảm bảo điểm số 100 nằm trong khoảng 91-100
                $scoreDistribution[$index]++;
            }
            
            $stats['scoreLabels'] = $scoreRanges;
            $stats['scoreDistribution'] = $scoreDistribution;
            
            // Thống kê theo thời gian
            $timeData = [];
            $dateLabels = [];
            
            // Lấy dữ liệu trong 30 ngày gần nhất
            for ($i = 0; $i < 30; $i++) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dateLabels[] = now()->subDays($i)->format('d/m');
                $timeData[] = $testAttempts->filter(function ($attempt) use ($date) {
                    return $attempt->created_at->format('Y-m-d') == $date;
                })->count();
            }
            
            // Đảo ngược mảng để hiển thị theo thứ tự tăng dần
            $stats['dateLabels'] = array_reverse($dateLabels);
            $stats['timeData'] = array_reverse($timeData);
        }
        
        return view('admin.tests.statistics', compact('test', 'testAttempts', 'stats'));
    }

    /**
     * Kích hoạt/Vô hiệu hóa bài kiểm tra
     */
    public function toggle($id)
    {
        $test = Test::findOrFail($id);
        
        // Đảo ngược trạng thái
        $test->is_active = !$test->is_active;
        $test->save();
        
        $status = $test->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->route('admin.tests.show', $test->id)
                        ->with('success', "Bài kiểm tra đã được $status thành công!");
    }
}
