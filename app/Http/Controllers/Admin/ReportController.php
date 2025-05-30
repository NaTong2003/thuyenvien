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
use App\Models\UserResponse;

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
        
        return view('admin.reports.performance_trend', compact(
            'performanceData',
            'positions',
            'shipTypes',
            'tests',
            'totalAttempts',
            'averageScore',
            'scoreTrend',
            'scoreTrendPercent',
            'dateRange',
            'topSeafarers'
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
        $user = User::with(['thuyenVien.position', 'thuyenVien.shipType', 'testAttempts.test'])
                    ->findOrFail($userId);
        
    
        // Lấy tất cả lượt thi của thuyền viên
        $testAttempts = $user->testAttempts()
                            ->with(['test', 'userResponses.question', 'userResponses.answer'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        // Tính toán các số liệu thống kê
        $totalAttempts = $testAttempts->count();
        $completedAttempts = $testAttempts->where('is_completed', true)->count();
        $averageScore = $testAttempts->where('is_completed', true)->avg('score') ?? 0;
        
        // Số lượt đạt/không đạt
        $passedAttempts = 0;
        $failedAttempts = 0;
        
        foreach ($testAttempts->where('is_completed', true) as $attempt) {
            if ($attempt->isPassed()) {
                $passedAttempts++;
            } else {
                $failedAttempts++;
            }
        }
        
        // Tỷ lệ đạt
        $passRate = $completedAttempts > 0 ? round(($passedAttempts / $completedAttempts) * 100, 1) : 0;
        
        // Lấy các chứng chỉ của thuyền viên
        $certificates = $user->certificates()->with('test')->orderBy('created_at', 'desc')->get();
        
        // Dữ liệu cho biểu đồ lịch sử điểm
        $scoreHistory = $testAttempts->where('is_completed', true)
                                   ->sortBy('created_at')
                                   ->map(function($attempt) {
                                       return [
                                           'date' => $attempt->created_at->format('d/m/Y'),
                                           'score' => $attempt->score,
                                           'test_name' => $attempt->test->title ?? 'N/A',
                                           'attempt_id' => $attempt->id
                                       ];
                                   })->values();
        
        // Dữ liệu cho biểu đồ hiệu suất theo loại bài kiểm tra
        $testPerformance = [];
        $attemptsByTest = $testAttempts->where('is_completed', true)->groupBy('test_id');
        
        foreach ($attemptsByTest as $testId => $attempts) {
            $test = Test::find($testId);
            if ($test) {
                $testPerformance[] = [
                    'test_name' => $test->title,
                    'average_score' => $attempts->avg('score'),
                    'attempts_count' => $attempts->count(),
                    'test_id' => $testId
                ];
            }
        }
        
        // Dữ liệu cho biểu đồ phân phối điểm số
        $scoreRanges = [
            '0-10', '11-20', '21-30', '31-40', '41-50', 
            '51-60', '61-70', '71-80', '81-90', '91-100'
        ];
        
        $scoreDistribution = array_fill(0, count($scoreRanges), 0);
        
        foreach ($testAttempts->where('is_completed', true) as $attempt) {
            $score = $attempt->score;
            $index = min(floor($score / 10), 9); // Đảm bảo điểm số 100 nằm trong khoảng 91-100
            $scoreDistribution[$index]++;
        }
        
        // Phân tích nội dung
        $strengths = [];
        $weaknesses = [];
        
        // Nếu có ít nhất một lượt thi
        if ($testAttempts->where('is_completed', true)->count() > 0) {
            // Phân tích câu trả lời để tìm điểm mạnh/yếu
            $userResponses = UserResponse::whereIn('test_attempt_id', $testAttempts->pluck('id'))
                                        ->with(['question', 'answer'])
                                        ->get();
            
            // Nhóm câu trả lời theo danh mục
            $responsesByCategory = [];
            
            foreach ($userResponses as $response) {
                if (isset($response->question) && $response->question) {
                    $category = $response->question->category ?? 'Khác';
                    
                    if (!isset($responsesByCategory[$category])) {
                        $responsesByCategory[$category] = [
                            'total' => 0,
                            'correct' => 0
                        ];
                    }
                    
                    $responsesByCategory[$category]['total']++;
                    
                    if ($response->isCorrect()) {
                        $responsesByCategory[$category]['correct']++;
                    }
                }
            }
            
            // Xác định điểm mạnh và điểm yếu
            foreach ($responsesByCategory as $category => $stats) {
                if ($stats['total'] >= 3) { // Chỉ xét các danh mục có ít nhất 3 câu trả lời
                    $correctRate = ($stats['correct'] / $stats['total']) * 100;
                    
                    if ($correctRate >= 70) {
                        $strengths[] = [
                            'category' => $category,
                            'correct_rate' => round($correctRate, 1),
                            'total' => $stats['total']
                        ];
                    } elseif ($correctRate <= 50) {
                        $weaknesses[] = [
                            'category' => $category,
                            'correct_rate' => round($correctRate, 1),
                            'total' => $stats['total']
                        ];
                    }
                }
            }
            
            // Sắp xếp điểm mạnh theo tỷ lệ đúng giảm dần
            usort($strengths, function($a, $b) {
                return $b['correct_rate'] <=> $a['correct_rate'];
            });
            
            // Sắp xếp điểm yếu theo tỷ lệ đúng tăng dần
            usort($weaknesses, function($a, $b) {
                return $a['correct_rate'] <=> $b['correct_rate'];
            });
        }
        
        return view('admin.reports.seafarer', compact(
            'user',
            'testAttempts',
            'totalAttempts',
            'completedAttempts',
            'averageScore',
            'passedAttempts',
            'failedAttempts',
            'passRate',
            'certificates',
            'scoreHistory',
            'testPerformance',
            'scoreRanges',
            'scoreDistribution',
            'strengths',
            'weaknesses'
        ));
    }
    
    /**
     * Hiển thị báo cáo chi tiết cho một lần làm bài kiểm tra
     */
    public function attemptReport($id)
    {
        $attempt = TestAttempt::with(['test', 'user', 'userResponses.question', 'userResponses.answer', 'certificates'])
                              ->findOrFail($id);
        
        // Tính toán thông tin bổ sung cần thiết cho view
        $test = $attempt->test;
        $user = $attempt->user;
        $testAttempt = $attempt;
        
        // Tính thời gian làm bài
        $duration = null;
        if ($attempt->start_time && $attempt->end_time) {
            $duration = $attempt->start_time->diffInMinutes($attempt->end_time);
        }
        
        // Đếm số câu trả lời đúng và sai
        $correctAnswers = 0;
        $incorrectAnswers = 0;
        
        foreach ($attempt->userResponses as $response) {
            if ($response->isCorrect()) {
                $correctAnswers++;
            } else {
                $incorrectAnswers++;
            }
        }
        
        // Tính độ chính xác
        $totalAnswers = $correctAnswers + $incorrectAnswers;
        $accuracy = ($totalAnswers > 0) ? round(($correctAnswers / $totalAnswers) * 100, 1) : 0;
        
        // Kiểm tra trạng thái đạt/không đạt
        $isPassed = ($attempt->score >= $test->passing_score);
        
        // Kiểm tra nếu đã có chứng chỉ được cấp
        $hasCertificate = $attempt->certificates()->count() > 0;
        
        return view('admin.reports.attempt_detail', compact(
            'attempt', 
            'test', 
            'user', 
            'testAttempt', 
            'correctAnswers', 
            'incorrectAnswers', 
            'accuracy', 
            'isPassed',
            'duration',
            'hasCertificate'
        ));
    }
    
    /**
     * Hiển thị trang chấm điểm bài thi có câu hỏi tự luận
     */
    public function markingPage(Request $request)
    {
        // Lấy danh sách bài thi cần chấm điểm
        $query = TestAttempt::where('needs_marking', true)
                         ->with(['test', 'user']);
                         
        // Xử lý tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('id', 'like', '%' . $search . '%')
                ->orWhereHas('test', function($testQuery) use ($search) {
                    $testQuery->where('title', 'like', '%' . $search . '%');
                });
            });
        }
        
        $attempts = $query->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        // Nếu có thông báo đã chấm điểm thành công, lưu số lượng vào session
        if (session('success') && strpos(session('success'), 'chấm điểm') !== false) {
            session(['recently_marked_count' => session('recently_marked_count', 0) + 1]);
        }
        
        return view('admin.reports.marking', compact('attempts'));
    }
    
    /**
     * Hiển thị chi tiết bài thi để chấm điểm
     */
    public function markAttempt($id)
    {
        $attempt = TestAttempt::with(['test', 'user', 'userResponses.question'])
                             ->findOrFail($id);
        
        // Lấy các câu trả lời cần chấm điểm (tự luận, tình huống, thực hành, mô phỏng)
        $subjectiveResponses = $attempt->userResponses()
                                    ->whereHas('question', function($query) {
                                        $query->whereIn('type', ['Tự luận', 'Tình huống', 'Thực hành', 'Mô phỏng']);
                                    })
                                    ->with(['question', 'question.category'])
                                    ->get();
        
        // Đếm số lượng câu hỏi và trạng thái
        $totalSubjective = $subjectiveResponses->count();
        $ungraded = $subjectiveResponses->whereNull('score')->count();
        $graded = $totalSubjective - $ungraded;
        
        return view('admin.reports.mark_attempt', compact(
            'attempt', 
            'subjectiveResponses',
            'totalSubjective',
            'ungraded',
            'graded'
        ));
    }
    
    /**
     * Lưu điểm số đã chấm
     */
    public function saveMarking(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $attempt = TestAttempt::findOrFail($id);
            
            // Lấy dữ liệu từ form
            $scores = $request->input('score', []);
            $comments = $request->input('comment', []);
            $responseIds = $request->input('response_id', []);
            
            $totalProcessed = 0;
            
            // Cập nhật điểm số và nhận xét cho từng câu trả lời
            foreach ($responseIds as $index => $responseId) {
                if (isset($scores[$index])) {
                    $response = UserResponse::findOrFail($responseId);
                    $score = $scores[$index] !== '' ? $scores[$index] : null;
                    $comment = $comments[$index] ?? null;
                    
                    $response->score = $score;
                    $response->admin_comment = $comment;
                    $response->save();
                    
                    if ($score !== null) {
                        $totalProcessed++;
                    }
                }
            }
            
            // Kiểm tra nếu tất cả câu hỏi chủ quan đã được chấm
            $allResponses = $attempt->userResponses()
                ->whereHas('question', function($query) {
                    $query->whereIn('type', ['Tự luận', 'Tình huống', 'Thực hành', 'Mô phỏng']);
                })
                ->count();
                
            $gradedResponses = $attempt->userResponses()
                ->whereHas('question', function($query) {
                    $query->whereIn('type', ['Tự luận', 'Tình huống', 'Thực hành', 'Mô phỏng']);
                })
                ->whereNotNull('score')
                ->count();
            
            // Nếu tất cả câu hỏi chủ quan đã được chấm
            if ($allResponses > 0 && $allResponses == $gradedResponses) {
                // Tính toán lại điểm tổng và cập nhật trạng thái bài thi
                $this->calculateFinalScore($attempt);
            }
            
            DB::commit();
            return redirect()->route('admin.reports.mark.attempt', $id)
                ->with('success', 'Đã lưu điểm thành công cho ' . $totalProcessed . ' câu trả lời.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi khi lưu điểm: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Tính toán điểm tổng của bài thi
     */
    private function calculateFinalScore(TestAttempt $attempt)
    {
        // Lấy tất cả các câu trả lời của bài thi
        $responses = $attempt->userResponses()->with('question')->get();
        
        $totalPoints = 0;
        $earnedPoints = 0;
        
        // Tính điểm cho từng loại câu hỏi
        foreach ($responses as $response) {
            $questionType = $response->question->type;
            
            // Xử lý câu hỏi khách quan (tự động chấm)
            if (in_array($questionType, ['Trắc nghiệm', 'Đúng/Sai', 'Ghép đôi'])) {
                $totalPoints++;
                
                if ($response->isCorrect()) {
                    $earnedPoints += 1;
                }
            }
            // Xử lý câu hỏi chủ quan (giám khảo chấm)
            else if (in_array($questionType, ['Tự luận', 'Tình huống', 'Thực hành', 'Mô phỏng']) && $response->score !== null) {
                $totalPoints++;
                $earnedPoints += $response->score;
            }
        }
        
        // Tính điểm tổng (thang điểm 100)
        if ($totalPoints > 0) {
            $finalScore = ($earnedPoints / $totalPoints) * 100;
            $attempt->score = round($finalScore, 2);
        } else {
            $attempt->score = 0;
        }
        
        // Cập nhật trạng thái đã chấm điểm
        $attempt->is_completed = true;
        $attempt->is_marked = true;
        $attempt->needs_marking = false;
        $attempt->save();
        
        return $attempt->score;
    }
    
    /**
     * Xuất danh sách thuyền viên có hiệu suất cao
     */
    public function export()
    {
        // Tính năng xuất dữ liệu báo cáo
        return redirect()->back()->with('info', 'Chức năng xuất dữ liệu đang được phát triển.');
    }
    
    /**
     * Tìm kiếm thuyền viên và kết quả bài kiểm tra
     */
    public function search(Request $request)
    {
        $search = $request->input('search');
        
        if (empty($search)) {
            return redirect()->route('admin.reports.index')
                            ->with('error', 'Vui lòng nhập từ khóa tìm kiếm.');
        }
        
        // Tìm thuyền viên theo tên hoặc thông tin liên hệ
        $seafarerId = Role::where('name', 'Thuyền viên')->first()->id;
        
        $users = User::where('role_id', $seafarerId)
                    ->where(function($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                              ->orWhere('email', 'like', '%' . $search . '%')
                              ->orWhere('phone', 'like', '%' . $search . '%');
                    })
                    ->with(['thuyenVien.position', 'testAttempts.test'])
                    ->get();
        
        // Lấy các lượt thi của các thuyền viên tìm được
        $testAttempts = TestAttempt::whereIn('user_id', $users->pluck('id'))
                                  ->with(['user', 'test'])
                                  ->orderBy('created_at', 'desc')
                                  ->get();
        
        // Tính toán thống kê
        $stats = [
            'userCount' => $users->count(),
            'attemptCount' => $testAttempts->count(),
            'averageScore' => $testAttempts->avg('score') ?? 0,
        ];
        
        // Tính số lượt đạt/không đạt
        $passCount = 0;
        foreach ($testAttempts as $attempt) {
            if ($attempt->isPassed()) {
                $passCount++;
            }
        }
        
        $stats['passCount'] = $passCount;
        $stats['failCount'] = $testAttempts->count() - $passCount;
        $stats['passRate'] = $testAttempts->count() > 0 ? round(($passCount / $testAttempts->count()) * 100, 1) : 0;
        
        return view('admin.reports.search_results', compact('users', 'testAttempts', 'stats', 'search'));
    }
}
