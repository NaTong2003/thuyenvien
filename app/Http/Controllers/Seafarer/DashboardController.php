<?php

namespace App\Http\Controllers\Seafarer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\ThuyenVien;
use App\Models\Position;
use Illuminate\Support\Facades\DB;
use App\Models\Certificate;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard cho thuyền viên
     */
    public function index()
    {
        $user = Auth::user();
        
        // Lấy thông tin thuyền viên
        $thuyenVien = $user->thuyenVien;
        
        // Lấy các lượt kiểm tra gần đây của thuyền viên
        $recentTestAttempts = TestAttempt::where('user_id', $user->id)
                                ->with('test')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
        
        // Lấy danh sách bài kiểm tra có sẵn
        $position_id = $thuyenVien ? $thuyenVien->position_id : null;
        $ship_type_id = $thuyenVien ? $thuyenVien->ship_type_id : null;
        
        $availableTests = Test::where(function($query) use ($position_id, $ship_type_id) {
                            $query->where('position_id', $position_id)
                                  ->orWhereNull('position_id');
                        })
                        ->where(function($query) use ($ship_type_id) {
                            $query->where('ship_type_id', $ship_type_id)
                                  ->orWhereNull('ship_type_id');
                        })
                        ->orderBy('created_at', 'desc')
                        ->with(['position', 'shipType', 'questions'])
                        ->take(6)
                        ->get();
        
        // Lấy dữ liệu cho biểu đồ radar
        $skillScores = [];
        
        if (TestAttempt::where('user_id', $user->id)->count() > 0) {
            $skillScores = TestAttempt::join('tests', 'test_attempts.test_id', '=', 'tests.id')
                            ->join('categories', 'tests.category_id', '=', 'categories.id')
                            ->where('test_attempts.user_id', $user->id)
                            ->select('categories.name', DB::raw('AVG(test_attempts.score) as average_score'))
                            ->groupBy('categories.name')
                            ->get()
                            ->pluck('average_score', 'name')
                            ->toArray();
        }
        
        // Lấy thông tin chứng chỉ
        $activeCertificatesCount = Certificate::where('user_id', $user->id)
                                    ->where('status', 'active')
                                    ->where(function($query) {
                                        $query->whereNull('expiry_date')
                                            ->orWhere('expiry_date', '>', now());
                                    })
                                    ->count();
        
        $expiringSoonCertificatesCount = Certificate::where('user_id', $user->id)
                                    ->where('status', 'active')
                                    ->whereNotNull('expiry_date')
                                    ->where('expiry_date', '>', now())
                                    ->where('expiry_date', '<=', now()->addDays(30))
                                    ->count();
        
        $recentCertificates = Certificate::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->take(3)
                                ->get();
        
        return view('seafarer.dashboard', compact(
            'thuyenVien',
            'recentTestAttempts',
            'availableTests',
            'skillScores',
            'activeCertificatesCount',
            'expiringSoonCertificatesCount',
            'recentCertificates'
        ));
    }
}
