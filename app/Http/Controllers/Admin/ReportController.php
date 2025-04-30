<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Position;
use App\Models\ShipType;
use App\Models\Question;
use App\Models\TestQuestion;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Hiển thị trang danh sách báo cáo
     */
    public function index()
    {
        // Lấy số lượng thuyền viên
        $seafarerId = Role::where('name', 'Thuyền viên')->first()->id;
        $seafarerCount = User::where('role_id', $seafarerId)->count();
        
        // Lấy số lượng bài kiểm tra và lượt thi
        $testCount = Test::count();
        $testAttemptCount = TestAttempt::count();
        
        // Điểm trung bình toàn hệ thống
        $averageScore = TestAttempt::avg('score') ?? 0;
        
        // Tỷ lệ đạt
        $testAttempts = TestAttempt::all();
        $passCount = 0;
        foreach ($testAttempts as $attempt) {
            if ($attempt->isPassed()) {
                $passCount++;
            }
        }
        $passRate = $testAttemptCount > 0 ? round(($passCount / $testAttemptCount) * 100, 1) : 0;
        
        // Thống kê thuyền viên theo chức danh
        $seafarersByPosition = Position::leftJoin('thuyen_viens', 'positions.id', '=', 'thuyen_viens.position_id')
                            ->select('positions.name', DB::raw('COUNT(thuyen_viens.id) as count'))
                            ->groupBy('positions.id', 'positions.name')
                            ->get();
        
        // Thống kê điểm trung bình theo loại bài kiểm tra
        $averageScoresByTest = Test::leftJoin('test_attempts', 'tests.id', '=', 'test_attempts.test_id')
                            ->select('tests.id', 'tests.title', DB::raw('AVG(test_attempts.score) as average_score'), 
                                    DB::raw('COUNT(test_attempts.id) as attempt_count'))
                            ->groupBy('tests.id', 'tests.title')
                            ->having('attempt_count', '>', 0)
                            ->orderBy('average_score', 'desc')
                            ->get();
        
        // Lấy danh sách thuyền viên có thành tích tốt nhất
        $topSeafarers = User::whereHas('role', function($q) {
                        $q->where('name', 'Thuyền viên');
                    })
                    ->withCount('testAttempts')
                    ->withAvg('testAttempts as average_score', 'score')
                    ->having('test_attempts_count', '>', 0)
                    ->orderBy('average_score', 'desc')
                    ->take(5)
                    ->get();
        
        // Lượt thi gần đây
        $recentAttempts = TestAttempt::with(['user', 'test'])
                        ->orderBy('created_at', 'desc')
                        ->take(10)
                        ->get();
        
        return view('admin.reports.index', compact(
            'seafarerCount',
            'testCount',
            'testAttemptCount',
            'averageScore',
            'passRate',
            'seafarersByPosition',
            'averageScoresByTest',
            'topSeafarers',
            'recentAttempts'
        ));
    }
    
    /**
     * Hiển thị báo cáo xu hướng hiệu suất
     */
    public function performance(Request $request)
    {
        // Lấy tham số từ request
        $dateRange = $request->input('date_range', 90); // Mặc định 90 ngày
        $positionId = $request->input('position_id');
        $shipTypeId = $request->input('ship_type_id');
        $testId = $request->input('test_id');
        
        // Tính toán ngày bắt đầu dựa trên khoảng thời gian
        $startDate = now()->subDays($dateRange);
        
        // Query cơ bản
        $query = TestAttempt::whereBetween('created_at', [$startDate, now()]);
        
        // Áp dụng bộ lọc nếu có
        if ($positionId) {
            $query->whereHas('user.thuyenVien', function($q) use ($positionId) {
                $q->where('position_id', $positionId);
            });
        }
        
        if ($shipTypeId) {
            $query->whereHas('test', function($q) use ($shipTypeId) {
                $q->where('ship_type_id', $shipTypeId);
            });
        }
        
        if ($testId) {
            $query->where('test_id', $testId);
        }
        
        // Lấy dữ liệu hiệu suất theo thời gian
        $performanceData = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(score) as average_score'),
                DB::raw('COUNT(*) as attempt_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Lấy danh sách chức danh, loại tàu, bài kiểm tra cho bộ lọc
        $positions = Position::all();
        $shipTypes = ShipType::all();
        $tests = Test::all();
        
        // Tính toán số liệu tổng hợp
        $totalAttempts = $performanceData->sum('attempt_count');
        $averageScore = $performanceData->avg('average_score') ?? 0;
        
        // Tính xu hướng (so sánh với kỳ trước)
        $halfPoint = ceil($dateRange / 2);
        $firstHalf = $performanceData->take($halfPoint);
        $secondHalf = $performanceData->skip($halfPoint);
        
        $firstHalfAvg = $firstHalf->avg('average_score') ?? 0;
        $secondHalfAvg = $secondHalf->avg('average_score') ?? 0;
        
        $scoreTrend = $secondHalfAvg - $firstHalfAvg;
        $scoreTrendPercent = $firstHalfAvg > 0 ? ($scoreTrend / $firstHalfAvg) * 100 : 0;
        
        return view('admin.reports.performance_trend', compact(
            'performanceData',
            'positions',
            'shipTypes',
            'tests',
            'totalAttempts',
            'averageScore',
            'scoreTrend',
            'scoreTrendPercent',
            'dateRange'
        ));
    }
    
    /**
     * Hiển thị báo cáo cho một bài kiểm tra cụ thể
     */
    public function testReport($testId)
    {
        $test = Test::with(['position', 'shipType'])->findOrFail($testId);
        
        // Lấy các lượt thi của bài kiểm tra này
        $testAttempts = TestAttempt::with('user')
                        ->where('test_id', $testId)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        // Tính điểm trung bình
        $averageScore = $testAttempts->avg('score') ?? 0;
        
        // Tính tỷ lệ đạt
        $passCount = 0;
        foreach ($testAttempts as $attempt) {
            if ($attempt->isPassed()) {
                $passCount++;
            }
        }
        $passRate = $testAttempts->count() > 0 ? round(($passCount / $testAttempts->count()) * 100, 1) : 0;
        
        // Thống kê phân bố điểm
        $scoreDistribution = [
            '0-10' => $testAttempts->whereBetween('score', [0, 10])->count(),
            '11-20' => $testAttempts->whereBetween('score', [11, 20])->count(),
            '21-30' => $testAttempts->whereBetween('score', [21, 30])->count(),
            '31-40' => $testAttempts->whereBetween('score', [31, 40])->count(),
            '41-50' => $testAttempts->whereBetween('score', [41, 50])->count(),
            '51-60' => $testAttempts->whereBetween('score', [51, 60])->count(),
            '61-70' => $testAttempts->whereBetween('score', [61, 70])->count(),
            '71-80' => $testAttempts->whereBetween('score', [71, 80])->count(),
            '81-90' => $testAttempts->whereBetween('score', [81, 90])->count(),
            '91-100' => $testAttempts->whereBetween('score', [91, 100])->count(),
        ];
        
        // Lấy danh sách câu hỏi trong bài kiểm tra
        $testQuestions = TestQuestion::where('test_id', $testId)
                        ->with('question')
                        ->get()
                        ->pluck('question');
        
        return view('admin.reports.test', compact(
            'test',
            'testAttempts',
            'averageScore',
            'passRate',
            'scoreDistribution',
            'testQuestions'
        ));
    }
    
    /**
     * Hiển thị báo cáo cho một thuyền viên cụ thể
     */
    public function seafarerReport($userId)
    {
        $user = User::with('thuyenVien.position')->findOrFail($userId);
        
        // Lấy các lượt thi của thuyền viên
        $testAttempts = TestAttempt::with(['test.position', 'test.shipType'])
                        ->where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        // Tính điểm trung bình
        $averageScore = $testAttempts->avg('score') ?? 0;
        
        // Tính tỷ lệ đạt
        $passCount = 0;
        foreach ($testAttempts as $attempt) {
            if ($attempt->isPassed()) {
                $passCount++;
            }
        }
        $passRate = $testAttempts->count() > 0 ? round(($passCount / $testAttempts->count()) * 100, 1) : 0;
        
        // Lấy điểm theo từng loại bài kiểm tra
        $scoresByTestType = Test::join('test_attempts', 'tests.id', '=', 'test_attempts.test_id')
                            ->where('test_attempts.user_id', $userId)
                            ->select('tests.title', DB::raw('AVG(test_attempts.score) as average_score'))
                            ->groupBy('tests.id', 'tests.title')
                            ->get();
        
        // Xác định điểm mạnh và điểm yếu dựa trên các kỹ năng
        $categoriesPerformance = DB::table('test_attempts')
                        ->join('test_questions', 'test_attempts.test_id', '=', 'test_questions.test_id')
                        ->join('questions', 'test_questions.question_id', '=', 'questions.id')
                        ->join('categories', 'questions.category_id', '=', 'categories.id')
                        ->join('test_answers', function($join) {
                            $join->on('test_attempts.id', '=', 'test_answers.test_attempt_id')
                                ->on('questions.id', '=', 'test_answers.question_id');
                        })
                        ->where('test_attempts.user_id', $userId)
                        ->select(
                            'categories.id',
                            'categories.name',
                            DB::raw('COUNT(DISTINCT questions.id) as total_questions'),
                            DB::raw('SUM(CASE WHEN test_answers.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
                        )
                        ->groupBy('categories.id', 'categories.name')
                        ->get();
        
        $skillsPerformance = [];
        foreach ($categoriesPerformance as $category) {
            if ($category->total_questions > 0) {
                $percentage = ($category->correct_answers / $category->total_questions) * 100;
                $skillsPerformance[] = [
                    'name' => $category->name,
                    'percentage' => round($percentage, 1),
                ];
            }
        }
        
        // Sắp xếp theo % chính xác
        usort($skillsPerformance, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });
        
        return view('admin.reports.seafarer', compact(
            'user',
            'testAttempts',
            'averageScore',
            'passRate',
            'scoresByTestType',
            'skillsPerformance'
        ));
    }
    
    /**
     * Hiển thị báo cáo chi tiết cho một lần làm bài kiểm tra
     */
    public function attemptReport($attemptId)
    {
        $testAttempt = TestAttempt::with(['user', 'test', 'userResponses.question', 'userResponses.answer'])
                            ->findOrFail($attemptId);
        
        // Lấy thông tin người dùng và bài kiểm tra
        $user = $testAttempt->user;
        $test = $testAttempt->test;
        
        // Tính toán số liệu
        $totalQuestions = $testAttempt->userResponses->count();
        $correctAnswers = $testAttempt->userResponses->filter(function($response) {
            return $response->answer && $response->answer->is_correct;
        })->count();
        
        $incorrectAnswers = $totalQuestions - $correctAnswers;
        $accuracy = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
        
        // Kiểm tra trạng thái đạt hay không đạt
        $isPassed = $testAttempt->score >= $test->passing_score;
        
        // Tính thời gian làm bài
        $duration = null;
        if ($testAttempt->started_at && $testAttempt->completed_at) {
            $startTime = strtotime($testAttempt->started_at);
            $endTime = strtotime($testAttempt->completed_at);
            $duration = round(($endTime - $startTime) / 60); // Thời gian tính bằng phút
        }
        
        return view('admin.reports.attempt_detail', compact(
            'testAttempt',
            'user',
            'test',
            'totalQuestions',
            'correctAnswers',
            'incorrectAnswers',
            'accuracy',
            'isPassed',
            'duration'
        ));
    }
}
