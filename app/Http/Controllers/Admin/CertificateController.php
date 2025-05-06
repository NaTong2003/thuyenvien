<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Hiển thị danh sách chứng chỉ
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'test', 'issuer']);
        
        // Lọc theo tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', '%' . $search . '%')
                  ->orWhere('title', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Lọc theo trạng thái
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Lọc theo loại bài kiểm tra
        if ($request->has('test_id') && !empty($request->test_id)) {
            $query->where('test_id', $request->test_id);
        }
        
        $certificates = $query->orderBy('created_at', 'desc')->paginate(10);
        $tests = Test::all();
        
        return view('admin.certificates.index', compact('certificates', 'tests'));
    }

    /**
     * Hiển thị form tạo chứng chỉ mới
     */
    public function create()
    {
        $users = User::whereHas('role', function($q) {
            $q->where('name', 'Thuyền viên');
        })->get();
        $tests = Test::where('is_active', true)->get();
        
        return view('admin.certificates.create', compact('users', 'tests'));
    }

    /**
     * Lưu chứng chỉ mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'test_id' => 'nullable|exists:tests,id',
            'test_attempt_id' => 'nullable|exists:test_attempts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Tạo số chứng chỉ duy nhất
            $certificateNumber = 'CERT-' . date('Y') . '-' . Str::random(8);
            
            $certificate = new Certificate();
            $certificate->user_id = $request->user_id;
            $certificate->test_id = $request->test_id;
            $certificate->test_attempt_id = $request->test_attempt_id;
            $certificate->certificate_number = $certificateNumber;
            $certificate->title = $request->title;
            $certificate->description = $request->description;
            $certificate->issue_date = $request->issue_date;
            $certificate->expiry_date = $request->expiry_date;
            $certificate->status = 'active';
            $certificate->issued_by = auth()->id();
            
            // Xử lý upload file
            if ($request->hasFile('certificate_file')) {
                $file = $request->file('certificate_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('certificates', $fileName, 'public');
                $certificate->certificate_file = $filePath;
            }
            
            $certificate->save();
            
            DB::commit();
            return redirect()->route('admin.certificates.index')
                            ->with('success', 'Chứng chỉ đã được tạo thành công!');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị thông tin chi tiết chứng chỉ
     */
    public function show($id)
    {
        $certificate = Certificate::with(['user', 'test', 'testAttempt', 'issuer'])->findOrFail($id);
        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Hiển thị form chỉnh sửa chứng chỉ
     */
    public function edit($id)
    {
        $certificate = Certificate::findOrFail($id);
        $users = User::whereHas('role', function($q) {
            $q->where('name', 'Thuyền viên');
        })->get();
        $tests = Test::all();
        
        // Lấy các bài thi của thuyền viên
        $testAttempts = TestAttempt::where('user_id', $certificate->user_id)
                                 ->with('test')
                                 ->orderBy('created_at', 'desc')
                                 ->get();
        
        return view('admin.certificates.edit', compact('certificate', 'users', 'tests', 'testAttempts'));
    }

    /**
     * Cập nhật thông tin chứng chỉ
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:active,revoked,expired',
            'revocation_reason' => 'required_if:status,revoked|nullable|string',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        try {
            DB::beginTransaction();
            
            $certificate = Certificate::findOrFail($id);
            $certificate->title = $request->title;
            $certificate->description = $request->description;
            $certificate->issue_date = $request->issue_date;
            $certificate->expiry_date = $request->expiry_date;
            $certificate->status = $request->status;
            
            if ($request->status === 'revoked') {
                $certificate->revocation_reason = $request->revocation_reason;
            }
            
            // Xử lý upload file
            if ($request->hasFile('certificate_file')) {
                // Xóa file cũ nếu có
                if ($certificate->certificate_file && Storage::disk('public')->exists($certificate->certificate_file)) {
                    Storage::disk('public')->delete($certificate->certificate_file);
                }
                
                $file = $request->file('certificate_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('certificates', $fileName, 'public');
                $certificate->certificate_file = $filePath;
            }
            
            $certificate->save();
            
            DB::commit();
            return redirect()->route('admin.certificates.show', $certificate->id)
                            ->with('success', 'Chứng chỉ đã được cập nhật thành công!');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa chứng chỉ
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        // Xóa file đính kèm nếu có
        if ($certificate->certificate_file && Storage::disk('public')->exists($certificate->certificate_file)) {
            Storage::disk('public')->delete($certificate->certificate_file);
        }
        
        $certificate->delete();
        
        return redirect()->route('admin.certificates.index')
                        ->with('success', 'Chứng chỉ đã được xóa thành công!');
    }
    
    /**
     * Hiển thị form tạo chứng chỉ từ bài thi
     */
    public function createFromAttempt($attemptId)
    {
        $attempt = TestAttempt::with(['user', 'test'])->findOrFail($attemptId);
        
        // Kiểm tra xem bài thi có đạt điểm chuẩn không
        $test = $attempt->test;
        $passingScore = $test->passing_score ?? 50;
        
        if ($attempt->score < $passingScore) {
            return redirect()->back()->with('error', 'Bài thi này không đạt điểm chuẩn để cấp chứng chỉ!');
        }
        
        return view('admin.certificates.create_from_attempt', compact('attempt'));
    }
    
    /**
     * Lưu chứng chỉ từ bài thi
     */
    public function storeFromAttempt(Request $request, $attemptId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        try {
            DB::beginTransaction();
            
            $attempt = TestAttempt::with(['user', 'test'])->findOrFail($attemptId);
            
            // Tạo số chứng chỉ duy nhất
            $certificateNumber = 'CERT-' . date('Y') . '-' . Str::random(8);
            
            $certificate = new Certificate();
            $certificate->user_id = $attempt->user_id;
            $certificate->test_id = $attempt->test_id;
            $certificate->test_attempt_id = $attempt->id;
            $certificate->certificate_number = $certificateNumber;
            $certificate->title = $request->title;
            $certificate->description = $request->description;
            $certificate->issue_date = $request->issue_date;
            $certificate->expiry_date = $request->expiry_date;
            $certificate->status = 'active';
            $certificate->issued_by = auth()->id();
            
            // Xử lý upload file
            if ($request->hasFile('certificate_file')) {
                $file = $request->file('certificate_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('certificates', $fileName, 'public');
                $certificate->certificate_file = $filePath;
            }
            
            $certificate->save();
            
            DB::commit();
            return redirect()->route('admin.certificates.show', $certificate->id)
                            ->with('success', 'Chứng chỉ đã được tạo thành công!');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Tạo chứng chỉ PDF cho thuyền viên
     */
    public function generatePdf($id)
    {
        $certificate = Certificate::with(['user', 'test', 'issuer'])->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.certificates.pdf', compact('certificate'));
        
        return $pdf->download($certificate->certificate_number . '.pdf');
    }
    
    /**
     * Hiển thị lịch sử bài kiểm tra của thuyền viên
     */
    public function testHistory($userId)
    {
        $user = User::with('thuyenVien')->findOrFail($userId);
        $testAttempts = TestAttempt::where('user_id', $userId)
                                 ->with(['test', 'certificates'])
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(15);
        
        return view('admin.certificates.test_history', compact('user', 'testAttempts'));
    }
}
