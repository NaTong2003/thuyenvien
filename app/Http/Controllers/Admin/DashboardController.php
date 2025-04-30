<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Position;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard cho admin
     */
    public function index()
    {
        // Lấy số lượng thuyền viên
        $seafarerId = Role::where('name', 'Thuyền viên')->first()->id;
        $seafarerCount = User::where('role_id', $seafarerId)->count();
        
        // Lấy số lượng câu hỏi, bài kiểm tra và lượt thi
        $questionCount = Question::count();
        $testCount = Test::count();
        $testAttemptCount = TestAttempt::count();
        
        // Lấy danh sách bài kiểm tra gần đây
        $recentTests = Test::with(['position', 'shipType'])
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
        
        // Lấy danh sách lượt thi gần đây
        $recentTestAttempts = TestAttempt::with(['user', 'test'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        
        // Thống kê thuyền viên theo chức danh
        $seafarersByPosition = Position::leftJoin('thuyen_viens', 'positions.id', '=', 'thuyen_viens.position_id')
                            ->select('positions.name', DB::raw('COUNT(thuyen_viens.id) as count'))
                            ->groupBy('positions.id', 'positions.name')
                            ->get();
        
        // Điểm trung bình theo loại bài kiểm tra
        $averageScoresByTest = Test::leftJoin('test_attempts', 'tests.id', '=', 'test_attempts.test_id')
                            ->select('tests.title', DB::raw('AVG(test_attempts.score) as average_score'))
                            ->groupBy('tests.id', 'tests.title')
                            ->get();
        
        return view('admin.dashboard', compact(
            'seafarerCount', 
            'questionCount', 
            'testCount', 
            'testAttemptCount',
            'recentTests',
            'recentTestAttempts',
            'seafarersByPosition',
            'averageScoresByTest'
        ));
    }
}
