<?php

namespace App\Http\Controllers\Seafarer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Hiển thị danh sách chứng chỉ của thuyền viên
     */
    public function index()
    {
        $user = Auth::user();
        $certificates = Certificate::where('user_id', $user->id)
                                ->with(['test', 'testAttempt', 'issuer'])
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Phân loại chứng chỉ
        $activeCertificates = $certificates->filter(function($certificate) {
            return $certificate->status == 'active' && 
                  (!$certificate->expiry_date || $certificate->expiry_date->isFuture());
        });
        
        $expiredCertificates = $certificates->filter(function($certificate) {
            return $certificate->status == 'expired' || 
                  ($certificate->expiry_date && $certificate->expiry_date->isPast());
        });
        
        $revokedCertificates = $certificates->filter(function($certificate) {
            return $certificate->status == 'revoked';
        });
        
        // Lấy bài thi đạt điểm chuẩn nhưng chưa có chứng chỉ
        $successfulTestsWithoutCertificate = TestAttempt::where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereDoesntHave('certificates')
            ->with('test')
            ->get()
            ->filter(function($attempt) {
                return $attempt->isPassed();
            });
        
        return view('seafarer.certificates.index', compact(
            'activeCertificates',
            'expiredCertificates',
            'revokedCertificates',
            'successfulTestsWithoutCertificate'
        ));
    }
    
    /**
     * Hiển thị chi tiết chứng chỉ
     */
    public function show($id)
    {
        $user = Auth::user();
        $certificate = Certificate::with(['test', 'testAttempt', 'issuer'])
                               ->where('user_id', $user->id)
                               ->findOrFail($id);
        
        return view('seafarer.certificates.show', compact('certificate'));
    }
    
    /**
     * Tải chứng chỉ dưới dạng PDF
     */
    public function download($id)
    {
        $user = Auth::user();
        $certificate = Certificate::with(['test', 'issuer'])
                               ->where('user_id', $user->id)
                               ->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.certificates.pdf', compact('certificate'));
        
        return $pdf->download($certificate->certificate_number . '.pdf');
    }
} 